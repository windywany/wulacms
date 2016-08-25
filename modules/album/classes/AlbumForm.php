<?php
class AlbumForm extends AbstractForm {
	private $page_id = array ('widget' => 'hidden' );
	private $album_pics = array ('widget' => 'mimage','label' => '上传相片','defaults' => '{"water":1}' );
}