<?php
/**
 * 微信消息回复 新闻回复
 * @author dq
 *
 */
class WeixinMsgRpNewsForm extends AbstractForm {
	private $id = array ('widget' => 'hidden','rules' => array ('digits' => '编号只能是数字.','regexp(/^[1-9][\d]*$/)' => '编号只能是数字,最大长度为10'));
	private $block_id = array ('rules' => array ('digits' => '编号只能是数字.','regexp(/^[1-9][\d]*$/)' => '编号只能是数字,最大长度为10'));
	
	
	
	
	/**
	 * 保存回复的消息
	 * @author dq
	 * @param array $data content uid
	 * @param number $msgId
	 * @return bool 
	 */
	public function save($data = array(), $msgId = 0){
	    $msgId = intval($msgId);
	    if(!is_array($data) || empty($data) || empty($data['block_id']) || $msgId<=0  || empty($data['table'])){
	        return false;
	    }
	    $exist = dbselect('*')->from('{weixin_msg_rp_news}')->where(array('msg_id'=>$msgId))->desc('id')->get();
	    
	    $time = time();
	    $save = array(
	        'update_time' => $time,
	        'update_uid' => $data['uid'],
	        'msg_id' => $msgId,
	        'block_id' => $data['block_id'],
	        'table' => $data['table']
	    );
	    unset($data['id']);
	    if($exist['id']>0){
	        $return = dbupdate('{weixin_msg_rp_news}')->set($save)->where(array('id'=>$exist['id']))->exec();
	    }else{
	        $save = array_merge($save,array('create_uid'=>$data['uid'],'create_time'=>$time));
	        $return = dbinsert($save)->into('{weixin_msg_rp_news}')->exec();
	    }
	    
	    if($return === false){
	        return false;
	    }else{
	        return true;
	    }
	}
}
