<?php
namespace sms\classes;

/**
 * 业务短信模板基类.
 *
 * @author Leo Ning.
 *
 */
abstract class SMSTemplate {

    protected  $params = [ ];

    protected $options = [ ];

    protected $content = null;

    /**
     * 模板名称.
     *
     * @return string 模板名称.
     */
    public abstract function getName();

    /**
     * 短信模板,模板中变量使用{}包起来.
     *
     * @return 获取短信模板.
     */
    public abstract function getTemplate();

    /**
     * 获取此模板的参数.
     *
     * @return array 模板参数.
     */
    public function getArgs() {
        return [ ];
    }

    /**
     * 获取参数描述.
     *
     * @return array key=>value.
     */
    public function getArgsDesc() {
        return array ();
    }

    /**
     * 设置业务参数.
     *
     * @param array $args 参数.
     */
    public function setParams($params) {
        $this->params = $params;
    }

    /**
     * 设置选项.
     *
     * @param array $options
     */
    public function setOptions($options) {
        $this->options = $options;
    }

    /**
     * 获取配置选项.
     *
     * @return array 配置选项.
     */
    public function getOptions() {
        return $this->options;
    }

    public function setContent($content) {
        $this->content = $content;
    }

    /**
     * 获取要发送的内容.
     *
     * @return string 内容.
     */
    public function getContent() {
        $tpl = $this->content ? $this->content : $this->getTemplate ();
        $s = [ ];
        $r = [ ];
        $args = $this->getArgs ();
        foreach ( $args as $k => $v ) {
            $s [] = '{' . $k . '}';
            $r [] = $v;
        }
        $content = str_replace ( $s, $r, $tpl );
        return $content;
    }
}