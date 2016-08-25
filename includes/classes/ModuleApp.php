<?php
abstract class ModuleApp {
	/**
	 *
	 * @var Passport
	 */
	protected $user;
	/**
	 * 设置用户.
	 *
	 * @param Passport $user        	
	 */
	public function setUser($user) {
		$this->user = $user;
	}	
}