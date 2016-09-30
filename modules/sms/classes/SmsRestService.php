<?php
namespace sms\classes;

class SmsRestService {

	/**
	 *
	 * @param RestServer $server
	 */
	public static function on_init_rest_server($server) {
		$server->registerClass(new SmsRestService (), '1.0', 'sms');

		return $server;
	}

	/**
	 * 验证码是否启用.
	 *
	 * @param array  $param
	 * @param string $key
	 * @param string $secret
	 *
	 * @return array
	 */
	public function rest_get_captcha_enabled($param, $key, $secret) {
		$enabled = bcfg('captcha_enabled@sms');

		return ['error' => 0, 'data' => ['enabled' => $enabled]];
	}

	/**
	 * 发送验证码
	 *
	 * @param array ('phone','msg','vendor')
	 *
	 * @return $array
	 */
	public function rest_get_send($param, $key, $secret) {
		$rtn     = array('error' => 404, 'message' => 'error');
		$phone   = get_condition_value('phone', $param);
		$content = get_condition_value('tid', $param);

		if (bcfg('captcha_enabled@sms')) {
			$captcha = get_condition_value('captcha', $param);
			$code    = new \CaptchaCode ();
			if (!$captcha || !$code->validate($captcha, false, true)) {
				$rtn ['message'] = '验证码不正确';
				$rtn ['error']   = 405;

				return $rtn;
			}
		}
		if (!$phone) {
			$rtn ['message'] = '请输入手机号！';
			$rtn ['error']   = 406;

			return $rtn;
		}
		// 正则手机
		if (preg_match('/^1[34578]\d{9}$/', $phone) == 0) {
			$rtn ['message'] = '手机格式错误！';
			$rtn ['error']   = 407;

			return $rtn;
		}
		if (!$content) {
			$rtn ['message'] = '发送内容为空！';
			$rtn ['error']   = 408;

			return $rtn;
		}
		$rst = Sms::send($phone, $content, $param);
		if ($rst) {
			$rtn ['error']   = 0;
			$rtn ['message'] = 'ok';
			$rtn ['timeout'] = $param['exp'];
		} else {
			$rtn ['error']   = 409;
			$rtn ['message'] = '短信发送失败.';
		}

		return $rtn;
	}
}