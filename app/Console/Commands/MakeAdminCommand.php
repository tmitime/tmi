<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use App\Actions\Fortify\CreateNewUser;
use Illuminate\Validation\ValidationException;

class MakeAdminCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tmi:admin
                            {--email= : The user email address.}
                            {--password= : The user password.}
                            {--name= : The user name to use. Default the email user.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create an admin user';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $email = $this->option('email');
        $name = $this->option('name') ?? $this->getUsernameFrom($email);
        $password = $this->option('password');
            
        if (empty($password) && $this->input->isInteractive()) {
            $password = $this->secret("Please specify an 8 character password for the administrator");
        }

        $createUserAction = new CreateNewUser();

        try{
            $user = $createUserAction->create([
                'name' => $name,
                'email' => $email,
                'password' => $password,
                'password_confirmation' => $password,
            ]);
            
            $user->role = User::ROLE_MANAGER;
            $user->save();
    
            $this->line('');
            $this->line("TMI Administrator, <comment>$email</comment>, created.");       
            $this->line('');
    
            return self::SUCCESS;
        }
        catch(ValidationException $ex)
        {
            if($ex->validator->errors()->has('email') && 
               $ex->validator->errors()->first('email') === 'The email has already been taken.'){

                $this->line('');
                $this->error("User already existing");
                $this->line('');

                return self::INVALID;
            }
  

            $this->line('');
            $this->error("Validation errors");
            $this->line('');

            foreach ($ex->errors() as $key => $messages) {
                $this->comment($key);

                foreach ($messages as $message) {
                    $this->line("  - {$message}");
                }
                $this->line('');
            }

            return self::FAILURE;
        }

    }

    private function getUsernameFrom($email)
    {
        $et_offset = strpos($email, '@');
        return $et_offset !== false ? substr($email, 0, $et_offset) : $email;
    }
}
