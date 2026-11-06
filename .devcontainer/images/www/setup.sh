#!/bin/bash -ue
cd "$(cd "$(dirname "${0}")"; pwd)"

DST=fs/app/PeServer/Core/Libs

if [ ! -f "${DST}" ] ; then
	rm -rf "${DST}"
	mkdir -p "$(dirname "${DST}")"
fi
cp -Rfv "../../../PeServer/Core/Libs" "${DST}"
