<?php
defined ( 'KISSGO' ) or exit ( 'No direct script access allowed' );
/**
 *
 * @param RestServer $server        	
 * @return unknown
 */
function hook_for_init_rest_server($server) {
	if (bcfg ( 'allow_remote@media' ) && ! bcfg ( 'store_type@media' )) {
		$server->registerClass ( new MediaRestService (), '1', 'media' );
	}
	return $server;
}