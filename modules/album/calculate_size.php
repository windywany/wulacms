<?php
define ( 'WEB_ROOT', realpath ( dirname ( __FILE__ ) . '/../../' ) . DIRECTORY_SEPARATOR );
include_once WEB_ROOT . 'bootstrap.php';

$i = 0;
echo "start...", "\n";
while ( true ) {
	$rows = dbselect ( 'id,url,width' )->from ( '{album_item}' )->where ( array ('deleted' => 0 ) )->limit ( $i * 100, 100 )->toArray ();
	if ($rows) {
		foreach ( $rows as $row ) {
			if ($row ['width']) {
				continue;
			}
			$id = $row ['id'];
			$pic = WEB_ROOT . $row ['url'];
			if (ImageUtil::isImage ( $pic )) {
				$img = new image ( $pic );
				$data ['width'] = $img->imagesx ();
				$data ['height'] = $img->imagesy ();
				$img->destroyImage ();
				dbupdate ( '{album_item}' )->set ( $data )->where ( array ('id' => $id ) )->exec ();
			} else {
				log_debug ( 'not image:' . $pic );
			}
		}
		$i ++;
	} else {
		break;
	}
}
echo "done";