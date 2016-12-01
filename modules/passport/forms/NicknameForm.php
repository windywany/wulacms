<?php

namespace passport\forms;

class NicknameForm extends \AbstractForm {
	private $id       = array('widget' => 'hidden', 'rules' => array('regexp(/^[0-9]+$/)' => '非法的编号.'));
	private $nickname = array('widget' => 'textarea', 'note' => '一行一个', 'rules' => array('required' => '请填写禁止的关键字'));

	public static function isExist($keyword = '') {
		$keyword = trim($keyword);
		$keyword = urlencode($keyword); // 将关键字编码
		$keyword = preg_replace('/(%7E|%60|%21|%40|%23|%24|%25|%5E|%26|%27|%2A|%28|%29|%2B|%7C|%5C|%3D|\-|_|%5B|%5D|%7D|%7B|%3B|%22|%3A|%3F|%3E|%3C|%2C|\.|%2F|%A3%BF|%A1%B7|%A1%B6|%A1%A2|%A1%A3|%A3%AC|%7D|%A1%B0|%A3%BA|%A3%BB|%A1%AE|%A1%AF|%A1%B1|%A3%FC|%A3%BD|%A1%AA|%A3%A9|%A3%A8|%A1%AD|%A3%A4|%A1%A4|%A3%A1|%E3%80%82|%EF%BC%81|%EF%BC%8C|%EF%BC%9B|%EF%BC%9F|%EF%BC%9A|%E3%80%81|%E2%80%A6%E2%80%A6|%E2%80%9D|%E2%80%9C|%E2%80%98|%E2%80%99)+/', '', $keyword);
		$keyword = urldecode($keyword); // 将过滤后的关键字解码
		$exist   = dbselect()->from('{member_nickname_black}')->where(array('nickname' => $keyword))->get('id');

		return $exist ? true : false;
	}
}