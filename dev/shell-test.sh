#!/bin/bash -ue
#
# Bash スクリプトのテスト窓口
#
#shellcheck disable=SC2164
cd "$(cd "$(dirname "${0}")"; pwd)"

#shellcheck disable=SC1091
source shell/assert.sh

export ASSERT_TESTS_DIR=test
export ASSERT_TESTS_PATTERN="*.test.sh"

assert::tests
