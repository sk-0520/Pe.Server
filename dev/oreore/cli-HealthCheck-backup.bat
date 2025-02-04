echo off
cd /d %~dp0

call env.bat

cd

pushd ..\..\
cd
php PeServer\App\Cli\app.php --mode development --class "PeServer\App\Cli\HealthCheck\HealthCheckApplication" --echo "%DATE% %TIME%"
popd

