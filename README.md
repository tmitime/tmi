
# About TMI

TMI (tmaɪ) is a time tracker designed to help you stay on track during a consultancy.

If you used an Excel sheet in the past to track activities you know that at some
point tasks are insterted in batches and formulas were required to understand
at which progress of the project or consultancy you are just to find out that
you worked more hours than planned.

Time tracking should be simple and integrated in your workflow. 

🚧 **TMI is a work in progress**. Please, try it out and report back using [Discussions](https://github.com/tmitime/tmi/discussions).


**Current features**

- Projects, a container for the tasks (or activities) you track
- Tasks define the activity and how many minutes or hours you worked on it 
- Check a project status using the summary views 
- One tag (`tmi:Task` or `tmi:Meeting`) for each task to not be overwhelmed on the categorization of each task
- Teams to group projects together, especially useful if you have more that one project with a client

**Considered features**

- Define a work hours schedule on a project basis, useful in case by contract you have defined slots ([discussion#6](https://github.com/tmitime/tmi/discussions/6))
- Scrum ready, include in the tags hierarchy scrum specific naming ([discussion#7](https://github.com/tmitime/tmi/discussions/7))
- Auto-tagging based on task description, you should not waste time applying tags to tasks ([discussion#8](https://github.com/tmitime/tmi/discussions/8))
- Timers shared across devices, start your timer on one device and continue on the other ([discussion#10](https://github.com/tmitime/tmi/discussions/10))
- Connection to Gitlab to synchronize projects and tasks ([discussion#11](https://github.com/tmitime/tmi/discussions/11))

And [many more](https://github.com/tmitime/tmi/discussions?discussions_q=label%3A%22Under+Consideration+%28idea%29%22).

> Considered features are based on [suggested Ideas](https://github.com/tmitime/tmi/discussions/categories/ideas)

## Installation

_to be documented_


## Usage

_to be documented_


## Development

### Getting started

TMI is built using the [Laravel framework](https://laravel.com/) and 
[Jetstream](https://jetstream.laravel.com/2.x/introduction.html). 
[Livewire](https://laravel-livewire.com/) is used to deliver dynamic
components, while [TailwindCSS](https://tailwindcss.com/) powers
the UI styling.

Given the selected stack TMI requires:

- [PHP 7.4](https://www.php.net/) or above
- [Composer 2](https://getcomposer.org/)
- [NodeJS](https://nodejs.org/en/) version 12 or above with [Yarn](https://classic.yarnpkg.com/en/docs/install) package manager (v1.x)
- [MariaDB](https://mariadb.org/) version 10.6 or above
- [Docker](https://www.docker.com/)

### Testing

```
composer test
```

## Changelog

Please see [CHANGELOG](./CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](./.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](https://github.com/tmitime/tmi/security/policy) on how to report security vulnerabilities.

## Credits

- [Alessio](https://github.com/avvertix)
- [All Contributors](https://github.com/tmitime/tmi/contributors)

## License

TMI is open-sourced software licensed under the [AGPL-3.0 license](https://opensource.org/licenses/AGPL-3.0).
