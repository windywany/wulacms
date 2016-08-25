<?php

/*
 * basic validator kissgo framework that keep it simple and stupid, go go go ~~ @author Leo Ning @package kissgo $Id$
 */
class FormValidator {
	protected static $extra_methods = array ();
	private $formName;
	private $form;
	private $idx = 0;
	public function __construct($form) {
		$this->form = $form;
		$this->formName = $form->getName ();
	}
	/**
	 * 生成jquery.validate.js可用的规则.
	 *
	 * @param array $rules        	
	 * @param array $data        	
	 * @param string $field        	
	 * @return array
	 */
	public function getRuleClass($rules, $data = array(), $field = '', $scope = '') {
		$rs = array ();
		$ms = array ();
		foreach ( $rules as $m => $exp ) {
			switch ($m) {
				case 'required' :
					if ($exp ['option']) {
						$exp ['option'] = explode ( ':', $exp ['option'] );
						if (count ( $exp ['option'] ) > 2) {
							$exp ['option'] = array_slice ( $exp ['option'], 0, 2 );
						}
						$exp ['option'] = implode ( ':', $exp ['option'] );
						$rs [$m] = "#{$exp ['option']}";
					} else {
						$rs [$m] = true;
					}
					break;
				case 'callback' :
					$op = $exp ['option'] [0];
					if ($op == '@' || $op == '#' || $op == '&') {
						$m = 'remote';
						$exps = substr ( $exp ['option'], 1 );
						$exps = explode ( ',', $exps );
						$func = $exps [0];
						if ($op == '@') {
							$url = tourl ( 'system/ajax/validate' ) . $this->formName . '/' . $func . '/' . $field . '/';
						} else if ($op == '&') {
							$funcs = explode ( '.', $func );
							if (count ( $funcs ) == 2) {
								$func = $funcs [1];
								$formName = str_replace ( '\\', '.', ltrim ( $funcs [0], '\\' ) );
								$url = tourl ( 'system/ajax/validate' ) . $formName . '/' . $func . '/' . $field . '/';
							} else {
								trigger_error ( 'error callback rule:' . $exp ['option'] );
							}
						} else {
							if ($scope && method_exists ( $this->form, 'registerCallback' )) {
								$this->form->registerCallback ( $func, array ($scope,$func ) );
							}
							$url = tourl ( 'system/ajax/dyvalidate' ) . $this->formName . '/' . $func . '/' . $field . '/';
						}
						$args = array ();
						if (count ( $exps ) > 1) {
							array_shift ( $exps );
							foreach ( $exps as $f ) {
								$args [$f] = isset ( $data [$f] ) ? $data [$f] : '';
							}
							if ($args) {
								$url .= '?' . http_build_query ( $args );
							}
						}
						$this->form->setCallbackArgs ( $func, $args );
						$exp ['option'] = $url;
					}

				case 'accept' :
				case 'notEqual' :
					$rs [$m] = $exp ['option'];
					break;
				case 'min' :
				case 'max' :
				case 'minlength' :
				case 'maxlength' :
				case 'gt' :
				case 'ge' :
				case 'lt' :
				case 'le' :
					$rs [$m] = intval($exp ['option']);
					break;
				case 'pattern' :
				case 'regexp' :
					$rs ['pattern'] = $exp ['option'];
					$m = 'pattern';
					break;
				case 'equalTo' :
				case 'notEqualTo' :
					$rs [$m] = "#{$exp ['option']}";
					break;
				case 'range' :
				case 'rangelength' :
					$lens = explode ( ',', $exp ['option'] );
					$rs [$m] = array (intval ( $lens [0] ),intval ( $lens [1] ) );
					break;
				case 'num' :
					$m = 'number';
				case 'email' :
				case 'url' :
				case 'number' :
				case 'digits' :
				case 'date' :
				default :
					$rs [$m] = true;
					break;
			}
			$this->idx = 0;
			$ms [$m] = preg_replace_callback ( '#%s#', array ($this,'convert' ), __ ( $exp ['message'] ) );
		}
		return array ($rs,$ms );
	}
	public static function add_method($rule, $callable) {
		if (is_callable ( $callable )) {
			self::$extra_methods [$rule] = $callable;
		}
	}
	public function valid($value, $data, $rules, $scope) {
		foreach ( $rules as $rule => $option ) {
			$valid = true;
			$valid_m = 'v_' . $rule;
			if (method_exists ( $this, $valid_m )) {
				$valid = $this->$valid_m ( $value, $option ['option'], $data, $scope, $option ['message'] );
			} else if (isset ( self::$extra_methods [$rule] )) {
				$valid_m = self::$extra_methods [$rule];
				if (is_callable ( $valid_m )) {
					$valid = call_user_func_array ( $valid_m, array ($value,$option ['option'],$data,$scope,$option ['message'] ) );
				}
			}
			if ($valid !== true) {
				return $valid;
			}
		}
		return true;
	}
	
	// 必填项目
	protected function v_required($value, $exp, $data, $scope, $message) {
		if ($exp) {
			$expx = explode ( ':', $exp );
			$exp = array_shift ( $expx );
			if (count ( $expx ) == 2) {
				$v = $expx [1];
				$exp = str_replace ( '_' . $v, '', $exp );
				if (! isset ( $data [$exp] ) || empty ( $data [$exp] ) || $this->emp ( $data [$exp] ) || $data [$exp] != $v) {
					return true;
				}
			} else {
				$selector = $expx [0];
				if ($selector == 'blank') {
					if (! $this->emp ( $data [$exp] )) {
						return true;
					}
				} else {
					if (! isset ( $data [$exp] ) || empty ( $data [$exp] ) || $this->emp ( $data [$exp] )) {
						return true;
					}
				}
			}
		}
		if (! $this->emp ( $value )) {
			return true;
		} else {
			return empty ( $message ) ? __ ( 'This field is required.' ) : __ ( $message );
		}
	}
	
	// 相等
	protected function v_equalTo($value, $exp, $data, $scope, $message) {
		$rst = false;
		if (isset ( $data [$exp] )) {
			$rst = $value == $data [$exp];
		}
		if ($rst) {
			return true;
		} else {
			return empty ( $message ) ? __ ( 'Please enter the same value again.' ) : __ ( $message );
		}
	}
	
	// 不相等
	protected function v_notEqualTo($value, $exp, $data, $scope, $message) {
		$rst = false;
		if (isset ( $data [$exp] )) {
			$rst = $value != $data [$exp];
		}
		if ($rst) {
			return true;
		} else {
			return empty ( $message ) ? __ ( 'Please enter the different value.' ) : __ ( $message );
		}
	}
	
	// 不相等
	protected function v_notEqual($value, $exp, $data, $scope, $message) {
		$rst = $value != $exp;
		if ($rst) {
			return true;
		} else {
			return empty ( $message ) ? __ ( 'Please enter the different value.' ) : __ ( $message );
		}
	}
	
	// 数值,包括整数与实数
	protected function v_num($value, $exp, $data, $scope, $message) {
		if ($this->emp ( $value ) || is_numeric ( $value )) {
			return true;
		} else {
			return empty ( $message ) ? __ ( 'Please enter a valid number.' ) : __ ( $message );
		}
	}
	protected function v_number($value, $exp, $data, $scope, $message) {
		return $this->v_num ( $value, $exp, $data, $scope, $message );
	}
	
	// 整数
	protected function v_digits($value, $exp, $data, $scope, $message) {
		if ($this->emp ( $value ) || preg_match ( '/^(0|[1-9]\d*)$/', $value )) {
			return true;
		} else {
			return empty ( $message ) ? __ ( 'Please enter only digits.' ) : __ ( $message );
		}
	}
	
	// min
	protected function v_min($value, $exp, $data, $scope, $message) {
		if ($this->emp ( $value )) {
			return true;
		}
		$value = floatval ( $value );
		if ($value >= floatval ( $exp )) {
			return true;
		} else {
			return empty ( $message ) ? sprintf ( __ ( 'Please enter a value greater than or equal to %s.', $exp ) ) : __ ( $message, $exp );
		}
	}
	
	// max
	protected function v_max($value, $exp, $data, $scope, $message) {
		if ($this->emp ( $value )) {
			return true;
		}
		$value = floatval ( $value );
		if ($value <= floatval ( $exp )) {
			return true;
		} else {
			return empty ( $message ) ? sprintf ( __ ( 'Please enter a value less than or equal to %s.' ), $exp ) : __ ( $message, $exp );
		}
	}
	
	// gt 大于
	protected function v_gt($value, $exp, $data, $scope, $message) {
		if ($this->emp ( $value )) {
			return true;
		}
		if ($value > $exp) {
			return true;
		} else {
			return empty ( $message ) ? sprintf ( __ ( 'Please enter a value greater than %s.' ), $exp ) : __ ( $message, $exp );
		}
	}
	
	// gt 大于 表单中的值
	protected function v_gt2($value, $exp, $data, $scope, $message) {
		if ($this->emp ( $value )) {
			return true;
		}
		if ($value > $data [$exp]) {
			return true;
		} else {
			return empty ( $message ) ? sprintf ( __ ( 'Please enter a value greater than %s.' ), $data [$exp] ) : __ ( $message, $data [$exp] );
		}
	}
	
	// ge 大于等于
	protected function v_ge($value, $exp, $data, $scope, $message) {
		if ($this->emp ( $value )) {
			return true;
		}
		if ($value >= $exp) {
			return true;
		} else {
			return empty ( $message ) ? sprintf ( __ ( 'Please enter a value greater than or equal to %s.' ), $exp ) : __ ( $message, $exp );
		}
	}
	
	// ge2 大于等于
	protected function v_ge2($value, $exp, $data, $scope, $message) {
		if ($this->emp ( $value )) {
			return true;
		}
		if ($value >= $data [$exp]) {
			return true;
		} else {
			return empty ( $message ) ? sprintf ( __ ( 'Please enter a value greater than or equal to %s.' ), $data [$exp] ) : __ ( $message, $data [$exp] );
		}
	}
	
	// gt 小于
	protected function v_lt($value, $exp, $data, $scope, $message) {
		if ($this->emp ( $value )) {
			return true;
		}
		if ($value < $exp) {
			return true;
		} else {
			return empty ( $message ) ? sprintf ( __ ( 'Please enter a value less than %s.' ), $exp ) : __ ( $message, $exp );
		}
	}
	
	// gt 小于
	protected function v_lt2($value, $exp, $data, $scope, $message) {
		if ($this->emp ( $value )) {
			return true;
		}
		if ($value < $data [$exp]) {
			return true;
		} else {
			return empty ( $message ) ? sprintf ( __ ( 'Please enter a value less than %s.' ), $data [$exp] ) : __ ( $message, $data [$exp] );
		}
	}
	
	// ge 小于等于
	protected function v_le($value, $exp, $data, $scope, $message) {
		if ($this->emp ( $value )) {
			return true;
		}
		if ($value <= $exp) {
			return true;
		} else {
			return empty ( $message ) ? sprintf ( __ ( 'Please enter a value less than or equal to %s.' ), $exp ) : __ ( $message, $exp );
		}
	}
	
	// ge2 小于等于
	protected function v_le2($value, $exp, $data, $scope, $message) {
		if ($this->emp ( $value )) {
			return true;
		}
		if ($value <= $data [$exp]) {
			return true;
		} else {
			return empty ( $message ) ? sprintf ( __ ( 'Please enter a value less than or equal to %s.' ), $data [$exp] ) : __ ( $message, $data [$exp] );
		}
	}
	
	// 取值范围
	protected function v_range($value, $exp, $data, $scope, $message) {
		if ($this->emp ( $value )) {
			return true;
		}
		$exp = explode ( ',', $exp );
		if (count ( $exp ) >= 2) {
			$value = floatval ( $value );
			if ($value >= $exp [0] && $value <= $exp [1]) {
				return true;
			} else {
				return empty ( $message ) ? sprintf ( __ ( 'Please enter a value between %s and %s.' ), $exp [0], $exp [1] ) : __ ( $message, $exp [0], $exp [1] );
			}
		}
		return true;
	}
	
	// minlength
	protected function v_minlength($value, $exp, $data, $scope, $message) {
		if ($this->emp ( $value )) {
			return true;
		}
		$value = function_exists ( 'mb_strlen' ) ? mb_strlen ( $value ) : strlen ( $value );
		if ($value >= intval ( $exp )) {
			return true;
		} else {
			return empty ( $message ) ? sprintf ( __ ( 'Please enter at least %s characters.' ), $exp ) : __ ( $message, $exp );
		}
	}
	
	// maxlength
	protected function v_maxlength($value, $exp, $data, $scope, $message) {
		if ($this->emp ( $value )) {
			return true;
		}
		$value = function_exists ( 'mb_strlen' ) ? mb_strlen ( $value ) : strlen ( $value );
		if ($value <= intval ( $exp )) {
			return true;
		} else {
			return empty ( $message ) ? sprintf ( __ ( 'Please enter no more than %s characters.' ), $exp ) : __ ( $message, $exp );
		}
	}
	
	// rangelength
	protected function v_rangelength($value, $exp, $data, $scope, $message) {
		if ($this->emp ( $value )) {
			return true;
		}
		$exp = explode ( ',', $exp );
		if (is_array ( $exp ) && count ( $exp ) >= 2) {
			$value = function_exists ( 'mb_strlen' ) ? mb_strlen ( $value ) : strlen ( $value );
			if ($value >= intval ( $exp [0] ) && $value <= intval ( $exp [1] )) {
				return true;
			} else {
				return empty ( $message ) ? __ ( 'Please enter a value between %s and %s characters long.', $exp [0], $exp [1] ) : __ ( $message, $exp [0], $exp [1] );
			}
		}
		return true;
	}
	
	// 用户自定义校验函数
	protected function v_callback($value, $exp, $data, $scope, $message) {
		if ($exp [0] == '@' || $exp [0] == '#' || $exp [0] == '&') {
			$op = $exp [0];
			$exp = substr ( $exp, 1 );
			$exps = explode ( ',', $exp );
			if ($op == '&') {
				$exps = explode ( '.', $exps [0] );
				$scope = new $exps [0] ();
				$func = array ($scope,$exps [1] );
			} else {
				$func = array ($scope,$exps [0] );
			}
		} else {
			$exps = explode ( ',', $exp );
			$func = array_shift ( $exps );
		}
		if (is_callable ( $func )) {
			return call_user_func_array ( $func, array ($value,$data,__ ( $message ) ) );
		}
		return __ ( 'error callback' );
	}
	protected function v_pattern($value, $exp, $data, $scope, $message) {
		return $this->v_regexp ( $value, $exp, $data, $scope, $message );
	}
	// 正则表达式
	protected function v_regexp($value, $exp, $data, $scope, $message) {
		if ($this->emp ( $value )) {
			return true;
		}
		
		if (@preg_match ( $exp, $value )) {
			return true;
		} else {
			return empty ( $message ) ? __ ( 'Please enter a value with a valid extension.' ) : __ ( $message );
		}
	}
	
	// email
	protected function v_email($value, $exp, $data, $scope, $message) {
		if ($this->emp ( $value )) {
			return true;
		}
		if (function_exists ( 'filter_var' )) {
			$rst = filter_var ( $value, FILTER_VALIDATE_EMAIL );
		} else {
			$rst = preg_match ( '/^[_a-z0-9\-]+(\.[_a-z0-9\-]+)*@[a-z0-9][a-z0-9\-]+(\.[a-z0-9-]*)*$/i', $value );
		}
		return $rst ? true : (empty ( $message ) ? __ ( 'Please enter a valid email address.' ) : __ ( $message ));
	}
	
	// url
	protected function v_url($value, $exp, $data, $scope, $message) {
		if ($this->emp ( $value )) {
			return true;
		}
		if (function_exists ( 'filter_var' )) {
			$rst = filter_var ( $value, FILTER_VALIDATE_URL );
		} else {
			$rst = preg_match ( '/^[a-z]+://[^\s]$/i', $value );
		}
		return $rst ? true : (empty ( $message ) ? __ ( 'Please enter a valid URL.' ) : __ ( $message ));
	}
	protected function v_url3($value, $exp, $data, $scope, $message) {
		if ($this->emp ( $value )) {
			return true;
		}
		$rst = preg_match ( '/^((http|ftp)s?:\/\/|\/).*$/', $value );
		return $rst ? true : (empty ( $message ) ? __ ( 'Please enter a valid URL.' ) : __ ( $message ));
	}
	
	// url
	protected function v_ip($value, $exp, $data, $scope, $message) {
		if ($this->emp ( $value )) {
			return true;
		}
		if (function_exists ( 'filter_var' )) {
			$rst = filter_var ( $value, FILTER_VALIDATE_IP, $exp == '6' ? FILTER_FLAG_IPV6 : FILTER_FLAG_IPV4 );
		} else {
			$rst = ip2long ( $value ) === false ? false : true;
		}
		return $rst ? true : (empty ( $message ) ? __ ( 'Please enter a valid IP.' ) : __ ( $message ));
	}
	
	// date:true
	// date:"-"
	// date:"msg"
	// date:["-","msg"]
	protected function v_date($value, $exp, $data, $scope, $message) {
		if ($this->emp ( $value )) {
			return true;
		}
		$sp = is_string ( $exp ) && strlen ( $exp ) == 1 ? $exp : '-';
		$value = explode ( $sp, $value );
		if (count ( $value ) == 3 && @checkdate ( ltrim ( $value [1], '0' ), ltrim ( $value [2], '0' ), $value [0] )) {
			return true;
		}
		return empty ( $message ) ? __ ( 'Please enter a valid date.' ) : __ ( $message );
	}
	
	// datetime:true
	// datetime:"-"
	// datetime:"msg"
	// datetime:["-","msg"]
	protected function v_datetime($value, $exp, $data, $scope, $message) {
		if ($this->emp ( $value )) {
			return true;
		}
		$sp = is_string ( $exp ) && strlen ( $exp ) == 1 ? $exp : '-';
		$times = explode ( ' ', $value );
		$value = explode ( $sp, $times [0] );
		if (count ( $value ) == 3 && isset ( $times [1] ) && @checkdate ( ltrim ( $value [1], '0' ), ltrim ( $value [2], '0' ), $value [0] )) {
			$time = explode ( ':', $times [1] );
			if (count ( $time ) == 3 && $time [0] >= 0 && $time [0] < 24 && $time [1] >= 0 && $time [1] < 59 && $time [2] >= 0 && $time [2] < 59) {
				return true;
			}
		}
		return empty ( $message ) ? __ ( 'Please enter a valid datetime.' ) : __ ( $message );
	}
	protected function emp($value) {
		return is_array ( $value ) ? empty ( $value ) : strlen ( trim ( $value ) ) == 0;
	}
	public function convert($matched) {
		return '{' . ($this->idx ++) . '}';
	}
}