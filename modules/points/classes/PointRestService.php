<?php

namespace points\classes;

class PointRestService {
	public static function on_init_rest_server($server) {
		$server->registerClass ( new \points\classes\PointRestService (), '1.0', 'point' );
		return $server;
	}
	/**
	 * 积分日志
	 *
	 * @author zhangqian
	 *         @date 2016年8月8日 18:10:05
	 * @param        	
	 *
	 * @return
	 *
	 *
	 */
	public function rest_post_point_log($params, $key, $secret) {
		$rtn = array ('error' => 404,'message' => 'param error' );
		$token = trim ( $params ['token'] );
		if (empty ( $token )) {
			$rtn ['message'] = '请先登录';
			return $rtn;
		}
		
		// 获取用户信息通过token
		$rsUser = \passport\classes\PassportUser::getUserInfoByToken ( $token );
		if (empty ( $rsUser )) {
			$rtn ['error'] = 444;
			$rtn ['message'] = '请先登录';
			return $rtn;
		}
		$page = intval ( $params ['page'] );
		$pageSize = intval ( $params ['pageSize'] );
		$type = $params ['type'];
		$log = dbselect ( '*' )->from ( '{ds_user_point_log}' )->where ( [ 'mid' => $rsUser ['mid'] ] )->desc ( 'id' );
		$log->limit ( ($page - 1) * $pageSize, $pageSize );
		$list = [ ];
		foreach ( $log as $k => $l ) {
			$list [$k] ['id'] = $l ['id'];
			$list [$k] ['amount_show'] = $l ['point'];
			if ($l ['from_type'] == 0) {
				$list [$k] ['full_pay_type_desc'] = '签到奖励';
			} elseif ($l ['from_type'] == 1) {
				$list [$k] ['full_pay_type_desc'] = '邀请好友';
			}
			$list [$k] ['time'] = date ( 'Y-m-d', $l ['create_time'] );
		}
		$rtn ['error'] = 0;
		$rtn ['message'] = 'ok';
		$rtn ['data'] = [ 'list' => $list ];
		return $rtn;
	}
}
