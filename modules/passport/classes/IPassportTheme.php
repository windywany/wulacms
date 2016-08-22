<?php

/**
 * 通行证模板.
 * @author ngf
 *
 */
interface IPassportTheme {
	function getStyles();
	/**
	 * get the join(registration) form template file.
	 *
	 * @return string template file name.
	 */
	function join();
	/**
	 * get the join(registration) form done template file.
	 *
	 * @return string template file name.
	 */
	function done();
	/**
	 * get the active tip page template file.
	 *
	 * @return string template file name.
	 */
	function active();
	/**
	 * get the actived tip page template file.
	 *
	 * @return string template file name.
	 */
	function actived();
	/**
	 * get the forgetpassword form page template file.
	 *
	 * @return string template file name.
	 */
	function forgetpassword();
	/**
	 * get the mail content for forget password.
	 *
	 * @return array <br/><b>subject</b> - the mail subject.<br/><b>content</b> - the mail content.<br/><b>type</b> - the type of mail content - html|text.
	 *        
	 */
	function forgetpassword_mail($user);
	/**
	 * get the login form page template file.
	 *
	 * @return string template file name.
	 */
	function login();	
	/**
	 * get active mail content template.
	 *
	 * @param $user the
	 *        	user information.
	 * @return array <br/><b>subject</b> - the mail subject.<br/><b>content</b> - the mail content.<br/><b>type</b> - the type of mail content - html|text.
	 */
	function active_mail($user);
}