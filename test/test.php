<?php

declare(strict_types=1);

namespace PeServerTest;

ini_set('memory_limit', '-1');
error_reporting(E_ALL);

$files = glob(__DIR__ . '/phpunit.phar.*');
require_once($files[0]);
require_once(__DIR__ . '/../PeServer/Core/AutoLoader.php');

use Exception;
use PeServer\App\Models\AppStartup;
use PeServer\Core\DefinedDirectory;
use PeServer\Core\DI\IDiContainer;
use PeServer\Core\DI\IDiRegisterContainer;
use PeServer\Core\IO\Directory;
use PeServer\Core\IO\File;
use PeServer\Core\IO\Path;
use PeServer\Core\Store\SpecialStore;
use PeServer\Core\Web\UrlHelper;
use PeServerTest\TestClass;

$appTestMode = getenv("APP_TEST_MODE");
if (!is_string($appTestMode) || $appTestMode === '') {
	throw new Exception('$appTestMode');
}

$autoLoader = new \PeServer\Core\AutoLoader(
	[
		'PeServer' => [
			'directory' => __DIR__ . '/../PeServer',
		],
		'PeServerTest' => [
			'directory' => __DIR__ . '/PeServerTest',
		],
		'PeServerUT' => [
			'directory' => __DIR__ . '/PeServerUT',
		],
		'PeServerIT' => [
			'directory' => __DIR__ . '/PeServerIT',
		],
		'PeServerST' => [
			'directory' => __DIR__ . '/PeServerST',
		],
	]
);
$autoLoader->register();

$isIntegrationTest = $appTestMode === 'it' || $appTestMode === 'uit';

$startup = new AppStartup(
	new DefinedDirectory(
		__DIR__ . '/..',
		'public_html'
	)
);
$container = $startup->setup(
	AppStartup::MODE_TEST,
	[
		'environment' => 'test',
		'revision' => ':REVISION:',
		'special_store' => $isIntegrationTest ? new TestSetupSpecialStore() : new SpecialStore(),
		'url_helper' => new UrlHelper(''),
	]
);
Directory::setTemporaryDirectory(Path::combine(__DIR__, "/storage-$appTestMode/temp"));
TestClass::$_do_not_use_container_user_test = $container;

$testSettingFilePath = Path::combine(__DIR__, '@setting.json');
if (File::exists($testSettingFilePath)) {
	$setting = File::readJsonFile($testSettingFilePath);
	TestClass::$setting = array_replace_recursive(TestClass::$setting, $setting);
}
