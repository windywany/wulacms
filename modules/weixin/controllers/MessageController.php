<?php
/**
 * 消息
 * @author dingqiang
 * 
 */
class MessageController extends Controller {
    protected $checkUser = true;
    
    private $_msgType = array(
        'text' => '文本消息',
        'image' => '图片消息',
        'voice' => '语音消息',
        'video' => '视频消息',
        'music' => '音乐消息',
        'news' => '图文消息',
    );
    
    
    
    
    /**
     * 订阅消息管理
     * 
     */
    public function sub(){
        $data = $this->_edit(1);
        return view('message/message_edit.tpl',$data);
    }
    
    /**
     * 自动回复消息
     *
     */
    public function auto(){
        $data = $this->_edit(2);
        return view('message/message_edit.tpl',$data);
    }
    /**
     * 关键字消息 回复 详情
     */
    function info($id = 0){
        $data = $this->_edit(3,$id);
        return view('message/message_edit.tpl',$data);
    }
    /**
     * 保存 订阅、自动回复、关键词 消息回复
     * 
     */
    public function msgsave(){
        $table = irqst('msg_table',1);
        $return = $this->_save($table);
        return $return['event'];
    }
    
     /**
        * 消息管理
        * @author DQ
        * @date 2015年10月30日 下午3:45:12
        * 
        */
    function index(){
        $data = array();
        return view('message/index.tpl',$data);
    }
    
    
     /**
        * 消息列表
        * @author DQ
        * @date 2015年10月26日 上午11:30:51
        * 
        */
    public function data($_cp = 1, $_lt = 20, $_sf = 'id', $_od = 'd', $_ct = 0) {
        $rows = dbselect ('*') ->from ( '{weixin_msg_keyword}' )->limit ( ($_cp - 1) * $_lt, $_lt );
        $keyword = rqst ( 'keyword' );
        if($keyword){
            $where ['keyword LIKE'] = "%{$keyword}%";
        }
        $where ['deleted'] = 0;
        $rows->where($where);
        $rst = $rows->sort ( $_sf, $_od )->toArray();
        $total = '';
        if ($_ct) {
            $total = $rows->count ( 'id' );
        }
        foreach($rst as $key => $val){
            $rst[$key]['msgName'] = $this->_msgType[$val['msg_type']];
        }
        $data = array ('total' => $total,'rows' => $rst );
        return view ( 'message/data.tpl', $data );
    }
    
    
    /**
     * 详细信息
     * @author DQ
     * @date 2015年9月6日 下午5:30:30
     * @param
     * @return
     *
     */
    public function edit($id = 0){
        $data = array();
        if($id>0){
            $data = dbselect('*')->from('{weixin_msg_keyword}')->where()->desc('id')->get();
        }
        $form = new WeixinMsgKeywordForm($data);
        $data['rules'] = $form->rules();
        $data['widgets'] = new DefaultFormRender($form->buildWidgets($data));
        $data['formName'] = get_class($form);
        return view('message/form.tpl',$data);
    }
    
    /**
     * 关键字信息 保存
     * 
     */
    function save(){
        $form = new WeixinMsgKeywordForm ();
        $data = $form->valid ();
        if ($data) {
            $time = time ();
            $uid = $this->user->getUid ();
            $data ['update_time'] = $time;
            $data ['update_uid'] = $uid;
            $data ['deleted'] = 0;
            $id = $data ['id'];
            unset ( $data ['id'] );
            if (empty ( $id )) {
                $data ['create_time'] = $time;
                $data ['create_uid'] = $uid;
                $db = dbinsert ( $data );
                $rst = $db->into ( '{weixin_msg_keyword}' )->exec ();
            } else {
                $db = dbupdate ( '{weixin_msg_keyword}' );
                $rst = $db->set ( $data )->where ( array ('id' => $id ) )->exec ();
            }
            if ($rst) {
                return NuiAjaxView::click ( '#rtn2ads', '信息已经保存.' );
            } else {
                return NuiAjaxView::error ( '保存信息出错啦:' . DatabaseDialect::$lastErrorMassge );
            }
        } else {
            return NuiAjaxView::validate ( get_class ( $form ), '表单数据格式有误', $form->getErrors () );
        }
    }
    
     /**
        * 删除用户
        * @author DQ
        * @date 2015年10月26日 下午2:36:20
        * @param int 文章详情
        * @return view 
        * 
        */
	function del($id){
	    $id = intval($id);
	    if($id<=0){
	        return NuiAjaxView::error('参数错误');
	    }
	    $rs = dbselect('*')->from('{weixin_msg_keyword}')->where(array('id'=>$id))->get(0);
	    if(empty($rs)){
	        return NuiAjaxView::error('不存在该数据！');
	    }
	    dbdelete()->from('{weixin_msg_rp_text}')->where(array('msg_id'=>$id,'table'=>'keyword'))->exec();
	    dbdelete()->from('{weixin_msg_rp_image}')->where(array('msg_id'=>$id,'table'=>'keyword'))->exec();
	    dbdelete()->from('{weixin_msg_rp_voice}')->where(array('msg_id'=>$id,'table'=>'keyword'))->exec();
	    dbdelete()->from('{weixin_msg_rp_video}')->where(array('msg_id'=>$id,'table'=>'keyword'))->exec();
	    dbdelete()->from('{weixin_msg_rp_music}')->where(array('msg_id'=>$id,'table'=>'keyword'))->exec();
	    dbdelete()->from('{weixin_msg_rp_news}')->where(array('msg_id'=>$id,'table'=>'keyword'))->exec();
	    
        $return = dbupdate('{weixin_msg_keyword}')->set(array('deleted'=>1,'update_time'=>time()))->where(array ( 'id' => $id ) )->exec();
	    if($return){
	        $recycle = new DefaultRecycle ( $id, 'weixin_msg_keyword', 'weixin_msg_keyword', 'ID:({id}) 名称: {name}' );
	        RecycleHelper::recycle ( $recycle );
	        return NuiAjaxView::reload ( '#page-table', '所选关键字已放入回收站.' );
	    }else{
	        return NuiAjaxView::error('删除失败！');
	    }
	}
	
	/**
	 * 批量删除
	 * @param string $ids
	 */
	public function dels($ids) {
	    $ids = safe_ids ( $ids, ',', true );
	    if (! empty ( $ids )) {
	        $data ['deleted'] = 1;
	        $data ['update_time'] = time ();
	        //删除表中数据
	        dbdelete()->from('{weixin_msg_rp_text}')->where(array('msg_id IN '=>$ids,'table'=>'keyword'))->exec();
	        dbdelete()->from('{weixin_msg_rp_image}')->where(array('msg_id IN '=>$ids,'table'=>'keyword'))->exec();
	        dbdelete()->from('{weixin_msg_rp_voice}')->where(array('msg_id IN '=>$ids,'table'=>'keyword'))->exec();
	        dbdelete()->from('{weixin_msg_rp_video}')->where(array('msg_id IN '=>$ids,'table'=>'keyword'))->exec();
	        dbdelete()->from('{weixin_msg_rp_music}')->where(array('msg_id IN '=>$ids,'table'=>'keyword'))->exec();
	        dbdelete()->from('{weixin_msg_rp_news}')->where(array('msg_id IN '=>$ids,'table'=>'keyword'))->exec();
	        
	        if (dbupdate ( '{weixin_msg_keyword}' )->set ( $data )->where ( array ('id IN' => $ids ) )->exec ()) {
	            $recycle = new DefaultRecycle ( $ids, 'weixin_msg_keyword', 'weixin_msg_keyword', 'ID:({id}) 名称: {name}' );
	            RecycleHelper::recycle ( $recycle );
	            return NuiAjaxView::ok ( '已删除', 'click', '#refresh' );
	        } else {
	            return NuiAjaxView::error ( '数据库操作失败.' );
	        }
	    } else {
	        Response::showErrorMsg ( '错误的编号', 500 );
	    }
	}
	
	
	/**
	 * 编辑数据准备
	 *
	 * @param number $msgType 1 订阅消息 2 自动回复消息 3 关键字消息
	 * @param number $mesageId 当消息类型为关键字时，则为关键字消息表id
	 */
	protected function _edit($msgType = 0 , $messageId = 0){
	    switch ($msgType){
	        case 1:
	            unset($this->_msgType['music']);
	            unset($this->_msgType['news']);
	            
	            $tableName = '{weixin_msg_sub}';
	            $data['msgTypeList'] = $this->_msgType;
	            $keyList = array_keys($this->_msgType);
	            $tableCol = 'sub';
	            $rs = dbselect('*')->from('{weixin_msg_sub}')->desc('id')->get();
	            break;
	        case 2:
	            unset($this->_msgType['music']);
	            unset($this->_msgType['news']);
	            
	            $tableName = '{weixin_msg_auto}';
	            $data['msgTypeList'] = $this->_msgType;
	            $keyList = array_keys($this->_msgType);
	            $tableCol = 'auto';
	            $rs = dbselect('*')->from('{weixin_msg_auto}')->desc('id')->get();
	            break;
	        case 3:
	            $tableName = '{weixin_msg_keyword}';
	            $data['msgTypeList'] = $this->_msgType;
	            $keyList = array_keys($this->_msgType);
	            $tableCol = 'keyword';
	            $rs = dbselect('*')->from($tableName)->where(array('id'=>$messageId))->desc('id')->get();
// 	            if(!$rs){
// 	                return NuiAjaxView::error ( '内容不存在！' );
// 	            }
	            break;
	    }
	    
	    
	    if(!in_array($rs['msg_type'],$keyList)){
	        $rs['msg_type'] = $keyList[0];
	    }
	    //文本消息
	    $rsText = dbselect('*')->from('{weixin_msg_rp_text}')->where(array('msg_id'=>$rs['id'],'table'=>$tableCol))->desc('id')->get();
	    $textForm = new WeixinMsgRpTextForm($rsText);
	    $data['text']['rules'] = $textForm->rules();
	    $data['text']['widgets'] = new DefaultFormRender($textForm->buildWidgets($rsText));
	    $data['text']['form'] = get_class($textForm);
	    
	    //图片消息
	    $rsImage = dbselect('*')->from('{weixin_msg_rp_image}')->where(array('msg_id'=>$rs['id'],'table'=>$tableCol))->desc('id')->get();
	    $imageForm = new WeixinMsgRpImageForm($rsImage);
	    $data['text']['rules'] = $imageForm->rules();
	    $data['image']['widgets'] = new DefaultFormRender($imageForm->buildWidgets($rsImage));
	    $data['image']['form'] = get_class($imageForm);
	    
	    //语音消息
	    $rsVoice = dbselect('*')->from('{weixin_msg_rp_voice}')->where(array('msg_id'=>$rs['id'],'table'=>$tableCol))->desc('id')->get();
	    $voiceForm = new WeixinMsgRpImageForm($rsVoice);
	    $data['voice']['rules'] = $imageForm->rules();
	    $data['voice']['widgets'] = new DefaultFormRender($voiceForm->buildWidgets($rsVoice));
	    $data['voice']['form'] = get_class($voiceForm);
	    
	    //视频消息
	    $rsVideo = dbselect('*')->from('{weixin_msg_rp_video}')->where(array('msg_id'=>$rs['id'],'table'=>$tableCol))->desc('id')->get();
	    $videoForm = new WeixinMsgRpVideoForm($rsVideo);
	    $data['video']['rules'] = $videoForm->rules();
	    $data['video']['widgets'] = new DefaultFormRender($videoForm->buildWidgets($rsVideo));
	    $data['video']['form'] = get_class($videoForm);
	    
	    
	    if($tableName == '{weixin_msg_keyword}'){
	        //音乐消息
	        $rsMusic = dbselect('*')->from('{weixin_msg_rp_music}')->where(array('msg_id'=>$rs['id'],'table'=>$tableCol))->desc('id')->get();
	        $musicForm = new WeixinMsgRpMusicForm();
	        $data['music']['rules'] = $musicForm->rules();
	        $data['music']['widgets'] = new DefaultFormRender($musicForm->buildWidgets($rsMusic));
	        $data['music']['form'] = get_class($musicForm);
	        
	        //新闻消息
	        $rsNews = dbselect('*')->from('{weixin_msg_rp_news}')->where(array('msg_id'=>$rs['id'],'table'=>$tableCol))->desc('id')->get();
	        //查找区块名称
	        if($rsNews){
	            $tmp = dbselect('*')->from('{cms_block}')->where(array('id'=>$rsNews['block_id']))->get('name');
	            $rsNews['name'] = $tmp?$rsNews['block_id'].':'.$tmp:'';
	        }
	        $newsForm = new WeixinMsgRpNewsForm();
	        $data['news']['rules'] = $newsForm->rules();
	        $data['news']['form'] = get_class($newsForm);
	        $data['news']['data'] = $rsNews;
	    }
	    $data['data'] = $rs;
	    $data['msgType'] = $msgType;
	    $data['keyList'] = $keyList;
	    
	    
	    return $data;
	}
	
	
	
	/**
	 * 消息回复内容 保存
	 * 
	 * @param number $msgType 1 订阅消息 2 自动回复消息 3 关键字消息
	 * @param number $mesageId 当消息类型为关键字时，则为关键字消息表id
	 */
	protected function _save($msgType = 0){
	    $rtn = array(
	        'msg' => '',
	        'status' => false
	    );
	    if(!in_array($msgType,array(1,2,3))){
	        $rtn['msg'] = '消息类型错误！';
	        return $rtn;
	    }
	    switch ($msgType){
	        case 1:
	            $tableName = '{weixin_msg_sub}';
	            $tableCol = 'sub';
	            $Form = new WeixinMsgSubForm();
	            break;
            case 2:
                $tableName = '{weixin_msg_auto}';
                $tableCol = 'auto';
                $Form = new WeixinMsgAutoForm();
                break;
            case 3:
                $tableName = '{weixin_msg_keyword}';
                $tableCol = 'keyword';
                $Form = new WeixinMsgKeywordForm();
                break;
	    }
	    
	    $Id = irqst('msg_id',0);
	    $data['msg_type'] = rqst('msg_type');
	    
	    if($data){
	        $uid = $this->user->getUid();
	        $time = time();
	        unset($data['msg_id']);
	        $data['update_time'] = $time;
	        $data['update_uid'] = $uid;
	        $data['deleted'] = 0;
	        
	        //关键字消息，必须有id
	        if($tableName == '{weixin_msg_keyword}'){
	            if($Id<=0){
	                $rtn['msg'] = '参数错误！';
	                $rtn['event'] = NuiAjaxView::error ( '出错啦:关键字消息参数错误！' );
	                return $rtn;
	            }
	            $return = dbupdate($tableName)->set($data)->where(array('id'=>$Id))->exec();
	        }else if($tableName == '{weixin_msg_sub}' || $tableName == '{weixin_msg_auto}'){
	            if($Id > 0){
	                $return = dbupdate($tableName)->set($data)->where(array('id'=>$Id))->exec();
	            }else{
	                $data = array_merge($data,array('create_time'=>$time,'create_uid'=>$uid));
	                $return =dbinsert($data)->into($tableName)->exec();
	                $Id = $return[0];
	            }
	        }else{
	            $rtn['msg'] = '参数错误！';
	            $rtn['event'] = NuiAjaxView::error ( '出错啦:请刷新重试.' );
	            return $rtn;
	        }
	    
	        if($data['msg_type'] == 'text'){
	            $textForm = new WeixinMsgRpTextForm();
	            $text = $textForm->valid();
	            if(!$text){
	                $msg = array_values($textForm->getErrors());
	                $rtn['event'] = NuiAjaxView::error( '出错啦: '.$msg[0].' !' );
	                return $rtn;
	            }
	            $returnAttr = $textForm->save(array_merge($text,array('uid'=>$uid,'table'=>$tableCol)),$Id);
	        }
	        if($data['msg_type'] == 'image'){
	            $imageForm = new WeixinMsgRpImageForm();
	            $image = $imageForm->valid();
	            if(!$image){
	                $msg = array_values($imageForm->getErrors());
	                $rtn['event'] = NuiAjaxView::error( '出错啦: '.$msg[0].' !' );
	                return $rtn;
	            }
	            $returnAttr = $imageForm->save(array_merge($image,array('uid'=>$uid,'table'=>$tableCol)),$Id);
	        }
	        if($data['msg_type'] == 'voice'){
	            $voiceform = new WeixinMsgRpVoiceForm();
	            $voice = $voiceform->valid();
	            if(!$voice){
	                $msg = array_values($voiceform->getErrors());
	                $rtn['event'] = NuiAjaxView::error( '出错啦: '.$msg[0].' !' );
	                return $rtn;
	            }
	            $returnAttr = $voiceform->save(array_merge($voice,array('uid'=>$uid,'table'=>$tableCol)),$Id);
	        }
	    
	        if($data['msg_type'] == 'video'){
	            $videoform = new WeixinMsgRpVideoForm();
	            $video = $videoform->valid();
	            if(!$video){
	                $msg = array_values($videoform->getErrors());
	                $rtn['event'] = NuiAjaxView::error( '出错啦: '.$msg[0].' !' );
	                return $rtn;
	            }
	            $returnAttr = $videoform->save(array_merge($video,array('uid'=>$uid,'table'=>$tableCol)),$Id);
	        }
	        
	        if($data['msg_type'] == 'music'){
	            $musicform = new WeixinMsgRpMusicForm();
	            $music = $musicform->valid();
	            if(!$music){
	                $msg = array_values($musicform->getErrors());
	                $rtn['event'] = NuiAjaxView::error( '出错啦: '.$msg[0].' !' );
	                return $rtn;
	            }
	            $returnAttr = $musicform->save(array_merge($music,array('uid'=>$uid,'table'=>$tableCol)),$Id);
	        }
	        if($data['msg_type'] == 'news'){
	            $newsform = new WeixinMsgRpNewsForm();
	            $news = $newsform->valid();
	            if(!$news){
	                $msg = array_values($newsform->getErrors());
	                $rtn['event'] = NuiAjaxView::error( '出错啦: '.$msg[0].' !' );
	                return $rtn;
	            }
	            $returnAttr = $newsform->save(array_merge($news,array('uid'=>$uid,'table'=>$tableCol)),$Id);
	        }
	    
	        if($return && $returnAttr ===true){
	            $rtn['status'] = true;
	            $rtn['event'] = NuiAjaxView::ok ( '保存成功', 'click', '#refresh' );
	        }else{
	            $rtn['event'] = NuiAjaxView::error( '出错啦:数据库操作失败.' );
	        }
	    }else{
	        $rtn['event'] = NuiAjaxView::error ( '出错啦:请刷新重试.' );
	    }
	    return $rtn;
	}
	
}