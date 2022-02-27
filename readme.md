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

## Symfony profiler and debugging

Installation: `composer require profiler --dev`

This adds a profler bar at the bottom of the page like in zend; Click on any of the clickable areas to expand. It also adds a `dump` and `dd` (dump and die) function.

Another bundle for debugging is debug which also installs monolog: `composer require debug`, which installs `symfony/debug-pack`, which is basically a meta package of other packages in a [composer.json](https://github.com/symfony/debug-pack/blob/main/composer.json) file indicating which bundle of libraries will be installed.

This turns on logging and puts the output of `dump` in the debug toolbar instead of the page. It also adds a `server:dump` console command to dump output in the console.

## Assets (CSS, images etc)

Assets can be put in the public directory like in any static site. The `asset` function (`composer require symfony/asset`) can (but doesn't have to be) used for this, which provides autmoatic path updates and prefixes from config files:

    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

Another option is to use webpack encore with minification, sass and react support (will be covered later).

## Generate URLs

We can check all routes with `php bin/console debug:router`. Each route has a name that can be either explicitely defined in the Controller (`@Route("/questions/{slug}", name="app_question")` or is autogenerated, e.g. `app_question_show`

Link targets can then be replaced with `{{  path('app_question_show', { slug: 'reversing-a-spell' }) }}`

## JSON API Endpoint

We can return JSON using JsonResponse in the controller:

`return new JsonResponse(['votes' => $currentVoteCount]);`

OR

`return $this->json(['votes' => $currentVoteCount]);`

## Javascript, Ajax & the profiler

We can add e.g. jquery either outside or inside the javascript block we defined in `base.html.twig`. Here we'll do it inside:

    {% block javascripts %}
        <script
                src="https://code.jquery.com/jquery-3.6.0.min.js"
                integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4="
                crossorigin="anonymous"></script>
    {% endblock %}

When we want to add javascript in a page that extends this base layout, we would usually overwrite the default, meaning the jquery include. We can also *add* to the defaults of blocks using `{{ parent() }}:

    {% block javascripts %}
        {{ parent() }}
        <script src="{{ asset('js/question_show.js') }}"></script>
    {% endblock %}

Ajax requests are also shown in the profiler toolbar.


## Smart routes: POST-only & validate {Wildcards}

We can tell our routes - here the JSON API route - to only respond to POST requests:

`@Route("/comments/{id}/vote/{direction}", methods="POST")`

We can check in the console which url matches which route (--method is optional and defaults to GET):

`php bin/console router:match /comments/10/vote/up --method=POST`                                                     
This shows details to our new route. Problem: `/comments/10/vote/banana` would also match it, though we only accept / expect `up` or `down`.

We can indicate which values are accepted with regular expressions:

    @Route("/comments/{id}/vote/{direction<up|down>}", methods="POST")

We could do the same with `{id<\d+>}`, but we won't as if something invalid is put there, the DB will simply not find it (and the DB layer will also take care of injection attacks etc.).

## Service Objects (=Services)

Definition: Just objects that provide services such as rendering, logging etc. Everything in symfony is done by a service. `$this->render` is just a shortcut for the render method of the twig service.

`php bin/console debug:autowiring` gives a list of all (currently) available service objects in the app, add a substring as an argument to filter.

Example: adding a logger is just adding a parameter to a controller method:

    public function commentVote($id, $direction, LoggerInterface $logger) {
            $logger->info('Voting ' . $direction);
            // ...

Another example (twig service):

    public function homepage(Environment $twigEnvironment) {
        $html = $twigEnvironment->render('question/homepage.html.twig');
        return new Response($html);
        //same as return $this->render('question/homepage.html.twig');
    }

## Webpack encore (intro)

Layer on top of webpack

[node](https://nodejs.org/en/download/) and [yarn](https://classic.yarnpkg.com/lang/en/docs/install/) must be installed.

Run `composer require "encore:^1.8"` (leave version out for latest version, this is to be compatible with the course) to install the webpack-encore-bundle, which adds a webpack.config.js and a package.json and adds `node_modules` etc. to the `.gitignore` file.

Now run `yarn install` to install the JS libriaries from `package.json`

This adds an "assets" directory in the project root with css and js examples.

Running `yarn watch` watches for changes there, runs webpack and adds them to the `public/build` directory. `yarn build` also minifies them. See `package.json` for available commands.

The scripts can be added now to the twig temples using `{{ encore_entry_link_tags('app') }}` for the css and `{{ encore_entry_script_tags('app') }}` for the javascript.

We can now move the stuff from the old css files to the `assets/styles/app.css` and delete the old one in `public`, the same for the javascript.

Using encore enables us to install and use libraries using `yarn add [package name]` and import them in JS scripts.

Example (replace CDN include by installing it with yarn)

`yarn add jquery`

assets/app.js

    import $ from 'jquery';

`yarn watch` will automatically update / recompile the javascript.

The same can be done with the bootstrap include (`yarn add bootstrap`, then add `@import "~bootstrap";` to the top of app.css).