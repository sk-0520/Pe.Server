#!/bin/bash -ue
ROOT_DIR="$(cd "$(dirname "${0}")/.."; pwd)"
WEB_DIR="${ROOT_DIR}/public_html"
CLI_DIR="${ROOT_DIR}/PeServer/App/Cli"


sed --in-place "s/:REVISION:/${1}/" "${WEB_DIR}/index.php"
sed --in-place "s/:REVISION:/${1}/" "${CLI_DIR}/app.php"
