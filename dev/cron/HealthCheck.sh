#!/bin/sh -ue

PHP= /usr/local/php80/bin/php80
${PHP} /home/ctto/domains/pe.content-type-text.org/PeServer/App/Cli/app.php  --mode production --class "PeServer\App\Cli\HealthCheck\HealthCheckApplication" --echo "cron!"
