# Charming development with symfony course notes

## Installation

[Installer](https://symfony.com/download)

Requirements for local development (when not using containers): https://symfony.com/doc/5.4/setup.html

## Symfony CLI and other useful commands

- `symfony new project_name` what it says, also creates git repo
- `symfony check:req` checks requirements
- `symfony serve` starts local web server; optionally
  - install `apt install libnss3-tools` to automatically install the certificates in the firefox and chrome
  - **then**, install ca certificate  symfony server:ca:install to use SSL
  `symfony serve -d` to start the server in the background
  `--help` for more commands such as `symfony server:status` etc



## Project structure

- `config`
- `public` contains front controller and other assets
- `src`

## IDE setup

- Install the symphony support, PHP annotations and php toolbox plugins in IntelliJ / PHPStorm
- Make sure `synchronize IDE settings with composer` is enabled in setting ->languages and frameworks -> php -> composer

## Routes, controllers & responses

- Route defines the page URL (`config/routes.yaml` - see default example in that file for usage - or using annotations, in which case we don't need the yaml)
- Controller: PHP code for that page (`src/Controller`); The controller must be App\Controller; PHPStorm will handle this automatically with the above plugins when creating a new PHP class using right click
- Actions: Controller functions. No "Action" suffix necessary like in Zend1
  - must return a symfony response object ( `Symfony\Component\HttpFoundation\Response`)

## Annotations and wildcard routes

Install annotations support with `composer require annotations`.

Now we can add annotations above the Controller methods and don't need the `routes.yaml` file anymore.

src/Controller/QuestionController.php

    namespace App\Controller;
    use Symfony\Component\HttpFoundation\Response;
    class QuestionController
    {
        /**
         * @Route("/")
         */
        public function homepage() {
            return new Response("Hey hey");
        }
    }

Wildcard routes / slugs example:

    /**
     * @Route("/questions/{slug}")
     */
    public function show($slug) {
        return new Response(sprintf('Question "%s"', 
                                    str_$slug));
    }

## The symfony console command

`bin/console`
