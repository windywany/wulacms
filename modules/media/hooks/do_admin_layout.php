<?php
defined ( 'KISSGO' ) or exit ( 'No direct script access allowed' );
/**
 * 注册导航菜单.
 *
 * @param AdminLayoutManager $layout        	
 */
function hook_for_do_admin_layout_media($layout) {
	if (icando ( 'm:media' )) {
		$pictureMenu = new AdminNaviMenu ( 'picture_menu', '媒体', 'fa-picture-o', tourl ( 'media', false ) );
		$pictureMenu->addSubmenu ( array ('rmedia','媒体文件','fa-picture-o',tourl ( 'media', false ) ), 'm:media', 0 );
		$pictureMenu->addSubmenu ( array ('cmedia','上传文件','fa-cloud-upload',tourl ( 'media/add', false ) ), 'upload:media', 1 );
		
		$layout->addNaviMenu ( $pictureMenu );
	}
	if (icando ( 'm:system/preference' )) {
		$sysMenu = $layout->getNaviMenu ( 'system' );
		$settingMenu = $sysMenu->getItem ( 'preferences' );
		$settingMenu->addSubmenu ( array ('cmsmedia','多媒体设置','fa-cog',tourl ( 'media/preference', false ) ), 'media:system/preference' );
	}
}
function hook_for_activity_types_media($types) {
	$types ['Upload'] = __ ( 'Upload' );
	return $types;
}
function hook_for_media_types_media($types) {
	$types ['gif'] = $types ['png'] = $types ['bmp'] = $types ['jpeg'] = $types ['jpg'] = array ('image','图片' );
	$types ['zip'] = $types ['rar'] = $types ['7z'] = $types ['tar'] = $types ['gz'] = $types ['bz2'] = $types ['tgz'] = array ('zip','压缩文档' );
	$types ['pdf'] = $types ['doc'] = $types ['docx'] = $types ['txt'] = $types ['ppt'] = $types ['pptx'] = $types ['xls'] = $types ['xlsx'] = array ('office','办公文档' );
	$types ['mp3'] = $types ['aac'] = array ('mp3','音乐' );
	$types ['avi'] = $types ['mp4'] = array ('vedio','视频' );
	$types ['flv'] = $types ['swf'] = array ('flash','Flash' );
	$types ['bin'] = $types ['exe'] = array ('bin','应用程序' );
	$types ['apk'] = array ('app','安卓应用' );
	return $types;
}
