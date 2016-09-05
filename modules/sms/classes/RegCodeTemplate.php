<?php
namespace sms\classes;

class RegCodeTemplate extends SMSTemplate {

    private $code = null;

    public static function get_sms_templates($tpls) {
        $tpls ['reg_verify'] = new RegCodeTemplate ();
        return $tpls;
    }

    public function getTemplate() {
        return '验证码是：{code},请不要把验证码透漏给其他人。';
    }

    public function getArgsDesc() {
        return [ 'code' => '验证码' ];
    }
    
    /*
     * (non-PHPdoc) @see \sms\classes\SMSTemplate::getArgs()
     */
    public function getArgs() {
        if (! $this->code) {
            $this->code = rand_str ( 6, '0-9' );
            $_SESSION ['reg_verify_code'] = $this->code;
            $_SESSION ['reg_verify_expire'] = time () + 1800;
        }
        return [ 'code' => $this->code ];
    }

    public function getName() {
        return '注册验证码';
    }

    public static function validate($code) {
        $code1 = sess_get ( 'reg_verify_code' );
        $time1 = sess_get ( 'reg_verify_expire', 0 );
        if ($time1 > time ()) {
            return $code && $code1 && strtolower ( $code1 ) == strtolower ( $code );
        } else {
            sess_del ( 'reg_verify_expire' );
            sess_del ( 'reg_verify_code' );
            return false;
        }
    }
}