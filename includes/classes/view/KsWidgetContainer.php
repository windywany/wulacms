<?php
class KsWidgetContainer implements Renderable, IteratorAggregate {
	private $name;
	private $id;
	private $widgets = array ();
	private $position = 'left';
	/**
	 *
	 * @param string $id
	 *        	page id.
	 * @param string $name
	 *        	page name.
	 * @param string $position
	 *        	container position.
	 */
	public function __construct($id, $name, $position = 'left') {
		$this->id = $id;
		$this->name = $name;
		$this->position = $position;
	}
	/**
	 * 添加一个widget到容器中.
	 *
	 * @param KsWidget $widget        	
	 * @param number $order        	
	 * @param bool $system
	 *        	是否是系统自动提供.
	 */
	public function add($widget, $order = 999) {
		if ($widget instanceof KsWidget) {
			$id = $widget->getId ();
			$this->widgets [$id] = array ('widget' => $widget,'pos' => $order );
		}
	}
	public function remove($id) {
		unset ( $this->widgets [$id] );
	}
	public function getID() {
		return $this->id;
	}
	public function getName() {
		return $this->name;
	}
	public function hasWidget() {
		return ! empty ( $this->widgets );
	}
	public function getWidgets() {
		return $this->widgets;
	}
	public function getPosition() {
		return $this->position;
	}
	public function prepareResources(&$view) {
		foreach ( $this->widgets as $w ) {
			$w ['widget']->addResources ( $view );
		}
	}
	public function render() {
		$html = array ();
		if ($this->widgets) {
			usort ( $this->widgets, ArrayComparer::compare ( 'pos' ) );
			foreach ( $this->widgets as $w ) {
				if ($w ['widget']->isHidden ()) {
					continue;
				}
				$html [] = $w ['widget']->render ();
			}
		}
		return implode ( "\n", $html );
	}
	public function getIterator() {
		usort ( $this->widgets, ArrayComparer::compare ( 'pos' ) );
		return new ArrayIterator ( $this->widgets );
	}
	/**
	 * 加载容器.
	 *
	 * @param array $containers        	
	 * @param Passport $user        	
	 * @return array
	 */
	public static function loads($containers, $loadedEvent = true) {
		$data = array ();
		if ($containers) {
			foreach ( $containers as $id => $c ) {
				$wc = new KsWidgetContainer ( $id, $c [1], $c [2] );
				$dbw = self::getWidgetsFromDB ( $c [0], $c [2] );
				$plw = self::getWidgetsFromPlugin ( $c [0] . '_' . $c [2] );
				if ($plw) {
					foreach ( $plw as $w ) {
						$wc->add ( $w, $w->getOrder () );
					}
				}
				if ($dbw) {
					foreach ( $dbw as $w ) {
						$wc->add ( $w, $w->getOrder () );
					}
				}
				
				// give user a chance to remove load
				if ($loadedEvent) {
					$data [$id] = apply_filter ( 'widgets_loaded_' . $c [0] . '_' . $c [2], $wc );
				} else {
					$data [$id] = $wc;
				}
			}
		}
		return $data;
	}
	
	/**
	 *
	 * @param string $id        	
	 * @return mixed
	 */
	public static function getWidgetsFromPlugin($id) {
		$widgets = apply_filter ( 'add_widget_into_' . $id, array () );
		return $widgets;
	}
	public static function getWidgetsFromDB($page, $pos) {
		$widgets = array ();
		$ws = dbselect ( '*' )->from ( '{widgets}' )->where ( array ('page' => $page,'pos' => $pos ) )->asc ( 'pos' )->toArray ();
		foreach ( $ws as $s ) {
			$vcls = $s ['viewcls'];
			$dcls = $s ['datacls'];
			if (class_exists ( $vcls ) && class_exists ( $dcls )) {
				$w = new KsWidget ( $s ['wid'], $s ['name'], $s ['sort'] );
				$vopts = @unserialize ( $s ['view_options'] );
				$vLz = new $vcls ( $vopts );
				$dopts = @unserialize ( $s ['data_options'] );
				$dLz = new $dcls ( $dopts );
				$w->setData ( $dLz );
				$w->setView ( $vLz );
				$w->setPosition ( $s ['pos'] );
				if ($s ['hidden']) {
					$w->hide ();
				}
				$widgets [] = $w;
			}
		}
		return $widgets;
	}
	public static function getSupportedViews() {
		static $views = false;
		if ($views === false) {
			$views = apply_filter ( 'get_widget_views', array () );
		}
		return $views;
	}
	public static function getSupportedDataProvidors() {
		static $providors = false;
		if ($providors === false) {
			$providors = apply_filter ( 'get_widget_data_providors', array () );
		}
		return $providors;
	}
}