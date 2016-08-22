<?php
class MSiteHooks {
	private static $SITES = false;
	private static $DOMAINS = false;
	private static $ISMOBILE = false; // 主站移动域名访问
	private static $ISMOBILE1 = false; // 栏目站移动域名访问
	/**
	 *
	 * @param AdminLayoutManager $layout
	 */
	public static function do_admin_layout($layout) {
		if (icando ( 'm:cms' ) && icando ( 'msite:cms' )) {
			$menu = $layout->getNaviMenu ( 'site' );
			$modelMenu = new AdminNaviMenu ( 'msite_menu', '站点', 'fa-globe', tourl ( 'msite', false ) );
			$menu->addItem ( $modelMenu, false, 0 );
		}
		return $layout;
	}
	/**
	 *
	 * @param AclResourceManager $manager
	 */
	public static function get_acl_resource($manager) {
		$acl = $manager->getResource ( 'cms', '网站管理' );
		$acl->addOperate ( 'msite', '多站点管理' );
		return $manager;
	}
	// 主页
	public static function on_render_homepage($data) {
		self::initSites ();
		$mobi_domain = cfg ( 'mobi_domain' );
		// 当前以设定的移动域名访问
		if ($mobi_domain && $mobi_domain == REAL_HTTP_HOST) {
			return $data;
		}
		if (isset ( self::$SITES ['domains'] [CUR_SUBDOMAIN] )) {
			$channel = self::$SITES ['domains'] [CUR_SUBDOMAIN];
			if ($channel ['default_page']) {
				$url = $channel ['list_page_url'];
			} else {
				$url = $channel ['url'];
			}
			$page = CmsPage::load ( $url, true );
			if ($page) {
				$data1 = $page->getFields ();
			} else {
				$data1 = false;
			}
		}
		if ($data1) {
			$data = array_merge ( $data, $data1 );
		}
		return $data;
	}
	public static function get_mobile_url($url, $page) {
		static $cms_domain = false;
		if ($cms_domain === false) {
			$base_url = cfg ( 'cms_url@cms', defined ( 'ENABLE_SUB_DOMAIN' ) ? DETECTED_ABS_URL : DETECTED_URL );
			$host = preg_match ( '#^https?://.+#i', $base_url ) ? preg_replace ( '#^https?://#i', '', trim ( $base_url, '/' ) ) : $_SERVER ['HTTP_HOST'];
			$domain = strstr ( $host, '.' );
			$protocol = isset ( $_SERVER ['HTTPS'] ) ? 'https://' : 'http://';
			$cms_domain [0] = $protocol;
			$cms_domain [1] = $domain . '/';
		}
		if (! isset ( $page ['root'] ) || empty ( $page ['root'] )) {
			return $url;
		}
		if (preg_match ( '#^(f|ht)tps?://.+#i', $url )) {
			return $url;
		}
		self::initSites ();
		$channel = $page ['root'];
		if (isset ( self::$SITES ['channels'] [$channel] )) {
			$cfg = self::$SITES ['channels'] [$channel];
			$domain = $cfg [2];
			if ($domain) {
				$url = $cms_domain [0] . $domain . $cms_domain [1] . ltrim ( $url, '/' );
			}
		}
		return $url;
	}
	// 每一个页面
	public static function filter_data_for_safe_url($data) {
		self::initSites ();
		if (! isset ( $data ['bind'] ) && isset ( $data ['root'] ) && ! empty ( $data ['root'] )) {
			$channel = $data ['root'];
			if (isset ( self::$SITES ['channels'] [$channel] )) {
				$cfg = self::$SITES ['channels'] [$channel];
				// 移动域名浏览且当前栏目指定了移动域名
				$data ['bind'] = self::$ISMOBILE && $cfg [2] ? $cfg [2] : $cfg [0];
				// 只有栏目可以做为网站的首页.
				if ($cfg [1] && ($data ['channel'] == $channel || (isset ( $data ['refid'] ) && $data ['refid'] == $channel))) {
					$data ['url'] = 'index.html';
					if (isset ( $data ['list_page_url'] )) {
						$data ['list_page_url'] = 'index.html';
					}
				}
				$data ['inherit'] = 0;
			}
		}
		return $data;
	}
	// 具体页面
	public static function on_render_page($page) {
		$mobi_domain = cfg ( 'mobi_domain' );
		if ($mobi_domain && $mobi_domain == REAL_HTTP_HOST) {
			return $page;
		}
		self::initSites ();
		$data = $page->getFields ();
		if ($data ['root']) {
			$channel = $data ['root'];
			if (isset ( self::$SITES ['channels'] [$channel] )) {
				$cfg = self::$SITES ['channels'] [$channel];
				if (self::$ISMOBILE1 && $cfg [2] != CUR_SUBDOMAIN) {
					return false;
				}
				if (! self::$ISMOBILE1 && $cfg [0] != CUR_SUBDOMAIN) {
					return false;
				}
			}
		}
		return $page;
	}
	public static function after_save_channel($channel) {
		RtCache::delete ( 'msite_sites' );
	}
	private static function initSites() {
		if (self::$SITES === false) {
			$mobi_domain = cfg ( 'mobi_domain' );
			if ($mobi_domain && $mobi_domain == REAL_HTTP_HOST) {
				self::$ISMOBILE = true;
			}
			if (CUR_SUBDOMAIN) {
				self::$ISMOBILE1 = bcfg ( CUR_SUBDOMAIN . '@msite_mdomain' );
				if (self::$ISMOBILE1) {
					self::$ISMOBILE = true;
				}
			}
			self::$SITES = RtCache::get ( 'msite_sites', false );
			if (! is_array ( self::$SITES )) {
				$sites = dbselect ( 'domain,mdomain,channel,topics,url,list_page_url,default_page' )->from ( '{cms_msite}' );
				$sites->join ( '{cms_channel}', 'channel = refid' );
				foreach ( $sites as $site ) {
					$domain = $site ['domain'];
					$mdomain = $site ['mdomain'];
					$channel = $site ['channel'];
					$topics = $site ['topics'];
					self::$SITES ['channels'] [$channel] = array (0 => $domain,1 => true,2 => $mdomain );
					self::$SITES ['domains'] [$domain] = $site;
					if ($mdomain) {
						self::$SITES ['domains'] [$mdomain] = $site;
					}
					if ($topics) {
						$topics = explode ( '><', trim ( $topics, '><' ) );
						foreach ( $topics as $topic ) {
							self::$SITES ['channels'] [$topic] = array (0 => $domain,1 => false,2 => $mdomain );
						}
					}
				}
				RtCache::add ( 'msite_sites', self::$SITES );
			}
		}
	}
}
