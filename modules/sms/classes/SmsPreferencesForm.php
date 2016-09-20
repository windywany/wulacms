<?php
namespace sms\classes;

class SmsPreferencesForm extends \AbstractForm {

    private $sms_enabled = array ('group' => 1,'col' => 3,'label' => '是否启用','widget' => 'radio','defaults' => "0=停用\n1=启用",'default' => 0 );

    private $captcha_enabled = array ('group' => 1,'col' => 3,'label' => '启用验证码','widget' => 'radio','defaults' => "0=停用\n1=启用",'default' => 0 );

	private $test_mode = array ('group' => 1,'col' => 3,'label' => '测试模式','widget' => 'radio','defaults' => "0=停用\n1=启用",'default' => 1,'note'=>'用于测试业务逻辑' );

    private $vendor = array ('group' => 1,'col' => 3,'label' => '通道提供商','widget' => 'select','rules' => [ 'required(sms_enabled_1:checked)' => '请选择短信通道提供商' ] );

    /**
     * (non-PHPdoc)
     *
     * @see AbstractForm::init_form_fields()
     */
    protected function init_form_fields($data, $value_set) {
        $vendors = Sms::vendors ();
        $vs = [ '=请选择短信通道' ];
        foreach ( $vendors as $k => $v ) {
            $vs [] = $k . '=' . $v->getName ();
        }
        $vs = implode ( "\n", $vs );
        $this->getField ( 'vendor' )
            ->setOptions ( [ 'defaults' => $vs ] );
        $vendor = cfg ( 'vendor@sms' );
        $rq = false;
        if ($vendor) {
            $v = $vendor;
            if (isset ( $vendors [$v] )) {
                $v = $vendors [$v];
                $v->init_preference_fields ( $this );
                if ($v->usePlatformTemplate ()) {
                    $rq = true;
                }
            }
        }
        $row = 100;
        $this->addField ( '_sp', $this->seperator ( '短信模板' ) );
        $templates = Sms::templates ();
        foreach ( $templates as $t => $v ) {
            $args = $v->getArgsDesc ();
            $notes = [ ];
            foreach ( $args as $arg => $desc ) {
                $notes [] = '{' . $arg . '}:' . $desc;
            }
            if ($rq) {
                $this->addField ( $t . '_cnt', [ 'group' => $row,'col' => 9,'label' => $v->getName (),'default' => $v->getTemplate (),'note' => implode ( '；', $notes ) ] );
                $this->addField ( $t . '_tpl', [ 'group' => $row,'col' => 3,'label' => $v->getName () . '的第三方模板','rules' => [ 'required(sms_enabled_1:checked)' => '请为此短信指定模板' ] ] );
            } else {
                $this->addField ( $t . '_cnt', [ 'label' => $v->getName (),'default' => $v->getTemplate (),'note' => implode ( '；', $notes ) ] );
            }
        }
    }
}
