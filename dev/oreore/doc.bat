echo off
cd /d %~dp0

call env.bat

echo %DATE:/=-%T%TIME: =0%+09:00

echo.
echo --MEMO--
echo  --phpdoc:setting-graphs

"%BASH%" -e ..\doc.sh --phpdoc:cache-folder %TEMP%\phpdocumentor %*
