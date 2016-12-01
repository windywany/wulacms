<?php

function smarty_modifiercompiler_argsurl($params, $compiler) {
	$base_url = array_shift($params);
	$args     = array_shift($params);
	$value    = array_shift($params);
	$aname    = array_shift($params);
	$value    = $value ? $value : 'null';
	$aname    = is_null($aname) ? 'null' : $aname;
	$output   = "safe_args_url({$base_url},{$args},{$value},{$aname})";

	return $output;
}

function safe_args_url($url, \args\ArgGroup $args, $value = null, $arg = null) {
	static $urls = [];
	if (isset($urls[ $url ])) {
		$url = $urls[ $url ];
	} else {
		$urls[ $url ] = rtrim(preg_replace('#index\.s?html?$#', '', $url), '/');
		if (!preg_match('#^(https?://|/).+$#', $urls[ $url ])) {
			$urls[ $url ] = '/' . $urls[ $url ];
		}
		$url = $urls[ $url ];
	}
	$url = $args->parse($url, $value, $arg);

	return $url;
}

