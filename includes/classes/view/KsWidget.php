<?php
class KsWidget implements Renderable {
	protected $id;
	protected $name;
	protected $data;
	protected $view;
	protected $hidden;
	protected $order = 999;
	protected $position;
	protected $system = false;
	protected $renderForm = null;
	protected $layout;
	public function __construct($id, $name, $sort = 999, $system = false) {
		$this->id = $id;
		$this->name = $name;
		$this->order = $sort;
		$this->system = $system;
	}
	public function render() {
		$view = null;
		if ($this->view instanceof KsWidgetView) {
			$view = $this->view->getView ();
			$this->view->setParent ( $this->layout );
		}
		if ($view instanceof View) {
			$data = array_merge ( $this->view->getOptions (), $this->layout->getData () );
			$data ['widget_data'] = false;
			if ($this->data instanceof KsDataProvidor) {
				if ($this->view->supportDataType () == $this->data->getDataType ()) {
					$data ['widget_data'] = $this->data->getData ();
				}
			}
			$data ['viewCls'] = $this->view;
			$view->assign ( $data );
			return $view->render ();
		}
		return '<-- no view -->';
	}
	public function getConfigFormRender() {
		if ($this->renderForm == null) {
			$form = new DynamicForm ( null );
			$dps = KsWidgetContainer::getSupportedDataProvidors ();
			$url = tourl ( 'system/layout/views' );
			$form ['wid'] = array ('widget' => 'hidden' );
			$form ['pos'] = array ('widget' => 'hidden' );
			$form ['name'] = array ('id' => $this->id . '-name','label' => '部件名称','group' => 0,'col' => '3s' );
			$form ['datacls'] = array ('id' => $this->id . '-datacls','label' => '数据源','group' => '0','col' => 3,'widget' => 'select','defaults' => $dps );
			$form ['viewcls'] = array ('label' => '展示视图','group' => '0','col' => 3,'widget' => 'combox','defaults' => '{"parent":"' . $this->id . '-datacls","url":"' . $url . '"}' );
			$form ['hidden'] = array ('id' => $this->id . '-hidden','label' => '隐藏小部件','group' => 0,'col' => 2,'widget' => 'radio','default' => '0','defaults' => "1=是\n0=否" );
			$form ['sort'] = array ('id' => $this->id . '-sort','label' => '排序','group' => 0,'col' => '1' );
			$data ['hidden'] = $this->isHidden () ? 1 : 0;
			$data ['name'] = $this->name;
			$data ['wid'] = $this->id;
			$data ['sort'] = $this->order;
			$data ['pos'] = $this->position;
			if ($this->data) {
				$data ['datacls'] = get_class ( $this->data );
				$this->data->getConfigFields ( $form );
				$data = array_merge ( $data, $this->data->getOptions () );
			}
			if ($this->view) {
				$vcls = get_class ( $this->view );
				$data ['viewcls'] = $vcls . ':' . $this->view->getName ();
				$this->view->getConfigFields ( $form );
				$data = array_merge ( $data, $this->view->getOptions () );
			}
			$this->renderForm = new DefaultFormRender ( $form->buildWidgets ( $data ) );
		}
		return $this->renderForm;
	}
	
	/**
	 *
	 * @param View $layout        	
	 */
	public function addResources(&$layout) {
		if ($this->view && $this->view->getView () instanceof View) {
			$view = $this->view->getView ();
			$layout->addStyle ( $view->getStyles () );
			$layout->addScript ( $view->getScripts ( 'foot' ), 'foot' );
			$layout->addScript ( $view->getScripts ( 'head' ), 'head' );
			$this->layout = $layout;
		}
	}
	public function hide() {
		$this->hidden = true;
	}
	public function show() {
		$this->hidden = false;
	}
	public function setOrder($order) {
		$this->order = $order;
	}
	
	/**
	 *
	 * @return the $position
	 */
	public function getPosition() {
		return $this->position;
	}
	
	/**
	 *
	 * @param field_type $position        	
	 */
	public function setPosition($position) {
		$this->position = $position;
	}
	public function getOrder() {
		return $this->order;
	}
	public function isHidden() {
		return $this->hidden;
	}
	/**
	 * 编号.
	 */
	public function getId() {
		return $this->id;
	}
	/**
	 * 名称.
	 */
	public function getName() {
		return $this->name;
	}
	/**
	 * 设置数据源.
	 *
	 * @param KsDataProvidor $data        	
	 */
	public function setData($data) {
		$this->data = $data;
	}
	/**
	 *
	 * @param KsWidgetView $view        	
	 */
	public function setView($view = null) {
		$this->view = $view;
	}
	/**
	 *
	 * @return the $data
	 */
	public function getData() {
		return $this->data;
	}
	
	/**
	 *
	 * @return the $view
	 */
	public function getView() {
		return $this->view;
	}
	/**
	 *
	 * @return the $system
	 */
	public function isSystem() {
		return $this->system;
	}
	
	/**
	 *
	 * @param boolean $system        	
	 */
	public function setSystem($system) {
		$this->system = $system;
	}
}