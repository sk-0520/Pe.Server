#!/bin/bash -ue

cd $(cd $(dirname $0); pwd)

PACKAGE_CACHE_DIR=/var/cache/apt/archives

CI_MODE=${1}

function execute_install() {
	PACKAGE_SAVE_DIR=${1}
	PHP_VERSION=${2}

	# if [ -d "${PACKAGE_SAVE_DIR}" ] ; then
	# 	sudo sh -c "cp --force --verbose ${PACKAGE_SAVE_DIR}/*.deb ${PACKAGE_CACHE_DIR}"
	# fi

	apt --yes install software-properties-common
	add-apt-repository ppa:ondrej/php
	apt --yes update
	apt --yes install php${PHP_VERSION} php${PHP_VERSION}-fpm php${PHP_VERSION}-mysql php${PHP_VERSION}-mbstring php${PHP_VERSION}-zip php${PHP_VERSION}-xml
	update-alternatives --set php /usr/bin/php${PHP_VERSION}

	# if [ ! -d "${PACKAGE_SAVE_DIR}" ] ; then
	# 	mkdir --parents --verbose "${PACKAGE_SAVE_DIR}"
	# fi
	# sudo sh -c "cp --force --verbose ${PACKAGE_CACHE_DIR}/*.deb ${PACKAGE_SAVE_DIR}"
}

case ${CI_MODE} in
	install )
		execute_install ${2} ${3}
		;;

	* )
		echo "ERROR: ${CI_MODE}";
		exit 1
esac

exit 0;


