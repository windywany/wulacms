<?php
/**
 * 微信消息回复 音乐回复
 * @author dq
 *
 */
class WeixinMsgRpMusicForm extends AbstractForm {
	private $id = array ('widget' => 'hidden','rules' => array ('digits' => '编号只能是数字.','regexp(/^[1-9][\d]*$/)' => '编号只能是数字,最大长度为10'));
	private $media_id = array ('group' => 1,'col' => 4,'label' => '多媒体ID','rules' => array ('required' => '请填写多媒体id.' ));
	private $title = array ('group' => 1,'col' => 4,'label' => '标题');
	private $note = array ('group' => 1,'col' => 4,'label' => '描述');
	private $url = array ('group' => 1,'col' => 4,'label' => '音乐链接');
	private $hq = array ('group' => 1,'col' => 4,'label' => '高质量音乐链接','note'=>'高质量音乐链接，WIFI环境优先使用该链接播放音乐');
	
	
	
	
	/**
	 * 保存回复的消息
	 * @author dq
	 * @param array $data content uid
	 * @param number $msgId
	 * @return bool 
	 */
	public function save($data = array(), $msgId = 0){
	    $msgId = intval($msgId);
	    if(!is_array($data) || empty($data) || empty($data['media_id']) || $msgId<=0 || empty($data['table'])){
	        return false;
	    }
	    $exist = dbselect('*')->from('{weixin_msg_rp_music}')->where(array('msg_id'=>$msgId))->desc('id')->get();
	    
	    $time = time();
	    $save = array(
	        'update_time' => $time,
	        'update_uid' => $data['uid'],
	        'media_id' => $data['media_id'],
	        'msg_id' => $msgId,
	        'title' => $data['title'],
	        'note' => $data['note'],
	        'url' => $data['url'],
	        'hq' => $data['hq'],
	        'table' => $data['table']
	    );
	    if($exist['id']>0){
	        $return = dbupdate('{weixin_msg_rp_music}')->set($save)->where(array('id'=>$exist['id']))->exec();
	    }else{
	        $save = array_merge($save,array('create_uid'=>$data['uid'],'create_time'=>$time));
	        $return = dbinsert($save)->into('{weixin_msg_rp_music}')->exec();
	    }
	    if($return === false){
	        return false;
	    }else{
	        return true;
	    }
	}
}
