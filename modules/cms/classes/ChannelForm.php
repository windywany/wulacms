<?php
/**
 * 栏目表单.
 * @author Guangfeng
 *
 */
class ChannelForm extends AbstractForm {
	private $id = array ('rules' => array ('regexp(/^[0-9]+$/)' => '非法的编号.' ) );
	private $gid = array ('rules' => array ('regexp(/^[0-9]+$/)' => '非法的编号.' ) );
	private $is_topic_channel;
	private $list_page;
	private $index_page;
	private $upid = array ('rules' => array ('regexp(/^(0|[1-9]\d*)$/)' => '请正确填写上级栏目ID' ),'type' => 'int' );
	private $default_model = array ('rules' => array ('required' => '请选择内容模型' ) );
	private $name = array ('rules' => array ('required' => '请填写名称' ) );
	private $refid = array ('rules' => array ('required' => '请识别ID','regexp(/^[a-z][a-z0-9_]*$/i)' => '识别ID只能是字母,数字和下划线的组合.','callback(@checkRefId,id)' => '已经存在' ) );
	private $basedir = array ('rules' => array ('regexp(/^[\/a-z_0-9]*[a-z0-9]$/i)' => '目录名格式不正确.' ) );
	private $isfinal;
	private $default_template = array ('rules' => array ('required' => '请填写文章页模板','regexp(/^[\/a-z_0-9]+\.tpl$/i)' => '文件名格式不正确.' ) );
	private $default_url_pattern = array ('rules' => array ('required' => '请填写文章页命名规则','regexp(/^[\/\{\}a-z_0-9]+\.s?html$/i)' => '文件名格式不正确.' ) );
	private $index_page_tpl = array ('rules' => array ('required' => '请填写模板','regexp(/^[\/a-z_0-9]+\.tpl$/i)' => '文件名格式不正确.' ) );
	private $page_name = array ('rules' => array ('required' => '请填写页面名称','regexp(/^[a-z_0-9\/\{\}]+\.s?html$/i)' => '文件名格式不正确.' ) );
	private $list_page_tpl = array ('rules' => array ('required' => '请填写模板','regexp(/^[\/a-z_0-9]+\.tpl$/i)' => '文件名格式不正确.' ) );
	private $list_page_name = array ('rules' => array ('required' => '请填写命名规则','regexp(/^[\/\{\}a-z_0-9]+\.s?html$/i)' => '文件名格式不正确.' ) );
	private $title;
	private $keywords;
	private $description;
	private $hidden;
	private $catalog = array ('type' => 'bool' );
	private $sort = array ('rules' => array ('regexp(/^[0-9]{1,3}$/)' => '请填写正确的序号(0-999).' ) );
	private $page_cache = array ('rules' => array ('regexp(/^(\-1|0|[1-9]\d*)$/)' => '请填写正确的缓存时间.' ) );
	private $default_cache = array ('rules' => array ('regexp(/^(\-1|0|[1-9]\d*)$/)' => '请填写正确的缓存时间.' ) );
	private $list_cache = array ('rules' => array ('regexp(/^(\-1|0|[1-9]\d*)$/)' => '请填写正确的缓存时间.' ) );
	/**
	 * 检测ID是否重复.
	 *
	 * @param string $value        	
	 * @param array $data        	
	 * @param string $message        	
	 * @return Ambigous <string, string, unknown>|boolean
	 */
	public function checkRefId($value, $data, $message) {
		$rst = dbselect ( 'id' )->from ( '{cms_channel}' );
		$where ['refid'] = $value;
		if (! empty ( $data ['id'] )) {
			$where ['id !='] = $data ['id'];
		}
		$rst->where ( $where );
		if ($rst->count ( 'id' ) > 0) {
			return $message;
		}
		return true;
	}
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
						ChannelForm::get_channel_tree_options ( $options, $data, $model, $c ['id'], $level + 1 );
					}
				} else if (count ( $data ) > 0) {
					ChannelForm::get_channel_tree_options ( $options, $data, $model, $c ['id'], $level );
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
						ChannelForm::get_channel_tree_all_options ( $options, $data, $model, $c ['id'], $level + 1 );
					}
				} else if (count ( $data ) > 0) {
					ChannelForm::get_channel_tree_all_options ( $options, $data, $model, $c ['id'], $level );
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
		$channels = dbselect ( 'refid,name,upid,id,default_model,isfinal,hidden' )->from ( '{cms_channel}' );
		$channels->where ( $where )->asc ( 'sort' );
		$options = array ('' => '请选择栏目' );
		$channels = $channels->toArray ();
		if ($all) {
			ChannelForm::get_channel_tree_all_options ( $options, $channels, $model, 0, 0 );
		} else {
			ChannelForm::get_channel_tree_options ( $options, $channels, $model, 0, 0 );
		}
		if (count ( $options ) == 2) {
			array_shift ( $options );
		}
		return $options;
	}
}