<?php
/**
 * 微信关键字消息回复  
 * @author dingqiang
 *
 */
class WeixinMsgKeywordForm extends AbstractForm {
	private $id = array ('widget' => 'hidden','rules' => array ('digits' => '编号只能是数字.','regexp(/^[1-9][\d]*$/)' => '编号只能是数字,最大长度为10'));
	private $name = array('group'=>1,'col'=>6,'label'=>'规则名称','rules' => array ('required' => '请填写规则名称'));
	private $keyword = array('group'=>1,'col'=>6,'label'=>'关键词','note'=>'用英文逗号(,)分隔开','rules' => array ('required' => '请填写规则名称'));
	private $msg_type = array ('widget'=>'radio','group' => 2,'col' => 12,'label' => '消息类型','rules' => array ('required' => '请选择消息类型.' ),'default'=>'text','defaults'=>array('text' => '文本消息','image' => '图片消息','voice' => '语音消息','video' => '视频消息','music' => '音乐消息','news' => '图文消息'));
	
	
	
	
	static public function getKeywordContent($keyword = '') {
	    $keyword = trim($keyword);
	    $data = array();
	    if(!$keyword){
	        return $data;
	    }
	    $rs = dbselect('*')->from('{weixin_msg_keyword}')->where(array('deleted'=>0,'keyword LIKE '=>"%{$keyword}%"))->get();
	    
	    if($rs['msg_type']){
	        switch ($rs['msg_type']){
	            case 'text':
	                $tmp = dbselect('*')->from('{weixin_msg_rp_text}')->where(array('msg_id'=>$rs['id'],'table'=>'keyword'))->get();
	                $data = array(
	                    'MsgType' => $rs['msg_type'],
	                    'Content' =>$tmp['content'] 
	                );
	                break;
                case 'image':
                    $tmp = dbselect('*')->from('{weixin_msg_rp_image}')->where(array('msg_id'=>$rs['id'],'table'=>'keyword'))->get();
                    $data = array(
                        'MsgType' => $rs['msg_type'],
                        'MediaId' => $tmp['media_id']
                    );
                    break;
                case 'voice':
                    $tmp = dbselect('*')->from('{weixin_msg_rp_voice}')->where(array('msg_id'=>$rs['id'],'table'=>'keyword'))->get();
                    $data = array(
                        'MsgType' => $rs['msg_type'],
                        'MediaId' => $tmp['media_id']
                    );
                    break;
                case 'video':
                    $tmp = dbselect('*')->from('{weixin_msg_rp_video}')->where(array('msg_id'=>$rs['id'],'table'=>'keyword'))->get();
                    $data = array(
                        'MsgType' => $rs['msg_type'],
                        'MediaId' => $tmp['media_id'],
                        'Title' => $tmp['title'],
                        'Description' => $tmp['note'],
                    );
                    break;
                case 'music':
                    $tmp = dbselect('*')->from('{weixin_msg_rp_music}')->where(array('msg_id'=>$rs['id'],'table'=>'keyword'))->get();
                    $data = array(
                        'MsgType' => $rs['msg_type'],
                        'media_id' =>$tmp['media_id'],
                        'Title' =>$tmp['title'],
                        'Description' =>$tmp['note'],
                        'MusicURL' =>$tmp['url'],
                        'HQMusicUrl' =>$tmp['hq'],
                        'ThumbMediaId' =>$tmp['media_id']
                    );
                    break;
                case 'news':
                    $exist = dbselect('*')->from('{weixin_msg_rp_news}')->where(array('msg_id'=>$rs['id'],'table'=>'keyword'))->get();
                    if($exist['block_id']){
                        $tmp = dbselect('title as Title,url as Url,image as PicUrl,description as Description')->from('{cms_block_items}')->where(array('block'=>$exist['block_id']))->limit(0, 10)->asc('sort')->toArray();
                        $data = array(
                            'MsgType' => $rs['msg_type'],
                            'ArticleCount' => count($tmp),
                            'Articles' => $tmp
                        );
                    }
                    break;
	        }
	    }
	    return $data;
	}
}
