echo off
cd /d %~dp0

rem wget -O phpunit https://phar.phpunit.de/phpunit-9.5.10.phar
call env.bat

echo %DATE:/=-%T%TIME: =0%+09:00

"%BASH%" ..\test.sh
