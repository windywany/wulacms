<?php

namespace country;

class Country {
	const CACHE_ID = 'country.cities';
	private        $cities;
	private static $INSTANCE;

	private function __construct() {
		$cities = \RtCache::get(self::CACHE_ID);
		if ($cities) {
			$this->cities = $cities;
		} else {
			include __DIR__ . '/cities.php';
			$this->init($cities);
		}
	}

	/**
	 * 获取{@link Country}实例.
	 *
	 * @return Country
	 */
	public static function getInstance() {
		if (!self::$INSTANCE) {
			self::$INSTANCE = new Country();
		}

		return self::$INSTANCE;
	}

	/**
	 * @return array array(City)
	 */
	public function getPovinces() {
		$provinces = [];
		foreach ($this->cities as $city) {
			$provinces[] = new City($city[0], $this);
		}

		return $provinces;
	}

	/**
	 * @param string|mixed $id 省id
	 *
	 * @return array
	 */
	public function getAllCites($id = null) {
		$cities = [];
		if ($id) {
			$id = substr($id, 0, 2);
			foreach ($this->cities[ $id ][1] as $city) {
				$cities[] = new City($city[0], $this);
			}
		} else {
			foreach ($this->cities as $city) {
				if (isset($city[1])) {
					foreach ($city[1] as $c) {
						$cities[] = new City($c[0], $this);
					}
				}
			}
		}

		return $cities;
	}

	/**
	 * @param string $id
	 *
	 * @return array
	 */
	public function getCities($id) {
		$id1 = substr($id, 0, 2);
		$id2 = substr($id, 2, 2);
		$id3 = substr($id, 4, 2);
		if ($id3 && $id3 != '00') {
			//区县
			$cities = $this->cities[ $id1 ][1][ $id2 ][1][ $id3 ][1];
		} elseif ($id2 && $id2 != '00') {
			//城市
			$cities = $this->cities[ $id1 ][1][ $id2 ][1];
		} else {
			//省
			$cities = $this->cities[ $id1 ][1];
		}
		$cis = [];
		if ($cities) {
			foreach ($cities as $city) {
				$cis[] = new City($city[0], $this);
			}
		}

		return $cis;
	}

	/**
	 * 取一个City,可以是省,市,区,商圈中的任何一个。
	 *
	 * @param string $id 省,市,区,商圈ID.
	 *
	 * @return City
	 */
	public function getCity($id) {
		$id1 = substr($id, 0, 2);
		$id2 = substr($id, 2, 2);
		$id3 = substr($id, 4, 2);
		$id4 = substr($id, 6, 3);
		if ($id4 && $id4 != '000') {
			$city = $this->cities[ $id1 ][1][ $id2 ][1][ $id3 ][1][ $id4 ];
		} elseif ($id3 && $id3 != '00') {
			//区县
			$city = $this->cities[ $id1 ][1][ $id2 ][1][ $id3 ][0];
		} elseif ($id2 && $id2 != '00') {//城市
			$city = $this->cities[ $id1 ][1][ $id2 ][0];
		} else {
			//省
			$city = $this->cities[ $id1 ][0];
		}

		return new City($city, $this);
	}

	private function init($cities) {
		$this->cities = [];
		if ($cities) {
			foreach ($cities as $id => $city) {
				$id1     = substr($id, 0, 2);
				$id2     = substr($id, 2, 2);
				$id3     = substr($id, 4, 2);
				$id4     = substr($id, 6, 3);
				$city[2] = $id;
				if ($id4 && $id4 != '000') {
					//商圈
					$city[3]                                               = 4;
					$this->cities[ $id1 ][1][ $id2 ][1][ $id3 ][1][ $id4 ] = $city;
				} elseif ($id3 && $id3 != '00') {
					//区县
					$city[3]                                       = 3;
					$this->cities[ $id1 ][1][ $id2 ][1][ $id3 ][0] = $city;
				} elseif ($id2 && $id2 != '00') {
					//城市
					$city[3]                            = 2;
					$this->cities[ $id1 ][1][ $id2 ][0] = $city;
				} else {
					//省
					$city[3]                 = 1;
					$this->cities[ $id1 ][0] = $city;
				}

			}
		}
		\RtCache::add(self::CACHE_ID, $this->cities);
	}
}