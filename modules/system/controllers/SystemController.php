<?php
defined ( 'KISSGO' ) or exit ( 'No direct script access allowed' );
/**
 * 系统登录模块.
 *
 * @author Guangfeng Ning <windywany@gmail.com>
 *        
 */
class SystemController extends Controller {
	/**
	 * 输出验证码.
	 *
	 * @param string $type        	
	 * @param string $size        	
	 * @param number $font        	
	 */
	public function captcha($type = 'gif', $size = '60x20', $font = 15) {
		Response::nocache ();
		$size = explode ( 'x', $size );
		if (count ( $size ) == 1) {
			$width = intval ( $size [0] );
			$height = $width * 3 / 4;
		} else if (count ( $size ) >= 2) {
			$width = intval ( $size [0] );
			$height = intval ( $size [1] );
		} else {
			$width = 60;
			$height = 20;
		}
		$font = intval ( $font );
		$font = max ( array (18,$font ) );
		$type = in_array ( $type, array ('gif','png' ) ) ? $type : 'png';
		$auth_code_obj = new CaptchaCode ();
		// 定义验证码信息
		$arr ['code'] = array ('characters' => 'A-H,J-K,M-N,P-Z,3-9','length' => 4,'deflect' => true,'multicolor' => true );
		$auth_code_obj->setCode ( $arr ['code'] );
		// 定义干扰信息
		$arr ['molestation'] = array ('type' => 'both','density' => 'normal' );
		$auth_code_obj->setMolestation ( $arr ['molestation'] );
		// 定义图像信息. 设置图象类型请确认您的服务器是否支持您需要的类型
		$arr ['image'] = array ('type' => $type,'width' => $width,'height' => $height );
		$auth_code_obj->setImage ( $arr ['image'] );
		// 定义字体信息
		$arr ['font'] = array ('space' => 5,'size' => $font,'left' => 5 );
		$auth_code_obj->setFont ( $arr ['font'] );
		// 定义背景色
		$arr ['bg'] = array ('r' => 255,'g' => 255,'b' => 255 );
		$auth_code_obj->setBgColor ( $arr ['bg'] );
		$auth_code_obj->paint ();
		Response::getInstance ()->close ( true );
	}
	/**
	 *
	 * @param string $value        	
	 */
	public function checkcode($value) {
		$auth_code_obj = new CaptchaCode ();
		$rst = true;
		if (! $auth_code_obj->validate ( $value, false,false )) {
			$rst = false;
		}
		$data ['success'] = $rst;
		if (! $rst) {
			$data ['msg'] = '验证码错误';
		}
		return new JsonView ( $data );
	}
}
