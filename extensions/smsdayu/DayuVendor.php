<?php
namespace smsdayu;

use sms\classes\SmsVendor;

class DayuVendor extends SmsVendor {

    /**
     * (non-PHPdoc)
     *
     * @see \sms\classes\SmsVendor::init_preference_fields()
     */
    public function init_preference_fields($form) {
        $form->addField ( 'dy_appkey', [ 'label' => 'App Key','group' => 'd2','col' => '3','rules' => [ 'required' => '请填写帐号' ] ] );
        $form->addField ( 'dy_appsecret', [ 'label' => 'App Secret','widget' => 'password','group' => 'd2','col' => '3','rules' => [ 'required' => '请填写密码' ] ] );
        $form->addField ( 'sign_name', [ 'group' => 'd2','col' => 3,'label' => '短信签名','rules' => [ 'required' => '请填写短信签名' ] ] );
        $form->addField ( 'dy_ssl', [ 'label' => '启用SSL','group' => 'd2','col' => '3','widget' => 'radio','defaults' => "0=否\n1=是",'default' => 0 ] );
    }

    /**
     * (non-PHPdoc)
     *
     * @see \sms\classes\ISMSVendor::send()
     */
    public function send($template, $phone) {
        $c = new \TopClient ();
        $c->appkey = cfg ( 'dy_appkey@sms' );
        $c->secretKey = cfg ( 'dy_appsecret@sms' );
        $c->format = 'json';
        if (bcfg ( 'dy_ssl@sms' )) {
            $c->gatewayUrl = 'https://eco.taobao.com/router/rest';
        }
        $req = new \AlibabaAliqinFcSmsNumSendRequest ();
        $req->setSmsType ( "normal" );
        $req->setSmsFreeSignName ( cfg ( 'sign_name@sms' ) );
        $req->setSmsParam ( json_encode ( $template->getArgs () ) );
        $req->setRecNum ( $phone );
        $tpl = $template->getOptions ()['tpl'];
        $req->setSmsTemplateCode ( $tpl );
        $resp = $c->execute ( $req );
        if (isset ( $resp->result ) && ! $resp->result->err_code) {
            return true;
        }
        $this->error = '[' . $resp->code . ']' . $resp->sub_msg;
        return false;
    }

    public function getName() {
        return '阿里大鱼';
    }
    
    /*
     * (non-PHPdoc) @see \sms\classes\SmsVendor::usePlatformTemplate()
     */
    public function usePlatformTemplate() {
        return true;
    }

    public static function get_sms_vendors($vendors) {
        $vendors ['dayu'] = new DayuVendor ();
        return $vendors;
    }
}