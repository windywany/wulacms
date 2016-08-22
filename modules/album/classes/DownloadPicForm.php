<?php
class DownloadPicForm extends AbstractForm {
	private $remote_pics = array ('label' => '远程图片','note' => '一行一个远程图片，可使用||将图片地址与图片标题分开.','widget' => 'textarea','row'=>10,'rules' => array ('required' => '请填写远程图片地址' ) );
	private $resize_h = array ('label' => '底部自动去除','note' => '高度值，单位象素.','group' => '2','col' => 3,'rules' => array ('regexp(/^[1-9]\d*$/)' => '只能是数字' ) );
}

