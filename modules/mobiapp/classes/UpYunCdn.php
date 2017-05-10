<?php
class UpYunCdn {
    // SDK 版本
    const VERSION = '2.0';

    const EXPECT = 'Expect:';
    const AUTHORIZATION = 'Authorization:';
    const DATE = 'Date:';

    // 缓存刷新接口
    const PURGE_API = 'http://purge.upyun.com/purge/';

    private $_bucketname;
    private $_username;
    private $_password;

    // 获取当前 SDK 版本号
    public function version() {
        return self::VERSION;
    }

    // 初始化 UpYun 接口
    public function __construct($bucketname = '', $username ='', $password ='') {
        $bucketname = empty($bucketname)?cfg('bucketname@mobiapp'):$bucketname;
        $this->_bucketname = $bucketname;
        
        $username = empty($username)?cfg('username@mobiapp'):$username;
        $this->_username = $username;
        
        $password = empty($password)?cfg('password@mobiapp'):$password;
        $this->_password = md5($password);
    }

    // 刷新缓存
    public function purge($url) {
        $date = gmdate('D, d M Y H:i:s \G\M\T');
        $sign = md5("{$url}&{$this->_bucketname}&{$date}&{$this->_password}");

        $ch = curl_init(self::PURGE_API);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            self::EXPECT,
            self::AUTHORIZATION . " UpYun {$this->_bucketname}:{$this->_username}:{$sign}",
            self::DATE . $date
        ));
        curl_setopt($ch, CURLOPT_POSTFIELDS, "purge=" . urlencode($url));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($code != 200) {
            $return = array(
                'status' => false,
                'msg' => $result
            );
        }
        else {
            $return = array(
                'status' => true,
                'msg' => 'success'
            );
        }
        curl_close($ch);
        return $return;
    }
}
