echo off
cd /d %~dp0

IF NOT DEFINED OREORE_ENV (
	call env.bat
)

php -r "$phar = new Phar('..\\..\\test\\phpunit.phar'); $phar->extractTo('..\\PHPUnit', null, true);"

