<?php
/**
 *
 * User: Leo Ning.
 * Date: 08/10/2016 13:45
 */

namespace passport\models\param;

class ChpasswdParam extends \ParameterDef {
	public $mid;
	public $oldPasswd;
	public $newPasswd;
	public $confirmPasswd;
}