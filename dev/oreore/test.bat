echo off
cd /d %~dp0

IF NOT DEFINED OREORE_ENV (
	rem wget -O phpunit https://phar.phpunit.de/phpunit-9.5.10.phar
	call env.bat
)


"%BASH%" ..\test.sh
