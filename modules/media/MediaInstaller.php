<?php
class MediaInstaller extends AppInstaller {
	public function getAuthor() {
		return '宁广丰';
	}
	public function getDscription() {
		return '提供多媒体文件的管理功能，如上传，浏览等.';
	}
	public function getName() {
		return '媒体库';
	}
	public function getWebsite() {
		return 'http://www.kissgo.org/';
	}
	public function getDependences() {
		$dependences ['rest'] = '[0.0.1,)';
		return $dependences;
	}
	public function getVersionLists() {
		$v ['0.0.1'] = '20140730001';
		$v ['1.0.0'] = '20141217002';
		$v ['1.1.0'] = '20150312003';
		$v ['1.5.0'] = '20151011004';
		return $v;
	}
	public function uninstall() {
		parent::uninstall ();
		dbdelete ()->from ( '{preferences}' )->where ( array ('preference_group' => 'media' ) )->exec ();
		return true;
	}
	public function upgradeTo20141217002($dialect) {
		$types = apply_filter ( 'get_media_types', array () );
		dbupdate ( '{media}' )->set ( array ('type' => 'unknown' ) )->exec ();
		foreach ( $types as $type => $v ) {
			dbupdate ( '{media}' )->set ( array ('type' => $v [0] ) )->where ( array ('ext' => $type ) )->exec ();
		}
		return true;
	}
}
?>