<?php
class DashboardUIManager implements Renderable {
	private $rows = array ();
	/**
	 * 取Grid中的一行
	 *
	 * @param int $row        	
	 * @return DashboardRow
	 */
	public function getRow($row) {
		if (! empty ( $row )) {
			if (! isset ( $this->rows [$row] )) {
				$this->rows [$row] = new DashboardRow ();
			}
			return $this->rows [$row];
		}
		trigger_error ( 'empty row num', E_USER_ERROR );
	}
	/**
	 * 取cell.
	 *
	 * @param int $row        	
	 * @param int $col        	
	 * @param string $width        	
	 * @return DashboardCol
	 */
	public function getCell($row, $col, $width = 'col-sm-12') {
		$row = $this->getRow ( $row );
		return $row->getCol ( $col, $width );
	}
	/**
	 * 将一个组件添加到单元格中.
	 *
	 * @param int $row        	
	 * @param int $col        	
	 * @param Renderable $widget        	
	 * @param string $width        	
	 * @return DashboardUIManager
	 */
	public function setCell($row, $col, $widget, $width = 'col-sm-12') {
		$cell = $this->getCell ( $row, $col, $width );
		if ($cell) {
			$cell->addWidget ( $widget );
		}
		return $this;
	}
	public function render() {
		$html = array ();
		foreach ( $this->rows as $row ) {
			$html [] = '<div class="row">';
			$html [] = $row->render ();
			$html [] = '</div>';
		}
		return implode ( '', $html );
	}
}