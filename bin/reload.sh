#!/bin/sh

basepath=$(cd `dirname $0`; pwd)/swoole.pid

kill -USR1 `cat $basepath`;

