<?php

use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__).'/vendor/autoload.php';

// ignore apidoc for annotations
\Doctrine\Common\Annotations\AnnotationReader::addGlobalIgnoredName('api');
\Doctrine\Common\Annotations\AnnotationReader::addGlobalIgnoredName('apiDefine');
\Doctrine\Common\Annotations\AnnotationReader::addGlobalIgnoredName('apiDeprecated');
\Doctrine\Common\Annotations\AnnotationReader::addGlobalIgnoredName('apiDescription');
\Doctrine\Common\Annotations\AnnotationReader::addGlobalIgnoredName('apiError');
\Doctrine\Common\Annotations\AnnotationReader::addGlobalIgnoredName('apiErrorExample');
\Doctrine\Common\Annotations\AnnotationReader::addGlobalIgnoredName('apiExample');
\Doctrine\Common\Annotations\AnnotationReader::addGlobalIgnoredName('apiGroup');
\Doctrine\Common\Annotations\AnnotationReader::addGlobalIgnoredName('apiHeader');
\Doctrine\Common\Annotations\AnnotationReader::addGlobalIgnoredName('apiHeaderExample');
\Doctrine\Common\Annotations\AnnotationReader::addGlobalIgnoredName('apiIgnore');
\Doctrine\Common\Annotations\AnnotationReader::addGlobalIgnoredName('apiName');
\Doctrine\Common\Annotations\AnnotationReader::addGlobalIgnoredName('apiParam');
\Doctrine\Common\Annotations\AnnotationReader::addGlobalIgnoredName('apiParamExample');
\Doctrine\Common\Annotations\AnnotationReader::addGlobalIgnoredName('apiPermission');
\Doctrine\Common\Annotations\AnnotationReader::addGlobalIgnoredName('apiPrivate');
\Doctrine\Common\Annotations\AnnotationReader::addGlobalIgnoredName('apiSampleRequest');
\Doctrine\Common\Annotations\AnnotationReader::addGlobalIgnoredName('apiSuccess');
\Doctrine\Common\Annotations\AnnotationReader::addGlobalIgnoredName('apiSuccessExample');
\Doctrine\Common\Annotations\AnnotationReader::addGlobalIgnoredName('apiUse');
\Doctrine\Common\Annotations\AnnotationReader::addGlobalIgnoredName('apiVersion');

// Load cached env vars if the .env.local.php file exists
// Run "composer dump-env prod" to create it (requires symfony/flex >=1.2)
if (is_array($env = @include dirname(__DIR__).'/.env.local.php') && ($_SERVER['APP_ENV'] ?? $_ENV['APP_ENV'] ?? $env['APP_ENV'] ?? null) === ($env['APP_ENV'] ?? null)) {
    foreach ($env as $k => $v) {
        $_ENV[$k] = $_ENV[$k] ?? (isset($_SERVER[$k]) && 0 !== strpos($k, 'HTTP_') ? $_SERVER[$k] : $v);
    }
} elseif (!class_exists(Dotenv::class)) {
    throw new RuntimeException('Please run "composer require symfony/dotenv" to load the ".env" files configuring the application.');
} else {
    // load all the .env files
    (new Dotenv(false))->loadEnv(dirname(__DIR__).'/.env');
}

$_SERVER += $_ENV;
$_SERVER['APP_ENV'] = $_ENV['APP_ENV'] = ($_SERVER['APP_ENV'] ?? $_ENV['APP_ENV'] ?? null) ?: 'dev';
$_SERVER['APP_DEBUG'] = $_SERVER['APP_DEBUG'] ?? $_ENV['APP_DEBUG'] ?? 'prod' !== $_SERVER['APP_ENV'];
$_SERVER['APP_DEBUG'] = $_ENV['APP_DEBUG'] = (int) $_SERVER['APP_DEBUG'] || filter_var($_SERVER['APP_DEBUG'], FILTER_VALIDATE_BOOLEAN) ? '1' : '0';
