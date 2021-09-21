#!/bin/bash

## The instances' public URL
APP_URL=${APP_URL:-}

## Application key
APP_KEY=${APP_KEY:-}

## Application environment
APP_ENV=${APP_ENV:-production}

## Enable/Disable the debug mode
APP_DEBUG=${APP_DEBUG:-false}

## Maximum file size for upload (KB)
UPLOAD_LIMIT=${UPLOAD_LIMIT:-204800}

## Database connection
DB_DATABASE=${DB_DATABASE:-tmi}
DB_HOST=${DB_HOST:-127.0.0.1}
DB_USERNAME=${DB_USERNAME:-tmi}
DB_PASSWORD=${DB_PASSWORD:-}
DB_TABLE_PREFIX=${DB_TABLE_PREFIX:-tmi_}

## Administration account
ADMIN_USERNAME=${ADMIN_USERNAME:-}
ADMIN_PASSWORD=${ADMIN_PASSWORD:-}

## User under which the commands will run
SETUP_USER=www-data

function startup_config () {

    # echo "- Writing php configuration..."

    # if [ -z "$PHP_POST_MAX_SIZE" ]; then
    #     # calculating the post max size based on the upload limit
    #     PHP_POST_MAX_SIZE_CALCULATION=$((UPLOAD_LIMIT+20048))
    #     PHP_POST_MAX_SIZE="${PHP_POST_MAX_SIZE_CALCULATION}K"
    # fi

    # if [ -z "$PHP_UPLOAD_MAX_FILESIZE" ]; then
    #     # calculating the upload max filesize based on the upload limit
    #     PHP_UPLOAD_MAX_FILESIZE_CALCULATION=$((UPLOAD_LIMIT+2048))
    #     PHP_UPLOAD_MAX_FILESIZE="${PHP_UPLOAD_MAX_FILESIZE_CALCULATION}K"
    # fi
    
    # # Set post and upload size for php if customized for the specific deploy
    # cat > /usr/local/etc/php/conf.d/php-runtime.ini <<-EOM &&
	# 	post_max_size=${PHP_POST_MAX_SIZE}
    #     upload_max_filesize=${PHP_UPLOAD_MAX_FILESIZE}
    #     memory_limit=${PHP_MEMORY_LIMIT}
    #     max_input_time=${PHP_MAX_INPUT_TIME}
    #     max_execution_time=${PHP_MAX_EXECUTION_TIME}
	# EOM

    init_empty_dir $DIR/storage && 
    echo "Changing folder groups and permissions" &&
    chgrp -R $SETUP_USER $DIR/storage &&
    chgrp -R $SETUP_USER $DIR/bootstrap/cache &&
    chmod -R g+rw $DIR/bootstrap/cache &&
    chmod -R g+rw $DIR/storage &&
    write_config &&
    wait_mariadb &&
    update &&
    chgrp -R $SETUP_USER $DIR/storage/logs &&
    chgrp -R $SETUP_USER $DIR/bootstrap/cache &&
    chmod -R g+rw $DIR/bootstrap/cache &&
    chmod -R g+rw $DIR/storage/logs &&
	echo "Configured."
    
    su -s /bin/sh -c "php artisan storage:link" $SETUP_USER
}

function write_config() {

    if [ -z "$APP_URL" ]; then
        # application URL not set
        echo "**************"
        echo "Public URL not set. Set the public URL using APP_URL."
        echo "**************"
        return 240
    fi
    
    if [ -z "$APP_KEY" ]; then
        # application Key not set
        echo "**************"
        echo "App KEY not set. Set the application key using APP_KEY."
        echo "**************"
        return 240
    fi

    echo "- Writing env file..."

	cat > ${DIR}/.env <<-EOM &&
		APP_KEY=${APP_KEY:-}
		APP_URL=${APP_URL}
		APP_ENV=${APP_ENV}
		APP_DEBUG=${APP_DEBUG}
		UPLOAD_LIMIT=${UPLOAD_LIMIT}
		DB_DATABASE=${DB_DATABASE}
		DB_HOST=${DB_HOST}
		DB_USERNAME=${DB_USERNAME}
		DB_PASSWORD=${DB_PASSWORD}
		DB_TABLE_PREFIX=${DB_TABLE_PREFIX}
	EOM

    su -s /bin/sh -c "php artisan config:clear" $SETUP_USER

    if [ -z "$APP_KEY" ] && [ -n "$PLAY_WITH_DOCKER" ]; then
        # generate a temporary key if we run under play with docker
        echo "**************"
        echo "Generating temporary APP_KEY."
        echo "**************"
        
        php artisan key:generate
    fi

	echo "- ENV file written! $DIR/.env"
}

function update() {
    cd ${DIR} || return 242
    echo "- Launching update procedure..."
    su -s /bin/sh -c "php artisan migrate --force" $SETUP_USER
    # create_admin
}

function wait_mariadb () {
    wait_command mariadb_test 6 10
}

function mariadb_test () {
   php -f /usr/local/bin/db-connect-test.php -- -d "${DB_DATABASE}" -H "${DB_HOST}" -u "${DB_USERNAME}" -p "${DB_PASSWORD}"
}

function wait_command () {
    local command=$1
    local retry_times=$2
    local sleep_seconds=$3

    for i in $(seq "$retry_times"); do
        echo "- Waiting for ${command} ... Retry $i"
        if [[ "$command" -eq 0 ]]; then
            return 0
        else
            sleep "$sleep_seconds"
        fi
    done
    return 1
}


function create_admin () {

    if [ -z "$ADMIN_USERNAME" ] &&  [ -z "$ADMIN_PASSWORD" ]; then
        # if both username and password are not defined or empty, tell to create the user afterwards an end return
        echo "**************"
        echo "Remember to create an admin user: php artisan create-admin --help"
        echo "**************"
        return 0
    fi

    if [ -z "$ADMIN_USERNAME" ] &&  [ -n "$ADMIN_PASSWORD" ]; then
        # username not set, but password set => error
        echo "**************"
        echo "Admin email not specified. Please specify an email address using the variable ADMIN_USERNAME"
        echo "**************"
        return 240
    fi
    
    if [ -n "$ADMIN_USERNAME" ] &&  [ -z "$ADMIN_PASSWORD" ]; then
        # username set, but empty password => the user needs to be created after the setup
        echo "**************"
        echo "Skipping creation of default administrator. Use php artisan create-admin after the startup is complete."
        echo "**************"
        return 0
    fi

    su -s /bin/sh -c "php artisan create-admin '$ADMIN_USERNAME' --password '$ADMIN_PASSWORD'" $SETUP_USER

    local ret=$?
    if [ $ret -eq 2 ]; then
        echo "Admin user is already there, good for us"
        return 0
    elif [ $ret -eq 0 ]; then
        return 0
    else
        echo "Admin user creation fail. Error $ret"
        return $ret
    fi
}

## Initialize an empty storage directory with the required default folders
function init_empty_dir() {
    local dir_to_init=$1

    echo "- Checking storage directory structure..."

    if [ ! -d "${dir_to_init}/framework/cache" ]; then
        mkdir -p "${dir_to_init}/framework/cache"
        echo "-- [framework/cache] created."
    fi
    if [ ! -d "${dir_to_init}/framework/cache/data" ]; then
        mkdir -p "${dir_to_init}/framework/cache/data"
        echo "-- [framework/cache/data] created."
    fi
    if [ ! -d "${dir_to_init}/framework/sessions" ]; then
        mkdir -p "${dir_to_init}/framework/sessions"
        echo "-- [framework/sessions] created."
    fi
    if [ ! -d "${dir_to_init}/framework/views" ]; then
        mkdir -p "${dir_to_init}/framework/views"
        echo "-- [framework/views] created."
    fi
    if [ ! -d "${dir_to_init}/logs" ]; then
        mkdir -p "${dir_to_init}/logs"
        echo "-- [logs] created."
    fi

}

startup_config >&2
