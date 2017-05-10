<?php
class CdnController extends Controller {
	protected $checkUser = true;
	protected $acls = array ('*' => 'ver:mobi' );
	
	 /**
	    * 应用市场对应的cdn刷新
	    * @author DQ
	    * @date 2015年12月11日 下午2:19:51
	    * @param
	    * @return 
	    * 
	    */
	public function generate($id = 0,$market = 0) {
		$id = intval ( $id );
		if (empty ( $id )) {
			Response::respond ( 404 );
		}
		if($market == 0){
		    $rs = dbselect('url')->from('{app_version_market}')->where(array('id'=>$id))->get();
		}
		if($market == 1){
		    $rs = dbselect('url')->from('{app_version}')->where(array('id'=>$id))->get();
		}
		
		if(!$rs['url']){
		    return NuiAjaxView::error ('url 不存在');
		}
		if(cfg('working@mobiapp') != 1){
		    return NuiAjaxView::error ('upYun 配置未开启');
		}
		$lib = new UpYunCdn();
		$return = $lib->purge($rs['url']);
		if($return['status'] == true){
		    return NuiAjaxView::ok ($return['msg']);
		}else{
		    return NuiAjaxView::error ($return['msg']);
		}
	}
}