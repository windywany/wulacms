<?php
/**
 * 基部导航菜单.
 * @author Guangfeng
 *
 */
class AdminNaviMenu implements Renderable {
	private $id;
	private $name;
	private $icon;
	private $href;
	private $title;
	private $ajaxOptions = array ();
	private $items = array ();
	private $count = 0;
	/**
	 * 创建一个菜单项.
	 *
	 * @param string $id        	
	 * @param string $name        	
	 * @param string $icon        	
	 * @param string $href        	
	 * @param string $title        	
	 */
	public function __construct($id, $name, $icon, $href = '#', $title = '', $ajaxOption = array()) {
		$this->id = $id;
		$this->name = $name;
		$this->icon = $icon;
		$this->href = $href;
		$this->title = empty ( $title ) ? $this->name : $title;
		$this->ajaxOptions = $ajaxOption ? $ajaxOption : array ();
	}
	/**
	 *
	 * @param AdminNaviMenu $menu        	
	 */
	public function cloneit($menu) {
		$this->name = $menu->name;
		$this->icon = $menu->icon;
		$this->href = $menu->href;
		$this->title = empty ( $menu->title ) ? $this->name : $menu->title;
		$this->ajaxOptions = $menu->ajaxOption ? $menu->ajaxOption : array ();
		if ($menu->items) {
			foreach ( $menu->items as $id => $m ) {
				$this->items [$id] = $m;
			}
		}
	}
	/**
	 * 是否有子菜单.
	 * 
	 * @return bool 有返回true，反之返回false.
	 */
	public function hasSubMenu() {
		return count ( $this->items ) > 0;
	}
	public function setTipCount($count) {
		$this->count = $count;
	}
	/**
	 * 取菜单项ID。
	 *
	 * @return string
	 */
	public function getId() {
		return $this->id;
	}
	public function getMenu($id, $name = '', $icon = '', $href = '#', $title = '', $ajaxOption = array()) {
		$menu = $this->getItem ( $id );
		if ($menu == null) {
			$this->addSubmenu ( array ($id,$name,$icon,$href,$title,$ajaxOption ), false );
			$menu = $this->getItem ( $id );
			return $menu;
		} else {
			if ($name) {
				$menu->name = $name;
			}
			if ($icon) {
				$menu->icon = $icon;
			}
			if ($href) {
				$menu->href = $href;
			}
			if ($title) {
				$menu->title = $title;
			}
			if ($ajaxOption) {
				$menu->ajaxOptions = $ajaxOption;
			}
			return $menu;
		}
	}
	/**
	 * 添加菜单项.
	 *
	 * @param AdminNaviMenu $item        	
	 */
	public function addItem($item, $acl = '', $pos = 9999) {
		if ($acl === false || icando ( $acl )) {
			if ($item instanceof AdminNaviMenu) {
				$id = $item->getId ();
				$this->items [$id] = array ('pos' => $pos,'menu' => $item );
			}
		}
	}
	/**
	 * 快捷添加子菜单项.
	 *
	 * @param array $menu
	 *        	array(id, name, icon, href, title)
	 * @param number $pos        	
	 */
	public function addSubmenu($menu, $acl = '', $pos = 9999) {
		list ( $id, $name, $icon, $href, $title, $ajaxOption ) = array_pad ( $menu, 6, '' );
		$item = new AdminNaviMenu ( $id, $name, $icon, $href, $title, $ajaxOption );
		$this->addItem ( $item, $acl, $pos );
	}
	/**
	 * 获取菜单项。
	 *
	 * @param string $id        	
	 * @return AdminNaviMenu 菜单实例或null.
	 */
	public function getItem($id) {
		if (isset ( $this->items [$id] )) {
			return $this->items [$id] ['menu'];
		}
		return null;
	}
	/**
	 * 绘制菜单项.
	 *
	 * @see Renderable::render()
	 */
	public function render($level = 0) {
		$html = array ();
		$html [] = '<li>';
		$opts = array ();
		if (! empty ( $this->ajaxOptions )) {
			foreach ( $this->ajaxOptions as $name => $v ) {
				$opts [] = $name . '="' . $v . '"';
			}
		}
		$html [] = '<a href="' . $this->href . '" ' . implode ( ' ', $opts ) . '>';
		if ($this->icon) {
			if ($level == 0) {
				$html [] = '<i class="fa fa-lg fa-fw ' . $this->icon . '">';
				if ($this->count > 0) {
					$html [] = '<em>' . $this->count . '</em>';
				}
			} else {
				$html [] = '<i class="fa fa-fw ' . $this->icon . '">';
			}
			$html [] = '</i> ';
		}
		if ($level == 0) {
			$html [] = '<span class="menu-item-parent">' . $this->name . '</span>';
		} else {
			$html [] = $this->name;
		}
		$html [] = '</a>';
		$level += 1;
		if ($this->items) {
			$html [] = '<ul>';
			usort ( $this->items, ArrayComparer::compare ( 'pos' ) );
			foreach ( $this->items as $item ) {
				$html [] = $item ['menu']->render ( $level );
			}
			$html [] = '</ul>';
		}
		$html [] = '</li>';
		return implode ( '', $html );
	}
}
