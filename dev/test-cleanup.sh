#!/bin/bash -ue

cd "$(cd "$(dirname "${0}")"; pwd)"

DATA_DIR=../public_html/PeServer/data

if [[ -d "${DATA_DIR}" ]] ; then
	rm --recursive --force "${DATA_DIR}"
fi


