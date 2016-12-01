<?php
 /**
    * 微信分享配置表单
    * @author DQ
    * @date 2016年2月15日 下午5:16:50
    * 
    */
class WeixinSharePreferencesForm extends AbstractForm {
	private $share_status = array ('label' => '开启DEBUG','widget' => 'radio','group' => 1,'col' => 3,'default'=>0,'defaults' => array(0=>'否',1=>'是'));
	private $share_item = array ('label' => '分享类型','widget' => 'checkbox','group' => 1,'col' => 9,'default'=>0,'defaults' => array('onMenuShareTimeline'=>'分享到朋友圈', 'onMenuShareAppMessage'=>'发送给朋友','onMenuShareQQ'=>'分享到QQ','onMenuShareWeibo'=>'分享到微博','onMenuShareQZone'=>'分享到QQ空间'));
	
}
