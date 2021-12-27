echo off
cd /d %~dp0

call env.bat

"%BASH%" ..\phpstan.sh
