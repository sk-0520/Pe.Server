<?php

declare(strict_types=1);


namespace PeServer\App\Cli;

require_once(__DIR__ . '/../../Core/AutoLoader.php');

use Error;
use PeServer\App\Models\AppStartup;
use PeServer\Core\AutoLoader;
use PeServer\Core\StartupOptions;

error_reporting(E_ALL);

$autoLoader = new AutoLoader(
	[
		'PeServer' => [
			'directory' => __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'PeServer',
		]
	]
);
$autoLoader->register(false);

$startup = new AppStartup(
	new StartupOptions(
		root: __DIR__ . '/../../..',
		public: 'public_html'
	)
);

$options = getopt("", [
	"mode:",
	"class:",
]);
if ($options === false) {
	throw new Error("options");
}
if(!isset($options["class"]) || !is_string($options["class"]) || strlen($options["class"]) === 0) {
	throw new Error("options: class");
}

var_dump($options);

$container = $startup->setup(
	AppStartup::MODE_CLI,
	[
		'environment' => match ($options["mode"]) {
			"production" => "production",
			default => "development"
		},
		'revision' => ':REVISION:',
	]
);

/** @var CliApplicationBase */
$app = $container->new($options['class']);
$app->execute();
exit($app->exitCode);
