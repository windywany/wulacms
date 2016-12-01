<?php

namespace passport\models;

use passport\forms\PhoneSignUpForm;

class PhoneSignUpModel extends MailSignUpModel {
	protected function createForm($data = []) {
		return new PhoneSignUpForm($data);
	}

}