cd /d %~dp0

IF DEFINED OREORE_ENV exit /b

set BASH=C:\Program Files\Git\bin\bash.exe

set CUSTOM_SETTING_FILE=@env.bat
if exist "%CUSTOM_SETTING_FILE%" call "%CUSTOM_SETTING_FILE%"

set OREORE_ENV=1
