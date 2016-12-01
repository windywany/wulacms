<?php
/**
 * 栏目表单.
 * @author Guangfeng
 *
 */
class WeixinMenuForm extends AbstractForm {
	private $id = array ('rules' => array ('regexp(/^[0-9]+$/)' => '非法的编号.' ) );
	private $upid = array ('rules' => array ('regexp(/^[1-9]\d*$/)' => '' ) );
	private $name = array ('rules' => array ('required' => '请填写名称' ) );
	private $sort = array ('rules' => array ('regexp(/^[0-9]{1,3}$/)' => '请填写正确的序号(0-999).' ) );
	private $menu_type;
	private $key;
	
	
	/*
	 * 生成树形options array
	 */
	private static function get_channel_tree_options(&$options, &$data, $model, $upid, $level) {
		$tmp_data = array_slice ( $data, 0, count ( $data ), true );
		foreach ( $tmp_data as $key => $c ) {
			if (count ( $data ) == 0) {
				break;
			}
			if ($c ['upid'] == $upid) {
				unset ( $data [$key] );
				if ($c ['hidden']) {
					continue;
				}
				if (($model == null || $c ['default_model'] == $model) && $c ['isfinal']) {
					$pad = str_pad ( '&nbsp;&nbsp;|--', ($level * 24 + 15), '&nbsp;', STR_PAD_LEFT );
					$name = $pad . ' ' . $c ['name'];
					$tkey = $c ['refid'];
					$options [$tkey] = $name;
					if (count ( $data ) > 0) {
						WeixinMenuForm::get_channel_tree_options ( $options, $data, $model, $c ['id'], $level + 1 );
					}
				} else if (count ( $data ) > 0) {
					WeixinMenuForm::get_channel_tree_options ( $options, $data, $model, $c ['id'], $level );
				}
			}
		}
	}
	private static function get_channel_tree_all_options(&$options, &$data, $model, $upid, $level) {
		$tmp_data = array_slice ( $data, 0, count ( $data ), true );
		foreach ( $tmp_data as $key => $c ) {
			if (count ( $data ) == 0) {
				break;
			}
			if ($c ['upid'] == $upid) {
				unset ( $data [$key] );
				if ($c ['hidden']) {
					continue;
				}
				if (($model == null || $c ['default_model'] == $model)) {
					$pad = str_pad ( '&nbsp;&nbsp;|--', ($level * 24 + 15), '&nbsp;', STR_PAD_LEFT );
					$name = $pad . ' ' . $c ['name'];
					$tkey = $c ['refid'];
					$options [$tkey] = $name;
					if (count ( $data ) > 0) {
						WeixinMenuForm::get_channel_tree_all_options ( $options, $data, $model, $c ['id'], $level + 1 );
					}
				} else if (count ( $data ) > 0) {
					WeixinMenuForm::get_channel_tree_all_options ( $options, $data, $model, $c ['id'], $level );
				}
			}
		}
	}
	/**
	 * 获取channel的树形结构.
	 *
	 * @param string $model        	
	 * @param bool $isTopic        	
	 * @return array
	 */
	public static function getChannelTree($model, $isTopic = false, $all = false) {
		$where ['deleted'] = 0;
		if (! is_null ( $isTopic )) {
			$where ['is_topic_channel'] = $isTopic ? 1 : 0;
		}
		$channels = dbselect ( 'name,upid,id,hidden' )->from ( '{weixin_menu}' );
		$channels->where ( $where )->asc ( 'sort' );
		$options = array ('' => '请选择栏目' );
		$channels = $channels->toArray ();
		if ($all) {
			WeixinMenuForm::get_channel_tree_all_options ( $options, $channels, $model, 0, 0 );
		} else {
			WeixinMenuForm::get_channel_tree_options ( $options, $channels, $model, 0, 0 );
		}
		return $options;
	}
}