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

- `php bin/console` adds useful commands such as `php bin/console debug:router`
- commands can be added by hand or by packages

## Flex, Recipes and Aliases

We'll use "security checker" as an example for a symfony recipe (DEPRECATED AND WILL NOT WORK).

`symfony/flex` is a composer plugin that adds the possibility to add [recipes](https://flex.symfony.com/) (DEPRECATED) using composer.

`composer require sec-checker`

sec-checker is one alias for `sensiolabs/security_checker`.

The recipes get added in `symfony.lock` and add their own yaml files in `config/packages` which add new `bin/console` commands, in this case `security:check`

The recipes are pulled from `https://github.com/symfony/recipes` and `https://github.com/symfony/recipes-contrib`.

- `composer recipes` shows installed recipes. `console` itself is a recipe.
- `composer remove` removes the package and the recipe commands

## Twig

The templating engine `twig` is a recipe that can be installed (`composer require twig`).

This adds a bundle to `bundles.php`. A bundle is a symphony plugin.

Installing the twig bundle also creates a `templates` directory and a `config/packages/twig.yaml`.

Twig templates under `templates` are named `*.html.twig`

We can extend `Symfony\Bundle\FrameworkBundle\Controller\AbstractController` to get access to shortcut methods such as `render` (which returns a response object like we did before "by hand"):

    public function show($slug) {
        return $this->render('question/show.html.twig', [
            'question' => str_replace('-', ' ', $slug)
        ]);
    }

Twig syntax (similar to Django):

Rendering variables:

`{{ varname }}` renders the variable `varname`. This also allows ternary operators and the twig language (javascript-like).

Loops, blocks etc ("do something"):

`{% %}`

Comments:

{# comment #}

A twig component can extend another one. 

- Block names are arbitrary.
- Blocks can have default content

templates/base.html.twig:

    <!doctype html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport"
              content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>{% block title %}Welcome!{% endblock %}</title>
        {% block stylesheets %}{% endblock %}
    </head>
    <body>
    {% block body %}{% endblock %}
    {% block javascript %}{% endblock %}
    </body>
    </html>

templates/question/show.html.twig:

    {% extends 'base.html.twig' %}
    
    {% block body %}
    <h1>{{ question }}</h1>
    <div>
        Question will be shown here
    </div>
    <h2>Answers ({{ answers|length }})</h2>
    <ul>
        {% for answer in answers %}
            <li>{{ answer }}</li>
        {% endfor %}
    </ul>
    {% endblock %}

[Twig documentation and quick reference](https://twig.symfony.com/doc/3.x/)

## Symfony profiler