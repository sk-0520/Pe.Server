<?php

declare(strict_types=1);

namespace PeServer;

require_once(__DIR__ . '/../../Core/AutoLoader.php');

use PeServer\App\Models\AppStartup;
use PeServer\Core\AutoLoader;
use PeServer\Core\StartupOptions;

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
		root: __DIR__ . '/../..',
		public: 'public_html'
	)
);

echo "aaa\n";

$container = $startup->setup(
	AppStartup::MODE_CLI,
	[
		'environment' => $isLocalhost ? ($appTestMode !== '' ? $appTestMode : 'development') : 'production',
		'revision' => ':REVISION:',
		'special_store' => $specialStore,
		'url_helper' => $urlHelper,
	]
);
