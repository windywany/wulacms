<?php

namespace system\classes;

interface IService {
	/**
	 * 配置表单.
	 *
	 *
	 * @return \AbstractForm
	 */
	function getConfigForm();

	/**
	 * 服务名称.
	 *
	 * @return string
	 */
	function getName();

	/**
	 * 服务说明.
	 *
	 * @return string
	 */
	function getDescription();

	/**
	 * 最终说明.
	 *
	 * @param array $config 配置项
	 *
	 * @return string
	 */
	function getNote($config);
}