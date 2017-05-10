<?php
class ApkController extends Controller {
	protected $checkUser = true;
	protected $acls = array ('*' => 'ver:mobi' );
	public function generate($id) {
		$id = intval ( $id );
		if (empty ( $id )) {
			Response::respond ( 404 );
		}
		$return = ApkSignTool::generate ( $id );
		if ($return ['status'] === false) {
			return NuiAjaxView::error ( $return ['msg'] );
		} else {
			return NuiAjaxView::callback ( 'updateApkUrl', array ('url' => $return ['data'],'id' => $id ) );
		}
	}
	/**
	 * 删除软件包，放弃升级.
	 *
	 * @param integer $id
	 * @return NuiAjaxView
	 */
	public function delapk($id) {
		$id = intval ( $id );
		if (empty ( $id )) {
			Response::respond ( 404 );
		}
		$url = dbselect ()->from ( '{app_version_market}' )->where ( array ('id' => $id ) )->get ( 'url' );
		if ($url) {
			$url = preg_replace ( '#^(f|ht)tps?://[^/].+?/#', '', $url );
			if (is_file ( WEB_ROOT . $url )) {
				@unlink ( WEB_ROOT . $url );
			}
			dbupdate ( '{app_version_market}' )->set ( array ('url' => '' ) )->where ( array ('id' => $id ) )->exec ();
		}
		return NuiAjaxView::reload ( '#mobi-ch-table', '软件包已经删除.' );
	}
	/**
	 * 删除软件包，放弃升级.
	 *
	 * @param integer $id
	 * @return NuiAjaxView
	 */
	public function del($id) {
		$id = intval ( $id );
		if (empty ( $id )) {
			Response::respond ( 404 );
		}
		$url = dbselect ()->from ( '{app_version}' )->where ( array ('id' => $id ) )->get ( 'url' );
		if ($url) {
			$url = preg_replace ( '#^(f|ht)tps?://[^/].+?/#', '', $url );
			if (is_file ( WEB_ROOT . $url )) {
				@unlink ( WEB_ROOT . $url );
			}
			dbupdate ( '{app_version}' )->set ( array ('url' => '' ) )->where ( array ('id' => $id ) )->exec ();
		}
		return NuiAjaxView::reload ( '#mobi-ch-table', '软件包已经删除.' );
	}
	/**
	 * 生成母包
	 *
	 * @author DQ
	 *         @date 2015年12月7日 下午5:16:53
	 * @param
	 *
	 *
	 *
	 *
	 *
	 * @return
	 *
	 *
	 *
	 *
	 *
	 */
	public function resource($id) {
		$id = intval ( $id );
		if (empty ( $id )) {
			Response::respond ( 404 );
		}
		$apk = dbselect ( '*' )->from ( '{app_version}' )->where ( array ('id' => $id ) )->get ( 0 );
		if (empty ( $apk )) {
			Response::respond ( 404 );
		}
		$apk_file = WEB_ROOT . $apk ['apk_file'];
		if (! file_exists ( $apk_file )) {
			return NuiAjaxView::error ( '母包' . $apk ['apk_file'] . '未找到' );
		}
		
		$url = cfg ( 'apk_home@mobiapp', 'uploads' ) . DS . $apk ['prefix'] . '_' . $apk ['version'] . ($apk ['os'] == '2' ? '.ipa' : '.apk');
		$file = WEB_ROOT . $url;
		if ($apk ['os'] == '2') {
			$rst = ApkSignTool::repackIOS ( $apk_file, $file, array ('channel' => 'guanfang' ), $apk ['prefix'] );
		} else {
			$rst = ApkSignTool::repack ( $apk_file, $file, array ('channel' => 'guanfang' ) );
		}
		
		$host = cfg ( 'host@mobiapp' );
		if (! $host) {
			$host = untrailingslashit ( cfg ( 'site_url' ) );
		}
		$url = $host . '/' . str_replace ( '\\', '/', $url );
		if ($rst) {
			dbupdate ( '{app_version}' )->set ( array ('url' => $url ) )->where ( array ('id' => $id ) )->exec ();
			return NuiAjaxView::callback ( 'updateApkUrl', array ('url' => $url,'id' => $id ) );
		} else {
			return NuiAjaxView::error ( '无法生成渠道包文件，详细信息请查看日志.' );
		}
	}
	
	/**
	 * 渠道插件生成包
	 *
	 * @param int $id
	 */
	function channelplugin($id = 0) {
		$id = intval ( $id );
		if ($id <= 0) {
			Response::respond ( 404 );
		}
		
		$prefix = '';
		// 通过app_channel 找到pid，通过pid找到app_rpoduct 对应的 app_version
		$rsAppChannel = dbselect ( 'name,pid,aid' )->from ( '{app_channel}' )->where ( array ('id' => $id ) )->get ( 0 );
		if (! $rsAppChannel) {
			Response::showErrorMsg ( '渠道不存在！' );
		}
		$prefix = $rsAppChannel ['name'];
		// 获取生成包前缀
		if (! $rsAppChannel ['name']) {
			$rsAppAgent = dbselect ( 'channel' )->from ( '{app_channel_agent}' )->where ( array ('aid' => $rsAppChannel ['aid'] ) )->get ( 0 );
			if (! $rsAppAgent ['channel']) {
				Response::showErrorMsg ( '渠道商的渠道标识不存在！' );
			}
			$prefix = $rsAppAgent ['channel'];
		}
		
		$rsAppProduct = dbselect ( 'app_version_id' )->from ( '{app_product}' )->where ( array ('id' => $rsAppChannel ['pid'] ) )->get ( 0 );
		if (! $rsAppProduct) {
			Response::showErrorMsg ( '应用不存在！' );
		}
		
		$apk = dbselect ( '*' )->from ( '{app_version}' )->where ( array ('id' => $rsAppProduct ['app_version_id'] ) )->get ( 0 );
		if (empty ( $apk )) {
			Response::showErrorMsg ( '版本信息不存在！' );
		}
		
		$apk_file = WEB_ROOT . $apk ['apk_file'];
		if (! file_exists ( $apk_file )) {
			return NuiAjaxView::error ( '母包' . $apk ['apk_file'] . '未找到' );
		}
		$url = cfg ( 'apk_home@mobiapp', 'uploads' ) . DS . $apk ['prefix'] . '_' . $prefix . '_' . $apk ['version'] . '.apk';
		
		$file = WEB_ROOT . $url;
		$rst = ApkSignTool::repack ( $apk_file, $file, array ('channel' => 'guanfang' ) );
		$host = cfg ( 'host@mobiapp' );
		if (! $host) {
			$host = untrailingslashit ( cfg ( 'site_url' ) );
		}
		$url = $host . '/' . str_replace ( '\\', '/', $url );
		if ($rst) {
			dbupdate ( '{app_channel}' )->set ( array ('url' => $url ) )->where ( array ('id' => $id ) )->exec ();
			return NuiAjaxView::callback ( 'updateApkUrl', array ('url' => $url,'id' => $id ) );
		} else {
			return NuiAjaxView::error ( '无法生成APK文件，详细信息请查看日志.' );
		}
	}
}