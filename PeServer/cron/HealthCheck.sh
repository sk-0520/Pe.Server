#!/bin/bash -ue

cd "$(cd "$(dirname "${0}")"; pwd)"

. "php.sh"

"${PHP}" /home/ctto/domains/pe.content-type-text.org/PeServer/App/Cli/app.php --mode production --class "PeServer\App\Cli\HealthCheck\HealthCheckApplication" --echo "CRON!"
