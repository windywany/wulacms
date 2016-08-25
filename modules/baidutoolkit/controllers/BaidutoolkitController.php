<?php
class BaidutoolkitController extends NonSessionController {
	public function index() {
		if (! bcfg ( 'enable_bd@bdtkit' )) {
			Response::respond ( 404 );
		}
		if (! bcfg ( 'enable_mt@bdtkit' )) {
			Response::respond ( 404 );
		}
		$data ['pcRootURL'] [] = trailingslashit ( cfg ( 'cms_url@cms', DETECTED_URL ) );
		$data ['mDomain'] = 'http://' . cfg ( 'mobi_domain' ) . '/${1}';
		if (defined ( 'ENABLE_SUB_DOMAIN' )) {
			$base_url = cfg ( 'cms_url@cms', defined ( 'ENABLE_SUB_DOMAIN' ) ? DETECTED_ABS_URL : DETECTED_URL );
			$host = preg_match ( '#^https?://.+#i', $base_url ) ? preg_replace ( '#^https?://#i', '', trim ( $base_url, '/' ) ) : $_SERVER ['HTTP_HOST'];
			$domain = strstr ( $host, '.' );
			$sites = dbselect ( 'domain' )->from ( '{cms_msite}' );
			foreach ( $sites as $s ) {
				$data ['pcRootURL'] [] = 'http://' . $s ['domain'] . $domain . '/';
			}
		}
		return view ( 'pc2m.tpl', $data, array ('Content-Type' => 'text/xml' ) );
	}
}
