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
		T ) echo -ne "" ;;
		D ) echo -ne "" ;;
		I ) echo -ne "" ;;
		W ) echo -ne "" ;;
		E ) echo -ne "" ;;
		* ) echo -ne "" ;;
	esac

	echo -n ${@:2:($#-1)}
	echo -e "\e[m"
}

# タイトル表示
# $* タイトル
function title()
{
	echo ''
	echo $*
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

function saveData
{
	local KEY=$1
	local FILE=$2
	LINE=$(grep "^$KEY:" ${LOCAL_INIT_DATA})
	echo "${LINE#*:}" | base64 --decode > $FILE
	msg d "${KEY} -> ${LINE#*:}"
}

#-----------------------------------------------

title [LOCAL] ディレクトリクリア

msg i ${LOCAL_TEMP_DIR}
cleanupDir ${LOCAL_TEMP_DIR}
msg i ${LOCAL_FILES_DIR}
cleanupDir ${LOCAL_FILES_DIR}

title [DEPLOY] セットアップ

#curl --show-error -X POST -d seq=${SEQUENCE_INITIALIZE} --data-urlencode key=${ENC_ACCESS_KEY} ${SETTING_URL}
curl --show-error -X POST \
	-H "X-API-KEY: ${SETTING_API_KEY}" \
	-H "X-SECRET-KEY: ${SETTING_API_SECRET}" \
	${SETTING_API_URL}/startup

title recv

split --bytes=${SETTING_SPLIT_SIZE} --numeric-suffixes=1 --suffix-length=8 ${SETTING_ARCHIVE_FILE_NAME} ${LOCAL_FILES_DIR}/
INDEX=1
for PART_FILE in `ls -1 -v ${LOCAL_FILES_DIR}/`; do
	curl --show-error -X POST -F seq=${SEQUENCE_RECEIVE} -F file=@${LOCAL_FILES_DIR}/${PART_FILE} -F number=$INDEX  -H "${SETTING_AUTH_HEADER_NAME}: ${AUTH_HEADER_VALUE}" ${SETTING_URL}
	let INDEX++
done

title prepare

HASH=$(sha512sum --binary ${SETTING_ARCHIVE_FILE_NAME} | cut -d ' ' -f 1)
msg t $HASH
curl --show-error -X POST -d seq=${SEQUENCE_PREPARE} -d algorithm=sha512 -d hash=${HASH} -H "${SETTING_AUTH_HEADER_NAME}: ${AUTH_HEADER_VALUE}" ${SETTING_URL}

title update

curl --show-error -X POST -d seq=${SEQUENCE_UPDATE} --data-urlencode key=${ENC_ACCESS_KEY} -H "${SETTING_AUTH_HEADER_NAME}: ${AUTH_HEADER_VALUE}" ${SETTING_URL}

title END
