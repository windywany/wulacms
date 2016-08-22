<?php
class HtmlTagElm extends NamedArray implements Renderable {
	private $tag;
	private $text;
	private $text1;
	private $id;
	private $children = array ();
	private $parent;
	/**
	 * 创建一个{@link HtmlTagElm}实例.
	 *
	 * @param string $name        	
	 * @param array $attrs        	
	 */
	public function __construct($name, $attrs = array()) {
		$this->tag = $name;
		$this->attrs = $attrs;
	}
	/**
	 * 设置id.
	 *
	 * @param string $id        	
	 * @return HtmlTagElm
	 */
	public function id($id) {
		$this->attrs ['id'] = $id;
		$this->id = $id;
		return $this;
	}
	/**
	 * 设置类.
	 *
	 * @param string $class        	
	 * @return HtmlTagElm
	 */
	public function cls($class) {
		$this->attrs ['class'] = $class;
		return $this;
	}
	/**
	 * 设置文本.
	 *
	 * @param string $text        	
	 * @return HtmlTagElm
	 */
	public function text($text, $append = false) {
		if ($append) {
			$this->text1 = $text;
		} else {
			$this->text = $text;
		}
		return $this;
	}
	/**
	 * 删除一个字元素.
	 *
	 * @param string $index        	
	 * @return HtmlTagElm
	 */
	public function remove($index) {
		if (isset ( $this->children [$index] )) {
			unset ( $this->children [$index] );
		}
		return $this;
	}
	/**
	 * 查找一个子元素.
	 *
	 * @param string $index        	
	 * @return HtmlTagElm
	 */
	public function find($index) {
		if (isset ( $this->children [$index] )) {
			return $this->children [$index];
		}
		return null;
	}
	/**
	 * 添加一个子元素.
	 *
	 * @param HtmlTagElm $tag        	
	 * @param string $index        	
	 * @return HtmlTagElm
	 */
	public function child($tag, $index = null) {
		if ($index) {
			$this->children [$index] = $tag;
		} else {
			$this->children [] = $tag;
		}
		if ($tag instanceof HtmlTagElm) {
			$tag->ff ( $this );
		}
		return $this;
	}
	/**
	 * 设置或获取父元素.
	 *
	 * @param HtmlTagElm $parent        	
	 * @return HtmlTagElm
	 */
	public function ff($parent = null) {
		if (is_null ( $parent )) {
			return $this->parent;
		} else {
			$this->parent = $parent;
			return $this;
		}
	}
	/**
	 * 将一个元素放到所有最前边.
	 *
	 * @param HtmlTagElm $tag        	
	 * @return HtmlTagElm
	 */
	public function unshift($tag) {
		array_unshift ( $this->children, $tag );
		return $this;
	}
	public function render() {
		$html [] = '<' . $this->tag;
		if ($this->attrs) {
			$attrs = array ();
			foreach ( $this->attrs as $attr => $v ) {
				if ($v != null) {
					$attrs [] = $attr . '="' . $v . '"';
				}
			}
			if ($attrs) {
				$html [] = ' ';
				$html [] = implode ( ' ', $attrs );
			}
		}
		$html [] = '>';
		if ($this->text) {
			$html [] = $this->text;
		}
		if ($this->children) {
			foreach ( $this->children as $child ) {
				if ($child instanceof Renderable) {
					$html [] = $child->render ();
				} else {
					$html [] = $child;
				}
			}
		}
		if ($this->text1) {
			$html [] = $this->text1;
		}
		$html [] = '</' . $this->tag . '>';
		return implode ( '', $html );
	}
}