<?php
/**
 *
 * 文件上传器.
 * @author LeoNing
 *
 */
interface IUploader {
	/**
	 * 上传文件.
	 *
	 * @param string $filepath
	 *        	使用plupload上传后的文件.
	 * @param string $path
	 *        	存储路径,如果是null则自系统自动生成.
	 * @return array array(url,name,path,width,height,size)
	 *         <code>
	 *         <ul>
	 *         <li>url-相对URL路径</li>
	 *         <li>name-文件名</li>
	 *         <li>path-存储路径</li>
	 *         <li>size-文件体积</li>
	 *         <li>width-如果是图片，图片的宽</li>
	 *         <li>height-如果是图片，图片的高</li>
	 *         </ul>
	 *         </code>
	 */
	public function save($filepath, $path = null);
	/**
	 * 文件扩展名是否是允许的文件类型.
	 *
	 * @param $ext 是否允许上传扩展为$ext的文件.        	
	 * @return boolean 允许true,反之false.
	 */
	public function allowed($ext);
	/**
	 * 允许的最大体积.
	 *
	 * @return INT 单位为K.
	 */
	public function getMaxSize();
	/**
	 * 返回错误信息.
	 */
	public function get_last_error();
	/**
	 * delete the file.
	 *
	 * @param string $file
	 *        	要删除的文件路径.
	 * @return boolean 成功返回true,反之返回false.
	 */
	public function delete($file);
	/**
	 * close connection if there is.
	 */
	public function close();
}