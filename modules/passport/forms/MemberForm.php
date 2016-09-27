<?php

/**
 * 用户
 * @author DQ
 * @date 2016年4月20日 下午3:57:34
 * @param
 * @return
 *
 */
namespace passport\forms;

class MemberForm extends \AbstractForm {
	
	/**
	 * 获取meta属性值
	 *
	 * @author DQ
	 *         @date 2016年4月20日 下午5:14:25
	 * @param
	 *        	int mid
	 * @param
	 *        	string 名称 age/gender/channel/device
	 * @return
	 *
	 *
	 */
	public function getMeta($mid = 0, $name = '') {
		return dbselect ()->from ( '{member_meta}' )->where ( array ('mid' => $mid,'name' => $name ) )->get ( 'value' );
	}
	
	/**
	 * 修改meta属性值
	 *
	 * @param number $mid
	 *        	用户id
	 * @param string $value
	 *        	值
	 * @param string $name
	 *        	key名称
	 * @param bool $update
	 *        	是否更新 true 更新 false不更新
	 *        	是否更新数据
	 */
	public function saveMeta($mid = 0, $value = '', $name = '', $update = true) {
		$rsGender = dbselect ( '*' )->from ( '{member_meta}' )->where ( array ('mid' => $mid,'name' => $name ) )->get ();
		$time = time ();
		$data ['update_time'] = $time;
		$data ['value'] = $value;
		if (empty ( $rsGender )) {
			$data ['create_time'] = $time;
			$data ['mid'] = $mid;
			$data ['name'] = $name;
			$return = dbinsert ( $data )->into ( '{member_meta}' )->exec ( true );
		} else {
			$return = true;
			if ($update == true) {
				$return = dbupdate ( '{member_meta}' )->set ( $data )->where ( array ('mid' => $mid,'name' => $name ) )->exec ( true );
			}
		}
		return $return;
	}
	
	/**
	 * 保存性别
	 *
	 * @author DQ
	 *         @date 2016年4月20日 下午3:58:26
	 * @param
	 *        	int 用户mid
	 * @param
	 *        	int 性别 0 未知 1 男 2 女
	 * @return
	 *
	 *
	 */
	public function saveGender($mid = 0, $gender = 0) {
		if ($mid <= 0) {
			return false;
		}
		return $this->saveMeta ( $mid, $gender, 'gender' );
	}
	
	/**
	 * 保存年龄
	 *
	 * @author DQ
	 *         @date 2016年4月20日 下午3:58:26
	 * @param
	 *        	int 用户mid
	 * @param
	 *        	int 年龄
	 * @return
	 *
	 *
	 */
	public function saveAge($mid = 0, $age = 0) {
		if ($mid <= 0) {
			return false;
		}
		return $this->saveMeta ( $mid, $age, 'age' );
	}
	
	/**
	 * 保存渠道
	 *
	 * @author DQ
	 *         @date 2016年4月27日 下午5:05:00
	 * @param
	 *        	int 渠道id
	 * @param
	 *        	string 渠道号码
	 * @return
	 *
	 *
	 */
	public function saveChannel($mid = 0, $channel = '') {
		$return = false;
		if ($mid <= 0) {
			return $return;
		}
		return $this->saveMeta ( $mid, $channel, 'channel', false );
	}
	
	/**
	 * 保存设备号码
	 *
	 * @author DQ
	 *         @date 2016年4月27日 下午5:01:01
	 * @param
	 *        	int 用户mid
	 * @param
	 *        	string 设备号码
	 * @return
	 *
	 *
	 */
	public function saveDevice($mid = 0, $device = '') {
		if ($mid <= 0) {
			return false;
		}
		return $this->saveMeta ( $mid, $device, 'device' );
	}
	
	/**
	 * 保存设备号码
	 *
	 * @author DQ
	 *         @date 2016年4月27日 下午5:01:01
	 * @param
	 *        	int 用户mid
	 * @param
	 *        	int 1 Android 2 iOS 设备类型
	 * @return
	 *
	 *
	 */
	public function saveDeviceFrom($mid = 0, $deviceFrom = '') {
		if ($mid <= 0) {
			return false;
		}
		return $this->saveMeta ( $mid, $deviceFrom, 'device_from' );
	}
	
	/**
	 * 保存是否修改过用户昵称
	 *
	 * @author DQ
	 *         @date 2016年6月14日 下午3:25:45
	 * @param
	 *        	int 用户id
	 * @param
	 *        	int 是否修改用户昵称 0 未修改 1已经修改
	 * @return bool
	 *
	 */
	public function saveChangeNickname($mid = 0) {
		if ($mid <= 0) {
			return false;
		}
		return $this->saveMeta ( $mid, 1, 'change_nickname' );
	}
	
	/**
	 * 保存用户是否修改过用户头像
	 *
	 * @author DQ
	 *         @date 2016年6月14日 下午3:25:45
	 * @param
	 *        	int 用户id
	 * @param
	 *        	int 是否修改用户昵称 0 未修改 1已经修改
	 * @return bool
	 *
	 */
	public function saveChangeAvatar($mid = 0) {
		if ($mid <= 0) {
			return false;
		}
		return $this->saveMeta ( $mid, 1, 'change_avatar' );
	}
	
	/**
	 * 获取上级mid
	 *
	 * @author DQ
	 *         @date 2016年4月28日 下午6:40:37
	 * @param
	 *        	int mid
	 * @param
	 *        	int 查找层级
	 * @return array 父级mid 数组mid顺序 从关系近到远
	 *        
	 */
	public function getMaster($slaver = 0, $level = 1) {
		$rsParent = [ ];
		if ($slaver <= 0 || $level <= 0) {
			return $rsParent;
		}
		for($i = 1; $i <= $level; $i ++) {
			$tmp = dbselect ( '*' )->from ( '{member}' )->where ( array ('mid' => $slaver ) )->get ( 'invite_mid' );
			if (empty ( $tmp )) {
				break;
			}
			$slaver = $tmp;
			$rsParent [] = intval ( $tmp );
		}
		return $rsParent;
	}
	
	/**
	 * 获取子级
	 *
	 * @author DQ
	 *         @date 2016年7月14日 下午1:50:34
	 * @param
	 *        	int mid
	 * @param
	 *        	int 级别
	 * @return array key=> 用户mid , value=>级别
	 *        
	 */
	public function getSlaver($master = 0, $level = 1) {
		$rsSlaver = [ ];
		if ($master <= 0 || $level <= 0) {
			return $rsSlaver;
		}
		$where = [ 'invite_mid' => $master ];
		for($i = 1; $i <= $level; $i ++) {
			$tmp = dbselect ( '*' )->from ( '{member}' )->where ( $where )->desc ( 'mid' )->toArray ( 'mid' );
			if (empty ( $tmp )) {
				break;
			}
			$where = [ 'invite_mid IN ' => $tmp ];
			$newData = [ ];
			foreach ( $tmp as $key => $val ) {
				$newData [$val] = $i;
			}
			foreach ( $newData as $key => $val ) {
				$rsSlaver [$key] = $val;
			}
		}
		return $rsSlaver;
	}
	
	/**
	 * 判断邀请码是否存在
	 *
	 * @author DQ
	 *         @date 2016年4月29日 上午11:17:11
	 * @param
	 *        	int 邀请码
	 * @return int 返回master
	 *        
	 */
	public function existInvideCode($code = 0) {
		$mid = dbselect ()->from ( '{member}' )->where ( array ('mid' => $code,'status' => 1 ) )->get ( 'mid' );
		if (empty ( $mid )) {
			return 0;
		} else {
			return $mid;
		}
	}
	/**
	 * 判断是否已经被邀请过
	 *
	 * @author DQ
	 *         @date 2016年4月29日 上午11:21:35
	 * @param
	 *        	int 被邀请人mid
	 * @return bool true 已经邀请过 false没有被邀请
	 *        
	 */
	public function hasInvited($slaver = 0) {
		$masterMid = intval ( dbselect ()->from ( '{member}' )->where ( array ('mid' => $slaver ) )->get ( 'invite_mid' ) );
		if ($masterMid > 0) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * 判断你互相邀请
	 *
	 * @author DQ
	 *         @date 2016年4月29日 下午4:00:11
	 * @param        	
	 *
	 * @return true 互相邀请
	 *        
	 */
	public function eachOtherInvited($me = 0, $other = 0) {
		$inviteMid = dbselect ()->from ( '{member}' )->where ( array ('mid' => $other ) )->get ( 'invite_mid' );
		if ($inviteMid == $me) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * 修改邀请人
	 *
	 * @author DQ
	 *         @date 2016年4月29日 上午11:26:37
	 * @param
	 *        	int 被邀请人
	 * @param
	 *        	int 邀请人
	 * @return bool
	 *
	 */
	public function changeInviteMid($slaver = 0, $master = 0) {
		$return = dbupdate ( '{member}' )->set ( array ('invite_mid' => $master,'invite_code' => $master ) )->where ( array ('mid' => $slaver,'status' => 1 ) )->exec ( true );
		if ($return) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * 用户注册
	 *
	 * @author DQ
	 *         @date 2016年6月2日 下午4:18:22
	 * @param
	 *        	array 注册数据
	 * @return int 0 失败 1 成功
	 *        
	 */
	public function userRegsiter($data = array()) {
		$return = dbinsert ( $data )->into ( '{member}' )->exec ();
		if (empty ( $return [0] )) {
			return 0;
		}
		$mid = $return [0];
		fire ( 'dashen_user_reg', $mid );
		return $mid;
	}
}