<?php

declare(strict_types=1);


namespace PeServer\App\Cli;

require_once(__DIR__ . '/../../Core/AutoLoader.php');

use Error;
use PeServer\App\Models\AppStartup;
use PeServer\Core\AutoLoader;
use PeServer\Core\StartupOptions;
use PeServer\Core\Cli\CliApplicationBase;
use PeServer\Core\Cli\CommandLine;
use PeServer\Core\Cli\LongOptionKey;
use PeServer\Core\Cli\ParameterKind;

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

// $options = new CommandLine([
// 	new LongOptionKey("mode", ParameterKind::NeedValue),
// 	new LongOptionKey("class", ParameterKind::NeedValue),
// ]);
// if (!isset($options->data["class"])) {
// 	throw new Error("options: class");
// }
// $applicationClassName = $options->data["class"];
// if (!is_string($applicationClassName) || strlen($applicationClassName) === 0) {
// 	throw new Error("options: class");
// }

// var_dump($options);

// $container = $startup->setup(
// 	AppStartup::MODE_CLI,
// 	[
// 		'environment' => match ($options->data["mode"]) {
// 			"production" => "production",
// 			default => "development"
// 		},
// 		'revision' => ':REVISION:',
// 	]
// );

// /** @var CliApplicationBase */
// $app = $container->new($applicationClassName);
// $app->execute();
// exit($app->exitCode);
