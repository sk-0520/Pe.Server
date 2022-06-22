echo off
cd /d %~dp0

call env.bat

echo %DATE:/=-%T%TIME: =0%+09:00

"%BASH%" ..\code.sh %*
