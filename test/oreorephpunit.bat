echo off
cd /d %~dp0

rem wget -O phpunit https://phar.phpunit.de/phpunit-7.phar
set PATH=C:\Applications\xampp\xampp-portable-win32-7.1.1-0-VC14\xampp\php;%PATH%

php phpunit --bootstrap .\bootstrap.php --testdox %~dp0
rem php phpunit --testdox --coverage-html=coverage %~dp0

