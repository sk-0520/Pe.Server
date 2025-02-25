<?php

$settings = [
	[
		'pattern' => '..\\..\\test\\phpunit.phar.*',
		'output' => '..\\extracts\\PHPUnit',
	],
	[
		'pattern' => '..\\phpstan.phar.*',
		'output' => '..\\extracts\\phpstan',
	],
];

foreach($settings as $setting) {
	$files = glob($setting['pattern']);
	natsort($files);
	$phar = new Phar(end($files));
	$phar->extractTo($setting['output'], null, true);
}


