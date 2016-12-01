<?php
/**
 * 引导用户订阅
 * @author dingqiang
 *
 */
class SubController extends WeixinBaseController {
    
    protected $exludes = array('index');
    public function index(){
        $data = array();
//         $unionid = $this->user->getAttr('unionid');
//         $data['user'] = dbselect('nickname')->from('{weixin_subscriber}')->where(array('unionid'=>$unionid))->get('nickname');
        $data['QRImage'] = cfg('QRImage@weixin','');
        return view('sub/index.tpl',$data);
    }
}