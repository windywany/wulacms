<?php
/**
 * Created by PhpStorm.
 * DEC :
 * User: wangwei
 * Date: 2016/12/2
 * Time: 13:02
 */

namespace taoke\classes;

class Createtbk {
    public function create($text,$url,$user_id ='',$logo =''){
	    if($text==''|| $url=='')
	    {
		    return ['status'=>1,'msg'=>'内容，url必填'];
	    }
	    $appkey = '23553049';
	    $secret = '1a28c9927814ec04b49cc3ded288306c';
	    date_default_timezone_set('Asia/Shanghai');
	    $c = new \TopClient();
	    $c->appkey = cfg('appkey@taoke',$appkey);
	    $c->secretKey = cfg('appsecret@taoke',$secret);
	    $req = new \WirelessShareTpwdCreateRequest();
	    $tpwd_param = new \IsvTpwdInfo();
	    $tpwd_param->ext="{\"xx\":\"xx\"}";
	    if($logo){
		    $tpwd_param->logo = $logo;
	    }
	    if($user_id){
		    $tpwd_param->user_id = $user_id;
	    }
	    $tpwd_param->text=$text;
	     // $tpwd_param->logo="http://m.taobao.com/xxx.jpg";
	     // $tpwd_param->url="https://uland.taobao.com/coupon/edetail?e=Xcw7VZ6RQ8UN%2BoQUE6FNzCxN8ggHxbK5PBUJ9jkpzjeMhb402UYy37cZelJt%2Bzjyt8w7NH052rwgN8BRbUWHadzNwQTGaE3k14t9QUPD0GaTz0aCh2qIR3duc0ZlTo4S5cSwEenVbklVPLPbVtDM7GzvAc1oD5Kl&pid=mm_15441137_7412041_32732711&af=1";
	    //$tpwd_param->user_id="15441137";
	    $tpwd_param->url=$url;
	    $req->setTpwdParam(json_encode($tpwd_param));
	    $resp = $c->execute($req);
	    //保存数据
	    $data['logo'] = $logo;
	    $data['url'] = $url;
	    $data['content'] = $text;
	    $data['user_id'] = $user_id;
	    $data['token'] = $resp->model;
	    $data['create_time'] = time();
	    dbinsert($data)->into('{tbk_token}')->exec();
	    return ['status'=>0,'msg'=>$resp->model];
    }
}