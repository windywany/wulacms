<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/13 0013
 * Time: 下午 3:58
 */

namespace bbs\form;

class PostForm extends \AbstractForm {
	private $id          = ['type'=>'int','rules'=>['regexp(/^0|[1-9]\d*$/)'=>'只能是数字']];
	private $status      = ['type'=>'int','rules'=>['regexp(/^0|[1-9]\d*$/)'=>'只能是数字']];
	private $thread_id   = ['type'=>'int','rules'=>['required'=>'请填写回复ID','regexp(/^0|[1-9]\d*$/)'=>'只能是数字']];
	private $replyto     = ['type'=>'int','rules'=>['regexp(/^0|[1-9]\d*$/)'=>'只能是数字']];
	private $accept      = ['type'=>'int','rules'=>['regexp(/^0|[1-9]\d*$/)'=>'只能是数字']];
	private $reward      = ['type'=>'int','rules'=>['regexp(/^0|[1-9]\d*$/)'=>'只能是数字']];
	private $cost        = ['type'=>'int','rules'=>['regexp(/^0|[1-9]\d*$/)'=>'只能是数字']];
	private $up          = ['type'=>'int','rules'=>['regexp(/^0|[1-9]\d*$/)'=>'只能是数字']];
	private $down        = ['type'=>'int','rules'=>['regexp(/^0|[1-9]\d*$/)'=>'只能是数字']];
	private $ip          = ['rules'=>['required'=>'请填写IP']];
	private $content     = ['rules'=>['required'=>'请填写内容']];
}