<?php
class NewLineDataProvidor implements IFieldWidgetDataProvidor {
	private $options;
	public function getData($option = false) {
		$options = $this->options;
		$datas = array ();
		if (is_callable ( $options )) {
			$options = call_user_func_array ( $options, array () );
		}
		if (is_array ( $options )) {
			$datas = $options;
		} else if ($options) {
			$data = explode ( "\n", $options );
			foreach ( $data as $defaut ) {
				list ( $key, $d ) = explode ( '=', $defaut );
				$datas [$key] = $d;
			}
		}		
		return $datas;
	}
	public function getOptionsFormat() {
		return 'value=label 多个值以换行分隔.';
	}
	public function setOptions($options) {
		$this->options = $options;
	}
}