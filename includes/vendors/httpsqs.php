<?php
class httpsqs {
	public $httpsqs_host;
	public $httpsqs_port;
	public $httpsqs_auth;
	public $httpsqs_charset;
	public function __construct($host = '127.0.0.1', $port = 1218, $auth = '', $charset = 'utf-8') {
		$this->httpsqs_host = $host;
		$this->httpsqs_port = $port;
		$this->httpsqs_auth = $auth;
		$this->httpsqs_charset = $charset;
		return true;
	}
	public function http_get($query) {
		$socket =@fsockopen ( $this->httpsqs_host, $this->httpsqs_port, $errno, $errstr, 5 );
		if (! $socket) {
			trigger_error($errstr.'['.$errno.']',E_USER_ERROR);
			return false;
		}
		$out = "GET ${query} HTTP/1.1\r\n";
		$out .= "Host: {$this->httpsqs_host}\r\n";
		$out .= "Connection: close\r\n";
		$out .= "\r\n";
		fwrite ( $socket, $out );
		$line = trim ( fgets ( $socket ) );
		$header .= $line;
		list ( $proto, $rcode, $result ) = explode ( " ", $line );
		$len = - 1;
		while ( ($line = trim ( fgets ( $socket ) )) != "" ) {
			$header .= $line;
			if (strstr ( $line, "Content-Length:" )) {
				list ( $cl, $len ) = explode ( " ", $line );
			}
			if (strstr ( $line, "Pos:" )) {
				list ( $pos_key, $pos_value ) = explode ( " ", $line );
			}
			if (strstr ( $line, "Connection: close" )) {
				$close = true;
			}
		}
		if ($len < 0) {
			return false;
		}
		
		$body = fread ( $socket, $len );
		$fread_times = 0;
		while ( strlen ( $body ) < $len ) {
			$body1 = fread ( $socket, $len );
			$body .= $body1;
			unset ( $body1 );
			if ($fread_times > 100) {
				break;
			}
			$fread_times ++;
		}
		// if ($close) @fclose($socket);
		@fclose ( $socket );
		$result_array ["pos"] = ( int ) $pos_value;
		$result_array ["data"] = $body;
		return $result_array;
	}
	public function http_post($query, $body) {
		$socket =@fsockopen ( $this->httpsqs_host, $this->httpsqs_port, $errno, $errstr, 1 );
		if (! $socket) {
			trigger_error($errstr.'['.$errno.']',E_USER_ERROR);
			return false;
		}
		$out = "POST ${query} HTTP/1.1\r\n";
		$out .= "Host: {$this->httpsqs_host}\r\n";
		$out .= "Content-Length: " . strlen ( $body ) . "\r\n";
		$out .= "Connection: close\r\n";
		$out .= "\r\n";
		$out .= $body;
		fwrite ( $socket, $out );
		$line = trim ( fgets ( $socket ) );
		$header .= $line;
		list ( $proto, $rcode, $result ) = explode ( " ", $line );
		$len = - 1;
		while ( ($line = trim ( fgets ( $socket ) )) != "" ) {
			$header .= $line;
			if (strstr ( $line, "Content-Length:" )) {
				list ( $cl, $len ) = explode ( " ", $line );
			}
			if (strstr ( $line, "Pos:" )) {
				list ( $pos_key, $pos_value ) = explode ( " ", $line );
			}
			if (strstr ( $line, "Connection: close" )) {
				$close = true;
			}
		}
		if ($len < 0) {
			return false;
		}
		$body = @fread ( $socket, $len );
		// if ($close) @fclose($socket);
		@fclose ( $socket );
		$result_array ["pos"] = ( int ) $pos_value;
		$result_array ["data"] = $body;
		return $result_array;
	}
	public function put($queue_name, $queue_data) {
		$result = $this->http_post ( "/?auth=" . $this->httpsqs_auth . "&charset=" . $this->httpsqs_charset . "&name=" . $queue_name . "&opt=put", $queue_data );
		if ($result ["data"] == "HTTPSQS_PUT_OK") {
			return true;
		} else if ($result ["data"] == "HTTPSQS_PUT_END") {
			return $result ["data"];
		}
		return false;
	}
	public function get($queue_name) {
		$result = $this->http_get ( "/?auth=" . $this->httpsqs_auth . "&charset=" . $this->httpsqs_charset . "&name=" . $queue_name . "&opt=get" );
		if ($result == false || $result ["data"] == "HTTPSQS_ERROR" || $result ["data"] == false) {
			return false;
		}
		return $result ["data"];
	}
	public function gets($queue_name) {
		$result = $this->http_get ( "/?auth=" . $this->httpsqs_auth . "&charset=" . $this->httpsqs_charset . "&name=" . $queue_name . "&opt=get" );
		if ($result == false || $result ["data"] == "HTTPSQS_ERROR" || $result ["data"] == false) {
			return false;
		}
		return $result;
	}
	public function status($queue_name) {
		$result = $this->http_get ( "/?auth=" . $this->httpsqs_auth . "&charset=" . $this->httpsqs_charset . "&name=" . $queue_name . "&opt=status" );
		if ($result == false || $result ["data"] == "HTTPSQS_ERROR" || $result ["data"] == false) {
			return false;
		}
		return $result ["data"];
	}
	public function view($queue_name, $queue_pos) {
		$result = $this->http_get ( "/?auth=" . $this->httpsqs_auth . "&charset=" . $this->httpsqs_charset . "&name=" . $queue_name . "&opt=view&pos=" . $queue_pos );
		if ($result == false || $result ["data"] == "HTTPSQS_ERROR" || $result ["data"] == false) {
			return false;
		}
		return $result ["data"];
	}
	public function reset($queue_name) {
		$result = $this->http_get ( "/?auth=" . $this->httpsqs_auth . "&charset=" . $this->httpsqs_charset . "&name=" . $queue_name . "&opt=reset" );
		if ($result ["data"] == "HTTPSQS_RESET_OK") {
			return true;
		}
		return false;
	}
	public function maxqueue($queue_name, $num) {
		$result = $this->http_get ( "/?auth=" . $this->httpsqs_auth . "&charset=" . $this->httpsqs_charset . "&name=" . $queue_name . "&opt=maxqueue&num=" . $num );
		if ($result ["data"] == "HTTPSQS_MAXQUEUE_OK") {
			return true;
		}
		return false;
	}
	public function status_json($queue_name) {
		$result = $this->http_get ( "/?auth=" . $this->httpsqs_auth . "&charset=" . $this->httpsqs_charset . "&name=" . $queue_name . "&opt=status_json" );
		if ($result == false || $result ["data"] == "HTTPSQS_ERROR" || $result ["data"] == false) {
			return false;
		}
		return $result ["data"];
	}
	public function synctime($num) {
		$result = $this->http_get ( "/?auth=" . $this->httpsqs_auth . "&charset=" . $this->httpsqs_charset . "&name=httpsqs_synctime&opt=synctime&num=" . $num );
		if ($result ["data"] == "HTTPSQS_SYNCTIME_OK") {
			return true;
		}
		return false;
	}
}