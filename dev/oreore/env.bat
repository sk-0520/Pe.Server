cd /d %~dp0

set PATH=C:\Applications\xampp\xampp-portable-windows-x64-8.0.7-0-VS16\xampp\php;%PATH%
set BASH=C:\Program Files\Git\bin\bash.exe

set CUSTOM_SETTING_FILE=@env.bat
if exist "%CUSTOM_SETTING_FILE%" call "%CUSTOM_SETTING_FILE%"

set OREORE_ENV=1
