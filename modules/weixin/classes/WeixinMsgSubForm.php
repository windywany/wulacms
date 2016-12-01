<?php
/**
 * 微信订阅消息回复
 * @author dingqiang
 *
 */
class WeixinMsgSubForm extends AbstractForm {
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
	
	/**
	 * 回复
	 */
	static public function getContent() {
	    $data = array();
	    $rs = dbselect('*')->from('{weixin_msg_sub}')->where(array('deleted'=>0))->get();
	    if($rs['msg_type']){
	        switch ($rs['msg_type']){
	            case 'text':
	                $tmp = dbselect('*')->from('{weixin_msg_rp_text}')->where(array('msg_id'=>$rs['id'],'table'=>'sub'))->get();
	                $data = array(
	                    'MsgType' => $rs['msg_type'],
	                    'Content' =>$tmp['content']
	                );
	                break;
	            case 'image':
	                $tmp = dbselect('*')->from('{weixin_msg_rp_image}')->where(array('msg_id'=>$rs['id'],'table'=>'sub'))->get();
	                $data = array(
	                    'MsgType' => $rs['msg_type'],
	                    'MediaId' => $tmp['media_id']
	                );
	                break;
	            case 'voice':
	                $tmp = dbselect('*')->from('{weixin_msg_rp_voice}')->where(array('msg_id'=>$rs['id'],'table'=>'sub'))->get();
	                $data = array(
	                    'MsgType' => $rs['msg_type'],
	                    'MediaId' => $tmp['media_id']
	                );
	                break;
	            case 'video':
	                $tmp = dbselect('*')->from('{weixin_msg_rp_video}')->where(array('msg_id'=>$rs['id'],'table'=>'sub'))->get();
	                $data = array(
	                    'MsgType' => $rs['msg_type'],
	                    'MediaId' => $tmp['media_id'],
	                    'Title' => $tmp['title'],
	                    'Description' => $tmp['note'],
	                );
	                break;
	        }
	    }
	    return $data;
	}
	
}
