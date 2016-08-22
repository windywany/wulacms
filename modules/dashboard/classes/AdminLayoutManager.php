<?php
/**
 * 后台界面布局管理器.
 * @author Guangfeng
 *
 */
class AdminLayoutManager {
	private $menus = array ();
	private $scripts = array ();
	private $styles = array ();
	private $linkes = array ();
	private $linkesGroup = array ();
	/**
	 * 绘制导航菜单.
	 *
	 * @return string
	 */
	public function renderNaviMenu() {
		$html = array ();
		usort ( $this->menus, ArrayComparer::compare ( 'pos' ) );
		foreach ( $this->menus as $menu ) {
			$html [] = $menu ['menu']->render ();
		}
		return implode ( '', $html );
	}
	/**
	 * 获取导航菜单.
	 *
	 * @param string $id        	
	 * @return AdminNaviMenu
	 */
	public function getNaviMenu($id) {
		if (isset ( $this->menus [$id] )) {
			return $this->menus [$id] ['menu'];
		} else {
			$menu = new AdminNaviMenu ( $id, '', '' );
			$this->menus [$id] = array ('pos' => 9999,'menu' => $menu );
			return $this->menus [$id] ['menu'];
		}
	}
	/**
	 * 添加一个顶级菜单.
	 *
	 * @param AdminNaviMenu $menu        	
	 */
	public function addNaviMenu($menu, $pos = 9999) {
		$id = $menu->getId ();
		if (isset ( $this->menus [$id] )) {
			$menu1 = $this->menus [$id] ['menu'];
			$menu1->cloneit ( $menu );
		} else {
			$menu1 = $menu;
		}
		$this->menus [$id] = array ('pos' => $pos,'menu' => $menu1 );
	}
	/**
	 * 绘制链接.
	 *
	 * @param string $type        	
	 * @return string
	 */
	public function renderLink($type = 'model') {
		$html = array ();
		if (isset ( $this->linkesGroup [$type] )) {
			$linkTypes = apply_filter ( 'get_' . $type . '_link_groups', array ('topic' => '专题','page' => '文章' ) );
			foreach ( $this->linkesGroup [$type] as $role => $links ) {
				if ($links && isset ( $linkTypes [$role] )) {
					$html [] = '<li class="dropdown-submenu"><a href="javascript:void(0);" tabindex="-1">' . $linkTypes [$role] . '</a>';
					$html [] = '<ul class="dropdown-menu">';
					$this->render_links ( $html, $links );
					$html [] = '</ul></li>';
				}
			}
		}
		if (isset ( $this->linkes [$type] )) {
			if ($html) {
				$html [] = '<li class="divider"></li>';
			}
			$this->render_links ( $html, $this->linkes [$type] );
		}
		return implode ( '', $html );
	}
	public function hasLinkes($type){
		return isset ( $this->linkesGroup [$type] ) || isset ( $this->linkes [$type] );
	}
	/**
	 * 添加一个内容快速新建接口.
	 *
	 * @param string $name
	 *        	内容模型名称.
	 * @param string $href
	 *        	新建表单APP.
	 * @param string $icon
	 *        	ICon.
	 * @param string $title
	 *        	标题栏上的标题.
	 */
	public function addModelLink($name, $href, $icon = '', $title = '', $group = '') {
		if ($group) {
			$this->linkesGroup ['model'] [$group] [] = array ($name,$href,$icon,$title );
		} else {
			$this->linkes ['model'] [] = array ($name,$href,$icon,$title );
		}
	}
	public function addDivider($model) {
		$this->linkes [$model] [] = array ('divider' => true );
	}
	/**
	 * 添加一个用户管理接口.
	 *
	 * @param string $name
	 *        	操作名称.
	 * @param string $href
	 *        	页面APP.
	 * @param string $icon
	 *        	ICon.
	 * @param string $title
	 *        	标题栏上的标题.
	 */
	public function addUserProfileLink($name, $href, $icon = '', $title = '') {
		$this->linkes ['user'] [] = array ($name,$href,$icon,$title );
	}
	private function render_links(&$html, $links) {
		foreach ( $links as $link ) {
			if (isset ( $link ['divider'] )) {
				$html [] = '<li class="divider"></li>';
			} else {
				$html [] = '<li>';
				$html [] = '<a href="#' . $link [1] . '" title="' . ($link [3] ? $link [3] : $link [0]) . '">';
				if ($link [2]) {
					$html [] = '<i class="fa fa-fw ' . $link [2] . '"></i> ';
				}
				$html [] = $link [0] . '</a>';
				$html [] = '</li>';
			}
		}
	}
}
