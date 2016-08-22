<?php
class DefaultContentModel implements IContentModel {
	private $md;
	public function __construct($modelDefinition = null) {
		$this->md = $modelDefinition;
		$this->md ['create_time'] = $this->md ['update_time'] = time ();
		$this->md ['create_uid'] = $this->md ['update_uid'] = 0;
		$this->md ['search_page_limit'] = 15;
	}
	public final function install($dialect, $md = null) {
		if ($md) {
			$this->md = $md;
		}
		if ($dialect && $this->md) {
			$form = new ModelForm ( $this->md );
			if ($form->valid ( false )) {
				$rst = dbinsert ( $this->md )->into ( '{cms_model}' )->exec ();
				if ($rst) {
					return $rst [0];
				}
			}
		}
		return false;
	}
	public final function uninstall($name) {
		if (! is_array ( $name )) {
			$name = array ($name );
		}
		dbdelete ()->from ( '{cms_model}' )->where ( array ('refid IN' => $name ) )->exec ();
		dbdelete ()->from ( '{cms_model_field}' )->where ( array ('model IN' => $name ) )->exec ();
		dbdelete ()->from ( '{cms_page}' )->where ( array ('model IN' => $name ) )->exec ();
		return true;
	}
	public final function addField($dialect, $field) {
	}
	public function buildQuery(&$query, $where, $sort, $order) {
	}
	public function save($data, $form) {
	}
	public function delete($id) {
	}
	public function getPages($page){
		return null;
	}
	/**
	 * (non-PHPdoc)
	 *
	 * @see IContentModel::load()
	 */
	public function load(&$data, $id) {
	}
	/**
	 * (non-PHPdoc)
	 *
	 * @see IContentModel::getForm()
	 */
	public function getForm() {
		return null;
	}
	/**
	 * (non-PHPdoc)
	 *
	 * @see IContentModel::getSearchFields()
	 */
	public function getSearchFields($fields) {
		return $fields;
	}
}