#!/bin/bash -ue

SETTING_LOG_LEVEL="${SETTING_LOG_LEVEL:=i}"

LOCAL_TEMP_DIR=local-temp
LOCAL_FILES_DIR=local-files

# ログレベルの取得
# $1: t[race] < d[ebug] < i[nformation] < w[arning] < e[rror]
function getLogLevel()
{
	local LEVEL=$1 # T/D/I/W/E

	local RESULT=0
	case $LEVEL in
		[tT]|'trace' )
			RESULT=1
			;;
		[dD]|'debug' )
			RESULT=2
			;;
		[iI]|'info' )
			RESULT=3
			;;
		[wW]|'warn' )
			RESULT=4
			;;
		[eE]|'error' )
			RESULT=5
			;;
	esac

	echo $RESULT

	# return $RESULT
}

# メッセージ出力
# $1  ログレベル
# $2* メッセージ
function msg()
{
	local LEVEL=$1 # T/D/I/W/E
	local MSG_LEVEL=$(getLogLevel ${LEVEL})
	local DEF_LEVEL=$(getLogLevel ${SETTING_LOG_LEVEL})

	# echo "!!!!!!!!!!: $LEVEL < $SETTING_LOG_LEVEL"
	# echo "XXXXXXXXXX: $MSG_LEVEL < $DEF_LEVEL"

	if [ "$MSG_LEVEL" -lt "$DEF_LEVEL" ] ; then
		# echo "byebye: $MSG_LEVEL < $DEF_LEVEL"
		return
	fi

	case $LEVEL in
		1 ) echo -ne "" ;;
		2 ) echo -ne "" ;;
		3 ) echo -ne "" ;;
		4 ) echo -ne "" ;;
		5 ) echo -ne "" ;;
		* ) echo -ne "" ;;
	esac

	echo -n "${@:2:($#-1)}"
	echo -e "\e[m"
}

# タイトル表示
# $* タイトル
function title()
{
	echo ''
	echo "$@"
	echo ''
}

# ディレクトリのクリーンアップ
# $1 対象ディレクトリ
function cleanupDir
{
	local DIR_PATH=$1
	if [ -d ${DIR_PATH} ] ; then
		rm -rf ${DIR_PATH}
		mkdir ${DIR_PATH}
	else
		mkdir ${DIR_PATH}
	fi
}

function api
{
	local ENDPOINT_PATH=$1

	time curl --fail \
		--show-error \
		--request POST \
		--header "X-API-KEY: ${SETTING_API_KEY}" \
		--header "X-SECRET-KEY: ${SETTING_API_SECRET}" \
		"${@:2:($#-1)}" \
		"${SETTING_API_URL}/${ENDPOINT_PATH}"
	echo ""
}

#-----------------------------------------------

title [LOCAL] ディレクトリクリア

msg i ${LOCAL_TEMP_DIR}
cleanupDir ${LOCAL_TEMP_DIR}
msg i ${LOCAL_FILES_DIR}
cleanupDir ${LOCAL_FILES_DIR}

title [DEPLOY] スタートアップ

api startup

title [DEPLOY] アップロード

msg i "分割バイト数: ${SETTING_SPLIT_SIZE}"
split --bytes="${SETTING_SPLIT_SIZE}" --numeric-suffixes=1 --suffix-length=8 "${SETTING_ARCHIVE_FILE_NAME}" "${LOCAL_FILES_DIR}/"
INDEX=0
for PART_FILE in `ls -1 -v ${LOCAL_FILES_DIR}/`; do
	msg i "ファイル: {INDEX} - ${LOCAL_FILES_DIR}/${PART_FILE}"
	api upload \
		-F file=@${LOCAL_FILES_DIR}/${PART_FILE} \
		-F sequence=${INDEX}

	INDEX=$((INDEX+1))
done


title [DEPLOY] 準備

api prepare

title [DEPLOY] 更新

api update

title END
