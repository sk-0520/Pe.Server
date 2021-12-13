echo off
cd /d %~dp0

set PATH=C:\Applications\xampp\xampp-portable-win32-7.1.1-0-VC14\xampp\php;%PATH%

php -r "$phar = new Phar('..\\..\\test\\phpunit.phar'); $phar->extractTo('..\\PHPUnit', null, true);"

