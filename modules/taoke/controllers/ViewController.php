<?php
class ViewController extends NonSessionController {
	public function index($id) {
		$id = intval ( $id );
		if ($id) {
			
			$page = CmsPage::load ( $id, false );
			if ($page) {
				$model = new AlbumContentModel ();
				$page = $page->getFields ();
				$model->load ( $page, $id );
				return template ( 'album.tpl', $page );
			}
		}
		Response::respond ( 404 );
	}
}
