echo off
cd /d %~dp0

call env.bat

echo %DATE:/=-%T%TIME: =0%+09:00

"%BASH%" -e ..\test.sh --ignore-namespace %*

echo.
echo --MEMO--
echo  --mode [ut/st]
echo  --ignore-coverage
echo  --phpunit:filter [WORD*]
