<?php
/**
 * kissgo framework that keep it simple and stupid, go go go ~~
 *
 * @author Windywany
 * @package kissgo
 * @date 12-9-16 下午6:16
 * $Id$
 */
defined ( 'KISSGO' ) or exit ( 'No direct script access allowed' );
/**
 * Session处理器接口
 */
if (! interface_exists ( 'SessionHandlerInterface' )) {
	interface SessionHandlerInterface {
		function close();
		function destroy($session_id);
		function gc($max_life_time);
		function open($save_path, $name);
		function read($session_id);
		function write($session_id, $session_data);
	}
}
// use cookie for session id
@ini_set ( 'session.use_cookies', 1 );
/**
 * 得到session名.
 *
 * @return mixed
 */
function get_session_name() {
	return apply_filter ( 'get_session_name', APP_NAME . '_SID' );
}