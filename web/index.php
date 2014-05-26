<?php
/**
 * User: Pavlov Semyen
 * Date: 5/26/14
 */

require_once __DIR__.'/../vendor/autoload.php';

$app = new Silex\Application();

if (getenv('APP_ENV') != 'prod') {
    error_reporting(E_ALL);
    ini_set('error_reporting', E_ALL | E_STRICT);
    ini_set('display_errors', 'on');
    ini_set('log_errors', 'on');
    $app['debug'] = true;
}

//providers
$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
    'db.options' => array(
        'driver'    => 'pdo_mysql',
        'host'      => 'localhost',
        'dbname'    => 'cinema_example',
        'user'      => 'root',
        'password'  => '',
        'charset'   => 'utf8',
    ),
));
$app->register(new Silex\Provider\TwigServiceProvider(), array('twig.path' => __DIR__.'/../views'));
$app->register(new Silex\Provider\TranslationServiceProvider(), array('translator.messages' => array())) ;
$app->register(new Silex\Provider\FormServiceProvider());
$app->register(new Silex\Provider\UrlGeneratorServiceProvider());

//routes
$app->mount('/', include __DIR__.'/../controllers/index.php');
$app->mount('/api/cinema/', include __DIR__.'/../controllers/cinema.php');
$app->mount('/api/film/', include __DIR__.'/../controllers/film.php');
$app->mount('/api/session/', include __DIR__.'/../controllers/seance.php');
$app->mount('/api/tickets/', include __DIR__.'/../controllers/ticket.php');

$app->run();
