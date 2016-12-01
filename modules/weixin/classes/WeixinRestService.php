<?php
class WeixinRestService {
	/**
	 * 注册.
	 *
	 * @param RestServer $server        	
	 */
	public static function on_init_rest_server($server) {
		$server->registerClass ( new WeixinRestService (), '1.0', 'weixin' );
		return $server;
	}
    
	
	 /**
	    * 第三方调用 模板消息 msg
	    * 暂未完成
	    * 
	    * @author DQ
	    * @date 2016年2月15日 下午3:26:40
	    * 
	    */
	public function rest_post_msg($param, $appkey, $sceret) {
		$data = array(
// 			'touser' => 'oDMLSt-p1OCMhnhFzaw6EF0IDigA',
			'touser' => 'oDMLSt6VfKVVyYA-PflZgRxWCzMg',
			"template_id" => "ngqIpbwh8bUfcSsECmogfXcV14J0tQlEpBO27izEYtY",
			"data" => array(
				"first" => array("value"=>"系统通知！","color" => '#173177'),
				"keynote1" => array("value"=>"通过！","color" => '#173177'),
				"keynote2" => array("value"=>"001","color" => '#173177'),
				"remark" => array("value"=>"remark！","color" => '#173177')
			)
		);
		//微信配置信息
		$AppID = cfg('LoginAppID@weixin');
		
		$AppSecret = cfg('LoginAppSecret@weixin');
		$Token = cfg('LoginToken@weixin');
		//初始化该值
		$chatWX = new \weixin\classes\Wechat(array('token'=>$Token,'appid'=>$AppID,'appsecret'=>$AppSecret));
		$token = WeixinUtil::getAccessToken();
		$chatWX->checkAuth(null,null,$token);
		$return = $chatWX->sendTemplateMessage($data);
		
		if($return == false){
			$rtn = array('status'=>false,'msg'=>'失败！');
		}else{
			$rtn = array('status'=>true,'msg'=>'成功！');
		}
		return $rtn;
	}

	/*微信支付*/
	public function rest_post_bonus($param, $appkey, $sceret)
	{
		$bonus = new \weixin\classes\WeixinBonus();
		$ret   = $bonus->send_bonus($param['openid'],$param['money']);

		if(isset($ret['result_code'])&&$ret['result_code'] == 'SUCCESS'){
			$rtn = array('status'=>true,'msg'=>'成功！');
		}else{
			$rtn =array('status'=>false,'msg'=>'失败！'.$ret['err_code_des']);
		}
		return $rtn;
	}
	
}