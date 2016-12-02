<?php

namespace taoke\classes;

class TaokeContentModel extends \DefaultContentModel {
	public function __construct() {
		$model ['name']               = '淘宝客';
		$model ['refid']              = 'taoke';
		$model ['status']             = 1;
		$model ['is_topic_model']     = 0;
		$model ['creatable']          = 1;
		$model ['addon_table']        = 'taoke';
		$model ['search_page_prefix'] = '';
		$model ['search_page_tpl']    = '';
		$model ['search_page_limit']  = '';
		$model ['template']           = '';
		$model ['note']               = '淘宝客模型';
		$model ['role']               = '';
		parent::__construct($model);
	}

	public function getForm() {
		return new TaokeForm();
	}

	/*
	 * (non-PHPdoc) @see DefaultContentModel::save()
	 */
	public function save($page, $form) {
		$id = $page['id'];

		$goods            = $form->toArray();
		$goods['page_id'] = $id;
		//var_dump($goods);exit;
		dbsave($goods, ['page_id' => $id], 'id')->into('{tbk_goods}')->save();

	}

	/*
	 * (non-PHPdoc) @see DefaultContentModel::load()
	 */
	public function load(&$data, $id) {
		$goods = dbselect('*')->from('{tbk_goods}')->where(array('page_id' => $id))->get();
		if ($goods) {
			unset($id);
			foreach ($goods as $key => $v) {
				$data[ $key ] = $v;
			}
		}
	}
}