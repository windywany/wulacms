<?php

namespace passport\classes;

abstract class AbstractPassportController extends \Controller {
	protected $checkUser = array('passport', 'vip');
}