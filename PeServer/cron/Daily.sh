#!/bin/bash -ue

cd "$(cd "$(dirname "${0}")"; pwd)"

. "cronrc"

"${PHP}" "${APP_DIR}/App/Cli/app.php" --mode production --class "PeServer\App\Cli\Daily\DailyApplication"
