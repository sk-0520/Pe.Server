<?php

$settings = [
	[
		'pattern' => '..\\..\\test\\phpunit.phar.*',
		'output' => '..\\PHPUnit',
	],
	// [
	// 	'pattern' => '..\\php-cs-fixer.phar.*',
	// 	'output' => '..\\PhpCsFixer',
	// ],
];

foreach($settings as $setting) {
	$files = glob($setting['pattern']);
	$phar = new Phar($files[0]);
	$phar->extractTo($setting['output'], null, true);
}


