<?php
/**
 * 收回站接口.
 * @author ngf.
 *
 */
interface IRecycle {
	/**
	 * 还原.
	 *
	 * @param string $value        	
	 */
	public function restore($content);
	/**
	 * 彻底删除.
	 *
	 * @param string $content        	
	 */
	public function delete($content);
	/**
	 * 取内容类型.
	 *
	 * @return string
	 */
	public function getContentType();
	/**
	 * 取内容描述.
	 *
	 * @return string
	 */
	public function getMeta($id);
	/**
	 * 到被删除的内容。
	 *
	 * @return string
	 *
	 */
	public function getContent();	
}
?>