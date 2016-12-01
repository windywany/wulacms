<?php

namespace args;

class ArgGroup implements \IteratorAggregate, \ArrayAccess {
	private $args         = [];
	private $n2i          = [];
	private $i2n          = [];
	private $index        = 0;
	private $values       = [];
	private $pattern      = '';
	private $titlePattern = '';
	private $titleExtraP  = false;
	private $titleSearch  = [];
	private $titleReplace = [];
	private $titles;
	private $inited       = false;

	public function __construct($pattern = '', $titles = []) {
		$this->pattern = $pattern;
		$this->titles  = $titles;
	}

	public function addSr($s, $r) {
		$this->titleSearch[]  = $s;
		$this->titleReplace[] = $r;
	}

	public function enableExtraPattern() {
		$this->titleExtraP = true;
	}

	/**
	 * 添加参数.
	 *
	 * @param string    $name 参数名.
	 * @param \args\Arg $arg
	 */
	public function addArg($name, Arg $arg) {
		$arg->setGroup($this, $this->index);
		$this->n2i[ $name ]         = $this->index;
		$this->i2n[ $this->index ]  = $name;
		$this->args[ $this->index ] = $arg;
		$this->index += 1;
	}

	/**
	 * @param $index
	 *
	 * @return \args\Arg|null
	 */
	public function getArg($index) {
		if (isset($this->args[ $index ])) {
			return $this->args[ $index ];
		}

		return new Arg();
	}

	/**
	 * @param $name
	 *
	 * @return \args\Arg|null
	 */
	public function getArgByName($name) {
		$index = $this->n2i[ $name ];

		return $this->getArg($index);
	}

	/**
	 * 初始化参数值.
	 *
	 * @param array $values
	 */
	public function initWithValues($values) {
		if ($this->inited) {
			return;
		}
		$this->values = $values;
		foreach ($this->args as $idx => $arg) {
			if (!isset($this->values[ $idx ])) {
				$this->values[ $idx ] = $arg->getDefaultValue();
			}
		}
		$s = $v = [];
		foreach ($this->values as $index => $value) {
			if (isset($this->args[ $index ])) {
				$arg = $this->args[ $index ];
				$arg->setValue($value);
				$s[]                  = $this->titleSearch[] = '{' . $this->i2n[ $index ] . '}';
				$this->titleReplace[] = $arg->getTitle($value);
				if ($value == $arg->getDefaultValue()) {
					$v[] = '0';
				} else {
					$v[] = '1';
				}

			}
		}
		if ($this->titleSearch) {
			$this->titlePattern = str_replace($s, $v, $this->pattern);
		}
		$this->inited = true;
	}

	public function getValue($index = null) {
		if (is_string($index)) {
			$index = $this->n2i[ $index ];
		} elseif (is_null($index)) {
			$index = $this->index;
		}

		return isset($this->values[ $index ]) ? $this->values[ $index ] : '0';
	}

	public function parse($base, $value, $index) {
		static $hargsName = false, $replaces = [];
		if ($hargsName === false) {
			foreach ($this->args as $k => $v) {
				$hargsName[ $k ] = '{' . $this->i2n[ $k ] . '}';
				$replaces[ $k ]  = $this->getValue($k);
			}
		}
		$replaces1 = array_merge([], $replaces);
		if (isset($this->n2i[ $index ])) {
			$index               = $this->n2i[ $index ];
			$replaces1[ $index ] = $value;
			$arg                 = $this->getArg($index);
			$arg->unsetSubargs($replaces1);
		}
		$s = $hargsName;
		$r = $replaces1;

		return preg_replace('#/+#', '/', untrailingslashit($base . '/' . str_replace($s, $r, $this->pattern)));
	}

	public function act($name, $value, $cls = 'on') {
		$cv = $this->getValue($this->n2i[ $name ]);

		return $cv == $value ? $cls : '';
	}

	public function title($title = '') {
		$p = $this->titlePattern;
		if ($this->titleExtraP && isset($this->titles[ $p . ':e' ])) {
			$title = $this->titles[ $p . ':e' ];
		} elseif (isset($this->titles[ $p ])) {
			$title = $this->titles[ $p ];
		} elseif (isset($this->titles['*'])) {
			$title = $this->titles['*'];
		}

		return str_replace($this->titleSearch, $this->titleReplace, $title);
	}

	/**
	 * @param $name
	 *
	 * @return \args\Arg|null
	 */
	public function __get($name) {
		return $this->getArgByName($name);
	}

	public function offsetExists($offset) {
		if (is_string($offset)) {
			if (isset($this->n2i[ $offset ])) {
				$offset = $this->n2i[ $offset ];
			} else {
				return false;
			}
		}

		return isset($this->args[ $offset ]);
	}

	public function offsetGet($offset) {
		if (is_string($offset)) {
			return $this->getArgByName($offset);
		}

		return $this->getArg($offset);
	}

	public function offsetSet($offset, $value) {

	}

	public function offsetUnset($offset) {
	}

	public function getIterator() {
		return new \ArrayIterator($this->args);
	}
}