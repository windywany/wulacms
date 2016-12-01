<?php
namespace smschuanglan;

use sms\classes\SmsVendor;

class ChuangLanVendor extends SmsVendor {

    /**
     * 创蓝通道配置.
     *
     * @see \sms\classes\ISMSVendor::init_preference_fields()
     * @param \AbstractForm $form 设置表单.
     */
    public function init_preference_fields($form) {
        $form->addField ( 'cl_account', [ 'label' => '创蓝帐号','group' => 'c2','col' => '3','rules' => [ 'required' => '请填写帐号' ] ] );
        $form->addField ( 'cl_passwd', [ 'label' => '密码','widget' => 'password','group' => 'c2','col' => '3','rules' => [ 'required' => '请填写密码' ] ] );
        $form->addField ( 'cl_api', [ 'label' => '接口地址','group' => 'c2','col' => '6','rules' => [ 'required' => '请填写接口地址','url' => '请正确填写接口地址' ] ] );
    }

    public function send($template, $phone) {
        $account = cfg ( "cl_account@sms" );
        $pswd = cfg ( "cl_passwd@sms" );
        $url = cfg ( "cl_api@sms" );
        $content = $template->getContent ();
        if ($account && $pswd && $url) {
            if ($phone && $content) {
                $postData = array ('account' => $account,'pswd' => $pswd,'msg' => $content,'mobile' => $phone );
                $result = $this->sendSms ( $url, http_build_query ( $postData ) );
                if ($result) {
                    $this->error = $result;
                    $rst = explode ( ',', $result );
                    if (isset ( $rst [1] ) && $rst [1] == '0') {
                        return true;
                    }
                }
            } else {
                $this->error = '手机号或发送内容为空';
            }
        } else {
            $this->error = '请配置创蓝通道';
        }
        return false;
    }

    public function getName() {
        return '创蓝';
    }

    private function sendSms($url, $post_data) {
        $ch = curl_init ();
        if ($ch) {
            curl_setopt ( $ch, CURLOPT_POST, 1 );
            curl_setopt ( $ch, CURLOPT_HEADER, 0 );
            curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
            curl_setopt ( $ch, CURLOPT_URL, $url );
            curl_setopt ( $ch, CURLOPT_POSTFIELDS, $post_data );
            $result = curl_exec ( $ch );
            $this->error = curl_error ( $ch );
            curl_close ( $ch );
            return $result;
        } else {
            $this->error = '无法初始化curl';
        }
        return false;
    }

    public static function get_sms_vendors($vendors) {
        $vendors ['cl'] = new ChuangLanVendor ();
        return $vendors;
    }
}