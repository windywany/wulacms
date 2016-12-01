<?php
/**
 * 微信自动消息回复 
 * @author dingqiang
 *
 */
class WeixinMsgAutoForm extends AbstractForm {
	private $id = array ('widget' => 'hidden','rules' => array ('digits' => '编号只能是数字.','regexp(/^[1-9][\d]*$/)' => '编号只能是数字,最大长度为10'));
	private $msg_type = array (
        'widget'=>'radio','group' => 1,'col' => 4,'label' => '消息类型','rules' => array ('required' => '请选择消息类型.' ),'default'=>'text',
        'defaults'=>
    	    array(
    	        'image' => '图片消息',
    	        'voice' => '语音消息',
    	        'video' => '文本消息',
    	        'music' => '音乐消息',
    	        'music' => '音乐消息',
    	        'news' => '图文消息'
    	    )
	   );
}
