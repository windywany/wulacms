<?php
/**
 * 微信消息回复 视频回复
 * @author dq
 *
 */
class WeixinMsgRpVideoForm extends AbstractForm {
	private $id = array ('widget' => 'hidden','rules' => array ('digits' => '编号只能是数字.','regexp(/^[1-9][\d]*$/)' => '编号只能是数字,最大长度为10'));
	private $media_id = array ('group' => 1,'col' => 4,'label' => '多媒体ID','rules' => array ('required' => '请填写多媒体id.' ));
	private $title = array ('group' => 1,'col' => 4,'label' => '标题');
	private $note = array ('group' => 1,'col' => 4,'label' => '描述');
	
	
	
	/**
	 * 保存回复的消息
	 * @author dq
	 * @param array $data content uid
	 * @param number $msgId
	 * @return bool 
	 */
	public function save($data = array(), $msgId = 0){
	    $msgId = intval($msgId);
	    if(!is_array($data) || empty($data) || empty($data['media_id']) || $msgId<=0){
	        return false;
	    }
	    $exist = dbselect('*')->from('{weixin_msg_rp_video}')->where(array('msg_id'=>$msgId))->desc('id')->get();
	    
	    $time = time();
	    $save = array(
	        'update_time' => $time,
	        'update_uid' => $data['uid'],
	        'msg_id' => $msgId,
	        'media_id' => $data['media_id'],
	        'title' => $data['title'],
	        'note' => $data['note'],
	        'table' => $data['table']
	    );
	    if($exist['id']>0){
	        $return = dbupdate('{weixin_msg_rp_video}')->set($save)->where(array('id'=>$exist['id']))->exec();
	    }else{
	        $save = array_merge($save,array('create_uid'=>$data['uid'],'create_time'=>$time));
	        $return = dbinsert($save)->into('{weixin_msg_rp_video}')->exec();
	    }
	    if($return === false){
	        return false;
	    }else{
	        return true;
	    }
	}
}
