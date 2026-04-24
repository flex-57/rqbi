<?php

use App\Kernel;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__).'/vendor/autoload.php';

if (method_exists(Dotenv::class, 'bootEnv')) {
    (new Dotenv())->bootEnv(dirname(__DIR__).'/.env');
}

if ($_SERVER['APP_DEBUG']) {
    umask(0000);
}

$kernel = new Kernel('test', true);
$kernel->boot();

$app = new Application($kernel);
$app->setAutoExit(false);
$null = new NullOutput();

$app->run(new ArrayInput(['command' => 'doctrine:schema:drop', '--force' => true, '--no-interaction' => true]), $null);
$app->run(new ArrayInput(['command' => 'doctrine:schema:create', '--no-interaction' => true]), $null);
$app->run(new ArrayInput(['command' => 'doctrine:fixtures:load', '--no-interaction' => true]), $null);

$kernel->shutdown();
