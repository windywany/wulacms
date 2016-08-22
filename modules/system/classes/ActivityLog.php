<?php
/**
 * 活动日志.
 * @author Guangfeng
 *
 */
class ActivityLog {
	public static function debug($message, $activity = '') {
		ActivityLog::saveLog ( 'd', $message, $activity );
	}
	public static function info($message, $activity = '') {
		ActivityLog::saveLog ( 'i', $message, $activity );
	}
	public static function warn($message, $activity = '') {
		ActivityLog::saveLog ( 'w', $message, $activity );
	}
	public static function error($message, $activity = '') {
		ActivityLog::saveLog ( 'e', $message, $activity );
	}
	private static function saveLog($type, $message, $activity = '') {
		static $uid = false;
		if (! $uid) {
			$user = whoami ();
			$uid = $user->getUid ();
		}
		$log ['create_time'] = time ();
		$log ['user_id'] = $uid;
		$log ['meta'] = $message;
		$log ['level'] = $type;
		$log ['activity'] = empty ( $activity ) ? 'Syslog' : $activity;
		$log ['ip'] = $_SERVER ['REMOTE_ADDR'] ? $_SERVER ['REMOTE_ADDR'] : '127.0.0.1';
		dbinsert ( $log )->into ( '{activity_log}' )->exec ();
	}
}