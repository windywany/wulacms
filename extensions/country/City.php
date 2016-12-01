<?php

namespace country;

class City implements \ArrayAccess {
	public  $type;
	public  $id;
	public  $name;
	public  $py;
	public  $proviceId;
	public  $cityId;
	public  $townId;
	private $country;

	/**
	 * City constructor.
	 *
	 * @param array   $city
	 * @param Country $country
	 */
	public function __construct($city, $country) {
		$this->country = $country;
		$this->type    = $city[3];
		$this->id      = $city[2];
		$this->name    = $city[0];
		$this->py      = $city[1];

		$this->proviceId = substr($this->id, 0, 2);
		$this->cityId    = substr($this->id, 0, 4);
		$this->townId    = substr($this->id, 0, 6);
	}

	/**
	 * @return \country\City
	 */
	public function getProvince() {
		if ($this->type == 1) {
			return $this;
		}

		return $this->country->getCity($this->proviceId);
	}

	/**
	 * @return \country\City
	 */
	public function getCity() {
		if ($this->type == 2) {
			return $this;
		} else if ($this->type > 2) {
			return $this->country->getCity($this->cityId);
		}

		return null;
	}

	/**
	 * @return \country\City
	 */
	public function getTown() {
		if ($this->type == 3) {
			return $this;
		} elseif ($this->type > 3) {
			return $this->country->getCity($this->townId);
		}

		return null;
	}

	public function crumbs() {
		$crumbs = [];
		switch ($this->type) {
			case 4:
			case 3:
				array_unshift($crumbs, $this->getTown());
			case 2:
				array_unshift($crumbs, $this->getCity());
			default:
				array_unshift($crumbs, $this->getProvince());
		}

		return $crumbs;
	}

	/**
	 * 获取子区域
	 * @return array|null
	 */
	public function getCities() {
		if ($this->type == 4) {
			return null;
		}
		$id = str_pad(substr($this->id, 0, $this->type * 2), 9, '0', STR_PAD_RIGHT);

		return $this->country->getCities($id);
	}

	public function offsetExists($offset) {
		return isset($this->{$offset});
	}

	public function offsetGet($offset) {
		return $this->{$offset};
	}

	public function offsetSet($offset, $value) {

	}

	public function offsetUnset($offset) {
	}

}