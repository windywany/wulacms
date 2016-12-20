<?php

namespace finance\classes;

interface IPayChannel {
	function getName();

	function getSettingForm($form);

	function onCallback();
}