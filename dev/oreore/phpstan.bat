echo off
cd /d %~dp0

set PATH=C:\Applications\xampp\xampp-portable-win32-7.1.1-0-VC14\xampp\php;%PATH%
set BASH=C:\Program Files\Git\bin\bash.exe

"%BASH%" ..\phpstan.sh
