<?php

declare(strict_types=1);


namespace PeServer\App\Cli;

require_once(__DIR__ . '/../../Core/AutoLoader.php');

use Error;
use Exception;
use PeServer\App\Models\AppStartup;
use PeServer\Core\AutoLoader;
use PeServer\Core\StartupOptions;
use PeServer\Core\Cli\CliApplicationBase;
use PeServer\Core\Cli\CommandLine;
use PeServer\Core\Cli\LongOptionKey;
use PeServer\Core\Cli\ParameterKind;
use PeServer\Core\Text;

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

$options = new CommandLine([
	new LongOptionKey("mode", ParameterKind::NeedValue),
	new LongOptionKey("class", ParameterKind::NeedValue),
]);
$parsedResult = $options->parseArgv();

var_dump($parsedResult);

$applicationClassName = $parsedResult->getValue("class");
if(Text::isNullOrWhiteSpace($applicationClassName)) {
	throw new Exception("class");
}

$container = $startup->setup(
	AppStartup::MODE_CLI,
	[
		'environment' => match ($parsedResult->getValue("mode")) {
			"production" => "production",
			default => "development"
		},
		'revision' => ':REVISION:',
	]
);

/** @var CliApplicationBase */
$app = $container->new($applicationClassName);
$app->execute();
exit($app->exitCode);
