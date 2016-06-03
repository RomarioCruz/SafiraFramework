<?php

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;

date_default_timezone_set('America/Sao_Paulo');

/**
 * Diretório onde estão os modelos
 */
$paths = array(realpath(__DIR__ . '/../app/Models'));
$isDevMode = true;
/**
 * Configuração da conexão com banco de dados
 */
$connectionOptions = array(
    'user' => 'root',
    'password' => '',
    'host' => 'localhost',
    'port' => '3306',
    'dbname' => 'controleatividades',
    'driver' => 'pdo_mysql',
);

$config = Setup::createConfiguration($isDevMode);
$driver = new AnnotationDriver(new AnnotationReader(), $paths);

AnnotationRegistry::registerLoader('class_exists');
$config->setMetadataDriverImpl($driver);

$entityManager = EntityManager::create($connectionOptions, $config);

return $entityManager;
