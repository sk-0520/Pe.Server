@echo off
cd /d %~dp0

call env.bat

cd ..\..\

set PHPSTAN_FILE=dev\phpstan.phar

php "%PHPSTAN_FILE%" %*
