<?php

use Silex\Application;
use Silex\Provider\AssetServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;
use Silex\Provider\HttpFragmentServiceProvider;
use Silex\Provider\DoctrineServiceProvider;
use Silex\Provider\FormServiceProvider;
use Silex\Provider\CsrfServiceProvider;


$app = new Application();
$app->register(new ServiceControllerServiceProvider());
$app->register(new AssetServiceProvider());
$app->register(new TwigServiceProvider());
$app->register(new HttpFragmentServiceProvider());
$app->register(new Silex\Provider\ValidatorServiceProvider());
$app->register(new FormServiceProvider());
$app->register(new Silex\Provider\LocaleServiceProvider());
$app->register(new Silex\Provider\RoutingServiceProvider());
$app->register(new Silex\Provider\TranslationServiceProvider(), [
        'translator.domains' => [],
]);
$app->register(new CsrfServiceProvider());
$app->register(new Silex\Provider\DoctrineServiceProvider(), [
    'db.options' => [
        'driver'   => 'pdo_sqlite',
        'path'     => __DIR__.'/../db/db.sqlite',
    ]
]);
$app->register(new Silex\Provider\MonologServiceProvider(), array(
    // TODO change this path
    'monolog.logfile' => '/tmp/metadata-center.log',
));


$app['twig'] = $app->extend('twig', function ($twig, $app) {
    // add custom globals, filters, tags, ...

    $twig->addExtension(new Twig_Extensions_Extension_Text());
    return $twig;
});


$app['sets.repository'] = function() use ($app)
{
    return new RedIRIS\MetadataCenter\Repository\Sets(
        $app['db']
    );
};

$app['pyff.settings'] = function() use ($app)
{
    return new RedIRIS\MetadataCenter\PyFF\Settings(
        $app['pyff_command'],
        $app['pyff_timeout'],
        $app['pyff_config_dir'],
        $app['pyff_cache_dir'],
        $app['metadata_output_dir']
    );
};

$app['command.sets.generate'] = function() use ($app)
{
    return new RedIRIS\MetadataCenter\Command\Generate(
        $app['sets.repository'],
        $app['pyff.settings']
    );
};

return $app;
