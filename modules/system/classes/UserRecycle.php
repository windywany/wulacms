<?php
class UserRecycle implements IRecycle {
	private $content;
	/*
	 * (non-PHPdoc) @see IRecycle::delete()
	 */
	public function delete($content) {
		// TODO Auto-generated method stub
	}
	
	/*
	 * (non-PHPdoc) @see IRecycle::getContent()
	 */
	public function getContent() {
		// TODO Auto-generated method stub
	}
	
	/*
	 * (non-PHPdoc) @see IRecycle::getContentType()
	 */
	public function getContentType() {
		return 'User';
	}
	
	/*
	 * (non-PHPdoc) @see IRecycle::getMeta()
	 */
	public function getMeta() {
		// TODO Auto-generated method stub
	}
	
	/*
	 * (non-PHPdoc) @see IRecycle::restore()
	 */
	public function restore($content) {
		// TODO Auto-generated method stub
	}
	
	/*
	 * (non-PHPdoc) @see IRecycle::setContent()
	 */
	public function setContent($content) {
		$this->content = $content;
	}
}
?>