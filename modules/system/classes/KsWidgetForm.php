<?php
class KsWidgetForm extends AbstractForm {
	private $page = array ('widget' => 'hidden','rules' => array ('required' => '页面不能为空' ) );
	public function init_form_fields($data, $value_set) {
		$dps = KsWidgetContainer::getSupportedDataProvidors ();
		$url = tourl ( 'system/layout/views' );
		$this ['datacls'] = array ('label' => '数据源','group' => '0','col' => 3,'widget' => 'select','defaults' => $dps,'rules' => array ('required' => '请选择数据源' ) );
		$this ['viewcls'] = array ('label' => '展示视图','group' => '0','col' => 3,'widget' => 'combox','defaults' => '{"parent":"datacls","url":"' . $url . '"}','rules' => array ('required' => '请选择视图' ) );
		$this ['pos'] = array ('label' => '添加到','group' => 0,'col' => 2,'widget' => 'select','defaults' => $data ['positions'],'rules' => array ('required' => '请选择位置' ) );
		$this ['name'] = array ('label' => '部件名称','group' => 0,'col' => '3','rules' => array ('required' => '请填写部件名称' ) );
		$this ['_sp'] = array ('skip' => 1,'label' => '&nbsp;','group' => 0,'col' => '1','widget' => 'htmltag','defaults' => '<button class="btn btn-sm btn-primary" type="submit">添加</button>' );		
	}
}