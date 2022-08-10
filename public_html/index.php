<?php

declare(strict_types=1);

namespace PeServer;

require_once(__DIR__ . '/PeServer/Core/AutoLoader.php');

use PeServer\App\Models\AppConfiguration;
use PeServer\App\Models\AppRouting;
use PeServer\App\Models\AppSpecialStore;
use PeServer\App\Models\Initializer;
use PeServer\App\Models\RouteConfiguration;
use PeServer\Core\AutoLoader;
use PeServer\Core\Http\HttpMethod;
use PeServer\Core\Http\RequestPath;

ini_set('display_errors', '1');
error_reporting(E_ALL);

$autoLoader = new AutoLoader(
	[
		'PeServer' => [
			'directory' => __DIR__,
		]
	]
);
$autoLoader->register(false);

$baseUrlPath = '';
$specialStore = new AppSpecialStore();
Initializer::initialize(
	__DIR__,
	__DIR__ . '/PeServer',
	$baseUrlPath,
	$specialStore,
	$specialStore->getServer('SERVER_NAME') === 'localhost' ? 'development' : 'production',
	':REVISION:'
);

$method = HttpMethod::from($specialStore->getServer('REQUEST_METHOD'));
$requestPath = new RequestPath($specialStore->getServer('REQUEST_URI'), $baseUrlPath);
$routing = new AppRouting($method, $requestPath, RouteConfiguration::get(), AppConfiguration::$stores);
$routing->execute();
