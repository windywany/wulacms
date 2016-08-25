<?php
interface ICatalogProvider {
	public function getCustomForm($form, $data);
	public function save($data, $id);
}