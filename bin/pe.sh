#!/bin/sh
#parse excel,by php 
# 2016-12-15
dir=$(cd `dirname $0`;pwd)
path='/var/www/html/taobaoke/excel/'
excel=$(ls -t $path|grep \.xls|head -1)
if [ $excel ]
then
	echo "find excel file:"$path$excel
	echo "php /var/www/html/taobaoke/bin/artisan.php tbk_import1 -f $path$excel "
	eval "php /var/www/html/taobaoke/bin/artisan.php tbk_import1 -f $path$excel"
else
	echo "can't find excel file!"
fi
