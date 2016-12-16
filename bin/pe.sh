#!/bin/bash

#parse excel,by php
# 2016-12-15

dir=$(cd `dirname $0`;pwd)

path="${dir}/../excel"

if [ ! -e $path ]; then
    echo "${path} dose not exist"
    exit 1
fi

excel=$(ls -t ${path} | grep \.xls$ | head -1)

if [ $excel ]; then
	${dir}/artisan.php tbk_import1 -f "${path}/${excel}"
else
	echo "can't find excel file!"
	exit 1
fi