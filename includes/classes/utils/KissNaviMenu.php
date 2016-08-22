<?php
/**
 * 无限层级菜单基类.
 * @author leo
 *
 */
abstract class KissNaviMenu implements Renderable {
	protected $items = array ();
	private $position = array ();
	/**
	 * 创建一个菜单并得到NamedArray表示的菜单数据结构.
	 *
	 * @param string $id
	 *        	ID，多级菜单用类似url的结构表单.
	 * @param string $name
	 *        	菜单名称.
	 * @param string $url
	 *        	菜单的URL.
	 * @param string $icon
	 *        	ICON.
	 * @param integer $pos
	 *        	位置.
	 * @return NamedArray 一个基于NamedArray的菜单数据实例.
	 */
	public final function create($id, $name, $url = false, $icon = '', $pos = null) {
		$menu = $this->get ( $id );
		if ($menu) {
			$menu ['name'] = $name;
			$menu ['url'] = $url;
			$menu ['icon'] = $icon;
			$menu ['pos'] = is_int ( $pos ) ? $pos : $this->getPos ( $id );
		}
		return $menu;
	}
	/**
	 * 删除一个菜单.
	 *
	 * @param string $id
	 *        	菜单ID.
	 */
	public final function remove($id) {
		if (empty ( $id )) {
			return;
		}
		$ids = explode ( '/', $id );
		$lid = $ids [count ( $ids ) - 1];
		$items = $this->items;
		foreach ( $ids as $id ) {
			if (isset ( $items [$id] )) {
				if ($lid == $id) {
					unset ( $items [$id] );
					break;
				}
				$menu = $items [$id];
				$items = $menu ['items'];
			}
		}
	}
	/**
	 * 取一个{@link NamedArray}表示的菜单数据.
	 *
	 * @param string $id        	
	 * @return NamedArray 菜单.
	 */
	public final function &get($id) {
		if (empty ( $id )) {
			return null;
		}
		$ids = explode ( '/', $id );
		$items = &$this->items;
		foreach ( $ids as $id ) {
			if (isset ( $items [$id] )) {
				$menu = $items [$id];
			} else {
				$menu = nary ( array ('id' => $id,'items' => new NamedArray () ) );
				$items [$id] = $menu;
			}
			unset ( $items );
			$items = $menu->ref ( 'items' );
		}
		return $menu;
	}
	/**
	 * 取菜单项.
	 *
	 * @param NamedArray $menu
	 *        	如果为空则取顶级菜单项.
	 * @return array 菜单项列表.
	 */
	protected function getItems($menu = null) {
		if ($menu && isset ( $menu ['items'] )) {
			return $menu ['items'];
		} elseif (! $menu) {
			return $this->items;
		}
		return null;
	}
	/**
	 *	
	 * @param string $id        	
	 */
	private function getPos($id) {
		$ids = explode ( '/', $id );
		if (count ( $ids ) == 1) {
			$id = '/';
		} else {
			array_pop ( $ids );
			$id = implode ( '/', $ids );
		}
		if (! isset ( $this->position [$id] )) {
			$this->position [$id] = 1;
		}
		$this->position [$id] += 1;
		return $this->position [$id];
	}
}