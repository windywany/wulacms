<?php

/**
 * 用户关注
 * @author zhangqian
 * @date 2016年9月2日 09:56:31
 * 
 */
namespace passport\classes;

class FollowerClass {
	// $follow 0取消关注 1关注
	public static function follow($mid, $uid, $follow = 0) {
		if ($mid == $uid) {
			return '不能关注自己！';
		}
		$exist = dbselect ( 'id' )->from ( '{member_follower}' )->where ( [ 'mid' => $mid,'follower' => $uid ] )->get ();
		$res = '请重试';
		if ($follow == 0) {
			if ($exist) {
				$res = dbdelete ()->from ( '{member_follower}' )->where ( [ 'id' => $exist ['id'] ] )->exec ( true );
			} else {
				return '您未关注该用户！';
			}
		} else {
			if ($exist) {
				return '您已关注该用户！';
			} else {
				$res = dbinsert ( [ 'mid' => $mid,'follower' => $uid,'create_time' => time () ] )->into ( '{member_follower}' )->exec ( true );
			}
		}
		return $res;
	}
	// 关注用户的数量
	public static function follow_num($mid) {
		return dbselect ( 'id' )->from ( '{member_follower}' )->where ( [ 'mid' => $mid ] )->count ( 'id' );
	}
}