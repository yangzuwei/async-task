#!/bin/bash

ps aux|grep TaskServerInstance.php|grep -v grep|cut -c 9-15|xargs kill -9
