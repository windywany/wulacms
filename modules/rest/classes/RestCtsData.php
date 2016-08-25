<?php
class RestCtsData extends CtsData {
	/*
	 * (non-PHPdoc) @see CtsData::__construct()
	 */
	private static $datas = array ();
	private static $performed = array ();
	private $con;
	private $group = 'default';
	public function __construct($data = array(), $group = 'default') {
		$this->con = $data;
		$this->group = $group;
		RestCtsData::$datas [$group] [] = $this;
		$this->initData ( array (), null );
	}
	
	/*
	 * (non-PHPdoc) @see CtsData::count()
	 */
	public function count() {
		RestCtsData::perform ( $this->group );
		return $this->total;
	}
	
	/*
	 * (non-PHPdoc) @see CtsData::getCountTotal()
	 */
	public function getCountTotal() {
		RestCtsData::perform ( $this->group );
		return $this->countTotal;
	}
	
	/*
	 * (non-PHPdoc) @see CtsData::getData()
	 */
	public function getData() {
		RestCtsData::perform ( $this->group );
		return parent::getData ();
	}
	
	/*
	 * (non-PHPdoc) @see CtsData::getIterator()
	 */
	public function getIterator() {
		RestCtsData::perform ( $this->group );
		return parent::getIterator ();
	}
	public function toArray() {
		RestCtsData::perform ( $this->group );
		return $this->data;
	}
	/*
	 * (non-PHPdoc) @see CtsData::total()
	 */
	public function total() {
		RestCtsData::perform ( $this->group );
		return $this->total;
	}
	/**
	 *
	 * @param string $appKey        	
	 * @param string $appSecret        	
	 * @return RestClient
	 */
	public function getChannel($appKey, $appSecret) {
		$url = get_condition_value ( 'host', $this->con, '' );
		$datasource = get_condition_value ( 'datasource', $this->con );
		if (! $url || ! $datasource) {
			trigger_error ( 'no host or datasource for remove call.', E_USER_ERROR );
		}
		$ver = get_condition_value ( 'apiVer', $this->con, '1' );
		unset ( $this->con ['host'], $this->con ['apiVer'], $this->con ['loop'], $this->con ['group'] );
		$client = new RestClient ( $url, $appKey, $appSecret );
		$client->get ( 'rest.provider_data', $this->con, null, false );
		return $client;
	}
	private static function perform($group) {
		if (isset ( self::$performed [$group] )) {
			return;
		}
		self::$performed [$group] = true;
		$clients = self::$datas [$group];
		if ($clients) {
			$chs = array ();
			$appKey = cfg ( 'appkey@rest' );
			$appSecret = cfg ( 'appsecret@rest' );
			foreach ( $clients as $i => $c ) {
				$chs [$i] = $c->getChannel ( $appKey, $appSecret );
			}
			$rtns = RestClient::execute ( $chs );
			foreach ( $rtns as $i => $rtn ) {
				if ($rtn && $rtn ['error'] == 0) {
					$clients [$i]->initData ( $rtn ['data'], $rtn ['countTotal'] );
				}
			}
		}
	}
}