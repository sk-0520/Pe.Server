echo off
cd /d %~dp0

call env.bat

echo %DATE:/=-%T%TIME: =0%+09:00

"%BASH%" -e ..\code.sh --ignore-pplint %*

echo.
echo --MEMO--
echo  --ignore-phpstan
echo  --ignore-phpcs
echo  --phpcs-fix
echo  --phpcs:report [full,checkstyle,csv,summary]
echo  --phpcs:ignore-warning
