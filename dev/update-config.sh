#!/bin/bash -ue

PROGRAM_DIR="$(cd "$(dirname "${0}")"/../public_html; pwd)"

sed --in-place "s/:REVISION:/${1}/" "$PROGRAM_DIR/index.php"
