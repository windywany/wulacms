<?php
class BdpushController extends Controller {
	protected $checkUser = true;
	protected $acls = array ('*' => 'bdtkit:system/tools' );
	public function index() {
		if (! bcfg ( 'enable_bd@bdtkit' )) {
			Response::respond ( 404 );
		}
		$form = new BaiduPushForm ();
		$data ['rules'] = $form->rules ();
		$data ['formName'] = get_class ( $form );
		$data ['widgets'] = new DefaultFormRender ( $form->buildWidgets ( array () ) );
		return view ( 'push.tpl', $data );
	}
	public function index_post() {
		if (! bcfg ( 'enable_bd@bdtkit' )) {
			Response::respond ( 404 );
		}
		$remain = icfg ( 'remain@bdtkit', - 1 );
		$lt = icfg ( 'last_push_time@bdtkit', 0 );
		if ($remain == 0 && $lt > 0 && date ( 'Y-m-d' ) == date ( 'Y-m-d', $lt )) {
			return NuiAjaxView::error ( '今日配额已经用完.' );
		}
		$url = cfg ( 'bd_rest_url@bdtkit' );
		if ($url) {
			$form = new BaiduPushForm ();
			$data = $form->valid ();
			if ($data) {
				$bd = $data ['bd'];
				$sd = $data ['sd'];
				$cnt = intval ( $data ['cnt'] );
				$cnt = $cnt ? $cnt : 100;
				$where ['PG.deleted'] = 0;
				$where ['PG.hidden'] = 0;
				$where ['PG.baidu_sync'] = 0;
				if ($bd) {
					$bd = strtotime ( $bd . ' 00:00:00' );
					$where ['PG.update_time >='] = $bd;
				}
				if ($sd) {
					$sd = strtotime ( $sd . ' 23:59:59' );
					$where ['PG.update_time <='] = $sd;
				}
				$_SESSION ['bdtool_where'] = $where;
				$_SESSION ['bdtool_limit'] = $cnt;
				$total = dbselect ()->from ( '{cms_page} AS PG' )->where ( $where )->count ( 'PG.id' );
				if ($total == 0) {
					return NuiAjaxView::ok ( '没有找到数据,不需要推送.' );
				}
				return NuiAjaxView::callback ( 'startPushProgress', array ('start' => 0,'total' => $total ) );
			} else {
				return NuiAjaxView::validate ( get_class ( $form ), '', $form->getErrors () );
			}
		} else {
			return NuiAjaxView::error ( '请先设置百度推送接口.' );
		}
	}
	public function push($start, $total, $id = '') {
		if (! bcfg ( 'enable_bd@bdtkit' )) {
			Response::respond ( 404 );
		}
		set_time_limit ( 0 );
		$data ['success'] = true;
		$data ['total'] = $total;
		$limit = sess_get ( 'bdtool_limit', 0 );
		if (! $limit) {
			$limit = 50;
		}
		$remain = icfg ( 'remain@bdtkit', - 1 );
		$lt = icfg ( 'last_push_time@bdtkit', 0 );
		if ($remain == 0 && $lt > 0 && date ( 'Y-m-d' ) == date ( 'Y-m-d', $lt )) {
			if ($id) {
				return NuiAjaxView::error ( '今日配额已经用完' );
			} else {
				$data ['success'] = false;
				$data ['message'] = '今日配额已经用完';
				return new JsonView ( $data );
			}
		}
		$where = sess_get ( 'bdtool_where', array () );
		if ($id) {
			$where ['PG.id IN'] = safe_ids2 ( $id );
			$where ['PG.baidu_sync'] = 0;
		}
		if (empty ( $where ) && ! $id) {
			$data ['success'] = false;
			$data ['msg'] = '推送条件丢失，请重新推送.';
		} else if ($start < $total) {
			$pages = dbselect ( 'PG.id,CH.root,PG.url' )->from ( '{cms_page} AS PG' )->join ( '{cms_channel} AS CH', 'PG.channel=CH.refid' )->where ( $where );
			$pages->limit ( $start, $limit )->desc ( 'PG.update_time' );
			$urls = array ();
			$ids = array ();
			foreach ( $pages as $p ) {
				$urls [] = safe_url ( $p );
				$ids [] = $p ['id'];
			}
			if ($urls) {
				$url = cfg ( 'bd_rest_url@bdtkit' );
				$client = CurlClient::getClient ( 60, array ('Content-Type: text/plain' ) );
				$rst = $client->post ( $url, implode ( "\n", $urls ) );
				$client->close ();
				if ($rst) {
					$rstx = @json_decode ( $rst, true );
					if ($rstx) {
						if (isset ( $rstx ['success'] ) && $rstx ['success'] > 0) {
							dbupdate ( '{cms_page}' )->set ( array ('baidu_sync' => 1 ) )->where ( array ('id IN' => $ids ) )->exec ();
						}
						if (isset ( $rstx ['message'] )) {
							$data ['success'] = false;
							$data ['msg'] = $rstx ['message'];
						} else {
							$msg = '本次推送' . count ( $urls ) . '条，成功' . $rstx ['success'] . '条，剩余配额' . $rstx ['remain'] . '条';
							if (isset ( $rstx ['not_valid'] )) {
								$msg .= ',非法URL：' . count ( $rstx ['not_valid'] );
							}
							if (isset ( $rstx ['not_same_site'] )) {
								$msg .= ',不是本站URL：' . count ( $rstx ['not_same_site'] );
							}
							ActivityLog::info ( $msg, 'Bdkit' );
							set_cfg ( 'remain', $rstx ['remain'], 'bdtkit' );
							set_cfg ( 'last_push_time', time (), 'bdtkit' );
							if ($rstx ['remain'] == 0) {
								$data ['success'] = false;
								$data ['msg'] = '今日配额已经用完';
							} else {
								$data ['start'] = $start + $limit;
							}
						}
					} else {
						$data ['success'] = false;
						$data ['msg'] = $rst;
					}
				} else {
					$data ['success'] = false;
					$data ['msg'] = $client->error;
				}
			} else {
				$data ['start'] = $total;
			}
		} else {
			$data ['start'] = $total;
		}
		if ($id) {
			if ($data ['success']) {
				return NuiAjaxView::ok ( '推送成功' );
			} else {
				return NuiAjaxView::error ( '推送失败：' . $data ['msg'] );
			}
		} else {
			return new JsonView ( $data );
		}
	}
}
