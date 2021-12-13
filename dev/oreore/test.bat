echo off
cd /d %~dp0

rem wget -O phpunit https://phar.phpunit.de/phpunit-7.phar
set PATH=C:\Applications\xampp\xampp-portable-win32-7.1.1-0-VC14\xampp\php;%PATH%
set BASH=C:\Program Files\Git\bin\bash.exe

"%BASH%" ..\test.sh
