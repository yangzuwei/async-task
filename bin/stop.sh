#!/bin/sh

basepath=$(cd `dirname $0`; pwd)/swoole.pid
kill -15 `cat $basepath`

