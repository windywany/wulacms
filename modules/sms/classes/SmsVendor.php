<?php
namespace sms\classes;

/**
 * 短信通道基类.
 *
 * @author Leo Ning.
 *
 */
abstract class SmsVendor implements ISMSVendor {

    protected $error = null;

    public function init_preference_fields($form) {
    }

    public function getError() {
        return $this->error;
    }

    public function usePlatformTemplate() {
        return false;
    }
}