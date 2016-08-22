<?php
/**
 * 插件管理.
 *
 * @author Guangfeng
 */
class PluginController extends Controller {
	protected $checkUser = true;
	protected $acls = array ('installed' => 'r:plugin','uninstalled' => 'r:plugin','mapping_post' => 'u:plugin','upgrade' => 'u:plugin','install' => 'i:plugin','enable' => 'd:plugin','disable' => 'd:plugin','uninstall' => 'ui:plugin' );
	
	/**
	 * 已安装列表.
	 *
	 * @return SmartyView
	 */
	public function installed($status = 'installed') {
		$data ['installed'] = '1';
		$data ['status'] = $status;
		return view ( 'plugin/index.tpl', $data );
	}
	/**
	 * 未安装列表.
	 *
	 * @return SmartyView
	 */
	public function uninstalled() {
		$data ['installed'] = '0';
		$data ['status'] = 'uninstalled';
		return view ( 'plugin/index.tpl', $data );
	}
	public function mapping($app) {
		$appInstaller = AppInstaller::getAppInstaller ( $app );
		if (! $appInstaller) {
			return Response::showErrorMsg ( '未知应用:' . $app, 404 );
		} else {
			$data ['urlmapping'] = $appInstaller->getUrlMap ();
			$data ['app'] = $app;
			return view ( 'plugin/map.tpl', $data );
		}
	}
	public function mapping_post($app) {
		$appInstaller = AppInstaller::getAppInstaller ( $app );
		if (! $appInstaller) {
			return Response::showErrorMsg ( '未知应用:' . $app, 404 );
		}
		$urlmap = trim ( rqst ( 'urlmapping' ) );
		if (empty ( $urlmap )) {
			return Response::showErrorMsg ( 'URL不能为空,请填写URL.', 403 );
		}
		$urlmap = strtolower ( $urlmap );
		$urlmapKey = md5 ( $urlmap . '/index.html' );
		if (dbselect ()->from ( '{cms_page}' )->where ( array ('deleted' => 0,'url_Key' => $urlmapKey ) )->exist ( 'id' )) {
			return Response::showErrorMsg ( 'URL已经被文章页面占用,请填写重新填写URL.', 403 );
		}
		if (dbselect ()->from ( '{apps}' )->where ( array ('app !=' => $app,'urlmapping' => $urlmap ) )->exist ( 'app' )) {
			return Response::showErrorMsg ( 'URL已经被其它模块占用,请填写重新填写URL.', 403 );
		}
		if (dbupdate ( '{apps}' )->set ( array ('urlmapping' => $urlmap ) )->where ( array ('app' => $app ) )->exec ()) {
			RtCache::delete ( 'apps_installation' );
			if ($app == 'dashboard' || $app == 'system') {
				return NuiAjaxView::redirect ( 'URL修改成功.', tourl ( 'dashboard' ) . '#' . tourl ( 'system/plugin/installed', false ) );
			} else {
				return NuiAjaxView::refresh ( 'URL修改成功.' );
			}
		} else {
			return Response::showErrorMsg ( '数据库错误,请报BUG,谢谢.', 403 );
		}
	}
	/**
	 * 升级应用.
	 *
	 * @param string $app        	
	 * @param string $toVersion        	
	 * @return NuiAjaxView
	 */
	public function upgrade($app, $toVersion) {
		set_time_limit ( 0 );
		$appInstaller = AppInstaller::getAppInstaller ( $app );
		if (! $appInstaller) {
			return NuiAjaxView::error ( '未知应用.' );
		} else {
			$installedVer = $appInstaller->getInstalledVersion ();
			if ($installedVer && $appInstaller->upgrade ( $installedVer, $toVersion, DatabaseDialect::getDialect () )) {
				ActivityLog::info ( __ ( 'Update "%s" from %s to %s successfully!', $appInstaller->getName (), $installedVer, $toVersion ), 'Update Plugin' );
				return NuiAjaxView::refresh ( '升级成功.' );
			} else {
				ActivityLog::error ( __ ( 'Update "%s" from %s to %s failed!', $appInstaller->getName (), $installedVer, $toVersion ), 'Update Plugin' );
				return NuiAjaxView::error ( $appInstaller->getLastError () );
			}
		}
	}
	/**
	 * 安装应用
	 *
	 * @param string $app        	
	 * @return NuiAjaxView
	 */
	public function install($app) {
		global $__kissgo_exports;
		set_time_limit ( 0 );
		$appInstaller = AppInstaller::getAppInstaller ( $app );
		if (! $appInstaller) {
			return NuiAjaxView::error ( '未知应用.' );
		} else {
			$dialect = DatabaseDialect::getDialect ();
			$clz_dir = MODULES_PATH . $app . DS . 'classes';
			if (is_dir ( $clz_dir )) {
				$__kissgo_exports [] = $clz_dir;
			}
			// 安装操作
			$sqls = $appInstaller->getSqls ( $dialect->getDriverName (), $appInstaller->getCurrentVersion () );
			$rst = AppInstaller::execSql ( $dialect, $sqls );
			if ($rst === true) {
				$dialect->beginTransaction ();
				$appdata = array ('user_id' => $this->user->getUid (),'update_time' => time (),'app' => $app,'status' => 1,'urlmapping' => $app,'version' => $appInstaller->getCurrentVersion () );
				$sql = dbinsert ( $appdata )->into ( '{apps}' )->setDialect ( $dialect );
				$rst = $sql->exec ();
				if ($rst) {
					$rst = $appInstaller->onInstall ( $dialect );
				}
			} else {
				$appInstaller->setLastError ( $rst );
				$rst = false;
			}
			if (! $rst) {
				try {
					$rst = $dialect->rollBack ();
				} catch ( PDOException $e ) {
				}
				$appInstaller->uninstall ();
				ActivityLog::error ( __ ( 'Install "%s" failed!', $appInstaller->getName () ), 'Install Plugin' );
				return NuiAjaxView::error ( $appInstaller->getLastError () );
			} else {
				$dialect->commit ();
				AppInstaller::clearAppCache ();
				ActivityLog::info ( __ ( 'Install "%s" successfully!', $appInstaller->getName () ), 'Install Plugin' );
				return NuiAjaxView::refresh ( '安装完成.' );
			}
		}
	}
	/**
	 * 启用应用.
	 *
	 * @param string $app        	
	 * @return NuiAjaxView
	 */
	public function enable($app) {
		$appInstaller = AppInstaller::getAppInstaller ( $app );
		if (! $appInstaller) {
			return NuiAjaxView::error ( '未知应用.' );
		} else {
			$data ['user_id'] = $this->user->getUid ();
			$data ['update_time'] = time ();
			$data ['status'] = 1;
			if (dbupdate ( '{apps}' )->set ( $data )->where ( array ('app' => $app ) )->exec ()) {
				AppInstaller::clearAppCache ();
				ActivityLog::info ( __ ( 'Enable "%s" successfully!', $appInstaller->getName () ), 'Enable Plugin' );
				return NuiAjaxView::refresh ( '应用已启用.' );
			} else {
				ActivityLog::error ( __ ( 'Enable "%s" failed!', $appInstaller->getName () ), 'Enable Plugin' );
				return NuiAjaxView::error ( '启用应用出错了，数据库操作失败.' );
			}
		}
	}
	/**
	 * 停用应用.
	 *
	 * @param string $app        	
	 * @return NuiAjaxView
	 */
	public function disable($app) {
		$appInstaller = AppInstaller::getAppInstaller ( $app );
		if (! $appInstaller) {
			return NuiAjaxView::error ( '未知应用.' );
		} else {
			$data ['user_id'] = $this->user->getUid ();
			$data ['update_time'] = time ();
			$data ['status'] = 0;
			if (dbupdate ( '{apps}' )->set ( $data )->where ( array ('app' => $app ) )->exec ()) {
				AppInstaller::clearAppCache ();
				ActivityLog::info ( __ ( 'Disable "%s" successfully!', $appInstaller->getName () ), 'Disable Plugin' );
				return NuiAjaxView::refresh ( '应用已停用.' );
			} else {
				ActivityLog::error ( __ ( 'Disable "%s" failed!', $appInstaller->getName () ), 'Disable Plugin' );
				return NuiAjaxView::error ( '停用应用出错了，数据库操作失败.' );
			}
		}
	}
	public function uninstall($app) {
		set_time_limit ( 0 );
		$appInstaller = AppInstaller::getAppInstaller ( $app );
		if (! $appInstaller) {
			return NuiAjaxView::error ( '未知应用.' );
		} else {
			if ($appInstaller->uninstall ()) {
				if (dbdelete ()->from ( '{apps}' )->where ( array ('app' => $app ) )->exec ()) {
					AppInstaller::clearAppCache ();
					ActivityLog::info ( __ ( 'Uninstall "%s" successfully!', $appInstaller->getName () ), 'Uninstall Plugin' );
					return NuiAjaxView::refresh ( '应用已卸载.' );
				}
			}
		}
		ActivityLog::error ( __ ( 'Uninstall "%s" failed!', $appInstaller->getName () ), 'Uninstall Plugin' );
		return NuiAjaxView::error ( '卸载应用出错了，数据库操作失败.' );
	}
	public function data($installed = '1', $status = 'installed', $_cp = 1, $_lt = 10) {
		$data ['installed'] = $installed === '1' ? true : false;
		$apps = AppInstaller::getApps ( $data ['installed'], $status );
		$plname = rqst ( 'plname' );
		if ($plname) {
			$apps = array_filter ( $apps, function ($item) use ($plname) {
				if (strpos ( $item ['appname'], $plname ) !== false || strpos ( $item ['name'], $plname ) !== false) {
					return true;
				}
				return false;
			} );
		}
		$total = count ( $apps );
		$data ['total'] = $total;
		if ($total > 0) {
			$apps = array_slice ( $apps, ($_cp - 1) * $_lt, $_lt );
			$data ['apps'] = $apps;
		} else {
			$data ['apps'] = array ();
		}
		return view ( 'plugin/data.tpl', $data );
	}
}