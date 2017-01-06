<?php

namespace pay\classes;

interface IPayChannel {
	function getName();

	function getSettingForm($form);

	function onCallback();

	function onNotify();

	function checkForm();

	function doCheck();

	function getPayForm($order);
}