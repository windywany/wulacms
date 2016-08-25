<?php
/**
 * 
 * @author ngf
 *
 */
class RecycleHelper {
	/**
	 * 放入回收站.
	 *
	 * @param string $type        	
	 * @param IRecycle $recycle        	
	 */
	public static function recycle($recycle) {
		if ($recycle instanceof IRecycle) {
			$user = whoami ();
			$data ['user_id'] = $user->getUid ();
			$data ['recycle_time'] = time ();
			$data ['recycle_type'] = $recycle->getContentType ();
			if (! $data ['recycle_type']) {
				$data ['recycle_type'] = 'Unkown';
			}
			$data ['restore_clz'] = get_class ( $recycle );
			$contents = $recycle->getContent ();
			foreach ( $contents as $id => $content ) {
				$data ['restore_value'] = $content;
				$data ['meta'] = $recycle->getMeta ( $id );
				dbinsert ( $data )->into ( '{recycle}' )->exec ();
			}
		}
	}
	/**
	 * 从回收站还原内容.
	 *
	 * @param string|array $ids        	
	 * @return boolean
	 */
	public static function restore($ids) {
		if (is_string ( $ids )) {
			$ids = safe_ids ( $ids, ',', true );
		} else if (! is_array ( $ids )) {
			return false;
		}
		if ($ids) {
			$where = array ('id IN' => $ids );
			$logs = dbselect ( 'recycle_type,restore_clz,restore_value' )->from ( '{recycle} AS L' )->where ( $where );
			foreach ( $logs as $log ) {
				$clz = $log ['restore_clz'];
				if (class_exists ( $clz ) && is_subclass_of2 ( $clz, 'IRecycle' )) {
					$clz = new $clz ();
					$clz->restore ( $log ['restore_value'] );
				}
			}
			dbdelete ()->from ( '{recycle}' )->where ( $where )->exec ();
			return true;
		} else {
			return false;
		}
	}
}
?>