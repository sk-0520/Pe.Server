#!/bin/bash -ue

readonly LOGGER_LEVEL_TRACE=1
readonly LOGGER_LEVEL_DEBUG=2
readonly LOGGER_LEVEL_INFORMATION=3
readonly LOGGER_LEVEL_WARNING=4
readonly LOGGER_LEVEL_ERROR=5

_LOGGER_OLD_SETTING_U=${-//[^u]/}
set +u
LOGGER_DEFAULT_LEVEL=${LOGGER_DEFAULT_LEVEL:=${LOGGER_LEVEL_INFORMATION}}
LOGGER_DEFAULT_HEAD=$(basename "${0}")
if [[ -n "${_LOGGER_OLD_SETTING_U}" ]] ; then
	set -u
fi

# ログレベルの取得
# $1: t[race] < d[ebug] < i[nformation] < w[arning] < e[rror]
function logger::get_level()
{
	local LEVEL=$1 # T/D/I/W/E

	local RESULT=0

	case $LEVEL in
		[tT]|'trace' )
			RESULT=${LOGGER_LEVEL_TRACE}
			;;
		[dD]|'debug' )
			RESULT=${LOGGER_LEVEL_DEBUG}
			;;
		[iI]|'info' )
			RESULT=${LOGGER_LEVEL_INFORMATION}
			;;
		[wW]|'warn' )
			RESULT=${LOGGER_LEVEL_WARNING}
			;;
		[eE]|'error' )
			RESULT=${LOGGER_LEVEL_ERROR}
			;;
	esac

	return ${RESULT}
}

# メッセージ出力
# $1  ログレベル
# $2* メッセージ
function logger::log()
{
	local LEVEL=$1 # T/D/I/W/E

	local MSG_LEVEL
	set +e
	MSG_LEVEL=$(_logger::get_level "${LEVEL}")
	local DEF_LEVEL
	DEF_LEVEL=$(_logger::get_level "${LOGGER_DEFAULT_LEVEL}")
	set -e

	if [[ "${MSG_LEVEL}" -lt "${DEF_LEVEL}" ]] ; then
		return
	fi

	local STYLE
	local LOG_LEVEL
	case $LEVEL in
		"${LOGGER_LEVEL_TRACE}" )
			STYLE=""
			LOG_LEVEL="TRACE"
			;;
		"${LOGGER_LEVEL_DEBUG}" )
			STYLE=""
			LOG_LEVEL="DEBUG"
			;;
		"${LOGGER_LEVEL_INFORMATION}" )
			STYLE=""
			LOG_LEVEL="INFO "
			;;
		"${LOGGER_LEVEL_WARNING}" )
			STYLE=""
			LOG_LEVEL="WARN "
			;;
		"${LOGGER_LEVEL_ERROR}" )
			STYLE=""
			LOG_LEVEL="ERROR"
			;;
		* )
			STYLE=""
			;;
	esac

	if [[ $LEVEL = "${LOGGER_LEVEL_ERROR}" ]] ; then
		echo -e "${STYLE}" >&2
		#shellcheck disable=SC2145
		echo "[${LOGGER_DEFAULT_HEAD}] | ${LOG_LEVEL} | ${@:2:($#-1)}"
		echo -e "\e[m" >&2
	else
		echo -e "${STYLE}"
		#shellcheck disable=SC2145
		echo -n "[${LOGGER_DEFAULT_HEAD}] | ${LOG_LEVEL} | ${@:2:($#-1)}"
		echo -e "\e[m"
	fi
}

function logger::trace
{
	logger::log ${LOGGER_LEVEL_TRACE} "$@"
}

function logger::debug
{
	logger::log ${LOGGER_LEVEL_DEBUG} "$@"
}

function logger::info
{
	logger::log ${LOGGER_LEVEL_INFORMATION} "$@"
}

function logger::warn
{
	logger::log ${LOGGER_LEVEL_WARNING} "$@"
}

function logger::error
{
	logger::log ${LOGGER_LEVEL_ERROR} "$@"
}
