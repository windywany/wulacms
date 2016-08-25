<?php
class BaidutoolkitHookImpl {
	/**
	 *
	 * @param AdminLayoutManager $layout
	 */
	public static function do_admin_layout($layout) {
		// 网站菜单
		if (bcfg ( 'enable_bd@bdtkit' ) && icando ( 'm:system' )) {
			$sysMenu = $layout->getNaviMenu ( 'system' );
			$toolsMenu = $sysMenu->getMenu ( 'tools', '工具箱', 'fa-wrench' );
			$toolsMenu->addSubmenu ( array ('baidutoolkit','百度站长工具','fa-wrench',tourl ( 'baidutoolkit/bdpush', false ) ), 'bdtkit:system', 2 );
			$sysMenu->addItem ( $toolsMenu, 'm:system', 3 );
		}
	}
	public static function get_acl_resource($manager) {
		$acl = $manager->getResource ( 'system/tools', '工具箱' );
		$acl->addOperate ( 'm', '工具箱', '', true );
		$acl->addOperate ( 'bdtkit', '百度站长工具' );
		return $manager;
	}
	public static function get_cms_preference_groups($groups) {
		if (icando ( 'bdtkit:system/tools' )) {
			$groups ['bdtkit'] = array ('name' => '百度工具','form' => 'BaiduKitPreferenceForm','group' => 'bdtkit','icon' => 'fa-wrench' );
		}
		return $groups;
	}
	public static function get_activity_log_type($types) {
		$types ['Bdkit'] = '百度工具';
		return $types;
	}
	public static function on_init_pages_toolbar($toolbar) {
		if (icando ( 'bdtkit:system' ) && bcfg ( 'enable_bd@bdtkit' )) {
			$url = tourl ( 'baidutoolkit/bdpush/push/0/100' );
			$toolbar .= '<button type="button" title="将页面推送给百度"
					class="btn btn-primary"
					data-url="' . $url . '"
					target="ajax"
					data-grp="#page-table tbody input.grp:checked"
					data-arg="id"
					data-warn="请选择要推送的页面!"
					data-confirm="你真的要推送选中的页面吗?"
					><i class="fa fa-fw fa-share-square"></i> 推送
			</button> ';
		}
		return $toolbar;
	}
	public static function get_extra_saved_actions($html, $page) {
		if (bcfg ( 'enable_bd@bdtkit' ) && icfg ( 'push_interval@bdtkit', 0 ) == - 1) {
			// 启用审核功能
			if (bcfg ( 'disable_approving@cms', false )) {
				// 可访问待审核页面或页面状态不是2
				if ((bcfg ( 'view_in_time@cms' ) && ! in_array ( $page ['status'], array (2,4 ) )) || $page ['status'] != 2) {
					return $html;
				}
			}
			$urls [] = safe_url ( $page );
			$ids [] = $page ['id'];
			$url = cfg ( 'bd_rest_url@bdtkit' );
			self::push ( $url, $urls, $ids, false );
		}
		return $html;
	}
	public static function crontab() {
		// 向百度推送新文章
		if (! bcfg ( 'enable_bd@bdtkit' )) {
			return;
		}
		$push_interval = icfg ( 'push_interval@bdtkit', 0 ) * 3600;
		if ($push_interval <= 0) {
			return;
		}
		$url = cfg ( 'bd_rest_url@bdtkit' );
		$remain = icfg ( 'remain@bdtkit', - 1 );
		$last_push_time = icfg ( 'last_push_time@bdtkit', 0 );
		if ($remain == 0 && $last_push_time > 0 && date ( 'Y-m-d' ) == date ( 'Y-m-d', $last_push_time )) {
			return;
		}
		if (bcfg ( 'enable_bd@bdtkit' ) && cfg ( 'cms_url@cms' ) && $url) {
			$ctime = time ();
			if (($ctime - $last_push_time) > $push_interval) {
				set_cfg ( 'last_push_time', $ctime, 'bdtkit' );
				$where ['PG.deleted'] = 0;
				$where ['PG.hidden'] = 0;
				$where ['PG.baidu_sync'] = 0;
				$total = dbselect ()->from ( '{cms_page} AS PG' )->where ( $where )->count ( 'PG.id' );
				$limit = 30;
				$start = 0;
				if ($total > 0) {
					while ( $start < $total ) {
						$pages = dbselect ( 'PG.id,CH.root,PG.url' )->from ( '{cms_page} AS PG' )->join ( '{cms_channel} AS CH', 'PG.channel=CH.refid' )->where ( $where );
						$pages->limit ( $start, $limit )->desc ( 'PG.update_time' );
						$urls = array ();
						$ids = array ();
						foreach ( $pages as $p ) {
							$urls [] = safe_url ( $p );
							$ids [] = $p ['id'];
						}
						if ($urls) {
							if (! self::push ( $url, $urls, $ids, true )) {
								break;
							}
							$start += $limit;
						} else {
							break;
						}
					}
				}
			}
		}
	}
	private static function push($url, $urls, $ids, $update = true) {
		if (! $urls) {
			return false;
		}
		$client = CurlClient::getClient ( 60, array ('Content-Type: text/plain' ) );
		$rst = $client->post ( $url, implode ( "\n", $urls ) );
		$client->close ();
		if ($rst) {
			$rstx = @json_decode ( $rst, true );
			if ($rstx) {
				dbupdate ( '{cms_page}' )->set ( array ('baidu_sync' => 1 ) )->where ( array ('id IN' => $ids ) )->exec ();
				if (isset ( $rstx ['message'] )) {
					ActivityLog::warn ( $rstx ['message'], 'Bdkit' );
				} else {
					$msg = '本次推送' . count ( $urls ) . '条，成功' . $rstx ['success'] . '条，剩余配额' . $rstx ['remain'] . '条';
					if (isset ( $rstx ['not_valid'] )) {
						$msg .= ',非法URL：' . count ( $rstx ['not_valid'] );
					}
					if (isset ( $rstx ['not_same_site'] )) {
						$msg .= ',不是本站URL：' . count ( $rstx ['not_same_site'] );
					}
					ActivityLog::info ( $msg, 'Bdkit' );
					if ($rstx ['remain'] == 0) {
						// 配额用完
						return false;
					}
					if ($update) {
						set_cfg ( 'remain', $rstx ['remain'], 'bdtkit' );
					}
				}
			} else {
				ActivityLog::info ( $client->error, 'Bdkit' );
			}
		}else{
			ActivityLog::info ( $client->error, 'Bdkit' );
		}
		return true;
	}
}