echo off
cd /d %~dp0

call env.bat

cd

pushd ..\..\
cd
php PeServer\App\Cli\cli.php --mode abc
popd

