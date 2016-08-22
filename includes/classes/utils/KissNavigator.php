<?php
/**
 * 无限层级导航菜单类.
 * @author leo
 *
 */
class KissNavigator implements IteratorAggregate {
	private static $emptyMenu = array ();
	private $menus = array ();
	private $pos = array ();
	/**
	 * 添加菜单.
	 *
	 * @param string $id
	 *        	用-表示子菜单.只支持二级.
	 * @param string|array $name
	 *        	菜单名或菜单项.
	 * @param string $url
	 *        	url.
	 * @param number $pos
	 *        	位置.
	 * @return NamedArray 菜单定义数组，可修改。
	 */
	public function addItem($id, $name, $url = false, $pos = null) {
		if (empty ( $id )) {
			return self::$emptyMenu;
		}
		$ids = explode ( '-', $id );
		$pid = trim ( $ids [0] );
		if (empty ( $pid )) {
			return self::$emptyMenu;
		}
		if (! isset ( $this->menus [$pid] )) {
			$this->menus [$pid] = array ();
		}
		if (is_array ( $name )) {
			$name = implode ( '', $name );
			$url = false;
		}
		if (count ( $ids ) == 2) {
			if (empty ( $ids [1] )) {
				return self::$emptyMenu;
			}
			$pos = $this->getNextPos ( $pid, $pos );
			if (! isset ( $this->menus [$pid] ['items'] )) {
				$this->menus [$pid] ['items'] = new NamedArray ();
			}
			$this->menus [$pid] ['items'] [$id] = nary ( array ('id' => $id,'name' => $name,'url' => $url,'pos' => $pos ) );
			return $this->menus [$pid] ['items'] [$id];
		} else {
			$pos = $this->getNextPos ( '-', $pos );
			$this->menus [$pid] = nary ( array_merge ( $this->menus [$pid], array ('id' => $id,'name' => $name,'url' => $url,'pos' => $pos ) ) );
			return $this->menus [$pid];
		}
	}
	/**
	 * 删除一个菜单。
	 *
	 * @param string $id
	 *        	要删除的菜单编号.
	 */
	public function removeItem($id) {
		if (empty ( $id )) {
			return;
		}
		$ids = explode ( '-', $id );
		if (count ( $ids ) == 2) {
			unset ( $this->menus [$ids [0]] [$id] );
		} else {
			unset ( $this->menus [$ids [0]] );
		}
	}
	/**
	 * 取对应编号的菜单项。
	 *
	 * @param string $id
	 *        	菜单编号.
	 * @return array 菜单定义数组.
	 */
	public function getItem($id) {
		if (empty ( $id )) {
			return array ();
		}
		$ids = explode ( '-', $id );
		if (count ( $ids ) == 2) {
			return $this->menus [$ids [0]]['items'] [$id];
		} else {
			return $this->menus [$ids [0]];
		}
	}
	public function getIterator() {
		if ($this->menus) {
			usort ( $this->menus, ArrayComparer::compare ( 'pos' ) );
			foreach ( $this->menus as $id => $menu ) {
				$menu = $menu->toArray ();
				if (isset ( $menu ['items'] )) {
					$menu ['items'] = $menu ['items']->toArray ();
					usort ( $menu ['items'], ArrayComparer::compare ( 'pos' ) );
					foreach ( $menu ['items'] as $id1 => $item ) {
						$item = $item->toArray ();
						$menu ['items'] [$id1] = $item;
					}
				}
				$this->menus [$id] = $menu;
			}
		}
		return new ArrayIterator ( $this->menus );
	}
	
	private function getNextPos($id, $pos) {
		if (! $pos) {
			$pos = isset ( $this->pos [$id] ) ? $this->pos [$id] : 1;
			$pos += 1;
			$this->pos [$id] = $pos;
		}
		return $pos;
	}
}