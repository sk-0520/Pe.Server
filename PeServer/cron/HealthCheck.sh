#!/bin/bash -ue

cd "$(cd "$(dirname "${0}")"; pwd)"

#shellcheck disable=SC1091
. "cronrc"

"${PHP}" "${APP_DIR}/App/Cli/app.php" --mode production --class "PeServer\App\Cli\HealthCheck\HealthCheckApplication" --echo "CRON!"
