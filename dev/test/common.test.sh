#!/bin/bash

#shellcheck disable=SC2164
cd "$(cd "$(dirname "${0}")"; pwd)"

#shellcheck disable=SC1091
source ../shell/assert.sh

#shellcheck disable=SC1091
source ../shell/common.sh

function test_options_value_success
{
	common::parse_options 'abc' --abc ABC
	local RESULT
	RESULT=$(common::get_option_value abc)
	assert::return_success $?
	assert::equals "ABC" "${RESULT}"
}

# 空文字は現実装ではむり
function test_options_value_white_success
{
	common::parse_options 'abc' --abc ' '
	local RESULT
	RESULT=$(common::get_option_value abc)
	assert::return_success $?
	assert::equals " " "${RESULT}"
}

function test_options_value_error
{
	common::parse_options 'abc' --abc ABC
	local RESULT
	RESULT=$(common::get_option_value xyz)
	assert::return_failuer $?
}

# function test_is_in_success
# {
# 	common::common::is_in 'abc' 'abc'
# 	assert::return_success $?
# }

# function test_is_in_failuer
# {
# 	common::common::is_in 'abc' 'ABC'
# 	assert::return_failuer $?
# }

#--------------------------------
assert::test
