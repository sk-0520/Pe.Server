echo off
cd /d %~dp0

call env.bat

echo %DATE:/=-%T%TIME: =0%+09:00

set PHPCS_CACHE=%TEMP%\phpcs.cache

if not exist "%PHPCS_CACHE%" type nul > "%PHPCS_CACHE%"

"%BASH%" -e ..\code.sh --ignore-pplint --phpcs:cache %PHPCS_CACHE% %*

echo.
echo --MEMO--
echo  --ignore-phpstan
echo  --ignore-phpcs
echo  --phpcs-fix
echo  --phpcs:report [full,checkstyle,csv,summary]
echo  --phpcs:ignore-warning
