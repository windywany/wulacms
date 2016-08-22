<?php
class AdminPreferenceBaseForm {
	/**
	 *
	 * @param DynamicForm $form
	 */
	public static function init($form) {
		$form ['site_domain'] = array ('group' => '0','col' => 3,'label' => 'SEO监控域名' );
		$form ['cms_url'] = array ('group' => '0','col' => 3,'label' => '前台页面URL' );
		$form ['enable_short'] = array ('group' => '0','col' => '2','label' => '显示快捷','widget' => 'radio','default' => '0','defaults' => "1=启用\n0=不启用" );
		$form ['enable_report'] = array ('group' => '0','col' => '2','label' => '显示报表','widget' => 'radio','default' => '0','defaults' => "1=启用\n0=不启用" );
		$form ['enable_s_role'] = array ('group' => '0','col' => '2','label' => '新建菜单分组','widget' => 'radio','default' => '0','defaults' => "1=启用\n0=不启用" );
		$form ['keywords_count'] = array ('group' => '1','col' => 3,'label' => '自动获取关键词数量','note' => '不填写则使用默认值5.','rules' => array ('num' => '只能是整数。' ) );
		$form ['tags_count'] = array ('group' => '1','col' => 2,'label' => '替换内链个数','note' => '不填写时使用默认值10.','rules' => array ('num' => '只能是整数。' ) );
		$form ['tag_count'] = array ('group' => '1','col' => 2,'label' => '相同内链替换次数','note' => '为空时全部替换.','rules' => array ('num' => '只能是整数。' ) );
		$form ['default_suffix'] = array ('group' => '1','col' => 2,'label' => '默认页面扩展名','note' => '不填写则使用默认值html.' );
		$form ['allow_dash'] = array ('group' => '1','col' => 3,'label' => 'URL允许下划线','note' => '开启后将影响性能.','widget' => 'radio','default' => '0','defaults' => "1=启用\n0=不启用" );
		$form ['thumb_w'] = array ('group' => '1_1','col' => 3,'label' => '缩略图宽','note' => '0为不指定.','default' => '0','rules' => array ('num' => '只能是整数' ) );
		$form ['thumb_h'] = array ('group' => '1_1','col' => 3,'label' => '缩略图高','note' => '0为不指定.','default' => '0','rules' => array ('num' => '只能是整数' ) );
		
		
		$form ['title_repeatable'] = array ('group' => '1_1','col' => 2,'label' => '标题重复','widget' => 'radio','default' => '1','defaults' => "1=是\n0=否" );
		
		$form ['title2_repeatable'] = array ('group' => '1_1','col' => 2,'label' => '短标题重复','widget' => 'radio','default' => '1','defaults' => "1=是\n0=否" );
		
		$form ['tag_empty'] = array ('group' => '1_1','col' => 2,'label' => '标签为空','widget' => 'radio','default' => '1','defaults' => "1=是\n0=否" );
		
		
		
		$form ['img_follow'] = array ('label' => '图片跟随内容','widget' => 'textarea','note' => '文章正文中的所有图片后将跟随此内容.' );
		$form ['img_next_page'] = array ('label' => '图片分页无下一页时跳转页面' );
		$form ['enable_copy'] = array ('group' => '1_2','col' => '4','label' => '是否复制功能','widget' => 'radio','default' => '0','defaults' => "1=启用\n0=不启用" );
		$form ['disable_approving'] = array ('group' => '2','col' => '4','label' => '是否启用审核功能','note' => '启用审核功能后只有状态是已发布的页面才能被访问.','widget' => 'radio','default' => '0','defaults' => "1=启用\n0=不启用" );
		$status = array ();
		$pageStatus = get_cms_page_status ();
		foreach ( $pageStatus as $id => $v ) {
			if ($id) {
				$status [] = $id . '=' . $v;
			}
		}
		$status = implode ( "\n", $status );
		$form ['page_new_status'] = array ('group' => '2','col' => '2','label' => '新页面状态','widget' => 'select','default' => '3','defaults' => $status );
		$form ['page_update_status'] = array ('group' => '2','col' => '2','label' => '修改后状态','widget' => 'select','default' => '1','defaults' => $status );
		$form ['allow_bentch_approve'] = array ('group' => '2','col' => '3','label' => '启用批量审核','note' => '启用审核功能时有效','widget' => 'radio','default' => '0','defaults' => "1=启用\n0=不启用" );
		$form ['enable_bentch_publish'] = array ('group' => '3','col' => '4','label' => '启用定时发布','note' => '启用后,状态为"待发布"的页面将在指定的发布时自动发布.','widget' => 'radio','default' => '0','defaults' => "1=启用\n0=不启用" );
		$form ['view_in_time'] = array ('group' => '3','col' => '4','label' => '可访问待发布页面','note' => '启用后,状态为"待发布"的页面可以被用户访问.','widget' => 'radio','default' => '0','defaults' => "1=启用\n0=不启用" );
		$form ['enable_group_bind'] = array ('group' => '3','col' => '3','label' => '启用栏目与用户组绑定','widget' => 'radio','default' => '0','defaults' => "1=启用\n0=不启用" );
		$form ['enable_cron_update'] = array ('group' => '4','col' => '4','label' => '启用栏目滚动更新','widget' => 'radio','default' => '0','defaults' => "1=启用\n0=不启用" );
		$form ['update_chs'] = array ('group' => '4','col' => '8','label' => '滚动栏目ID','note' => '多个栏目用逗号分隔' );
		$form ['update_sub'] = array ('group' => '5','col' => '4','label' => '包括子栏目','widget' => 'radio','default' => '0','defaults' => "1=启用\n0=不启用" );
		$form ['update_method'] = array ('group' => '5','col' => '3','label' => '滚动更新方式','widget' => 'radio','default' => '0','defaults' => "1=按时间\n0=随机" );
		$form ['update_cnt'] = array ('group' => '5','col' => '3','label' => '一次更新条数','note' => '默认10条','rules' => array ('num' => '只能是整数。' ) );
	}
}