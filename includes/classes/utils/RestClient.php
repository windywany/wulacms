<?php

/**
 * RESTful Client. It depends on curl extension.
 * @author ngf
 * @since  1.0
 */
class RestClient {
	private $url;
	private $ver;
	private $appSecret;
	private $appKey;
	private $curl;
	private $timeout = 30;

	/**
	 * 构建一个RESTful Client 实例.
	 *
	 * @param string $url
	 *            the entry of the RESTful Server。
	 * @param string $appKey
	 *            app key.
	 * @param string $appSecret
	 *            app secret.
	 * @param string $ver
	 *            version of the API.
	 * @param int    $timeout
	 *            timeout.
	 */
	public function __construct($url, $appKey, $appSecret, $ver = '1', $timeout = 30) {
		$this->url       = $url;
		$this->appKey    = $appKey;
		$this->appSecret = $appSecret;
		$this->ver       = intval($ver) . '.0';
		$this->timeout   = intval($timeout);
	}

	/**
	 * 析构.
	 */
	public function __destruct() {
		$this->close();
	}

	/**
	 * 计算请求的CHECKSUM值.
	 *
	 * @param array  $args
	 *            参数.
	 * @param string $appSecret
	 *            app secret.
	 * @param string $type
	 *
	 * @return string CHECKSUM值.
	 */
	public static function chucksum($args, $appSecret, $type = 'sha1') {
		RestClient::sortArgs($args);
		$sign = [];
		foreach ($args as $key => $v) {
			if (is_array($v)) {
				foreach ($v as $k => $v1) {
					$sign [] = $key . "[{$k}]=" . $v1;
				}
			} else if ($v || is_numeric($v)) {
				$sign [] = $key . "=" . $v;
			} else {
				$sign [] = $key . "=";
			}
		}
		$str = str_replace(["\n", "\r"], '', implode('&', $sign) . $appSecret);
		if ($type == 'sha1') {
			return sha1($str);
		} else {
			return md5($str);
		}
	}

	/**
	 * 并行执行RestClient请求。
	 *
	 * @param array $clients
	 *
	 * @return array results for each request.
	 */
	public static function execute($clients) {
		if ($clients) {
			$mh      = curl_multi_init();
			$handles = [];
			foreach ($clients as $i => $curl) {
				$ch             = $curl->getHandle();
				$handles [ $i ] = ['h' => $ch, 'c' => $curl];
				curl_multi_add_handle($mh, $ch);
			}
			$active = null;
			do {
				curl_multi_exec($mh, $active);
				if ($active > 0) {
					usleep(50);
				}
			} while ($active > 0);
			$rsts = [];
			foreach ($handles as $i => $h) {
				$rsts [ $i ] = $h ['c']->getReturn(curl_multi_getcontent($h ['h']));
				curl_multi_remove_handle($mh, $h ['h']);
			}
			curl_multi_close($mh);

			return $rsts;
		}

		return [];
	}

	/**
	 * 使用get方法调用接口API.
	 *
	 * @param string  $api
	 *            接口.
	 * @param array   $params
	 *            参数.
	 * @param int     $timeout
	 * @param boolean $execute
	 *            是否立即执行并返回结果.
	 *
	 * @return array 接口的返回值.
	 */
	public function get($api, $params = [], $timeout = null, $execute = true) {
		$this->prepare($params, $api);
		curl_setopt($this->curl, CURLOPT_URL, $this->url . '?' . http_build_query($params));
		curl_setopt($this->curl, CURLOPT_HTTPGET, 1);
		curl_setopt($this->curl, CURLOPT_UPLOAD, false);
		if (is_numeric($timeout)) {
			$this->timeout = $timeout;
			curl_setopt($this->curl, CURLOPT_TIMEOUT, $timeout);
		}
		if ($execute) {
			$rst = curl_exec($this->curl);
			if ($rst === false) {
				log_error(curl_error($this->curl));
			}

			return $this->getReturn($rst);
		} else {
			return $this;
		}
	}

	/**
	 * 使用POST方法调用接口API.
	 *
	 * @param string  $api
	 *            接口.
	 * @param array   $params
	 *            参数.
	 * @param int     $timeout
	 * @param boolean $execute
	 *            是否立即执行并返回结果.
	 *
	 * @return array 接口的返回值.
	 */
	public function post($api, $params = [], $timeout = null, $execute = true) {
		$this->prepare($params, $api);
		curl_setopt($this->curl, CURLOPT_URL, $this->url);
		if (class_exists('CURLFile')) {
			curl_setopt($this->curl, CURLOPT_SAFE_UPLOAD, true);
			$this->preparePostData($params);
		} else if (defined('CURLOPT_SAFE_UPLOAD')) {
			curl_setopt($this->curl, CURLOPT_SAFE_UPLOAD, false);
		}
		curl_setopt($this->curl, CURLOPT_POST, true);
		curl_setopt($this->curl, CURLOPT_POSTFIELDS, $params);

		if (is_numeric($timeout)) {
			$this->timeout = $timeout;
			curl_setopt($this->curl, CURLOPT_TIMEOUT, $timeout);
		}
		if ($execute) {
			$rst = curl_exec($this->curl);
			if ($rst === false) {
				log_error(curl_error($this->curl));
			}

			return $this->getReturn($rst);
		} else {
			return $this;
		}
	}

	/**
	 * get the curl resource.
	 *
	 * @return resource null or curl resource.
	 */
	public function getHandle() {
		return $this->curl;
	}

	/**
	 * 关闭RESTful Client 和 Server之间的链接.
	 */
	public function close() {
		if ($this->curl) {
			curl_close($this->curl);
			$this->curl = null;
		}
	}

	/**
	 * 解析JOSN格式的返回值到array格式.
	 *
	 * @param string $rst
	 *            JSON格式的返回值.
	 *
	 * @return array 结果.
	 */
	public function getReturn($rst) {
		if (empty ($rst)) {
			return ['error' => 106, 'message' => __('Internal error.')];
		} else {
			$json = json_decode($rst, true);
			if ($json) {
				return $json;
			} else {
				return ['error' => 107, 'message' => __('Not supported response format.'), 'data' => $rst];
			}
		}
	}

	/**
	 * 准备连接请求.
	 *
	 * @param array  $params
	 * @param string $api
	 */
	private function prepare(&$params, $api) {
		$this->close();
		$params ['api']    = $api;
		$params ['appkey'] = $this->appKey;
		$params ['ver']    = $this->ver;
		$params ['crc']    = RestClient::chucksum($params, $this->appSecret);
		$this->curl        = curl_init();
		curl_setopt($this->curl, CURLOPT_AUTOREFERER, 1);
		curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($this->curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($this->curl, CURLOPT_TIMEOUT, $this->timeout);
		curl_setopt($this->curl, CURLOPT_POSTFIELDS, []);
	}

	/**
	 * 递归对参数进行排序.
	 *
	 * @param array $args
	 */
	private static function sortArgs(&$args) {
		ksort($args);
		foreach ($args as $key => $val) {
			if (is_string($val) && $val{0} == '@' && file_exists(realpath(trim(substr($val, 1), '"')))) {
				unset ($args [ $key ]);
				continue;
			}
			if (is_array($val)) {
				ksort($val);
				$args [ $key ] = $val;
				RestClient::sortArgs($val);
			}
		}
	}

	private function preparePostData(&$data) {
		foreach ($data as $key => $val) {
			if (is_string($val) && $val{0} == '@' && file_exists(trim(substr($val, 1), '"'))) {
				$data [ $key ] = new CURLFile (realpath(trim(substr($val, 1), '"')));
			} else if (is_array($val)) {
				$data [ $key ] = $this->preparePostData($val);
			}
		}
	}
}
