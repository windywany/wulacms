<?php
/**
 * 验证表单。
 * @author NingGuangfeng
 *
 */
class ValidateformController extends Controller {
	public function index($__fn = false) {
		$data = array ('success' => true );
		if (empty ( $__fn )) {
			$data ['success'] = false;
			$data ['msg'] = '无法校验表单';
			return new JsonView ( $data );
		}
		$__fn = str_replace ( '.', '\\', $__fn );
		if (! is_subclass_of2 ( $__fn, 'AbstractForm' )) {
			
			$data ['success'] = false;
			$data ['msg'] = '表单类' . $__fn . '不存在.';
			return new JsonView ( $data );
		}
		$form = new $__fn ();
		if (! $form->valid ()) {
			$data ['success'] = false;
			$data ['errors'] = $form->getErrors ();
		}
		return new JsonView ( $data );
	}
}