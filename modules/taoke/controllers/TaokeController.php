<?php
/**
 * 相册控制器.
 * @author Leo Ning.
 *
 */
class TaokeController extends Controller {
	protected $acls = array ('*' => 'r:cms/page','upload' => 'u:cms/page','upload_post' => 'u:cms/page','save' => 'u:cms/page','set_hot' => 'u:cms/page','edit' => 'u:cms/page','del' => 'd:cms/page' );
	protected $checkUser = true;

	public function index() {
		$data = array ();
		$data ['canDelPage'] = icando ( 'd:cms/page' );
		$data ['canAddPage'] = icando ( 'c:cms/page' );
		$data ['channels'] = ChannelForm::getChannelTree ( 'taoke', false, true );
		return view ( 'taoke.tpl', $data );
	}

	public function data($_cp = 1, $_lt = 20, $_sf = 'cp.id', $_od = 'd', $_ct = 0) {
		$data   = [];
		$where  = [];
		$name   = trim(rqst('name', ''));
		$id     = trim(rqst('pid', ''));

		if ($name != '') {
			$where ['tbk.shopname LIKE'] = '%' . $name . '%';
		}
		if ($id != '') {
			$where ['tbk.page_id'] = $id;
		}
        $where['cp.deleted'] = 0;
		$where['cp.model']  ='taoke';
		$row =dbselect('cp.id as cid,cp.image as image,tbk.*')->from('{cms_page} as cp')->join('{tbk_goods} as tbk','cp.id=tbk.page_id')->where($where);

		$data ['results'] = $row->limit(($_cp - 1) * $_lt, $_lt)->sort($_sf, $_od)->toArray();
		$data ['total']   = count($data['results']);
		//var_dump($data);exit();
		return view('data.tpl',$data);
	}
}
