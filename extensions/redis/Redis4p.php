<?php
namespace redis;

class Redis4p {

    private static $instance = [ ];

    private static $db = false;

    private static $host = false;

    private static $port = false;

    private static $auth = false;

    private function __construct() {
    }

    /**
     * get redis instance.
     *
     * @param int $db 数据库.
     * @return \Redis Redis实例.
     */
    public static function getRedis($db = null) {
        if (self::$db === false) {
            self::$db = cfg ( 'db@redis', '8' );
            self::$host = cfg ( 'host@redis', 'localhost' );
            self::$port = cfg ( 'port@redis', '6379' );
            self::$auth = cfg ( '@redis' );
        }
        $db = $db ? $db : self::$db;
        if (! isset ( self::$instance [$db] )) {
            $libRedis = new \Redis ();
            if ($libRedis->connect ( self::$host, self::$port )) {
                if (self::$auth) {
                    $libRedis->auth ( self::$auth );
                }
                $libRedis->select ( $db );
            }
            if (! $libRedis->isConnected ()) {
                throw new \Exception ( 'cannt connect redis db' );
            }
            self::$instance [$db] = $libRedis;
        }
        return self::$instance [$db];
    }

    /**
     * 加锁
     *
     * @param string 锁名
     * @param int $timeout 超时.
     * @return bool true 可用
     *
     */
    public static function lock($key = '', $timeout = 600) {
        $redis = self::getRedis ();
        $key = trim ( $key );
        if (empty ( $key )) {
            return false;
        }
        if ($redis->exists ( $key ) == true) {
            return false;
        }
        return $redis->set ( $key, 1, $timeout );
    }

    /**
     * 解锁.
     *
     * @param string $key 锁名.
     */
    public static function unlock($key = '') {
        $redis = self::getRedis ();
        $redis->delete ( $key );
    }
}
