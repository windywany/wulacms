<?php
namespace sms\classes;

/**
 * 短信工具类.
 *
 * @author Leo Ning.
 *
 */
class Sms {

	/**
	 * 发送短信.
	 *
	 * @param string $phone 手机号码.
	 * @param string $tid   模板编号.
	 * @param array  $args  参数数组.
	 *
	 * @return bool 发送成功返回true,反之返回false.
	 */
	public static function send($phone, $tid, &$args = null) {
		if (!bcfg('sms_enabled@sms')) {
			return false;
		}
		if (empty ($phone) || empty ($tid)) {
			log_error('手机号:' . $phone . ', 模板:' . $tid . ', 有一个为空', 'sms');

			return false;
		}
		if (!preg_match('#^1[345789]\d{9}$#', $phone)) {
			log_error('手机号:' . $phone . '非法', 'sms');

			return false;
		}
		$vendor = cfg('vendor@sms');
		if (empty ($vendor)) {
			log_error('未配置短信提供商', 'sms');

			return false;
		}
		$vendors = self::vendors();
		if (!isset ($vendors [ $vendor ])) {
			log_error('短信提供商' . $vendor . '不存在', 'sms');

			return false;
		}
		$v         = $vendors [ $vendor ];
		$templates = self::templates();
		if (!isset ($templates [ $tid ])) {
			log_error('模板' . $tid . '不存在', 'sms');

			return false;
		}
		$cfg ['tpl'] = cfg($tid . '_tpl@sms');
		$cfg ['cnt'] = cfg($tid . '_cnt@sms', null);
		$cfg ['exp'] = icfg($tid . '_exp@sms', 120);
		$args['exp'] = $cfg['exp'];
		$last_sent   = sess_get('sms_' . $tid . '_sent', 0);
		if (($last_sent + $cfg['exp']) > time()) {
			log_error('模板' . $tid . '发送太快', 'sms');

			return false;
		}
		$tpl           = $templates [ $tid ];
		$args['phone'] = $phone;
		$testMode      = bcfg('test_mode@sms');
		$tpl->setTestMode($testMode);
		$tpl->setParams($args);
		$tpl->setOptions($cfg);
		$data ['create_time'] = time();
		$data ['phone']       = $phone;
		$data ['tid']         = $tid;
		$data ['vendor']      = $v->getName();
		$tpl->setContent($cfg ['cnt']);
		$data ['content'] = $tpl->getContent();
		if ($data['content'] === false) {
			return false;
		}
		if ($testMode) {
			$rst = true;
		} else {
			$rst = $v->send($tpl, $phone);
		}
		if ($rst) {
			$data ['status'] = 1;
			$tpl->onSuccess();
			$_SESSION[ 'sms_' . $tid . '_sent' ] = time();
		} else {
			$data ['status'] = 0;
			$data ['note']   = $v->getError();
			$tpl->onFailure();
		}
		dbinsert($data)->into('{sms_log}')->exec();

		return $rst;
	}

	/**
	 * 短信提供商列表.
	 *
	 * @return array 短信提供商列表.
	 */
	public static function vendors() {
		$vendors = apply_filter('get_sms_vendors', []);

		return $vendors;
	}

	/**
	 * 系统业务模板.
	 *
	 * @return array 系统业务模板.
	 */
	public static function templates() {
		$templates = apply_filter('get_sms_templates', []);

		return $templates;
	}
}