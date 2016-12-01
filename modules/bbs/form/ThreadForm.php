<?php

namespace bbs\form;

class ThreadForm extends \AbstractForm {
	private $id           = ['type'=>'int','rules'=>['regexp(/^0|[1-9]\d*$/)'=>'只能是数字']];
	private $forum_id     = ['type'=>'int','rules'=>['required'=>'版块编号不能为空','regexp(/^0|[1-9]\d*$/)'=>'只能是数字']];
	private $mid          = ['type'=>'int','rules'=>['required'=>'会员编号不能为空','regexp(/^0|[1-9]\d*$/)'=>'只能是数字']];
	private $type         = ['type'=>'int','rules'=>['required'=>'帖子类型不能为空','regexp(/^0|[1-9]\d*$/)'=>'只能是数字']];
	private $status       = ['type'=>'int','rules'=>['regexp(/^0|[1-9]\d*$/)'=>'只能是数字']];
	private $post_id      = ['type'=>'int','rules'=>['regexp(/^0|[1-9]\d*$/)'=>'只能是数字']];
	private $post_count   = ['type'=>'int','rules'=>['regexp(/^0|[1-9]\d*$/)'=>'只能是数字']];
	private $last_post_id = ['type'=>'int','rules'=>['regexp(/^0|[1-9]\d*$/)'=>'只能是数字']];
	private $allow_post   = ['type'=>'int','rules'=>['regexp(/^0|[1-9]\d*$/)'=>'只能是数字']];
	private $topic        = [];
	private $subject      = ['rules'=>['required'=>'主题不能为空']];
	private $url          = ['rules'=>['required'=>'URL不能为空']];
	private $url_key      = [];
	private $flag0        = ['type'=>'int','rules'=>['regexp(/^0|[1-9]\d*$/)'=>'只能是数字']];
	private $flag1        = ['type'=>'int','rules'=>['regexp(/^0|[1-9]\d*$/)'=>'只能是数字']];
	private $flag2        = ['type'=>'int','rules'=>['regexp(/^0|[1-9]\d*$/)'=>'只能是数字']];
	private $flag3        = ['type'=>'int','rules'=>['regexp(/^0|[1-9]\d*$/)'=>'只能是数字']];
	private $flag4        = ['type'=>'int','rules'=>['regexp(/^0|[1-9]\d*$/)'=>'只能是数字']];
	private $flag5        = ['type'=>'int','rules'=>['regexp(/^0|[1-9]\d*$/)'=>'只能是数字']];
	private $flag6        = ['type'=>'int','rules'=>['regexp(/^0|[1-9]\d*$/)'=>'只能是数字']];
	private $flag7        = ['type'=>'int','rules'=>['regexp(/^0|[1-9]\d*$/)'=>'只能是数字']];
	private $flag8        = ['type'=>'int','rules'=>['regexp(/^0|[1-9]\d*$/)'=>'只能是数字']];
	private $flag9        = ['type'=>'int','rules'=>['regexp(/^0|[1-9]\d*$/)'=>'只能是数字']];
	private $closeat      = ['type'=>'int','rules'=>['regexp(/^0|[1-9]\d*$/)'=>'只能是数字']];
	private $reply_view   = ['type'=>'int','rules'=>['regexp(/^0|[1-9]\d*$/)'=>'只能是数字']];
	private $cost         = ['type'=>'int','rules'=>['regexp(/^0|[1-9]\d*$/)'=>'只能是数字']];
	private $cost_amount  = ['type'=>'int','rules'=>['regexp(/^0|[1-9]\d*$/)'=>'只能是数字']];
	private $view_passwd  = [];
	private $tags = [];
}