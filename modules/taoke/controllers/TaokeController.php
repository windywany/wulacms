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

	public function data($_cp = 1, $_lt = 20, $_sf = 'CP.id', $_od = 'd', $_ct = 0) {
		$rows = dbselect ( 'CP.id,CP.flag_h,CP.flag_c,CP.flag_a,CP.flag_b,CP.flag_j,CP.title,CP.title2,CP.status,CP.update_time,CP.create_time,CP.image,
				CP.publish_time,CP.keywords,CP.url,CH.root,CH.name as channelName,CM.name AS modelName,CU.nickname as cuname' )->from ( '{cms_page} AS CP' );
		$rows->field ( 'UU.nickname AS uuname' );
		$rows->join ( '{cms_channel} AS CH', 'CP.channel = CH.refid' );
		$rows->join ( '{cms_model} AS CM', 'CP.model = CM.refid' );
		$rows->join ( '{user} AS CU', 'CP.create_uid = CU.user_id' );
		$rows->join ( '{user} AS UU', 'CP.update_uid = UU.user_id' );
		$cnt = dbselect ( imv ( 'COUNT(AI.id)' ) )->from ( '{album_item} AS AI' )->where ( array ('AI.album_id' => imv ( 'CP.id' ),'AI.deleted' => 0 ) );
		$rows->field ( $cnt, 'album_cnt' );
		$where ['CP.deleted'] = 0;
		$where ['CP.hidden'] = 0;
		$pid = irqst ( 'pid' );
		if ($pid) {
			$where ['CP.id'] = $pid;
		} else {
			$where ['CP.model'] = 'album';
			$channel = rqst ( 'channel' );
			if ($channel) {
				$where ['CP.channel'] = $channel;
			}
			$keywords = rqst ( 'keywords' );
			if ($keywords) {
				$t = '%' . $keywords . '%';
				$keywords = convert_search_keywords ( $keywords );
				$where [] = array ('search_index MATCH' => $keywords,'||CP.title LIKE' => $t,'||CP.title2 LIKE' => $t );
			}
		}
		$rows->where ( $where );
		$rows->sort ( $_sf, $_od );
		$rows->limit ( ($_cp - 1) * $_lt, $_lt );
		$data = array ();
		$data ['total'] = '';
		if ($_ct) {
			$data ['total'] = $rows->count ( 'CP.id' );
		}
		$data ['rows'] = $rows;
		$data ['canDelPage'] = icando ( 'd:cms/page' );
		$data ['canEditPage'] = icando ( 'u:cms/page' );
		$data ['canEditTag'] = icando ( 'u:cms/tag' );
		$data ['cCache'] = icando ( 'cmc:system' ) && bcfg ( 'enabled@mem' );
		$data ['disable_approving'] = bcfg ( 'disable_approving@cms', false );
		$data ['enableCopy'] = bcfg ( 'enable_copy@cms' );
		$tpl = 'album_data.tpl';
		return view ( $tpl, $data );
	}
}