<?php
class AutoCompleteWidget implements IFieldWidget, IFieldWidgetDataProvidor {
	private $options;
	public static function get_custom_field_widgets($widgets) {
		$widgets->register ( new AutoCompleteWidget () );
		$widgets->register ( new TplFieldWidget () );
		$widgets->register ( new TreeViewWidget () );
		$widgets->register ( new TagAutoWidget () );
	}
	/**
	 *
	 * @param Query $where        	
	 * @return unknown
	 */
	public static function on_init_autocomplete_condition_user($where) {
		$where->where ( array ('ATABLE.deleted' => 0 ), true );
		return $where;
	}
	public function getType() {
		return 'auto';
	}
	public function getName() {
		return '自动完成';
	}
	public function render($definition, $cls = '') {
		$url = tourl ( 'system/ajax/autocomplete' );
		$id = $definition ['id'];
		$name = $definition ['name'];
		$type = $definition ['userType'];
		$values = $this->getDataProvidor ( $definition ['defaults'] )->getData ();
		list ( $table, $idx, $text, $acl, $utype, $plugin ) = $values;
		if ($table) {
			$attr = '';
			if ($values [6]) {
				$num = count ( $values );
				for($i = 6; $i < $num; $i ++) {
					$attr .= $values [$i];
					unset ( $values [$i] );
				}
			}
			
			if ($acl) {
				$values [3] = str_replace ( '/', '.', $acl );
			}
			if ($utype) {
				unset ( $values [4] );
			}
			if ($plugin) {
				unset ( $values [5] );
			}
			$v = $definition['value'];
			if($v && strpos($v,':')===false){
				$v = $this->getValues($v,$table,$idx,$text);
			}
			$url .= untrailingslashit ( implode ( '/', $values ) ) . '/?_ut=' . ($utype ? $utype : $type) . '&_up=' . ($plugin ? $plugin : '') . $attr;
			$readonly = isset ( $definition ['readonly'] ) ? ' readonly="readonly" ' : '';
			$disabled = isset ( $definition ['disabled'] ) ? ' disabled="disabled" ' : '';
			$placeholder = isset ( $definition ['placeholder'] ) ? ' placeholder="' . $definition ['placeholder'] . '"' : '';
			return '<label class="input" for="' . $id . '">
											<input type="hidden"
											data-widget="nuiCombox"
											style="width:100%"
											data-source="' . $url . '"
											name="' . $name . '" id="' . $id . '" value="' . $v. '"' . $readonly . $disabled . $placeholder . '/>
										</label>';
		} else {
			return '';
		}
	}
	/*
	 * (non-PHPdoc) @see IFieldWidget::getDataProvidor()
	 */
	public function getDataProvidor($options) {
		$this->setOptions ( $options );
		return $this;
	}
	
	/*
	 * (non-PHPdoc) @see IFieldWidgetDataProvidor::getData()
	 */
	public function getData($option = false) {
		if (! $option) {
			$values = explode ( ',', $this->options );
			return array_pad ( $values, 6, false );
		} else {
			return array ();
		}
	}
	
	/*
	 * (non-PHPdoc) @see IFieldWidgetDataProvidor::getOptionsFormat()
	 */
	public function getOptionsFormat() {
		return '格式: table(表名),id_field(ID字段),text_field(文本字段),acl[,type(用户类型),[plugin(调用接口)]]';
	}
	
	/*
	 * (non-PHPdoc) @see IFieldWidgetDataProvidor::setOptions()
	 */
	public function setOptions($options) {
		$this->options = $options;
	}
	private function getValues($v,$table,$idx,$text){
		$vs = safe_ids2($v);
		$items = dbselect($idx,$text)->from('{'.$table.'}')->where([$idx.' IN'=>$vs])->toArray();
		$values=[];
		foreach ($items as $item) {
			$values[]= $item[$idx].':'. html_escape($item[$text]);
		}
		return implode(',',$values);
	}
}