<?php


$files = glob('..\\..\\test\\phpunit.phar.*');
$phar = new Phar($files[0]);
$phar->extractTo('..\\PHPUnit', null, true);
