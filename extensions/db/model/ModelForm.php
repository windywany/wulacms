<?php

namespace db\model;

abstract class ModelForm extends \AbstractForm {
	/**
	 * 取本表单对应的模型.
	 *
	 * @uses {@link createModel}
	 * @param
	 *        	\DatabaseDialect 数据库实例.
	 * @return Model 本表单关联的模型.
	 */
	public function getModel($dialect = null) {
		$model = $this->createModel ( $dialect );
		if ($model instanceof Model) {
			foreach ( $this->__form_fields as $key => $field ) {
				$rules = $field->getValidateRule ();
				if ($rules) {
					$model->setValidateRule ( $key, $rules );
				}
			}
			return $model;
		}
		trigger_error ( 'invalide model', E_USER_ERROR );
	}
	/**
	 * 创建模型实例.
	 *
	 * @param \DatabaseDialect $dialect
	 *        	数据库实例.
	 * @return Model 模型实例.
	 */
	protected abstract function createModel($dialect);
}

