cd /d %~dp0

call env.bat

set WORK_LOCAL_HTTP_TEST=localhost:8080
set PUBLIC_DIR=../../test/http-ut

if defined LOCAL_HTTP_TEST (
	php -S %LOCAL_HTTP_TEST% -t %PUBLIC_DIR%
) else (
	php -S %WORK_LOCAL_HTTP_TEST% -t %PUBLIC_DIR%
)


