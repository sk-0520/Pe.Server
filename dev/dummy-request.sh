#!/bin/bash -ue
cd "$(cd "$(dirname "${0}")"; pwd)"

#shellcheck disable=SC1091
source shell/common.sh

common::parse_options "mode|feedback|crashreport! count" "$@"

BASE_ENDPOINT=http://localhost/api

ARG_MODE="$(common::get_option_value mode)"
if common::exists_option 'count' ; then
	ARG_COUNT="$(common::get_option_value count)"
else
	ARG_COUNT=1
fi

function feedback()
{
	local ENDPOINT="${BASE_ENDPOINT}/application/feedback"
	local KINDS=("KIND1" "KIND2" "KIND3")

	for COUNTER in $(seq "${ARG_COUNT}") ; do
		i=$(( COUNTER - 1 ))

		KIND=${KINDS[$(( i % ${#KINDS[@]} ))]}

		curl \
			--request POST \
			$ENDPOINT \
			--data @- <<EOF
			{
				"kind": "${KIND}",
				"subject": "SUBJECT-${COUNTER}",
				"content": "CONTENT-${COUNTER}",
				"version": "0.00.000",
				"revision": "NONE",
				"build": "DEBUG",
				"user_id": "NONE",
				"first_execute_timestamp": "2000-01-02T03:04:05Z",
				"first_execute_version": "0.00.000",
				"process": "x64",
				"platform": "x64",
				"os": "Windows 3.1",
				"clr": "MONO"
			}
EOF

	done
}

function crashreport()
{
	local ENDPOINT="${BASE_ENDPOINT}/application/crash-report"

	for COUNTER in $(seq "${ARG_COUNT}") ; do
		curl \
			--request POST \
			$ENDPOINT \
			--data @- <<EOF
			{
				"version": "0.00.000",
				"revision": "NONE",
				"build": "DEBUG",
				"user_id": "NONE",
				"exception": "EXCEPTION-${COUNTER}",
				"mail_address": "",
				"comment": ""
			}
EOF

	done
}

case "${ARG_MODE}" in
	feedback)
		feedback
		;;

	crashreport)
		crashreport
		;;
esac
