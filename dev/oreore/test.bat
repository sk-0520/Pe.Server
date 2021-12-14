echo off
cd /d %~dp0

rem wget -O phpunit https://phar.phpunit.de/phpunit-7.phar
call env.bat

"%BASH%" ..\test.sh
