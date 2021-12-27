echo off
cd /d %~dp0

call env.bat

php -r "$phar = new Phar('..\\..\\test\\phpunit.phar'); $phar->extractTo('..\\PHPUnit', null, true);"

