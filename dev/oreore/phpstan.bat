echo off
cd /d %~dp0

IF NOT DEFINED OREORE_ENV (
	call env.bat
)

"%BASH%" ..\phpstan.sh
