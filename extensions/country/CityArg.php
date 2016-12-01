<?php
namespace country;

use args\Arg;

class CityArg extends Arg {
	protected function initValues() {
		if ($this->parent) {//有父级
			$value = $this->parent->getCValue();
			if ($value) {
				$country = Country::getInstance();
				$p       = $country->getCity($value);
				if ($p->type == 1) {
					$this->values['0'] = '不限';
					$cities            = $p->getCities();
					foreach ($cities as $c) {
						$this->values[ substr($c->id, 2, 2) ] = $c->name;
					}
				}
			}
		} else {
			$this->values['0'] = '全国';
			$country           = Country::getInstance();
			$ps                = $country->getPovinces();
			foreach ($ps as $py) {
				$this->values[ $py->proviceId ] = $py->name;
			}
		}
	}

	public function getDefaultValue() {
		return '0';
	}

}