<?php
/**
 * 默认的回收站实现。它只能作用于拥有id(主键),update_time,update_uid,deleted字段的表上.
 * 
 * @author ngf
 */
class DefaultRecycle implements IRecycle {
	private $idfield = 'id';
	private $type;
	private $ids;
	private $table;
	private $content;
	private $meta;
	private $meta_fields = array ();
	public function __construct($ids = '', $type = '', $table = '', $meta = '', $idfield = 'id') {
		$this->type = $type;
		$this->ids = is_array ( $ids ) ? $ids : array ($ids );
		$this->table = $table;
		$this->meta = $meta;
		if ($this->meta) {
			if (preg_match_all ( '#\{([^\}]+)\}#', $this->meta, $f )) {
				$this->meta_fields = $f [1];
			}
		} else {
			$this->meta = '{id}';
			$this->meta_fields [] = 'id';
		}
		$this->idfield = $idfield;
	}
	public function getContentType() {
		return $this->type;
	}
	public function getContent() {
		$contents = array ();
		foreach ( $this->ids as $id ) {
			$contents [$id] = serialize ( array ('ids' => $id,'table' => $this->table,'idfield' => $this->idfield ) );
		}
		return $contents;
	}
	public function restore($content) {
		$content = @unserialize ( $content );
		if ($content) {
			$ids = $content ['ids'];
			$table = $content ['table'];
			$this->idfield = isset ( $content ['idfield'] ) ? $content ['idfield'] : 'id';
			$user = whoami ();
			$data ['update_time'] = time ();
			$data ['update_uid'] = $user->getUid ();
			$data ['deleted'] = 0;
			if (! is_array ( $ids )) {
				$ids = array ($ids );
			}
			dbupdate ( '{' . $table . '}' )->set ( $data )->where ( array ($this->idfield . ' IN' => $ids ) )->exec ();
			fire ( 'on_restore_' . $table, $ids );
		}
	}
	public function delete($content) {
		$content = @unserialize ( $content );
		if ($content) {
			$ids = $content ['ids'];
			$table = $content ['table'];
			$this->idfield = isset ( $content ['idfield'] ) ? $content ['idfield'] : 'id';
			if (! is_array ( $ids )) {
				$ids = array ($ids );
			}
			fire ( 'on_destroy_' . $table, $ids );
			dbdelete ()->from ( '{' . $table . '}' )->where ( array ($this->idfield . ' IN' => $ids ) )->exec ();
		}
	}
	public function getMeta($id) {
		$contents = dbselect ( implode ( ',', $this->meta_fields ) )->from ( '{' . $this->table . '}' )->where ( array ($this->idfield => $id ) );
		$metas = array ();
		foreach ( $contents as $content ) {
			$meta = $this->meta;
			foreach ( $content as $key => $val ) {
				$meta = str_replace ( '{' . $key . '}', $val, $meta );
			}
			$metas [] = $meta;
		}
		if ($metas) {
			return implode ( '<br/>', $metas );
		} else {
			return $id;
		}
	}
}
