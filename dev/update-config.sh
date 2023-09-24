#!/bin/bash -ue

PROGRAM_DIR"=$(cd "$(dirname "${0}")"/../public_html; pwd)"

sed -i "s/:REVISION:/${1}/" "$PROGRAM_DIR/index.php"
