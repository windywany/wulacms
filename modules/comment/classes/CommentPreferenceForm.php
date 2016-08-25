<?php
class CommentPreferenceForm extends AbstractForm {
	protected $__cfg_group = 'comment';
	private $enable_captcha = array ('group' => '1','col' => '3','label' => '启用验证码','widget' => 'radio','default' => '0','defaults' => "0=否\n1=是" );
	private $allow_anonymouse = array ('group' => '1','col' => '3','label' => '允许匿名评论.','widget' => 'radio','default' => '0','defaults' => "0=否\n1=是" );
	private $allow_anonymouse1 = array ('group' => '1','col' => '3','label' => '允许匿名留言.','widget' => 'radio','default' => '0','defaults' => "0=否\n1=是" );
	private $enable_address = array ('group' => '1','col' => '3','label' => '留言地址.','widget' => 'radio','default' => '0','defaults' => "0=否\n1=是\n2=必须" );
	private $interval = array ('group' => '2','col' => '6','label' => '二次评论或留言间的最小间隔(单位秒)','note' => '默认为60秒','rules' => array ('digits' => '只能是整数' ) );
	private $interval1 = array ('group' => '2','col' => '6','label' => '相同IP评论或留言间的最小间隔(单位秒)','note' => '默认为60秒','rules' => array ('digits' => '只能是整数' ) );
	private $enable_phone = array ('group' => '3','col' => '3','label' => '留言联系电话','widget' => 'radio','default' => '0','defaults' => "0=否\n1=是\n2=必须" );
	private $enable_qq = array ('group' => '3','col' => '3','label' => '留言QQ','widget' => 'radio','default' => '0','defaults' => "0=否\n1=是\n2=必须" );
	private $enable_weixin = array ('group' => '3','col' => '3','label' => '留言微信','widget' => 'radio','default' => '0','defaults' => "0=否\n1=是\n2=必须" );
	private $enable_weibo = array ('group' => '3','col' => '3','label' => '留言微博','widget' => 'radio','default' => '0','defaults' => "0=否\n1=是\n2=必须" );
	
	// private $custom_contacts = array ('label' => '留言自定义字段','widget' => 'textarea','row' => '4','note' => '一行一个,格式:name,label[,required],例:qq,腾讯QQ,1' );
	private $bad_words = array ('label' => '不允许出现的词','widget' => 'textarea','row' => '6','note' => '以逗号分隔每个词' );
	private $bad_ips = array ('label' => '禁止以下IP评论或留言','widget' => 'textarea','row' => '6','note' => '以逗号分隔每个IP,支持网段,范围写法.' );
	/**
	 * 取自定义联系字段.
	 *
	 * @return array();
	 */
	public static function getCustomContactField() {
		static $contacts = false;
		if ($contacts === false) {
			$contacts = array ();
			$fields = dbselect ( 'value' )->from ( '{preferences}' )->where ( array ('name' => 'custom_contacts','preference_group' => $this->__cfg_group ) )->get ( 'value' );
			if ($fields) {
				$fields = trim ( $fields );
				if ($fields) {
					$fields = explode ( "\n", $fields );
					foreach ( $fields as $f ) {
						$f = trim ( trim ( $f ), ',' );
						if ($f) {
							$fs = explode ( ',', $f );
							@list ( $n, $l, $r ) = $fs;
							if (preg_match ( '/^[a-z][a-z0-9_]*$/i', $n )) {
								$c = array ();
								$c ['field'] = $n;
								if ($l) {
									$c ['label'] = $l;
								} else {
									$c ['label'] = $n;
								}
								$c ['required'] = $r;
								$contacts [$n] = $c;
							}
						}
					}
				}
			}
		}
		return $contacts;
	}
}
