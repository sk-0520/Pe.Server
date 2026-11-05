echo off
cd /d %~dp0

call env.bat

php %*
