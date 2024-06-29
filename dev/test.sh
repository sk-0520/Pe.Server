#!/bin/bash -ue

pushd "$(cd "$(dirname "${0}")"; pwd)"
	#shellcheck disable=SC1091
	source shell/common.sh
	#shellcheck disable=SC2048,SC2086
	common::parse_options 'mode|ut|it|st|uit# no-exit! ignore-namespace! ignore-coverage! phpunit:filter phpunit:exclude-group' $*
popd

TEST_MODE="$(common::get_option_value mode)"

# サーバー側引き渡し用(bootstrapでも流用)
export APP_TEST_MODE="${TEST_MODE}"

cd "$(cd "$(dirname "${0}")/../test"; pwd)"

BASE_DIR=../PeServer

LOCAL_HTTP_TEST="${LOCAL_HTTP_TEST:=localhost:8080}"
LOCAL_HTTP_WAIT="${LOCAL_HTTP_WAIT:=1}"

PHPUNIT_VERSION=11.2.2
PHPUNIT_URL=https://phar.phpunit.de/phpunit-${PHPUNIT_VERSION}.phar
PHPUNIT_NAME=phpunit.phar
PHPUNIT_FILE=${PHPUNIT_NAME}.${PHPUNIT_VERSION}
PHPUNIT_BASE_DIR=../test

common::download_phar_if_not_exists "${PHPUNIT_BASE_DIR}/${PHPUNIT_FILE}" "${PHPUNIT_BASE_DIR}/${PHPUNIT_NAME}" "${PHPUNIT_URL}"

if ! common::exists_option 'ignore-namespace' ; then
	logger::info "[CHECKE NAMESPACE]"

	NAMESPACE_ERROR=false

	# Core に App 混入がないか確認
	CORE_DIR=${BASE_DIR}/Core
	pushd "${CORE_DIR}"
		#shellcheck disable=SC2044
		for FILE in $(find . -type f -name '*.php') ; do
			if [ "$(grep --count 'PeServer\\App' "${FILE}")" -ne 0 ] ; then
				logger::error "${FILE}:"
				grep --line-number 'PeServer\\App' "${FILE}"
				NAMESPACE_ERROR=true
			fi
		done
	popd

	if "${NAMESPACE_ERROR}" ; then
		logger::error "namespace error!"
		exit 1
	fi
fi

PHPUNIT_OPTION_FILTER=
if common::exists_option 'phpunit:filter' ; then
	PHPUNIT_OPTION_FILTER="--filter $(common::get_option_value phpunit:filter)"
fi
PHPUNIT_OPTION_EXCLUDE_GROUP=
if common::exists_option 'phpunit:exclude-group' ; then
	PHPUNIT_OPTION_EXCLUDE_GROUP="--exclude-group $(common::get_option_value phpunit:exclude-group)"
fi


PHPUNIT_OPTION_COVERAGE=
if ! common::exists_option 'ignore-coverage' ; then
	PHPUNIT_OPTION_COVERAGE="--coverage-html ../public_html/public/coverage/php/${TEST_MODE}"
fi

PUBLIC_DIR=../public_html
TEST_SUITE="--testsuite ${TEST_MODE}"
case "${TEST_MODE}" in
	ut | it )
		PUBLIC_DIR="${PHPUNIT_BASE_DIR}/http-${TEST_MODE}"
		;;
	uit )
		PUBLIC_DIR="${PHPUNIT_BASE_DIR}/http-ut"
		TEST_SUITE="--testsuite ut,it"
		;;
	st )
		;;
	*)
		exit 255
		;;
esac

# IT の場合 IT 用設定ファイルを使用するのでなければデフォルトを流用
case "${TEST_MODE}" in
	it | uit )
		APP_CONFIG_DIR="${BASE_DIR}/config"
		TEST_CONFIG_FILE="setting.it.json"
		if [ ! -f "${APP_CONFIG_DIR}/${TEST_CONFIG_FILE}" ] ; then
			cp "${APP_CONFIG_DIR}/@${TEST_CONFIG_FILE}" "${APP_CONFIG_DIR}/${TEST_CONFIG_FILE}"
		fi
		;;
	st )
		APP_CONFIG_DIR="${BASE_DIR}/config"
		TEST_CONFIG_FILE="setting.st.json"
		if [ ! -f "${APP_CONFIG_DIR}/${TEST_CONFIG_FILE}" ] && [ -f "${APP_CONFIG_DIR}/@${TEST_CONFIG_FILE}" ] ; then
			cp "${APP_CONFIG_DIR}/@${TEST_CONFIG_FILE}" "${APP_CONFIG_DIR}/${TEST_CONFIG_FILE}"
		fi
		;;
	*)
		;;
esac

cd ${PHPUNIT_BASE_DIR}/
STORAGE="storage-${TEST_MODE}"
if [[ -d "${STORAGE}" ]] ; then
	rm --recursive --force "${STORAGE}"
fi
mkdir "${STORAGE}"

echo $PUBLIC_DIR
php -S "${LOCAL_HTTP_TEST}" -t "${PUBLIC_DIR}" > "http-${TEST_MODE}.log" 2>&1 &
trap 'kill %1' 0
sleep "${LOCAL_HTTP_WAIT}"
#shellcheck disable=SC2086
php "${PHPUNIT_FILE}" --configuration "../dev/phpunit.xml" ${TEST_SUITE} ${PHPUNIT_OPTION_COVERAGE} ${PHPUNIT_OPTION_FILTER} ${PHPUNIT_OPTION_EXCLUDE_GROUP}

if common::exists_option 'no-exit' ; then
	read -r
fi
