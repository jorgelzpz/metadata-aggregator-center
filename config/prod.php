<?php

// configure your app for the production environment

$app['twig.path'] = array(__DIR__.'/../templates');
//$app['twig.options'] = array('cache' => '/tmp/cache/twig');

// Where to store pyFF configuration files
$app['pyff_config_dir'] = '/tmp';

// Path where generated metadata sets will be stored
$app['metadata_output_dir'] = '/tmp';

// pyFF path
$app['pyff_command'] = '/opt/pyff/bin/python /opt/pyff/bin/pyff';

// pyFF maximum running time
$app['pyff_timeout'] = 180;

// pyFF cache directory
$app['pyff_cache_dir'] = '/tmp';

// simpleSAMLphp directory
$app['ssp_directory'] = '/var/simplesamlphp/';

// simpleSAMLphp SP identifier
$app['ssp_sp'] = 'default-sp';
