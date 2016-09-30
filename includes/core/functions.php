<?php
/*
 * kissgo framework that keep it simple and stupid, go go go ~~ @author Leo Ning @package kissgo.libs $Id$
 */
defined('KISSGO') or exit ('No direct script access allowed');
/**
 * output cache header.
 *
 * @param string $last_modify
 * @param number $expire
 */
function out_cache_header($last_modify = null, $expire = 7200, $etag = null) {
	$last_modify = $last_modify == null ? time() : $last_modify;
	@header('Pragma: cache', true);
	@header('Cache-Control: max-age=' . $expire, true);
	@header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $last_modify) . ' GMT', true);
	@header('Expires: ' . gmdate('D, d M Y H:i:s', $last_modify + $expire) . ' GMT', true);
	if ($etag) {
		@header('Etag: ' . $etag);
	}
}

/**
 * use mapping to resolve the app url.
 *
 * @param string $url
 *
 * @return string
 */
function tourl($url, $base = true, $root = true) {
	return Router::url($url, $base, $root);
}

/**
 * 加载模块文章.
 *
 * @param string $module
 * @param mixed  $files
 * @param bool   $return
 */
function ksg_include($module, $files, $return = false) {
	global $__ksg_module_path;
	if (isset ($__ksg_module_path [ $module ])) {
		$dir = $__ksg_module_path [ $module ];
		if (!$return && is_array($files)) {
			foreach ($files as $f) {
				include_once $dir . $f;
			}
		} else if (!$return && is_string($files)) {
			include_once $dir . $files;
		} else if ($return && is_string($files)) {
			return $dir . $files;
		}
	}

	return './' . $files;
}

/**
 * 自动类加载器.
 *
 * 根据类名和已经注册到系统的类路径{@link $__kissgo_exports}动态加载类.
 *
 * @global array 系统类路径.
 *
 * @param  $clz  string
 *               类名.
 */
function _kissgo_class_loader($clz) {
	global $__kissgo_exports;
	$key      = 'class:' . $clz;
	$clz_file = RtCache::get($key, true);
	if ($clz_file) {
		include $clz_file;

		return;
	}
	if (strpos($clz, '\\') > 0) {
		// support namespace.
		$clzn     = str_replace('\\', DS, $clz) . '.php';
		$clz_file = EXTENSIONS_PATH . $clzn;
		if (is_file($clz_file)) {
			RtCache::add($key, $clz_file, true);
			include $clz_file;

			return;
		}
		$clz_file = MODULES_PATH . $clzn;
		if (is_file($clz_file)) {
			RtCache::add($key, $clz_file, true);
			include $clz_file;

			return;
		}
		$clz_file = INCLUDES . $clzn;
		if (is_file($clz_file)) {
			RtCache::add($key, $clz_file, true);
			include $clz_file;

			return;
		}
	}
	foreach ($__kissgo_exports as $path) {
		$clz_file = $path . DS . $clz . '.php';
		if (is_file($clz_file)) {
			RtCache::add($key, $clz_file, true);
			include $clz_file;

			return;
		}
	}
	$clz_file = apply_filter('auto_load_class', '', $clz);
	if ($clz_file && is_file($clz_file)) {
		RtCache::add($key, $clz_file, true);
		include $clz_file;
	}
}

/**
 *
 * @param string $name
 *            preference group and name
 * @param mixed  $default
 */
function cfg($name, $default = '', $reset = false) {
	global $_kissgo_processing_installation, $__ksg_rtk_hooks;
	static $cfgs = false;
	if ($cfgs === false || $reset) {
		$cfgs = RtCache::get('system_preferences');
	}
	if (empty ($cfgs)) {
		$cfgs = array();
		if (!$_kissgo_processing_installation) {
			$cpt       = dbselect('*')->from('{preferences}');
			$cfields   = dbselect('value,preference_group as pg')->from('{preferences}')->where(array('name' => 'custom_fields'));
			$cfieldsds = array();
			foreach ($cfields as $fs) {
				if ($fs ['value']) {
					$ds = @unserialize($fs ['value']);
					if ($ds) {
						$cfieldsds [ $fs ['pg'] ] = $ds;
					}
				}
			}
			foreach ($cpt as $p) {
				if (isset ($cfieldsds [ $p ['preference_group'] ] [ $p ['name'] ])) {
					$hook = 'parse_' . $cfieldsds [ $p ['preference_group'] ] [ $p ['name'] ] ['type'] . '_field_value';
					if (isset ($__ksg_rtk_hooks [ $hook ])) {
						$p ['value'] = apply_filter($hook, $p ['value']);
					}
				}
				$key           = $p ['name'] . '@' . $p ['preference_group'];
				$cfgs [ $key ] = $p ['value'];
			}
		}
		$settings = KissGoSetting::getSetting();
		$data     = $settings->toArray();
		foreach ($data as $key => $value) {
			$cfgs [ $key . '@core' ] = $value;
		}
		RtCache::add('system_preferences', $cfgs);
	}

	if (is_null($name)) {
		return $cfgs;
	}

	if (empty ($name)) {
		return $default;
	}
	if (strpos($name, '@', 1) === false) {
		$name .= '@core';
	}
	if (isset ($cfgs [ $name ]) && (is_numeric($cfgs [ $name ]) || !empty ($cfgs [ $name ]))) {
		$default = $cfgs [ $name ];
	}

	return $default;
}

function set_cfg($name, $value, $group) {
	global $_kissgo_processing_installation;
	$data ['name']             = $name;
	$data ['preference_group'] = $group;
	if (!$_kissgo_processing_installation) {
		if (is_null($value)) {
			dbdelete()->from('{preferences}')->where($data)->exec();
		} else {
			if (dbselect()->from('{preferences}')->where($data)->exist('preference_id')) {
				$cfg ['value']       = $value;
				$cfg ['update_time'] = time();
				dbupdate('{preferences}')->set($cfg)->where($data)->exec();
			} else {
				$data ['name']             = $name;
				$data ['preference_group'] = $group;
				$data ['value']            = $value;
				$data ['user_id']          = 0;
				$data ['update_time']      = time();
				dbinsert($data)->into('{preferences}')->exec();
			}
		}
	}
	$cfgs = RtCache::get('system_preferences', array());
	$key  = $name . '@' . $group;
	if (is_null($value)) {
		unset ($cfgs [ $key ]);
	} else {
		$cfgs [ $key ] = $value;
	}
	RtCache::add('system_preferences', $cfgs);
}

/**
 * 取布尔值类型的变量。
 *
 * @param string $name
 * @param string $default
 *
 * @return boolean
 */
function bcfg($name, $default = false) {
	$value = cfg($name, $default);
	if (empty ($value) || $value == 'false' || $value == '0') {
		return false;
	}

	return true;
}

function icfg($name, $default = 0) {
	return intval(cfg($name, $default));
}

/**
 * 根据规则生成URL.
 *
 * @param string $pattern
 * @param array  $data
 *            array('aid','tid','trid','model','create_time','name','path','page')
 *
 * @return string
 */
function parse_page_url($pattern, $data) {
	static $ps = array('{aid}', '{Y}', '{M}', '{D}', '{timestamp}', '{pinyin}', '{py}', '{typedir}', '{cc}', '{page}', '{tid}', '{trid}', '{mid}', '{path}', '{rpath}', '{title}', '{title2}');

	$r [0] = isset ($data ['aid']) ? $data ['aid'] : 0;

	if (isset ($data ['create_time'])) {
		$time = $data ['create_time'];
	} else {
		$time = time();
	}
	$r [1] = date('Y', $time);
	$r [2] = date('m', $time);
	$r [3] = date('d', $time);
	$r [4] = $time;

	if (isset ($data ['name'])) {
		$r [5] = Pinyin::c($data ['name']);
		$r [6] = Pinyin::c($data ['name'], true);
	} else {
		$r [5] = '';
		$r [6] = '';
	}

	if (isset ($data ['basedir'])) {
		$r [7] = $data ['basedir'];
	} else {
		$r [7] = '';
	}

	$r [8]  = 'cc';
	$r [9]  = isset ($data ['page']) ? $data ['page'] : 1;
	$r [10] = isset ($data ['tid']) ? $data ['tid'] : 0;
	$r [11] = isset ($data ['trid']) ? $data ['trid'] : 0;
	$r [12] = isset ($data ['model']) ? $data ['model'] : 0;
	if (isset ($data ['path']) && !empty ($data ['path'])) {
		$r [13] = trim($data ['path'], '/');
		$paths  = explode('/', $r [13]);
		array_shift($paths);
		$r [14] = implode('/', $paths);
	} else {
		$r [13] = '';
		$r [14] = '';
	}
	$r [15] = isset ($data ['title']) ? $data ['title'] : '';
	$r [16] = isset ($data ['title2']) ? $data ['title2'] : '';

	return ltrim(str_replace($ps, $r, $pattern), '/');
}

/**
 * 得到关键词列表.
 *
 * @param array  $keywords
 * @param string $string
 *
 * @return array
 */
function get_keywords($keywords, $string = '', $count = null, $dict = null) {
	if ($keywords) {
		$keywords = preg_split('#,+#', trim(trim(str_replace(array('，', ' ', '　', '-', ';', '；', '－'), ',', $keywords)), ','));
		$keywords = implode(' ', $keywords);
	} else if (extension_loaded('scws') && $string) {
		$scws = scws_new();
		$scws->set_charset('utf8');
		$attr = null;
		if ($dict && is_file($dict)) {
			@$scws->set_dict($dict);
			$attr = 'nk';
			$scws->set_multi(SCWS_MULTI_NONE);
		} else {
			$scws->set_multi(SCWS_MULTI_ZMAIN);
		}
		$scws->set_duality(false);
		$scws->set_ignore(true);
		$scws->send_text($string);
		if ($count == null) {
			$tmp = $scws->get_tops(cfg('keywords_count@cms', 5), $attr);
		} else {
			$tmp = $scws->get_tops($count, $attr);
		}
		if ($tmp) {
			$keywords = array();
			foreach ($tmp as $keyword) {
				$keywords [] = $keyword ['word'];
			}
			$keywords = implode(' ', $keywords);
		}
		$scws->close();
	}
	if (!empty ($keywords)) {
		return array(str_replace(' ', ',', $keywords), convert_search_keywords($keywords));
	} else {
		return array('', '');
	}
}

/**
 * 将以'，',' ','　','-',';','；','－'分隔的字符串转换成以逗号分隔的字符.
 *
 * @param string $string
 *
 * @return string
 */
function pure_comman_string($string) {
	if ($string) {
		return trim(trim(str_replace(array('，', ' ', '　', '-', ';', '；', '－'), ',', $string)), ',');
	}

	return '';
}

/**
 * 判断$tag是否在A标签中或是某个标签的属性.
 *
 * @param string $content
 * @param string $tag
 *
 * @return bool
 */
function in_atag($content, $tag) {
	$pos = strpos($content, $tag);
	if ($pos === false) {
		return false;
	}
	// 是否是某一个标签的属性
	$search = '`<[^>]*?' . preg_quote($tag, '`') . '[^>]*?>`ui';
	if (preg_match($search, $content)) {
		return true;
	}
	$pos  = strlen($content) - $pos;
	$spos = strripos($content, '<a', -$pos);
	$epos = strripos($content, '</a', -$pos);
	// 没有a标签
	if ($spos === false) {
		return false;
	}
	// 前边的a标签已经关掉
	if ($epos !== false && $epos > $spos) {
		return false;
	}

	return true;
}

/**
 * covert the charset of filename to UTF-8.
 *
 * @param string $filename
 *
 * @return string
 */
function thefilename($filename) {
	$encode = mb_detect_encoding($filename, "UTF-8,GBK,GB2312,BIG5,ISO-8859-1");
	if ($encode != 'UTF-8') {
		$filename = mb_convert_encoding($filename, "UTF-8", $encode);
	}

	return $filename;
}

/**
 * reimplements is_subclass_of function.
 *
 * @param mixed  $object
 * @param string $class_name
 *
 * @return boolean
 */
function is_subclass_of2($object, $class_name) {
	if (empty ($object)) {
		return false;
	} else if (is_string($object) && !class_exists($object)) {
		return false;
	} else if (is_array($object)) {
		return false;
	}
	if (version_compare('5.3.7', phpversion(), '>')) {
		if (is_subclass_of($object, $class_name)) {
			return true;
		} else {
			$intefaces = class_implements($object);

			return $intefaces && isset ($intefaces [ $class_name ]);
		}
	} else {
		return is_subclass_of($object, $class_name);
	}
}

/**
 * 发送邮件.
 *
 * @param array  $to
 *            接收人
 * @param string $subject
 *            主题
 * @param string $content
 *            正文
 * @param array  $attachments
 *            附件
 * @param
 *            string 正文类型
 *
 * @return boolean true发送成功,如果失败false
 */
function sendmail($to, $subject, $message, $attachments = array(), $type = 'html') {
	global $__mailer;
	if ($__mailer == null) {
		$__mailer = new DefaultMailer ();
	}
	if (!empty ($type)) {
		$__mailer->setMessageType($type);
	}

	return $__mailer->send($to, $subject, $message, $attachments);
}

/**
 * Set HTTP status header.
 *
 * @since 1.0
 *
 * @param int $header
 *            HTTP status code
 *
 */
function status_header($header) {
	$text = get_status_header_desc($header);

	if (empty ($text)) {
		return;
	}
	$protocol = $_SERVER ["SERVER_PROTOCOL"];
	if ('HTTP/1.1' != $protocol && 'HTTP/1.0' != $protocol) {
		$protocol = 'HTTP/1.0';
	}

	$status_header = "$protocol $header $text";

	@header($status_header, true, $header);
	if (php_sapi_name() == 'cgi-fcgi') {
		@header("Status: $header $text");
	}
}

/**
 * Retrieve the description for the HTTP status.
 *
 * @since 1.0
 *
 * @param int $code
 *            HTTP status code.
 *
 * @return string Empty string if not found, or description if found.
 */
function get_status_header_desc($code) {
	global $output_header_to_desc;

	$code = abs(intval($code));

	if (!isset ($output_header_to_desc)) {
		$output_header_to_desc = array(100 => 'Continue', 101 => 'Switching Protocols', 102 => 'Processing',

		                               200 => 'OK', 201 => 'Created', 202 => 'Accepted', 203 => 'Non-Authoritative Information', 204 => 'No Content', 205 => 'Reset Content', 206 => 'Partial Content', 207 => 'Multi-Status', 226 => 'IM Used',

		                               300 => 'Multiple Choices', 301 => 'Moved Permanently', 302 => 'Found', 303 => 'See Other', 304 => 'Not Modified', 305 => 'Use Proxy', 306 => 'Reserved', 307 => 'Temporary Redirect',

		                               400 => 'Bad Request', 401 => 'Unauthorized', 402 => 'Payment Required', 403 => 'Forbidden', 404 => 'Not Found', 405 => 'Method Not Allowed', 406 => 'Not Acceptable', 407 => 'Proxy Authentication Required', 408 => 'Request Timeout', 409 => 'Conflict', 410 => 'Gone', 411 => 'Length Required', 412 => 'Precondition Failed', 413 => 'Request Entity Too Large', 414 => 'Request-URI Too Long', 415 => 'Unsupported Media Type', 416 => 'Requested Range Not Satisfiable', 417 => 'Expectation Failed', 422 => 'Unprocessable Entity', 423 => 'Locked', 424 => 'Failed Dependency', 426 => 'Upgrade Required',

		                               500 => 'Internal Server Error', 501 => 'Not Implemented', 502 => 'Bad Gateway', 503 => 'Service Unavailable', 504 => 'Gateway Timeout', 505 => 'HTTP Version Not Supported', 506 => 'Variant Also Negotiates', 507 => 'Insufficient Storage', 510 => 'Not Extended');
	}

	if (isset ($output_header_to_desc [ $code ])) {
		return $output_header_to_desc [ $code ];
	} else {
		return '';
	}
}

/**
 * Appends a trailing slash.
 *
 * Will remove trailing slash if it exists already before adding a trailing
 * slash. This prevents double slashing a string or path.
 *
 * The primary use of this is for paths and thus should be used for paths. It is
 * not restricted to paths and offers no specific path support.
 *
 * @uses untrailingslashit() Unslashes string if it was slashed already.
 *
 * @param string $string
 *            What to add the trailing slash to.
 *
 * @return string String with trailing slash added.
 */
function trailingslashit($string) {
	return untrailingslashit($string) . '/';
}

/**
 * Removes trailing slash if it exists.
 *
 * The primary use of this is for paths and thus should be used for paths. It is
 * not restricted to paths and offers no specific path support.
 *
 *
 * @param string $string
 *            What to remove the trailing slash from.
 *
 * @return string String without the trailing slash.
 */
function untrailingslashit($string) {
	return rtrim($string, '/\\');
}

/**
 * Sanitizes a filename replacing whitespace with dashes
 *
 * Removes special characters that are illegal in filenames on certain
 * operating systems and special characters requiring special escaping
 * to manipulate at the command line. Replaces spaces and consecutive
 * dashes with a single dash. Trim period, dash and underscore from beginning
 * and end of filename.
 *
 * @since 2.1.0
 *
 * @param string $filename
 *            The filename to be sanitized
 *
 * @return string The sanitized filename
 */
function sanitize_file_name($filename) {
	$special_chars = array("?", "[", "]", "/", "\\", "=", "<", ">", ":", ";", ",", "'", "\"", "&", "$", "#", "*", "(", ")", "|", "~", "`", "!", "{", "}", chr(0));
	$filename      = str_replace($special_chars, '', $filename);
	$filename      = preg_replace('/[\s-]+/', '-', $filename);
	$filename      = trim($filename, '.-_');
	// Split the filename into a base and extension[s]
	$parts = explode('.', $filename);
	// Return if only one extension
	if (count($parts) <= 2) return $filename;

	// Process multiple extensions
	$filename  = array_shift($parts);
	$extension = array_pop($parts);

	$mimes = array('tmp', 'txt', 'jpg', 'gif', 'png', 'rar', 'zip', 'gzip', 'ppt');

	// Loop over any intermediate extensions. Munge them with a trailing
	// underscore if they are a 2 - 5 character
	// long alpha string not in the extension whitelist.
	foreach (( array )$parts as $part) {
		$filename .= '.' . $part;

		if (preg_match('/^[a-zA-Z]{2,5}\d?$/', $part)) {
			$allowed = false;
			foreach ($mimes as $ext_preg => $mime_match) {
				$ext_preg = '!(^' . $ext_preg . ')$!i';
				if (preg_match($ext_preg, $part)) {
					$allowed = true;
					break;
				}
			}
			if (!$allowed) $filename .= '_';
		}
	}
	$filename .= '.' . $extension;

	return $filename;
}

/**
 * Get a filename that is sanitized and unique for the given directory.
 *
 * If the filename is not unique, then a number will be added to the filename
 * before the extension, and will continue adding numbers until the filename is
 * unique.
 *
 * The callback must accept two parameters, the first one is the directory and
 * the second is the filename. The callback must be a function.
 *
 * @param string $dir
 * @param string $filename
 * @param string $unique_filename_callback
 *            Function name, must be a function.
 *
 * @return string New filename, if given wasn't unique.
 */
function unique_filename($dir, $filename, $unique_filename_callback = null) {
	$filename = sanitize_file_name($filename);
	$info     = pathinfo($filename);
	$ext      = !empty ($info ['extension']) ? '.' . $info ['extension'] : '';
	$name     = basename($filename, $ext);
	if ($name === $ext) {
		$name = '';
	}
	if ($unique_filename_callback && is_callable($unique_filename_callback)) {
		$filename = $unique_filename_callback ($dir, $name);
	} else {
		$number = '';
		if ($ext && strtolower($ext) != $ext) {
			$ext2      = strtolower($ext);
			$filename2 = preg_replace('|' . preg_quote($ext) . '$|', $ext2, $filename);
			while (file_exists($dir . "/$filename") || file_exists($dir . "/$filename2")) {
				$new_number = $number + 1;
				$filename   = str_replace("$number$ext", "$new_number$ext", $filename);
				$filename2  = str_replace("$number$ext2", "$new_number$ext2", $filename2);
				$number     = $new_number;
			}

			return $filename2;
		}
		while (file_exists($dir . "/$filename")) {
			if ('' == "$number$ext") {
				$filename = $filename . ++$number . $ext;
			} else {
				$filename = str_replace("$number$ext", ++$number . $ext, $filename);
			}
		}
	}

	return $filename;
}

/**
 * 查找文件.
 *
 * @param string   $dir
 *            起始目录
 * @param string   $pattern
 *            合法的正则表达式,此表达式只用于文件名
 * @param array    $excludes
 *            不包含的目录名
 * @param bool|int $recursive
 *            是否递归查找
 * @param int      $stop
 *            递归查找层数
 *
 * @return array 查找到的文件
 */
function find_files($dir = '.', $pattern = '', $excludes = array(), $recursive = 0, $stop = 0) {
	$files = array();
	$dir   = trailingslashit($dir);
	if (is_dir($dir)) {
		$fhd = @opendir($dir);
		if ($fhd) {
			$excludes  = is_array($excludes) ? $excludes : array();
			$_excludes = array_merge($excludes, array('.', '..'));
			while (($file = readdir($fhd)) !== false) {
				if ($recursive && is_dir($dir . $file) && !in_array($file, $_excludes)) {
					if ($stop == 0 || $recursive <= $stop) {
						$files = array_merge($files, find_files($dir . $file, $pattern, $excludes, $recursive + 1, $stop));
					}
				}
				if (is_file($dir . $file) && @preg_match($pattern, $file)) {
					$files [] = $dir . $file;
				}
			}
			@closedir($fhd);
		}
	}

	return $files;
}

/**
 * 删除目录.
 *
 * @param string $dir
 *
 * @return bool
 */
function rmdirs($dir, $keep = true) {
	$hd = @opendir($dir);
	if ($hd) {
		while (($file = readdir($hd)) != false) {
			if ($file == '.' || $file == '..') {
				continue;
			}
			if (is_dir($dir . DS . $file)) {
				rmdirs($dir . DS . $file, false);
			} else {
				@unlink($dir . DS . $file);
			}
		}
		closedir($hd);
		if (!$keep) {
			@rmdir($dir);
		}
	}

	return true;
}

/**
 * 只保留URL中部分参数.
 *
 * @param string $url
 * @param array  $include
 *            要保留的参数
 *
 * @return string
 */
function keepargs($url, $include = array()) {
	$urls = explode('?', $url);
	if (count($urls) < 2) {
		return $url;
	}
	$kargs = array();
	foreach ($include as $arg) {
		if (preg_match('/' . $arg . '=([^&]+)/', $urls [1], $m)) {
			$kargs [] = $m [0];
		}
	}
	if (!empty ($kargs)) {
		$urls [1] = implode('&', $kargs);

		return implode('?', $urls);
	} else {
		return $urls [0];
	}
}

/**
 * 去除URL中的参数.
 *
 * @param
 *            string url
 * @param
 *            array 要去除的参数
 *
 * @return string
 */
function unkeepargs($url, $exclude = array()) {
	$regex = array();
	$rpm   = array();
	if (is_string($exclude)) {
		$exclude = array($exclude);
	}
	foreach ($exclude as $ex) {
		$regex [] = '/&?' . $ex . '=[^&]*/';
		$rpm []   = '';
	}

	return preg_replace($regex, $rpm, $url);
}

/**
 * 从SESSION中取值.
 *
 * 如果未设置,则返回默认值 $default
 *
 * @param string $name
 *            值名
 * @param mixed  $default
 *            默认值
 *
 * @return mixed SESSION中的值
 */
function sess_get($name, $default = "") {
	if (isset ($_SESSION [ $name ])) {
		return $_SESSION [ $name ];
	}

	return $default;
}

/**
 * 从SESSION中删除变量$name,并将该变量值返回.
 *
 * @param string $name
 * @param string $default
 *
 * @return mixed
 */
function sess_del($name, $default = '') {
	$value = sess_get($name, $default);
	if (isset ($_SESSION [ $name ])) {
		$_SESSION [ $name ] = null;
		unset ($_SESSION [ $name ]);
	}

	return $value;
}

function rqst($name, $default = '', $xss_clean = true) {
	global $__rqst;
	if (!$__rqst) {
		$__rqst = Request::getInstance();
	}

	return $__rqst->get($name, $default, $xss_clean);
}

function arg($name, $default = '') {
	global $__rqst;
	if (!$__rqst) {
		$__rqst = Request::getInstance();
	}

	return $__rqst->get($name, $default, false);
}

function rqset($name) {
	return isset ($_GET [ $name ]) || isset ($_POST [ $name ]);
}

function irqst($name, $default = 0) {
	return intval(rqst($name, $default, true));
}

function frqst($name, $default = 0) {
	return floatval(rqst($name, $default, true));
}

/**
 * 安全ID.
 *
 * @param string  $ids
 *            以$sp分隔的id列表,只能是大与0的整形.
 * @param string  $sp
 *            分隔符.
 * @param boolean $array
 *            是否返回数组.
 *
 * @return mixed
 */
function safe_ids($ids, $sp = ',', $array = false) {
	if (empty ($ids)) {
		return $array ? array() : '';
	}
	$_ids = explode($sp, $ids);
	$ids  = array();
	foreach ($_ids as $id) {
		if (preg_match('/^[1-9]\d*$/', $id)) {
			$ids [] = intval($id);
		}
	}
	if ($array === false) {
		return empty ($ids) ? '' : implode($sp, $ids);
	} else {
		return empty ($ids) ? array() : $ids;
	}
}

/**
 * 安全ID.
 *
 * @param string $ids
 *            要处理的ids.
 * @param string $sp
 *            分隔字符，默认为','.
 *
 * @return array
 */
function safe_ids2($ids, $sp = ',') {
	return safe_ids($ids, $sp, true);
}

/**
 * 可读的size.
 *
 * @param int $size
 *
 * @return string
 */
function readable_size($size) {
	$size = intval($size);
	if ($size < 1024) {
		return $size . 'B';
	} else if ($size < 1048576) {
		return number_format($size / 1024, 2) . 'K';
	} else if ($size < 1073741824) {
		return number_format($size / 1048576, 2) . 'M';
	} else {
		return number_format($size / 1073741824, 2) . 'G';
	}
}

function readable_num($size) {
	$size = intval($size);
	if ($size < 1000) {
		return $size;
	} else if ($size < 10000) {
		return number_format($size / 1000, 2) . 'K';
	} else if ($size < 10000000) {
		return number_format($size / 10000, 2) . 'W';
	} else {
		return number_format($size / 10000000, 2) . 'KW';
	}
}

function readable_date($sec, $text = array('s' => '秒', 'm' => '分', 'h' => '小时', 'd' => '天')) {
	$size = intval($sec);
	if ($size == 0) {
		return '';
	} else if ($size < 60) {
		return $size . $text ['s'];
	} else if ($size < 3600) {
		return floor($size / 60) . $text ['m'] . readable_date(fmod($size, 60));
	} else if ($size < 86400) {
		return floor($size / 3600) . $text ['h'] . readable_date(fmod($size, 3600));
	} else {
		return floor($size / 86400) . $text ['d'] . readable_date(fmod($size, 86400));
	}
}

/**
 * 合并$base与$arr.
 *
 * @param mixed $base
 * @param array $arr
 *
 * @return array 如果$base为空或$base不是一个array则直接返回$arr,反之返回array_merge($base,$arr)
 */
function array_merge2($base, $arr) {
	if (empty ($base) || !is_array($base)) {
		return $arr;
	}
	if (empty ($arr) || !is_array($arr)) {
		return $base;
	}

	return array_merge($base, $arr);
}

function get_query_string() {
	$query_str = $_SERVER ['QUERY_STRING'];
	if ($query_str) {
		parse_str($query_str, $args);
		unset ($args ['_url'], $args ['preview']);
		$query_str = http_build_query($args);
	}

	return empty ($query_str) ? '' : '?' . rtrim($query_str, '=');
}

/**
 * 输入安全URL.
 *
 * @param string $url
 *
 * @return string
 */
function safe_url($node, $inherit = false) {
	static $domain = false, $protocol = false, $base_url = false;
	if (empty ($node)) {
		return '#';
	}
	$inherit = $inherit ? 1 : 0;
	if ($domain === false) {
		$base_url [0] = cfg('cms_url@cms', defined('ENABLE_SUB_DOMAIN') ? DETECTED_ABS_URL : DETECTED_URL);
		$host         = preg_match('#^https?://.+#i', $base_url [0]) ? preg_replace('#^https?://#i', '', trim($base_url [0], '/')) : $_SERVER ['HTTP_HOST'];
		$domain [0]   = strstr($host, '.');
		$protocol [0] = isset ($_SERVER ['HTTPS']) ? 'https://' : 'http://';

		$host         = $_SERVER ['HTTP_HOST'];
		$domain [1]   = strstr($host, '.');
		$protocol [1] = $protocol [0];
		$base_url [1] = DETECTED_URL;

		$domain [2]   = ''; // for mobi domain
		$protocol [2] = $protocol [0];
		$base_url [2] = DETECTED_URL;
	}
	if (is_string($node)) {
		if ($node{0} == '/' || $node{0} == '#' || preg_match('#^(http|ftp)s?://.+#ui', $node)) {
			if (preg_match('#/index\.s?html(\?.+)?$#ui', $node)) {
				return preg_replace('#(.*)/index\.s?html(\?.+)?$#ui', '\1\2', $node);
			} else {
				return $node;
			}
		}
		$url  = $node;
		$node = array();
	} else if (isset ($node ['url'])) {
		$mobi_domain = cfg('mobi_domain');
		if ($mobi_domain && $mobi_domain == REAL_HTTP_HOST) {
			$node ['bind'] = null;
			$inherit       = 1;
		}
		$node = apply_filter('filter_data_for_safe_url', $node);
		if (isset ($node ['default_page']) && $node ['default_page']) {
			$url = $node ['list_page_url'];
		} else {
			$url = $node ['url'];
		}
		if (isset ($node ['inherit'])) {
			$inherit = $node ['inherit'];
		}
	} else {
		return '#';
	}
	if ($url{0} == '/' || preg_match('#^(http|ftp)s?://.+#ui', $url)) {
		$rtn_url = $url;
	} else {
		$url = ltrim($url, '/');
		if (isset ($node ['bind']) && !empty ($node ['bind'])) { // 绑定了二级域名
			$rtn_url = $protocol [ $inherit ] . $node ['bind'] . $domain [ $inherit ] . '/' . $url;
		} else {
			$rtn_url = $base_url [ $inherit ] . $url;
		}
	}
	if (preg_match('#/index\.s?html(\?.+)?$#ui', $rtn_url)) {
		$rtn_url = preg_replace('#(.*)/index\.s?html(\?.+)?$#ui', '\1\2', $rtn_url);
	}

	return $rtn_url;
}

function mobile_url($page) {
	static $mobile_domain = false;
	if ($mobile_domain === false) {
		$protocol            = isset ($_SERVER ['HTTPS']) ? 'https://' : 'http://';
		$mobile_domain ['*'] = $protocol . cfg('mobi_domain', '127.0.0.1') . '/';
	}
	if (is_string($page)) {
		if (preg_match('#^(f|ht)tps?://.+#i', $page)) {
			$url = $page;
		} else {
			$url = $mobile_domain ['*'] . ltrim($page, '/');
		}
	} else if (isset ($page ['url'])) {
		$url = apply_filter('get_mobile_url', $page ['url'], $page);
		if (!preg_match('#^(f|ht)tps?://.+#i', $url)) {
			$url = $mobile_domain ['*'] . ltrim($url, '/');
		}
	} else {
		$url = '#';
	}
	if (preg_match('#/index\.s?html(\?.+)?$#ui', $url)) {
		$url = preg_replace('#(.*)/index\.s?html(\?.+)?$#ui', '\1\2', $url);
	}

	return $url;
}

/**
 * 生成栏目URL.
 *
 * @param array  $page
 * @param string $inherit
 *
 * @return string
 */
function channel_url($page, $inherit = false) {
	if (is_string($page)) {
		return safe_url($page, $inherit);
	}
	if (is_array($page)) {
		$ch = array();
		if (isset ($page ['root'])) {
			$ch ['root'] = $page ['root'];
		}
		if (isset ($page ['url']) && !isset ($page ['channel_pageid'])) {
			$ch ['url'] = $page ['url'];
		} else {
			$ch ['url'] = '#error';
			if (isset ($page ['channel_pageid']) && !$page ['channel_pageid'] && $page ['channel_index_url']) {
				$ch ['url'] = $page ['channel_index_url'];
			} else if (isset ($page ['channel_pageid']) && $page ['channel_pageid'] && $page ['channel_list_url']) {
				$ch ['url'] = $page ['channel_list_url'];
			}
		}

		return safe_url($ch, $inherit);
	} else {
		return '#error';
	}
}

/**
 * 记录debug信息.
 *
 * @param string $message
 */
function log_debug($message, $file = '') {
	$trace = debug_backtrace();
	log_message($message, $trace, DEBUG_DEBUG, $trace, $file);
}

/**
 * 记录info信息.
 *
 * @param string $message
 */
function log_info($message, $file = '') {
	$trace = debug_backtrace();
	log_message($message, $trace, DEBUG_INFO, $trace, $file);
}

/**
 * 记录warn信息.
 *
 * @param string $message
 */
function log_warn($message, $file = '') {
	$trace = debug_backtrace();
	log_message($message, $trace, DEBUG_WARN, $trace, $file);
}

/**
 * 记录error信息.
 *
 * @param string $message
 */
function log_error($message, $file = '') {
	$trace = debug_backtrace();
	log_message($message, $trace, DEBUG_ERROR, $trace, $file);
}

/**
 * 生成带参数的页面url.
 *
 * @param string $url  .
 * @param array  $args .
 *
 * @return string
 */
function build_page_url($url, $args) {
	static $params = null;
	if (is_null($params)) {
		parse_str($_SERVER ['QUERY_STRING'], $params);
		unset ($params ['_url']);
	}
	$url   = explode('?', $url);
	$url   = $url [0];
	$pargs = $params;
	if (!empty ($args)) {
		$argnames = array_shift($args);
		$argnames = explode(',', $argnames);
		$i        = 0;
		foreach ($argnames as $n) {
			if (preg_match('#^\-([a-z_][a-z\d_-]*)$#', $n, $m)) {
				unset ($pargs [ $m [1] ]);
			} else {
				$pargs [ $n ] = $args [ $i++ ];
			}
		}
	}
	if (!empty ($pargs) && !preg_match('/.*#$/', $url)) {
	} else {
		return $url;
	}
}

function url_append_args($url, $args) {
	if (strpos($url, '?') === false) {
		return $url . '?' . http_build_query($args);
	} else {
		return $url . '&' . http_build_query($args);
	}
}

/**
 * 生成html标签属性.
 *
 * @param array $properties
 *
 * @return string
 */
function html_tag_properties($properties) {
	if (empty ($properties)) {
		return '';
	}
	$tmp_ary = array();
	foreach ($properties as $name => $val) {
		$name       = trim($name);
		$tmp_ary [] = $name . '="' . $val . '"';
	}

	return ' ' . implode(' ', $tmp_ary) . ' ';
}

/**
 * 合并二个数组，并将对应值相加.
 *
 * @param array  $ary1
 *            被加数组.
 * @param array  $ary2
 *            数组.
 * @param string $sep
 *            相加时的分隔符.
 *
 * @return array 合并后的数组.
 */
function merge_add($ary1, $ary2, $sep = ' ') {
	foreach ($ary2 as $key => $val) {
		if (isset ($ary1 [ $key ])) {
			if (is_array($ary1 [ $key ]) && is_array($val)) {
				$ary1 [ $key ] = merge_add($ary1 [ $key ], $val);
			} else if (is_array($ary1 [ $key ]) && !is_array($val)) {
				$ary1 [ $key ] [] = $val;
			} else if (!is_array($ary1 [ $key ]) && is_array($val)) {
				$val []        = $ary1 [ $key ];
				$ary1 [ $key ] = $val;
			} else {
				$ary1 [ $key ] = $ary1 [ $key ] . $sep . $val;
			}
		} else {
			$ary1 [ $key ] = $val;
		}
	}

	return $ary1;
}

/**
 * get the current theme.
 *
 * @return string 主题.
 */
function get_theme() {
	$mobi_domain = cfg('mobi_domain');
	$langs       = get_system_support_langs();
	if ($langs) {
		$lang    = array_shift($langs);
		$dtheme  = $lang ['theme'];
		$dmtheme = $lang ['mobi_theme'];
	} else {
		$dtheme  = cfg('theme', 'default');
		$dmtheme = cfg('mobi_theme', 'default');
	}
	if ($mobi_domain && REAL_HTTP_HOST == $mobi_domain) {
		return $dmtheme;
	}
	if (CUR_SUBDOMAIN) {
		$theme = cfg(CUR_SUBDOMAIN . '@msite_theme', bcfg(CUR_SUBDOMAIN . '@msite_mdomain') ? $dmtheme : $dtheme);
	} else {
		$theme = $dtheme;
	}

	return $theme;
}

/**
 * log.
 *
 * @param string $message
 * @param array  $trace_info
 * @param int    $level
 * @param string $origin
 */
function log_message($message, $trace_info, $level, $origin = null, $file = '') {
	static $fb = false;
	static $log_name = array(DEBUG_INFO => 'INFO', DEBUG_WARN => 'WARN', DEBUG_DEBUG => 'DEBUG', DEBUG_ERROR => 'ERROR');
	if (empty ($trace_info)) {
		return;
	}
	if ($level >= DEBUG) {
		$ln = $log_name [ $level ];

		$msg = date("Y-m-d H:i:s") . "[$ln] {$message}\n\tfile: {$trace_info[0]['file']} at line {$trace_info[0]['line']}\n";
		if (isset ($trace_info [1]) && $trace_info [1]) {
			$msg .= "\t\t{$trace_info[1]['file']} at line {$trace_info[1]['line']}\n";
			if (isset ($trace_info [2]) && $trace_info [2]) {
				$msg .= "\t\t\t{$trace_info[2]['file']} at line {$trace_info[2]['line']}\n";
			}
		}
		$msg .= "\tscript: " . $_SERVER ['SCRIPT_NAME'] . "\n";
		$msg .= "\turi: " . $_SERVER ['REQUEST_URI'] . "\n";
		$dest_file = $file ? 'kissgo_' . $file . '.log' : 'kissgo.log';
		@error_log($msg, 3, APPDATA_PATH . 'logs/' . $dest_file);

		if (defined('DEBUG_FIREPHP') && DEBUG_FIREPHP) {
			if (!$fb) {
				$fb = true;
				FB::setEnabled(true);
			}
			$msg = $origin ? $origin : "[$ln] {$message} in {$trace_info[0]['file']} at line {$trace_info[0]['line']}";
			switch ($level) {
				case DEBUG_ERROR :
					FB::error($msg);
					break;
				case DEBUG_INFO :
					FB::info($msg);
					break;
				case DEBUG_WARN :
					FB::warn($msg);
					break;
			}
		}
	}
}

/**
 * 生成随机字符串.
 *
 * @param int    $len
 * @param string $chars
 *
 * @return string
 */
function rand_str($len = 8, $chars = "a-z,0-9,$,_,!,@,#,=,~,$,%,^,&,*,(,),+,?,:,{,},[,],A-Z") {
	$characters = explode(',', $chars);
	$num        = count($characters);
	for ($i = 0; $i < $num; $i++) {
		if (substr_count($characters [ $i ], '-') > 0) {
			$character_range = explode('-', $characters [ $i ]);
			$max             = ord($character_range [1]);
			for ($j = ord($character_range [0]); $j <= $max; $j++) {
				$array_allow [] = chr($j);
			}
		} else {
			$array_allow [] = $array_allow [ $i ];
		}
	}

	// 生成随机字符串
	mt_srand(( double )microtime() * 1000000);
	$code  = array();
	$index = 0;
	$i     = 0;
	while ($i < $len) {
		$index   = mt_rand(0, count($array_allow) - 1);
		$code [] = $array_allow [ $index ];
		$i++;
	}

	return implode('', $code);
}

/**
 * 来自ucenter的加密解密函数.
 *
 * @param string $string
 *            要解（加）密码字串
 * @param string $operation
 *            DECODE|ENCODE 解密|加密
 * @param string $key
 *            密码
 * @param int    $expiry
 *            超时
 *
 * @return string
 */
function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0) {
	$ckey_length = 4;

	$key  = md5($key ? $key : rand_str(3));
	$keya = md5(substr($key, 0, 16));
	$keyb = md5(substr($key, 16, 16));
	$keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length) : substr(md5(microtime()), -$ckey_length)) : '';

	$cryptkey   = $keya . md5($keya . $keyc);
	$key_length = strlen($cryptkey);

	$string        = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0) . substr(md5($string . $keyb), 0, 16) . $string;
	$string_length = strlen($string);

	$result = '';
	$box    = range(0, 255);

	$rndkey = array();
	for ($i = 0; $i <= 255; $i++) {
		$rndkey [ $i ] = ord($cryptkey [ $i % $key_length ]);
	}

	for ($j = $i = 0; $i < 256; $i++) {
		$j          = ($j + $box [ $i ] + $rndkey [ $i ]) % 256;
		$tmp        = $box [ $i ];
		$box [ $i ] = $box [ $j ];
		$box [ $j ] = $tmp;
	}

	for ($a = $j = $i = 0; $i < $string_length; $i++) {
		$a          = ($a + 1) % 256;
		$j          = ($j + $box [ $a ]) % 256;
		$tmp        = $box [ $a ];
		$box [ $a ] = $box [ $j ];
		$box [ $j ] = $tmp;
		$result .= chr(ord($string [ $i ]) ^ ($box [ ($box [ $a ] + $box [ $j ]) % 256 ]));
	}

	if ($operation == 'DECODE') {
		if ((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26) . $keyb), 0, 16)) {
			return substr($result, 26);
		} else {
			return '';
		}
	} else {
		return $keyc . str_replace('=', '', base64_encode($result));
	}
}

function minify_resources($content, $type) {
	static $cm = false;
	if (!bcfg('enabled@mem')) {
		return $content;
	}
	if ($type == 'js') {
		return JSMin::minify($content);
	} else {
		if ($cm === false) {
			$cm = new CSSmin ();
		}

		return $cm->run($content);
	}
}

/**
 * 合并资源(JS or CSS).
 *
 * @param string $content
 *
 * @return string
 */
function combinate_resources($content, $type) {
	if (!bcfg('enabled@mem') || !bcfg('combinate')) {
		return $content;
	}
	$filename = md5($content) . '.' . $type;
	$res_file = TMP_PATH . 'cache' . DS . $filename;
	if (!file_exists($res_file) || bcfg('develop_mode')) {
		if ($type == 'css') {
			$reg = '#href\s*=\s*"([^"]+)"#i';
		} else {
			$reg = '#src\s*=\s*"([^"]+)"#i';
		}
		if (preg_match_all($reg, $content, $ms)) {
			$base_url = KissGoSetting::detectBaseUrl();
			$cm       = new CSSmin ();
			$res      = $ms [1];
			$cnts     = array();
			foreach ($res as $file) {
				$rfile = preg_replace('#^' . $base_url . '#i', WEB_ROOT, $file);
				$cnt   = @file_get_contents($rfile);
				$mined = preg_match('#.*\.min\.(js|css)$#i', $file) || bcfg('develop_mode');
				if ($type == 'css') {
					$dir = rtrim(dirname($file), '/');
					$cnt = preg_replace('#url\s*\((?![\s\'"]*data:)[\'"]?(.+?)[\'"]?\s*\)#ims', 'url(' . $dir . '/\1)', $cnt);
					if (!$mined) {
						$cnt = $cm->run($cnt);
					}
				} else {
					if (!$mined) {
						$cnt = JSMin::minify($cnt);
					}
				}
				$cnts [] = $cnt;
			}
			$content = implode("\n", $cnts);
			@file_put_contents($res_file, $content);
		} else {
			return $content;
		}
	}
	$file = safe_url(tourl('memcached/cached', false) . $filename, true);
	if ($type == 'js') {
		$tag = '<script type="text/javascript" src="' . $file . '"></script>';
	} else {
		$tag = '<link href="' . $file . '" media="screen" rel="stylesheet" type="text/css"/>';
	}

	return $tag;
}

/**
 * 将关键词转换成数据库可以识别的格式.
 *
 * @param string $keywords
 *
 * @return string
 */
function convert_search_keywords($keywords) {
	$keywords = json_encode($keywords);

	return str_replace(array('\u', '"', '1', '2', '3', '4', '5', '6', '7', '8', '9', '0'), array('uu', '', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'), $keywords);
}

/**
 * 根据宽高生成缩略图文件名.
 *
 * @param string $filename
 *            原始文件名.
 * @param int    $w
 * @param int    $h
 *
 * @return string
 */
function get_thumbnail_filename($filename, $w, $h) {
	$pos = strrpos($filename, '.');
	if ($pos === false) {
		return $filename;
	}
	$shortname = substr($filename, 0, $pos);
	$ext       = substr($filename, $pos);

	return $shortname . "-{$w}x{$h}{$ext}";
}

/**
 * 缩略图全路径.
 *
 * @param string $src
 * @param int    $w
 * @param int    $h
 *
 * @return string 全路径.
 */
function the_thumbnail_src($src, $w, $h) {
	static $img_s_url = false;
	if (!$img_s_url) {
		$img_s_url = trailingslashit(cfg('media_url@media', BASE_URL));
	}
	$thumbfile = get_thumbnail_filename($src, $w, $h);

	return $img_s_url . $thumbfile;
}

/**
 * 多媒体文件的全路径.
 *
 * @param string $src
 *
 * @return string 全路径.
 */
function the_media_src($src) {
	static $img_s_url = false;
	if (preg_match('#^(/|http|ftp)s?://.+#i', $src)) {
		return $src;
	}
	if ($img_s_url === false) {
		$img_s_url = trailingslashit(cfg('media_url@media', BASE_URL));
		$img_s_url = explode(',', $img_s_url);
	}
	if (count($img_s_url) > 1) {
		return $img_s_url [ array_rand($img_s_url) ] . $src;
	} else {
		return $img_s_url [0] . $src;
	}
}

/**
 *
 * @param string $versions
 *            format:[(min,max)]
 *
 * @return array array(min,minop,max,maxop)
 */
function parse_version_pair($versions) {
	$rst = array(false, '', false, '');
	if (preg_match('#^([\[\(])(.*?),(.*?)([\]\)])$#', $versions, $m)) {
		if ($m [2]) {
			$rst [0] = $m [2];
			if ($m [1] == '[') {
				$rst [1] = '<=';
			} else {
				$rst [1] = '<';
			}
		}
		if ($m [3]) {
			$rst [2] = $m [3];
			if ($m [4] == ']') {
				$rst [3] = '>=';
			} else {
				$rst [3] = '>';
			}
		}
	}

	return $rst;
}

/**
 * 取主题列表.
 *
 * @return array key=>val
 */
function get_theme_list() {
	$hd     = opendir(THEME_PATH . THEME_DIR);
	$themes = array();
	if ($hd) {
		while (($f = readdir($hd)) != false) {
			if ($f != '.' && $f != '..' && is_dir(THEME_PATH . THEME_DIR . DS . $f)) {
				$themes [ $f ] = $f;
			}
		}
		closedir($hd);
	}

	return $themes;
}

function caiwu_menoy_format($menoy, $unit = 1000, $p = 3, $d = ',') {
	$p    = intval($p);
	$unit = intval($unit);
	$d    = empty ($d) ? ',' : $d;
	if (empty ($menoy)) {
		return number_format(0, $p, '.', $d);
	} else {
		$menoy = floatval($menoy);
		if ($unit > 0) {
			$menoy = $menoy / $unit;
		}

		return number_format($menoy, $p, '.', $d);
	}
}

/**
 * 创建一{@link NamedArray}实例.
 *
 * @return NamedArray.
 */
function nary($array = array()) {
	return new NamedArray ($array);
}

/**
 * 从数据$ary取数据并把它从原数组中删除.
 *
 * @param array $ary
 *
 * @return array
 * @since 1.0.3
 */
function get_then_unset(&$ary) {
	$rtnAry = array();
	$cnt    = func_num_args();
	if (is_array($ary) && $ary && $cnt > 1) {
		for ($i = 1; $i < $cnt; $i++) {
			$arg = func_get_arg($i);
			if (isset ($ary [ $arg ])) {
				$rtnAry [] = $ary [ $arg ];
				unset ($ary [ $arg ]);
			} else {
				$rtnAry [] = '';
			}
		}
	}

	return $rtnAry;
}

function html_escape($string, $esc_type = 'html', $char_set = null, $double_encode = true) {
	static $_double_encode = null;
	if ($_double_encode === null) {
		$_double_encode = version_compare(PHP_VERSION, '5.2.3', '>=');
	}

	if (!$char_set) {
		$char_set = 'UTF-8';
	}

	switch ($esc_type) {
		case 'html' :
			if ($_double_encode) {
				// php >=5.3.2 - go native
				return htmlspecialchars($string, ENT_QUOTES, $char_set, $double_encode);
			} else {
				if ($double_encode) {
					// php <5.2.3 - only handle double encoding
					return htmlspecialchars($string, ENT_QUOTES, $char_set);
				} else {
					// php <5.2.3 - prevent double encoding
					$string = htmlspecialchars($string, ENT_QUOTES, $char_set);

					return $string;
				}
			}

		case 'htmlall' :
			// mb_convert_encoding ignores htmlspecialchars()
			if ($_double_encode) {
				// php >=5.3.2 - go native
				$string = htmlspecialchars($string, ENT_QUOTES, $char_set, $double_encode);
			} else {
				if ($double_encode) {
					// php <5.2.3 - only handle double encoding
					$string = htmlspecialchars($string, ENT_QUOTES, $char_set);
				} else {
					// php <5.2.3 - prevent double encoding
					$string = htmlspecialchars($string, ENT_QUOTES, $char_set);

					return $string;
				}

				// htmlentities() won't convert everything, so use mb_convert_encoding
				return mb_convert_encoding($string, 'HTML-ENTITIES', $char_set);
			}
		case 'url' :
			return rawurlencode($string);

		case 'urlpathinfo' :
			return str_replace('%2F', '/', rawurlencode($string));

		case 'quotes' :
			// escape unescaped single quotes
			return preg_replace("%(?<!\\\\)'%", "\\'", $string);

		case 'hex' :
			// escape every byte into hex
			// Note that the UTF-8 encoded character ä will be represented as %c3%a4
			$return  = '';
			$_length = strlen($string);
			for ($x = 0; $x < $_length; $x++) {
				$return .= '%' . bin2hex($string [ $x ]);
			}

			return $return;

		case 'javascript' :
			// escape quotes and backslashes, newlines, etc.
			return strtr($string, array('\\' => '\\\\', "'" => "\\'", '"' => '\\"', "\r" => '\\r', "\n" => '\\n', '</' => '<\/'));

		default :
			return $string;
	}
}

function timediff($time) {
	static $ctime = false;
	if ($ctime === false) {
		$ctime = time();
	}
	$d = $ctime - $time;
	if ($d < 60) {
		return _('刚刚');
	} else if ($d < 3600) {
		$it = floor($d / 60);

		return _($it . '分钟前');
	} else if ($d < 86400) {
		$it = floor($d / 3600);

		return _($it . '小时前');
	} else if ($d < 604800) {
		$it = floor($d / 86400);

		return _($it . '天前');
	} else if ($d < 2419200) {
		$it = floor($d / 604800);

		return _($it . '周前');
	} else {
		$it = floor($d / 2592000);

		return _($it . '月前');
	}
}

if (!function_exists('_')) {
	function _($string) {
		return $string;
	}
}
/**
 * 将目录$path压缩到$zipFileName.
 *
 * @param string $zipFileName
 *            文件名.
 * @param string $path
 *            要压缩的路径.
 *
 * @return boolean Returns true on success or false on failure.
 */
function zipit($zipFileName, $path) {
	if (!file_exists($path)) {
		return false;
	}
	$zip = new ZipArchive ();
	if ($zip->open($zipFileName, ZipArchive::OVERWRITE)) {
		$dir_iterator = new RecursiveDirectoryIterator ($path, RecursiveDirectoryIterator::SKIP_DOTS);
		$iterator     = new RecursiveIteratorIterator ($dir_iterator, RecursiveIteratorIterator::SELF_FIRST);
		$success      = true;
		foreach ($iterator as $file) {
			if (is_dir($file)) {
				$dest = str_replace($path, '', $file);
				if (!$zip->addEmptyDir($dest)) {
					$success = false;
					break;
				}
			} else {
				$dest = str_replace($path, '', $file);
				if (!$zip->addFile($file, $dest)) {
					$success = false;
					break;
				}
			}
		}
		$zip->close();
		if (!$success) {
			@unlink($zipFileName);
		}

		return $success;
	}

	return false;
}

/**
 * 从$str中截取$str1与$str2之间的字符串.
 *
 * @param string  $str
 * @param string  $str1
 * @param string  $str2
 * @param boolean $include_str1
 */
function inner_str($str, $str1, $str2, $include_str1 = true) {
	if (!$str || !$str1 || !$str2) {
		return null;
	}
	$s    = $str1;
	$e    = $str2;
	$pos1 = strpos($str, $s);
	$pos2 = strpos($str, $e, $pos1 + strlen($s) + 1);
	if ($pos1 !== false && !$include_str1) {
		$pos1 += strlen($s);
	}
	if ($pos1 !== false && $pos2 !== false && $pos2 > $pos1) {
		$cnt = substr($str, $pos1, $pos2 - $pos1);

		return $cnt;
	} else {
		return null;
	}
}

/**
 * 从文件中读取内容.
 *
 * @param resource $file
 *            fopen返回的file resource.
 *
 * @return string file content.
 */
function file_read($file) {
	$contents = null;
	if ($file) {
		while (!feof($file)) {
			$contents .= fread($file, 2);
		}
	}

	return $contents;
}
// end of file functions.php