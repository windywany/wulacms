<?php

/**
 * kissgo framework that keep it simple and stupid, go go go ~~
 *
 * 基本的请求处理类
 * 提供从$_POST和$_GET中取数据的能力,同时可以进行XSS过滤.
 * 另外cookie也可以通过本类的实例来获取.
 *
 * @author Windywany
 * @package kissgo
 * @date 12-9-16 下午5:36
 * $Id$
 */
class Request implements ArrayAccess {
	private $userData = array ();
	private $getData = array ();
	private $postData = array ();
	protected $use_xss_clean = false;
	private static $xss_cleaner;
	private $quotes_gpc;
	private static $INSTANCE = null;
	private static $UUID = false;
	private static $SESSION_STARTED = false;
	public static $_GET = array ();
	public static $_POST = array ();
	private function __construct($xss_clean = true) {
		$this->use_xss_clean = $xss_clean;
		$this->quotes_gpc = get_magic_quotes_gpc ();
		if (Request::$xss_cleaner == null) {
			Request::$xss_cleaner = new XssCleaner ();
		}
		$this->_sanitize_globals ();
	}
	
	/**
	 * 得到request的实例.
	 *
	 * @return Request
	 */
	public static function getInstance($use_xss_clean = null) {
		if (self::$INSTANCE == null) {
			self::$INSTANCE = new Request ( $use_xss_clean );
		}
		if (is_bool ( $use_xss_clean )) {
			self::$INSTANCE->set_cleaner_enable ( $use_xss_clean );
		}
		return self::$INSTANCE;
	}
	/**
	 * 启动session.
	 *
	 * @return string session id or "" for the session did not start.
	 */
	public function startSession($sid = null) {
		if (self::$SESSION_STARTED) {
			return session_id ();
		}
		self::$SESSION_STARTED = true;
		$this->start_session ( $sid );
		return session_id ();
	}
	/**
	 * 本次请求的类型
	 *
	 * @return bool 如果是通过ajax请求的返回true,反之返回false
	 */
	public static function isAjaxRequest() {
		return isset ( $_SERVER ["HTTP_X_AJAX_TYPE"] ) || (isset ( $_SERVER ['HTTP_X_REQUESTED_WITH'] ) && strtolower ( $_SERVER ['HTTP_X_REQUESTED_WITH'] ) == 'xmlhttprequest');
	}
	public static function isGet() {
		return strtoupper ( $_SERVER ['REQUEST_METHOD'] ) == 'GET';
	}
	public static function isPost() {
		return strtoupper ( $_SERVER ['REQUEST_METHOD'] ) == 'POST';
	}
	
	/**
	 * set enable flag
	 *
	 * @param
	 *        	$enable
	 */
	public function set_cleaner_enable($enable) {
		$this->use_xss_clean = $enable;
	}
	/**
	 * 获取客户端传过来的值无论是通过GET方式还是POST方式
	 *
	 * @param string $name
	 * @param mixed $default
	 * @param boolean $xss_clean
	 *        	是否进行xss过滤
	 * @return mixed
	 */
	public function get($name, $default = '', $xss_clean = false) {
		if (! $this->use_xss_clean) {
			$ary = isset ( $this->userData [$name] ) ? $this->userData : (isset ( $this->postData [$name] ) ? $this->postData : $this->getData);
		} else if ($xss_clean) {
			$ary = isset ( $this->userData [$name] ) ? $this->userData : (isset ( $_POST [$name] ) ? $_POST : $_GET);
		} else {
			$ary = isset ( $this->userData [$name] ) ? $this->userData : (isset ( $this->postData [$name] ) ? $this->postData : $this->getData);
		}
		if (! isset ( $ary [$name] ) || (! is_numeric ( $ary [$name] ) && empty ( $ary [$name] ))) {
			if ($name == '_url') {
				if (isset ( $_SERVER ['PATH_INFO'] )) {
					$default = ltrim ( $_SERVER ['PATH_INFO'], '/' );
					if (empty ( $default )) {
						$default = '/';
					}
				} else if (isset ( $_SERVER ['REQUEST_URI'] )) {
					$fs = parse_url ( $_SERVER ['REQUEST_URI'] );
					$default = $fs ['path'];
					if (isset ( $fs ['query'] )) {
						$quries = array ();
						parse_str ( $fs ['query'], $quries );
						$this->userData += $quries;
					}
				}
				$this->userData [$name] = $default;
			}
			return $default;
		}
		return $ary [$name];
	}
	public function addUserData($data = array(), $reset = false) {
		if (is_array ( $data ) && $data) {
			if ($reset) {
				$this->userData = $data;
			} else {
				$this->userData = array_merge ( $this->userData, $data );
			}
		}
	}
	
	
	public function getUserData() {
		return $this->userData;
	}
	
	public function initRawPost() {
		$in = @fopen ( 'php://input', 'rb' );
		if ($in) {
			$tmp = [ ];
			do {
				$buff = fread ( $in, 4096 );
				if ($buff) {
					$tmp [] = $buff;
				}
			} while ( $buff );
			if ($tmp) {
				$data = @json_decode ( implode ( '', $tmp ), true );
				if ($data) {
					$data = $this->_clean_input_data ( $data );
					$this->addUserData ( $data );
				}
				unset ( $tmp, $data );
			}
			@fclose ( $in );
		}
	}
	public static function getUri() {
		if (isset ( $_SERVER ['REQUEST_URI'] )) {
			if (preg_match ( '#^(ht|f)tps?://.+#', $_SERVER ['REQUEST_URI'] )) {
				return $_SERVER ['REQUEST_URI'];
			} else {
				$url = $_SERVER ['REQUEST_URI'];
			}
		} else if (isset ( $_SERVER ['PATH_INFO'] )) {
			$url = $_SERVER ['SCRIPT_NAME'] . $_SERVER ['PATH_INFO'];
			$query_str = $_SERVER ['QUERY_STRING'];
		} else {
			$query_str = $_SERVER ['QUERY_STRING'];
			if ($_SERVER ['SCRIPT_NAME'] == '/index.php') {
				parse_str ( $query_str, $args );
				$url = $args ['_url'];
				unset ( $args ['_url'] );
				$query_str = http_build_query ( $args );
			} else {
				$url = $_SERVER ['SCRIPT_NAME'];
			}
		}
		$site_url = cfg ( 'site_url', DETECTED_ABS_URL );
		return trailingslashit ( $site_url ) . ltrim ( $url, '/' ) . (empty ( $query_str ) ? '' : '?' . $query_str);
	}
	public static function getIp(){
		if (! empty ( $_SERVER ["HTTP_CLIENT_IP"] )) {
			$cip = $_SERVER ["HTTP_CLIENT_IP"];
		} elseif (! empty ( $_SERVER ["HTTP_X_FORWARDED_FOR"] )) {
			$cip = $_SERVER ["HTTP_X_FORWARDED_FOR"];
		} elseif (! empty ( $_SERVER ["REMOTE_ADDR"] )) {
			$cip = $_SERVER ["REMOTE_ADDR"];
		} else {
			$cip = "";
		}
		return $cip;
	}
	public static function getVirtualPageUrl() {
		if (isset ( $_SERVER ['PATH_INFO'] )) {
			$url = $_SERVER ['PATH_INFO'];
		} else {
			$query_str = $_SERVER ['QUERY_STRING'];
			if ($_SERVER ['SCRIPT_NAME'] == '/index.php') {
				parse_str ( $query_str, $args );
				if (isset ( $args ['_url'] )) {
					$url = $args ['_url'];
				} else if (isset ( $_SERVER ['REQUEST_URI'] )) {
					$fs = parse_url ( $_SERVER ['REQUEST_URI'] );
					$url = $fs ['path'];
				} else {
					$url = '';
				}
			} else {
				$url = $_SERVER ['SCRIPT_NAME'];
			}
		}
		$url = '/' . ltrim ( $url, '/' );
		return $url;
	}
	/**
	 * 对值进行xss安全处理.
	 *
	 * @param $val 要进行xss处理的值
	 * @return string
	 */
	public static function xss_clean($val) {
		if (Request::$xss_cleaner == null) {
			Request::$xss_cleaner = new XssCleaner ();
		}
		$val = Request::$xss_cleaner->xss_clean ( $val );
		return $val;
	}
	public static function setUUID() {
		if (isset ( $_COOKIE ['_m_Uuid_'] )) {
			self::$UUID = $_COOKIE ['_m_Uuid_'];
			return;
		}
		self::$UUID = uniqid ();
		// 2 years = 63072000
		@setcookie ( '_m_Uuid_', self::$UUID, time () + 63072000, '/', '', false, true );
	}
	public static function getUUID() {
		return self::$UUID;
	}
	public function offsetExists($offset) {
		return isset ( $_GET [$offset] ) || isset ( $_POST [$offset] ) || isset ( $this->userData [$offset] );
	}
	public function offsetGet($offset) {
		return $this->get ( $offset );
	}
	public function offsetSet($offset, $value) {
		$this->userData [$offset] = $value;
	}
	public function offsetUnset($offset) {
		if (isset ( $this->userData [$offset] )) {
			unset ( $this->userData [$offset] );
		}
	}
	
	// 处理全局输入
	private function _sanitize_globals() {
		Request::$_GET = $_GET;
		Request::$_POST = $_POST;
		$this->getData = array_merge ( array (), $_GET );
		$this->postData = array_merge ( array (), $_POST );
		$_GET = $this->_clean_input_data ( $_GET );
		$_POST = $this->_clean_input_data ( $_POST );
		$_REQUEST = $this->_clean_input_data ( $_REQUEST );
		unset ( $_COOKIE ['$Version'] );
		unset ( $_COOKIE ['$Path'] );
		unset ( $_COOKIE ['$Domain'] );
		$_COOKIE = $this->_clean_input_data ( $_COOKIE );
	}
	
	/**
	 * Clean Input Data
	 *
	 * This is a helper function. It escapes data and
	 * standardizes newline characters to \n
	 *
	 * @access private
	 * @param
	 *        	string
	 * @return string
	 */
	private function _clean_input_data($str) {
		if (is_array ( $str )) {
			$new_array = array ();
			foreach ( $str as $key => $val ) {
				$new_array [$this->_clean_input_keys ( $key )] = $this->_clean_input_data ( $val );
			}
			return $new_array;
		}
		
		// We strip slashes if magic quotes is on to keep things consistent
		if ($this->quotes_gpc) {
			$str = stripslashes ( $str );
		}
		
		// Should we filter the input data?
		if ($this->use_xss_clean === true) {
			$str = Request::$xss_cleaner->xss_clean ( $str );
		}
		
		// Standardize newlines
		if (strpos ( $str, "\r" ) !== FALSE) {
			$str = str_replace ( array ("\r\n","\r" ), "\n", $str );
		}
		
		return $str;
	}
	
	/**
	 * Clean Keys
	 *
	 * This is a helper function. To prevent malicious users
	 * from trying to exploit keys we make sure that keys are
	 * only named with alpha-numeric text and a few other items.
	 *
	 * @access private
	 * @param
	 *        	string
	 * @return string
	 */
	private function _clean_input_keys($str) {
		if (! preg_match ( '/^[a-z0-9:_\-\/-\\\\*]+$/i', $str )) {
			log_error ( 'Disallowed Key Characters:' . $str );
			exit ( 'Disallowed Key Characters:' . $str );
		}
		return $str;
	}
	
	/**
	 * start the session
	 */
	private function start_session( $sid ) {
		$__ksg_session_handler = apply_filter ( 'get_session_handler', null );
		if ($__ksg_session_handler instanceof SessionHandlerInterface) {
			if (version_compare ( '5.4', phpversion (), '>=' )) {
				session_set_save_handler ( $__ksg_session_handler, true );
			} else {
				session_set_save_handler ( array ($__ksg_session_handler,'open' ), array ($__ksg_session_handler,'close' ), array ($__ksg_session_handler,'read' ), array ($__ksg_session_handler,'write' ), array ($__ksg_session_handler,'destroy' ), array ($__ksg_session_handler,'gc' ) );
				register_shutdown_function ( 'session_write_close' );
			}
		}
		$session_expire = abs ( icfg ( 'session_expire', 0 ) );
		$http_only = apply_filter ( 'alter_session_http_only', true );
		@session_set_cookie_params ( $session_expire, '/', '', false, $http_only );
		// @session_cache_expire ( $session_expire );
		$session_path = apply_filter ( 'get_session_path', '' );
		if (! empty ( $session_path ) && is_dir ( $session_path )) {
			@session_save_path ( $session_path );
		}
		$session_name = get_session_name ();
		if ($sid) {
            $session_id = $sid;
        } else {
            $session_id = isset ( $_REQUEST [$session_name] ) ? $_REQUEST [$session_name] : null;
            if (empty ( $session_id ) && isset ( $_REQUEST [$session_name] )) {
                $session_id = $_REQUEST [$session_name];
            }
        }
        @session_name ( $session_name );
        if (! empty ( $session_id )) {
            @session_id ( $session_id );
        }
        @session_start ();
    }
}

/**
 * XSS Clean
 *
 * Sanitizes data so that Cross Site Scripting Hacks can be
 * prevented. This function does a fair amount of work but
 * it is extremely thorough, designed to prevent even the
 * most obscure XSS attempts. Nothing is ever 100% foolproof,
 * of course, but I haven't been able to get anything passed
 * the filter.
 *
 * Note: This function should only be used to deal with data
 * upon submission. It's not something that should
 * be used for general runtime processing.
 *
 * This function was based in part on some code and ideas I
 * got from Bitflux: http://blog.bitflux.ch/wiki/XSS_Prevention
 *
 * To help develop this script I used this great list of
 * vulnerabilities along with a few other hacks I've
 * harvested from examining vulnerabilities in other programs:
 * http://ha.ckers.org/xss.html
 *
 * @access public
 */
class XssCleaner {
	private $xss_hash = '';
	/* never allowed, string replacement */
	private $never_allowed_str = array ('document.cookie' => '[removed]','document.write' => '[removed]','.parentNode' => '[removed]','.innerHTML' => '[removed]','window.location' => '[removed]','-moz-binding' => '[removed]','<!--' => '&lt;!--','-->' => '--&gt;','<![CDATA[' => '&lt;![CDATA[' );
	/* never allowed, regex replacement */
	private $never_allowed_regex = array ('javascript\s*:' => '[removed]','expression\s*(\(|&\#40;)' => '[removed]',	// CSS and IE
	'vbscript\s*:' => '[removed]',	// IE, surprise!
	'Redirect\s+302' => '[removed]' );
	
	/**
	 * XSS Clean
	 *
	 * Sanitizes data so that Cross Site Scripting Hacks can be
	 * prevented. This function does a fair amount of work but
	 * it is extremely thorough, designed to prevent even the
	 * most obscure XSS attempts. Nothing is ever 100% foolproof,
	 * of course, but I haven't been able to get anything passed
	 * the filter.
	 *
	 * Note: This function should only be used to deal with data
	 * upon submission. It's not something that should
	 * be used for general runtime processing.
	 *
	 * This function was based in part on some code and ideas I
	 * got from Bitflux: http://blog.bitflux.ch/wiki/XSS_Prevention
	 *
	 * To help develop this script I used this great list of
	 * vulnerabilities along with a few other hacks I've
	 * harvested from examining vulnerabilities in other programs:
	 * http://ha.ckers.org/xss.html
	 *
	 * @access public
	 * @param
	 *        	string
	 * @param bool $is_image
	 * @return string
	 */
	public function xss_clean($str, $is_image = FALSE) {
		if (empty ( $str ) || is_numeric ( $str )) {
			return $str;
		}
		/*
		 * Is the string an array?
		 */
		if (is_array ( $str )) {
			while ( (list ( $key ) = each ( $str )) != false ) {
				$str [$key] = $this->xss_clean ( $str [$key] );
			}
			
			return $str;
		}
		/*
		 * Remove Invisible Characters
		 */
		$str = $this->_remove_invisible_characters ( $str );
		if (strpos ( $str, '<' ) === false || strpos ( $str, '>' ) === false) {
			return $str;
		}
		/*
		 * Protect GET variables in URLs
		 */
		// 901119URL5918AMP18930PROTECT8198
		$str = preg_replace ( '|\&([a-z\_0-9]+)\=([a-z\_0-9]+)|i', $this->xss_hash () . "\\1=\\2", $str );
		
		/*
		 * Validate standard character entities Add a semicolon if missing. We do this to enable the conversion of entities to ASCII later.
		 */
		$str = preg_replace ( '#(&\#?[0-9a-z]{2,})([\x00-\x20])*;?#i', "\\1;\\2", $str );
		
		/*
		 * Validate UTF16 two byte encoding (x00) Just as above, adds a semicolon if missing.
		 */
		$str = preg_replace ( '#(&\#x?)([0-9A-F]+);?#i', "\\1\\2;", $str );
		
		/*
		 * Un-Protect GET variables in URLs
		 */
		$str = str_replace ( $this->xss_hash (), '&', $str );
		
		/*
		 * URL Decode Just in case stuff like this is submitted: <a href="http://%77%77%77%2E%67%6F%6F%67%6C%65%2E%63%6F%6D">Google</a> Note: Use rawurldecode() so it does not remove plus signs
		 */
		$str = rawurldecode ( $str );
		
		/*
		 * Convert character entities to ASCII This permits our tests below to work reliably. We only convert entities that are within tags since these are the ones that will pose security problems.
		 */
		
		$str = preg_replace_callback ( '/[a-z]+=([\'\"]).*?\\1/si', array ($this,'_convert_attribute' ), $str );
		
		$str = preg_replace_callback ( '/<\w+.*?(?=>|<|$)/si', array ($this,'_html_entity_decode_callback' ), $str );
		
		/*
		 * Remove Invisible Characters Again!
		 */
		$str = $this->_remove_invisible_characters ( $str );
		
		/*
		 * Convert all tabs to spaces This prevents strings like this: ja vascript NOTE: we deal with spaces between characters later. NOTE: preg_replace was found to be amazingly slow here on large blocks of data, so we use str_replace.
		 */
		
		if (strpos ( $str, "\t" ) !== FALSE) {
			$str = str_replace ( "\t", ' ', $str );
		}
		
		/*
		 * Capture converted string for later comparison
		 */
		$converted_string = $str;
		
		/*
		 * Not Allowed Under Any Conditions
		 */
		
		foreach ( $this->never_allowed_str as $key => $val ) {
			$str = str_replace ( $key, $val, $str );
		}
		
		foreach ( $this->never_allowed_regex as $key => $val ) {
			$str = preg_replace ( "#" . $key . "#i", $val, $str );
		}
		
		/*
		 * Makes PHP tags safe Note: XML tags are inadvertently replaced too: <?xml But it doesn't seem to pose a problem.
		 */
		if ($is_image === TRUE) {
			// Images have a tendency to have the PHP short opening and closing tags every so often
			// so we skip those and only do the long opening tags.
			$str = preg_replace ( '/<\?(php)/i', "&lt;?\\1", $str );
		}
		
		/*
		 * Compact any exploded words This corrects words like: j a v a s c r i p t These words are compacted back to their correct state.
		 */
		$words = array ('javascript','expression','vbscript','script','applet','alert','document','write','cookie','window' );
		foreach ( $words as $word ) {
			$temp = '';
			
			for($i = 0, $wordlen = strlen ( $word ); $i < $wordlen; $i ++) {
				$temp .= substr ( $word, $i, 1 ) . '\s*';
			}
			
			// We only want to do this when it is followed by a non-word character
			// That way valid stuff like "dealer to" does not become "dealerto"
			$str = preg_replace_callback ( '#(' . substr ( $temp, 0, - 3 ) . ')(\W)#is', array ($this,'_compact_exploded_words' ), $str );
		}
		
		/*
		 * Remove disallowed Javascript in links or img tags We used to do some version comparisons and use of stripos for PHP5, but it is dog slow compared to these simplified non-capturing preg_match(), especially if the pattern exists in the string
		 */
		do {
			$original = $str;
			
			if (preg_match ( "/<a/i", $str )) {
				$str = preg_replace_callback ( '#<a\s+([^>]*?)(>|$)#si', array ($this,'_js_link_removal' ), $str );
			}
			
			if (preg_match ( "/<img/i", $str )) {
				$str = preg_replace_callback ( '#<img\s+([^>]*?)(\s?/?>|$)#si', array ($this,'_js_img_removal' ), $str );
			}
			
			if (preg_match ( "/script/i", $str ) or preg_match ( "/xss/i", $str )) {
				$str = preg_replace ( '#<(/*)(script|xss)(.*?)\>#si', '[removed]', $str );
			}
		} while ( $original != $str );
		
		unset ( $original );
		
		/*
		 * Remove JavaScript Event Handlers Note: This code is a little blunt. It removes the event handler and anything up to the closing >, but it's unlikely to be a problem.
		 */
		$event_handlers = array ('[^a-z_\-]on\w*','xmlns' );
		
		if ($is_image === TRUE) {
			/*
			 * Adobe Photoshop puts XML metadata into JFIF images, including namespacing, so we have to allow this for images. -Paul
			 */
			unset ( $event_handlers [array_search ( 'xmlns', $event_handlers )] );
		}
		
		$str = preg_replace ( "#<([^><]+?)(" . implode ( '|', $event_handlers ) . ')(\s*=\s*[^><]*)([><]*)#i', "<\\1\\4", $str );
		
		/*
		 * Sanitize naughty HTML elements If a tag containing any of the words in the list below is found, the tag gets converted to entities. So this: <blink> Becomes: &lt;blink&gt;
		 */
		$naughty = 'alert|applet|audio|basefont|base|behavior|bgsound|blink|body|embed|expression|form|frameset|frame|head|html|ilayer|iframe|input|isindex|layer|link|meta|object|plaintext|style|script|textarea|title|video|xml|xss';
		$str = preg_replace_callback ( '#<(/*\s*)(' . $naughty . ')([^><]*)([><]*)#is', array ($this,'_sanitize_naughty_html' ), $str );
		
		/*
		 * Sanitize naughty scripting elements Similar to above, only instead of looking for tags it looks for PHP and JavaScript commands that are disallowed. Rather than removing the code, it simply converts the parenthesis to entities rendering the code un-executable. For example: eval('some code') Becomes: eval&#40;'some code'&#41;
		 */
		$str = preg_replace ( '#(cmd|passthru|eval|exec|expression|system|fopen|fsockopen|file|file_get_contents|readfile|unlink)(\s*)\((.*?)\)#si', "\\1\\2&#40;\\3&#41;", $str );
		
		/*
		 * Final clean up This adds a bit of extra precaution in case something got through the above filters
		 */
		foreach ( $this->never_allowed_str as $key => $val ) {
			$str = str_replace ( $key, $val, $str );
		}
		
		foreach ( $this->never_allowed_regex as $key => $val ) {
			$str = preg_replace ( "#" . $key . "#i", $val, $str );
		}
		
		/*
		 * Images are Handled in a Special Way - Essentially, we want to know that after all of the character conversion is done whether any unwanted, likely XSS, code was found. If not, we return TRUE, as the image is clean. However, if the string post-conversion does not matched the string post-removal of XSS, then it fails, as there was unwanted XSS code found and removed/changed during processing.
		 */
		
		if ($is_image === TRUE) {
			if ($str == $converted_string) {
				return TRUE;
			} else {
				return FALSE;
			}
		}
		return $str;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Random Hash for protecting URLs
	 *
	 * @access public
	 * @return string
	 */
	public function xss_hash() {
		if ($this->xss_hash == '') {
			if (phpversion () >= 4.2)
				mt_srand ();
			else
				mt_srand ( hexdec ( substr ( md5 ( microtime () ), - 8 ) ) & 0x7fffffff );
			
			$this->xss_hash = md5 ( time () + mt_rand ( 0, 1999999999 ) );
		}
		
		return $this->xss_hash;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Remove Invisible Characters
	 *
	 * This prevents sandwiching null characters
	 * between ascii characters, like Java\0script.
	 *
	 * @access public
	 * @param
	 *        	string
	 * @return string
	 */
	private function _remove_invisible_characters($str) {
		static $non_displayables = false;
		
		if (! $non_displayables) {
			// every control character except newline (dec 10), carriage return (dec 13), and horizontal tab (dec 09),
			$non_displayables = array ('/%0[0-8bcef]/',			// url encoded 00-08, 11, 12, 14, 15
			'/%1[0-9a-f]/',			// url encoded 16-31
			'/[\x00-\x08]/',			// 00-08
			'/\x0b/','/\x0c/',			// 11, 12
			'/[\x0e-\x1f]/' ); // 14-31
		}
		
		do {
			$cleaned = $str;
			$str = preg_replace ( $non_displayables, '', $str );
		} while ( $cleaned != $str );
		
		return $str;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Compact Exploded Words
	 *
	 * Callback function for xss_clean() to remove whitespace from
	 * things like j a v a s c r i p t
	 *
	 * @access public
	 * @param
	 *        	array
	 * @return string
	 */
	function _compact_exploded_words($matches) {
		return preg_replace ( '/\s+/s', '', $matches [1] ) . $matches [2];
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Sanitize Naughty HTML
	 *
	 * Callback function for xss_clean() to remove naughty HTML elements
	 *
	 * @access private
	 * @param
	 *        	array
	 * @return string
	 */
	function _sanitize_naughty_html($matches) {
		// encode opening brace
		$str = '&lt;' . $matches [1] . $matches [2] . $matches [3];
		
		// encode captured opening or closing brace to prevent recursive vectors
		$str .= str_replace ( array ('>','<' ), array ('&gt;','&lt;' ), $matches [4] );
		
		return $str;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * JS Link Removal
	 *
	 * Callback function for xss_clean() to sanitize links
	 * This limits the PCRE backtracks, making it more performance friendly
	 * and prevents PREG_BACKTRACK_LIMIT_ERROR from being triggered in
	 * PHP 5.2+ on link-heavy strings
	 *
	 * @access private
	 * @param
	 *        	array
	 * @return string
	 */
	function _js_link_removal($match) {
		$attributes = $this->_filter_attributes ( str_replace ( array ('<','>' ), '', $match [1] ) );
		return str_replace ( $match [1], preg_replace ( '#href=.*?(alert\(|alert&\#40;|javascript\:|charset\=|window\.|document\.|\.cookie|<script|<xss|base64\s*,)#si', '', $attributes ), $match [0] );
	}
	
	/**
	 * JS Image Removal
	 *
	 * Callback function for xss_clean() to sanitize image tags
	 * This limits the PCRE backtracks, making it more performance friendly
	 * and prevents PREG_BACKTRACK_LIMIT_ERROR from being triggered in
	 * PHP 5.2+ on image tag heavy strings
	 *
	 * @access private
	 * @param
	 *        	array
	 * @return string
	 */
	function _js_img_removal($match) {
		$attributes = $this->_filter_attributes ( str_replace ( array ('<','>' ), '', $match [1] ) );
		return str_replace ( $match [1], preg_replace ( '#src=.*?(alert\(|alert&\#40;|javascript\:|charset\=|window\.|document\.|\.cookie|<script|<xss|base64\s*,)#si', '', $attributes ), $match [0] );
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Attribute Conversion
	 *
	 * Used as a callback for XSS Clean
	 *
	 * @access public
	 * @param
	 *        	array
	 * @return string
	 */
	function _convert_attribute($match) {
		return str_replace ( array ('>','<','\\' ), array ('&gt;','&lt;','\\\\' ), $match [0] );
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * HTML Entity Decode Callback
	 *
	 * Used as a callback for XSS Clean
	 *
	 * @access public
	 * @param
	 *        	array
	 * @return string
	 */
	function _html_entity_decode_callback($match) {
		$charset = 'UTF-8';
		return $this->_html_entity_decode ( $match [0], strtoupper ( $charset ) );
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * HTML Entities Decode
	 *
	 * This function is a replacement for html_entity_decode()
	 *
	 * In some versions of PHP the native function does not work
	 * when UTF-8 is the specified character set, so this gives us
	 * a work-around. More info here:
	 * http://bugs.php.net/bug.php?id=25670
	 *
	 * @access private
	 * @param
	 *        	string
	 * @param
	 *        	string
	 * @return string
	 */
	/*
	 * ------------------------------------------------- /* Replacement for html_entity_decode() /* -------------------------------------------------
	 */
	
	/*
	 * NOTE: html_entity_decode() has a bug in some PHP versions when UTF-8 is the
	 * character set, and the PHP developers said they were not back porting the
	 * fix to versions other than PHP 5.x.
	 */
	private function _html_entity_decode($str, $charset = 'UTF-8') {
		if (stristr ( $str, '&' ) === FALSE)
			return $str;
			
			// The reason we are not using html_entity_decode() by itself is because
			// while it is not technically correct to leave out the semicolon
			// at the end of an entity most browsers will still interpret the entity
			// correctly. html_entity_decode() does not convert entities without
			// semicolons, so we are left with our own little solution here. Bummer.
		
		if (function_exists ( 'html_entity_decode' ) && (strtolower ( $charset ) != 'utf-8' or version_compare ( phpversion (), '5.0.0', '>=' ))) {
			$str = html_entity_decode ( $str, ENT_COMPAT, $charset );
			$str = preg_replace ( '~&#x(0*[0-9a-f]{2,5})~ei', 'chr(hexdec("\\1"))', $str );
			return preg_replace ( '~&#([0-9]{2,4})~e', 'chr(\\1)', $str );
		}
		
		// Numeric Entities
		$str = preg_replace ( '~&#x(0*[0-9a-f]{2,5});{0,1}~ei', 'chr(hexdec("\\1"))', $str );
		$str = preg_replace ( '~&#([0-9]{2,4});{0,1}~e', 'chr(\\1)', $str );
		
		// Literal Entities - Slightly slow so we do another check
		if (stristr ( $str, '&' ) === FALSE) {
			$str = strtr ( $str, array_flip ( get_html_translation_table ( HTML_ENTITIES ) ) );
		}
		
		return $str;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Filter Attributes
	 *
	 * Filters tag attributes for consistency and safety
	 *
	 * @access public
	 * @param
	 *        	string
	 * @return string
	 */
	private function _filter_attributes($str) {
		$out = '';
		
		if (preg_match_all ( '#\s*[a-z\-]+\s*=\s*(\042|\047)([^\\1]*?)\\1#is', $str, $matches )) {
			foreach ( $matches [0] as $match ) {
				$out .= preg_replace ( '#/\*.*?\*/#s', '', $match );
			}
		}
		
		return $out;
	}
}
// END OF FILE request.php