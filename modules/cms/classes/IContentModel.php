<?php
interface IContentModel {
	/**
	 * 保存当前内容.
	 *
	 * @param array $data        	
	 */
	public function save($data, $form);
	/**
	 * 加载内容(用于编辑时)
	 *
	 * @param array $data
	 *        	data.
	 * @param int $id
	 *        	page id.
	 */
	public function load(&$data, $id);
	public function delete($id);
	/**
	 * 用于搜索时.
	 *
	 * @param Query $query
	 *        	query.
	 * @param array $where
	 *        	condition.
	 * @param string $sort
	 *        	field.
	 * @param string $order
	 *        	sort order.
	 */
	public function buildQuery(&$query, $where, $sort, $order);
	/**
	 * 验证(编辑表单).
	 *
	 * @return AbstractForm .
	 */
	public function getForm();
	/**
	 * 获取分页.
	 * @param array $page
	 */
	public function getPages($page);
	/**
	 * 取可搜索字段列表
	 * 
	 * @param array $fields.        	
	 * @return array 可搜索的字段列表,每个字段必须包括以下内容(widget,defaults,label,name,default,cstore,sort).
	 */
	public function getSearchFields($fields);
}