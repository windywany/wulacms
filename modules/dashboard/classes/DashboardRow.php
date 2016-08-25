<?php
/**
 * Dashboard中的一行.
 * @author Guangfeng
 *
 */
class DashboardRow {
	private $cols;
	/**
	 * 取这行中的一列.
	 *
	 * @param int $col
	 *        	第几列
	 * @param string $width
	 *        	宽度.
	 * @return DashboardCol 列实例.
	 */
	public function getCol($col, $width = 'col-sm-12') {
		if (! empty ( $col )) {
			if (! isset ( $this->cols [$col] )) {
				$this->cols [$col] = new DashboardCol ( $width );
			}
			return $this->cols [$col];
		}
		trigger_error ( 'empty col num', E_USER_ERROR );
	}
	public function render() {
		$html = array ();
		foreach ( $this->cols as $col ) {
			$html [] = $col->render ();
		}
		return implode ( '', $html );
	}
}