<?php
defined('KISSGO') or exit ('No direct script access allowed');
/**
 * @param array $data
 */
function default_template_data(&$data) {
	global $city_hosts;

	$data['city_hosts'] = $city_hosts;
	if (!defined('MPS_CITY')) {
		define('MPS_CITY', '');
		define('MPS_CITY_NAME', '全国');
	}
	$data['current_city']     = MPS_CITY;
	$data['current_cityname'] = MPS_CITY_NAME;
	$cs                       = \country\Country::getInstance()->getPovinces();
	$city_list                = [];
	foreach ($cs as $p) {
		$py                      = $p->py;
		$id                      = $p->proviceId;
		$name                    = $p->name;
		$city_list[ $py ][ $id ] = $name;
	}
	ksort($city_list);
	$data['city_list'] = $city_list;
	//$data['saleCounts'] = getSalesCount(['gougou', 'maomao']);
	$data['saleCounts'] = ['maomao' => '8056', 'gougou' => '10188'];
}

function default_category_template_data(&$data) {
	$url             = $data['url'];
	$cs              = explode('/', $url);
	$data['url']     = '/' . $data['channel_url'];
	$data['pet_url'] = $url;
	$args            = getCatagoryListArgs($cs[0]);
	$args->initWithValues([]);
	$args->addSr('{pet}', $data['channel_name']);
	$args->addSr('{title}', $data['title']);
	$where['S.pet'] = $cs[0];
	if ($cs[1]) {
		$where['CP.channel'] = $cs[1];
		$args->enableExtraPattern();
	}
	$where['CP.deleted'] = 0;
	$where['CP.status']  = 2;
	//需要从页面中获取城市信息
	$p = $args->getValue('p');
	if ($p) {
		$where['province'] = $p . '0000';
		$city              = $args->getValue('c');
		if ($city && $city != '0') {
			$where['city'] = $p . $city . '00';
		}
	}
	$prce = intval($args->getValue('pr'));
	if ($prce) {
		switch ($prce) {
			case 1:
				$price[0] = 0;
				$price[1] = 500;
				break;
			case 2:
				$price[0] = 501;
				$price[1] = 1000;
				break;
			case 3:
				$price[0] = 1001;
				$price[1] = 1500;
				break;
			case 4:
				$price[0] = 1501;
				$price[1] = 2000;
				break;
			case 5:
				$price[0] = 2001;
				$price[1] = 5000;
				break;
			case 6:
				$price[0] = 5001;
				$price[1] = 10000;
				break;
			default:
				$price[0] = 10001;
				$price[1] = 10000000000;
		}

		$where['S.price BETWEEN'] = $price;
	}

	$age = intval($args->getValue('a'));
	if ($age) {
		$where['S.age'] = $age;
	}

	$data['args'] = $args;
	$page         = intval($args->getValue());
	$limit        = 40;
	$sales        = dbselect('S.*,CP.title,CP.image,CP.url,CP.channel')->from('{mps_petsales} AS S')->join('{cms_page} AS CP', 'S.page_id = CP.id')->where($where)->desc('S.update_time');
	$sales->limit($page * $limit, $limit);
	$total = $sales->toArray();
	$count = $sales->count('S.id');

	if ($count > $limit) {
		$totalPage         = floor($count / $limit);
		$data['totalPage'] = $totalPage;
		$data['startPage'] = $page - 5;
		if ($data['startPage'] < 0) {
			$data['startPage'] = 0;
		}
		$data['stopPage'] = $page + 5;
		if ($data['stopPage'] > $totalPage) {
			$data['stopPage'] = $totalPage;
		}
	} else {
		$data['totalPage'] = 0;
		$data['startPage'] = 0;
		$data['stopPage']  = 0;
	}
	$data['prevPage'] = $page - 1;
	$data['nextPage'] = $page + 1;
	$data['cPage']    = $page;
	$data['sales']    = new CtsData($total, $count);
}

function default_article_template_data(&$data) {
	if ($data['model'] == 'petsale') {
		$url             = $data['url'];
		$data['url']     = '/' . $data['channel_url'];
		$data['pet_url'] = $url;
		$city            = $data['city'];
		$c               = \country\Country::getInstance();
		$city            = $c->getCity($city);
		if ($city) {
			$p    = $city->getProvince();
			$name = $p->name;
			define('MPS_CITY', $p->proviceId);
			define('MPS_CITY_NAME', $name);
			$data['city_name'] = $city->name;
		}

		$data['petServices'] = \mps\classes\MpsValues::services();
		$data['petGenders']  = \mps\classes\MpsValues::gender();
		$data['petCerts']    = \mps\classes\MpsValues::certs();
		$data['petVedios']   = \mps\classes\MpsValues::vedio();
		$data['petAges']     = \mps\classes\MpsValues::age();
		if ($data['yimiao_time3']) {
			$data['yimiao_status'] = '已注射3针';
		} elseif ($data['yimiao_time2']) {
			$data['yimiao_status'] = '已注射2针';
		} elseif ($data['yimiao_time1']) {
			$data['yimiao_status'] = '已注射1针';
		} else {
			$data['yimiao_status'] = '未注射';
		}
		if ($data['quchong_time']) {
			$data['quchong_status'] = '已驱虫';
		} else {
			$data['quchong_status'] = '未驱虫';
		}
		$data['phone1'] = substr($data['phone'], 0, 8) . '***';
	}
}

function getSalesCount($pets) {
	$cnt = [];
	foreach ($pets as $pet) {
		$cnt[ $pet ] = dbselect()->from('{mps_petsales}')->where(['pet' => $pet])->count('id');
		if ($cnt[ $pet ] < 10000) {
			$cnt[ $pet ] = rand(10001, 10100);
		}
	}

	return $cnt;
}

