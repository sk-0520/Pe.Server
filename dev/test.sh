#!/bin/bash -ue

cd $(cd $(dirname $0); pwd)/../test

php phpunit.phar --bootstrap ./bootstrap.php --testdox .
