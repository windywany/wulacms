<?php
abstract class CustomPreferenceForm extends AbstractForm {
	/*
	 * (non-PHPdoc) @see AbstractForm::init_form_fields()
	 */
	protected function init_form_fields($data, $value_set) {
		if (isset ( $this->__cfg_group )) {
			$fields = dbselect ( 'value' )->from ( '{preferences}' )->where ( array ('name' => 'custom_fields','preference_group' => $this->__cfg_group ) )->get ( 'value' );
			if ($fields) {
				$fields = @unserialize ( $fields );
			}
			if ($fields) {
				usort ( $fields, ArrayComparer::compare ( 'sort' ) );
				foreach ( $fields as $d ) {
					$de = array ();
					if ($d ['group'] && $d ['col']) {
						$de ['group'] = $d ['group'];
						$de ['col'] = $d ['col'];
					}
					$de ['label'] = $d ['label'];
					$de ['widget'] = $d ['type'];
					$de ['defaults'] = $d ['defaults'];
					$de ['id'] = $d ['name'];
					$fn = $d ['name'];
					$this->addField ( $fn, $de );
				}
			}
		}
	}
}