<?php
namespace sms\classes;

interface ISMSVendor {

    /**
     * 短信提供商名称.
     *
     * @return string 短信提供商名称.
     */
    function getName();

    /**
     * 出错信息.
     *
     * @return string 当发送失败时平台返回的错误信息.
     */
    function getError();

    /**
     * 发送短信.
     *
     * @param ISMSTemplate $template 短信模板.
     * @param string $phone 手机号.
     * @return bool 发送是否成功.
     */
    function send($template, $phone);

    /**
     * 是否有平台模板.
     *
     * @return bool 是否平台上定义模板.
     */
    function usePlatformTemplate();

    /**
     * 配置表单.
     *
     * @param \AbstractForm $form 表单实例.
     */
    function init_preference_fields($form);
}