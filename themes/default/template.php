<?php
defined ( 'KISSGO' ) or exit ( 'No direct script access allowed' );
function prepare_template_data(&$data) {
	$data ['ngf'] = '宁广丰';
	$data ['formater'] = new ConditionURLFormater ( '{city}/shop/{region}/{cbd}' );
}

