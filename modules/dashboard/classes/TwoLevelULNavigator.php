<?php
class TwoLevelULNavigator extends KsWidgetView {
	public function getName() {
		return '基于ul标签的二级菜单';
	}
	public function supportDataType() {
		return KsDataProvidor::ARY;
	}
	public function getView() {
		return view ( 'dashboard/views/widgets/gnavi.tpl' );
	}
	public function getTitle() {
		$data = $this->options;
		$title = $this->get ( 'title' );
		if ($title) {
			$html = array ();
			$url = $this->get ( 'title_url' );
			if ($url) {
				$html [] = '<a href="' . $url . '" title="' . $title . '">';
				$html [] = $title;
				$html [] = '</a>';
			} else {
				$html [] = $title;
			}
			$title = implode ( '', $html );
			$wrap = $this->get ( 'title_wrap', '@title@' );
			if ($wrap) {
				$title = str_replace ( '@title@', $title, $wrap );
			}
		}
		return $title;
	}
	public function activeCls($id, $level) {
		if (is_array ( $this->parentData ['_view_actived_menu'] ) && isset ( $this->parentData ['_view_actived_menu'] [$id] )) {
			if ($level == '1') {
				$open_cls = $this->get ( 'open_cls' );
				return $open_cls . ' ' . $this->get ( 'li1_acls', 'active' );
			} else {
				return $this->get ( 'li2_acls', 'active' );
			}
		} else {
			return '';
		}
	}
	public function getTopItem($item) {
		$handle = $this->get ( 'handle' );
		$html = array ();
		if ($item ['url']) {
			$attrs = '';
			if (isset ( $item ['attrs'] )) {
				$attrs = html_tag_properties ( $item ['attrs'] );
			}
			$html [] = '<a href="' . $item ['url'] . '" ' . $attrs . '>';
			$html [] = $item ['name'];
			$html [] = '</a>';
		} else {
			$html [] = $item ['name'];
		}
		
		if (isset ( $item ['items'] ) && $handle) {
			$cls = $this->get ( 'open_cls', 'sub-open' );
			$html [] = '<' . $handle . ' class="menu-handle" onclick="$(this).parents(\'li\').toggleClass(\'' . $cls . '\')"></' . $handle . '>';
		}
		
		$html = implode ( '', $html );
		$wrap = $this->get ( 'li1_wrap', '<h6>@item@</h6>' );
		if ($wrap) {
			$html = str_replace ( '@item@', $html, $wrap );
		}
		return $html;
	}
	public function getSubItem($item) {
		$html = array ();
		if ($item ['url']) {
			$attrs = '';
			if (isset ( $item ['attrs'] )) {
				$attrs = html_tag_properties ( $item ['attrs'] );
			}
			$html [] = '<a href="' . $item ['url'] . '" ' . $attrs . '>';
			$html [] = $item ['name'];
			$html [] = '</a>';
		} else {
			$html [] = $item ['name'];
		}
		$html = implode ( '', $html );
		$wrap = $this->get ( 'li2_wrap', '@item@' );
		if ($wrap) {
			$html = str_replace ( '@item@', $html, $wrap );
		}
		return $html;
	}
	public function getConfigFields(&$fields) {
		$fields ['_sp_v'] = AbstractForm::seperator ( '视图设置' );
		
		$fields ['widget_cls'] = array ('label' => '部件类','group' => 'v','col' => 2,'default' => '' );
		$fields ['level'] = array ('label' => '启用二级','group' => 'v','col' => 3,'widget' => 'radio','defaults' => "1=是\n0=否",'default' => '1' );
		$fields ['handle'] = array ('label' => '菜单展开元素','group' => 'v','col' => 2,'default' => '' );
		$fields ['open_cls'] = array ('label' => '展开类','group' => 'v','col' => 2,'default' => 'sub-open' );
		$fields ['start_open'] = array ('label' => '默认展开','group' => 'v','col' => 3,'widget' => 'radio','defaults' => "1=是\n0=否",'default' => '1' );
		
		$fields ['title'] = array ('label' => '菜单标题','group' => 'v0','col' => 2,'default' => '' );
		$fields ['title_url'] = array ('label' => '点击标题跳转','group' => 'v0','col' => 4,'default' => '' );
		$fields ['title_wrap'] = array ('label' => '标题容器','default' => '@title@','note' => '使用@title@代表标题' );
		
		$fields ['ul1_cls'] = array ('label' => '顶级ul类','group' => 'v1','col' => 2,'default' => '' );
		$fields ['li1_cls'] = array ('label' => '顶级li类','group' => 'v1','col' => 2,'default' => '' );
		$fields ['li1_acls'] = array ('label' => '顶级选中类','group' => 'v1','col' => 2,'default' => 'active' );
		$fields ['li1_wrap'] = array ('label' => '顶级菜单项容器','default' => '@item@','note' => '使用@item@代表菜单项' );
		
		$fields ['ul2_cls'] = array ('label' => '二级ul类','group' => 'v3','col' => 2,'default' => '' );
		$fields ['li2_cls'] = array ('label' => '二级li类','group' => 'v3','col' => 2,'default' => '' );
		$fields ['li2_acls'] = array ('label' => '二级选中类','group' => 'v3','col' => 2,'default' => 'active' );
		$fields ['li2_wrap'] = array ('label' => '二级菜单项容器','default' => '@item@','note' => '使用@item@代表菜单项' );
	}
	protected function getDefaultOptions() {
		$fields ['level'] = 1;
		$fields ['title_wrap'] = '@title@';
		$fields ['li1_acls'] = 'active';
		$fields ['li1_wrap'] = '@item@';
		$fields ['li2_acls'] = 'active';
		$fields ['li2_wrap'] = '@item@';
		$fields ['open_cls'] = 'sub-open';
		$fields ['start_open'] = 1;
		return $fields;
	}
}
