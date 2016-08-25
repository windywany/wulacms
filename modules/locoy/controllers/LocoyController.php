<?php
class LocoyController extends Controller {
	public function index() {
		$data = array ();
		if (! bcfg ( 'locoy_enabled@locoy' )) {
			$data ['errorMsg'] = '接口未开启.';
		} else if ($this->user->isLogin ()) {
			return 'LOGIN-SUCCESS';
		}
		return view ( 'login.tpl', $data );
	}
	public function index_post() {
		$safe_code = rqst ( 'safe_code' );
		
		if (! bcfg ( 'locoy_enabled@locoy' ) || empty ( $safe_code ) || $safe_code != cfg ( 'locoy_secret@locoy' )) {
			$data ['errorMsg'] = '登录失败:接口未开启或安全码不对.';
		} else {
			$form = new AuthForm ();
			$formData = $form->valid ();
			
			if ($formData) {
				if (strpos ( $formData ['username'], '@' )) {
					$where ['email'] = $formData ['username'];
					$id = 'email';
				} else {
					$where ['username'] = $formData ['username'];
					$id = 'username';
				}
				$user = dbselect ( '*' )->from ( '{user}' )->where ( $where );
				if (count ( $user ) == 0 || $user [0] ['passwd'] != md5 ( $formData ['passwd'] ) || $user [0] [$id] != $formData ['username']) {
					$data ['errorMsg'] = __ ( '@auth:Invalide User Name or Password.' );
				} else if (empty ( $user [0] ['status'] )) {
					$data ['errorMsg'] = __ ( '@auth:User is locked!' );
				} else {
					$user = $user [0];
					$user ['logined'] = true;
					$this->user->save ( $user );
					$data ['successMsg'] = $this->user->getUserName ();
				}
			} else {
				$data ['errorMsg'] = __ ( '@auth:Invalide User Name or Password.' );
			}
		}
		
		if (empty ( $data ['errorMsg'] )) {
			ActivityLog::info ( __ ( '%s(%s) Login for Locoy successfully.', $this->user->getAccount (), $this->user->getDisplayName () ), 'Login' );
			return 'LOGIN-SUCCESS';
		}
		return view ( 'login.tpl', $data );
	}
	public function publish($model) {
		if (empty ( $model )) {
			return '{"status":300,"message":"未指定模型"}';
		}
		if (! bcfg ( 'locoy_enabled@locoy' )) {
			return '{"status":300,"message":"接口未开启"}';
		} else if (! $this->user->isLogin ()) {
			return '{"status":300,"message":"未登录"}';
		} else if (! icando ( 'c:cms/page' )) {
			return '{"status":300,"message":"无权限"}';
		}
		$allowed_models = cfg ( 'allowed_models@locoy' );
		if ($allowed_models && in_array ( $model, explode ( ',', $allowed_models ) )) {
			$cModel = dbselect ( '*' )->from ( '{cms_model}' )->where ( array ('refid' => $model,'deleted' => 0,'hidden' => 0,'creatable' => 1 ) )->get ();
			if (! $cModel) {
				return '{"status":300,"message":"模型不存在"}';
			}
			$type = 'page';
			$options = ChannelForm::getChannelTree ( $model, $type == 'topic' );
			$template = $cModel ['template'] ? $cModel ['template'] : ($type == 'topic' ? 'topic_form.tpl' : 'page_form.tpl');
			$data ['options'] = $options;
			$data ['page_type'] = $type;
			$data ['model'] = $model;
			$data ['pageTypeName'] = $type == 'topic' ? '专题' : '内容';
			$data ['modelName'] = $cModel ['name'];
			$data ['id'] = 0;
			$data ['img_pagination'] = true;
			$formName = ucfirst ( $type ) . 'Form';
			$form = new DynamicForm ( $formName );
			if (extension_loaded ( 'scws' )) {
				$data ['gkeywords'] = true;
			}
			$data ['channel'] = '';
			$data ['view_count'] = rand ( 0, 1000 );
			$widgets = ModelFieldForm::loadCustomerFields ( $form, $model );
			if ($widgets) {
				$data ['widgets'] = new DefaultFormRender ( AbstractForm::prepareWidgets ( CustomeFieldWidgetRegister::initWidgets ( $widgets ) ) );
			}
			$contentModel = get_page_content_model ( $model );
			$cform = $contentModel ? $contentModel->getForm () : false;
			
			$data ['rules'] = $form->rules ( $cform );
			if ($template {0} == '@') {
				$page_form = view ( substr ( $template, 1 ), $data );
			} else {
				$page_form = view ( 'cms/views/page/' . $template, $data );
			}
			$data ['page_form'] = str_replace ( 'target="ajax"', '', $page_form->render () );
			$data ['page_form'] = preg_replace ( '#<script.+/script>#ims', '', $data ['page_form'] );
			return view ( 'publish.tpl', $data );
		} else {
			return '{"status":300,"message":"模型不可发布"}';
		}
	}
	public function publish_post($model) {
		if (empty ( $model )) {
			return '{"status":300,"message":"未指定模型"}';
		}
		if (! bcfg ( 'locoy_enabled@locoy' )) {
			return '{"status":300,"message":"接口未开启"}';
		} else if (! $this->user->isLogin ()) {
			return '{"status":300,"message":"未登录"}';
		} else if (! icando ( 'c:cms/page' )) {
			return '{"status":300,"message":"无权限"}';
		}
		
		$allowed_models = cfg ( 'allowed_models@locoy' );
		
		if ($allowed_models && in_array ( $model, explode ( ',', $allowed_models ) )) {
			$content = trim ( rqst ( 'content' ) );
			if (empty ( $content )) {
				return '{"status":201,"message":"内容为空"}';
			}
			return CmsPage::save ( 'page', $model, $this->user );
		} else {
			return '{"status":300,"message":"此模型不支持采集"}';
		}
	}
	public function xcaiji() {
		if (! bcfg ( 'locoy_enabled@locoy' )) {
			return '{"status":300,"message":"接口未开启"}';
		} else if (! $this->user->isLogin ()) {
			return '{"status":300,"message":"未登录"}';
		} else if (! icando ( 'c:caiji/content' )) {
			return '{"status":300,"message":"无权限"}';
		}
		$url = rqst ( 'url', '' );
		if (! $url) {
			log_warn ( 'url is empty');
			return '{"status":300,"message":"URL为空"}';
		}
		$data ['create_time'] = $data ['update_time'] = time ();
		$data ['create_uid'] = $data ['update_uid'] = 0;
		$data ['title'] = rqst ( 'title', '' );
		$data ['content'] = rqst ( 'content', '' );
		$data ['keywords'] = rqst ( 'keywords', '' );
		$task = trim ( rqst ( 'group' ) );
		$data ['task_id'] = 0;
		if ($task) {
			$taskid = dbselect ( 'TSK.id' )->from ( '{caiji_task} AS TSK' )->join ( '{catalog} AS CA', 'TSK.type = CA.id' )->where ( array ('CA.name' => $task ) )->get ( 0 );
			if ($taskid && $taskid ['id']) {
				$data ['task_id'] = $taskid ['id'];
			}
		}
		if ($data ['task_id'] == 0) {
			log_warn ( 'group not found:' . $task );
			return '{"status":300,"message":"分组不存在"}';
		}
		
		$md5 = md5 ( $url );
		$data ['url_num'] = ord ( $md5 [0] );
		$data ['url_key'] = $md5;
		$data ['url'] = $url;
		dbinsert ( $data )->into ( '{caiji_page}' )->exec ();
		return '{"status":200,"message":"采集完成"}';
	}
}