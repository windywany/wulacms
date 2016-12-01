<?php
namespace db\model;
/**
 * 关联表单数据库模型基类.
 *
 * @author  Leo Ning.
 * @package db\model
 */
abstract class FormModel extends Model {
	private $form = null;

	public function __construct(&$data = null, $dialect = null) {
		parent::__construct($dialect);
		$this->form = $this->createForm($data);
		if ($this->form) {
			if ($data) {
				$data['formName'] = $this->form->getName();
				$data['rules']    = $this->form->rules();
			}
			foreach ($this->form as $key => $field) {
				$rules = $field->getValidateRule();
				if ($rules) {
					$this->setValidateRule($key, $rules);
				}
			}
		}
	}

	/**
	 * 获取本模型关联的表单.
	 *
	 * @return \AbstractForm
	 */
	public function getForm() {
		return $this->form;
	}

	/**
	 * 创建与本模型关联的表单实例.
	 *
	 * @param array $data 表单数据.
	 *
	 * @return \AbstractForm 表单实例.
	 */
	abstract protected function createForm($data = []);
}