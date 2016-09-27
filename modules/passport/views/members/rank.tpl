<div class="row">
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-4 hidden-xs hidden-sm">
		<h1 class="txt-color-blueDark">
			<i class="fa fa-fw fa-rmb"></i> 会员等级配置
		</h1>
	</div>

</div>
<section id="widget-grid">
	<div class="row">
		<article class="col-sm-12 sortable-grid ui-sortable">
			<div data-widget-sortable="false" data-widget-collapsed="false" data-widget-custombutton="false" data-widget-fullscreenbutton="false" data-widget-deletebutton="false" data-widget-togglebutton="false" data-widget-editbutton="false" data-widget-colorbutton="false" id="wid-apply-form-1" class="jarviswidget" role="widget">
				<header role="heading">
					<span class="widget-icon"> <i class="fa fa-edit"></i>
					</span>
					<h2>会员等级配置</h2>
				<span class="jarviswidget-loader"><i class="fa fa-refresh fa-spin"></i></span></header>
				<div role="content">
					<div class="widget-body widget-hide-overflow no-padding">
					
						<form target="ajax" id="apply-form" method="post" action="{'passport/level/index_post'|app}" data-widget="nuiValidate" name="ApplyForm" class="smart-form">
							<div class="tab-pane active" id="base-user-info">
								<fieldset>
								<div class="col-sm-6">
									<a href="javascript:;" class="btn btn-success add_btn_success" id="newrank"><i class="glyphicon glyphicon-plus"></i>新增等级</a>
								</div>
								<div id="add_html">
									<legend style="margin-bottom:20px;">&nbsp;</legend>
									{foreach $ranks as $k=>$r}
									<div id="del_id_{$r.id}" class="row">
										<section class="col col-2">
											<label class="label" for="level">等级</label>
											<label class="input">
												<input type="text" id="level" name="{$r.id}[level]" value="{$r.level}" class="proportion" readonly>
											</label>
										</section>

										<section class="col col-2">
											<label class="label" for="proportion">等级称号</label>
											<label class="input">
												<input type="text" id="spend_proportion" name="{$r.id}[rank]" value="{$r.rank}" class="spend_proportion">
											</label>
										</section>

										<section class="col col-2">
											<label class="label" for="proportion">所需积分（金币等）</label>
											<label class="input">
												<input type="text" id="spend_proportion" name="{$r.id}[coins]" value="{$r.coins}" class="spend_proportion">
											</label>
										</section>
										<section class="col col-3">
											<label class="label" for="status">操作</label>
											<div class="inline-group">
												<a class="btn btn-danger btn-xs" href="{'passport/level/del'|app}{$r.id}" target="ajax" data-confirm="你确定要删除这个吗?"> <i class="glyphicon glyphicon-trash"></i></a>
											</div>
										</section>
									</div>
									{foreachelse}
									<div id="del_id_0" class="row">
										<section class="col col-2">
											<label class="label" for="level">等级</label>
											<label class="input">
												<input type="text" id="level" name="0[level]" value="0" class="proportion">
											</label>
										</section>

										<section class="col col-2">
											<label class="label" for="proportion">等级称号</label>
											<label class="input">
												<input type="text" id="spend_proportion" name="0[rank]" value="" class="spend_proportion">
											</label>
										</section>

										<section class="col col-2">
											<label class="label" for="proportion">所需积分（金币等）</label>
											<label class="input">
												<input type="text" id="spend_proportion" name="0[coins]" value="0" class="spend_proportion">
											</label>
										</section>
										<section class="col col-3 hide">
											<label class="label" for="status">操作</label>
											<div class="inline-group">
												<a class="btn btn-danger btn-xs" href="{'passport/level/del'|app}{$r.id}" target="ajax" data-confirm="你确定要删除这个吗?"> <i class="glyphicon glyphicon-trash"></i></a>
											</div>
										</section>
									</div>
									{/foreach}
								</div>
								<fieldset>
							</fieldset></fieldset></div>
							<footer>
							<button id="submit" class="btn btn-primary" type="submit">保存</button>
								<button class="btn btn-default" type="reset">重置</button>
							</footer>
						</form>
					</div>
				</div>
			</div>
		</article>
	</div>
</section>
<script>
	$("#newrank").on("click",function(){
		var $id = (new Date()).getTime();
		var html = '<div id="del_id_'+$id+'" class="row">';
			html += '<section class="col col-2">';
			html += '<label class="label" for="level">等级</label>';
			html += '<label class="input">';
			html += '<input type="text" id="level" name="'+$id+'[level]" value="0" class="proportion">';
			html += '</label>';
			html += '</section>';
			html += '<section class="col col-2">';
			html += '<label class="label" for="proportion">等级称号</label>';
			html += '<label class="input">';
			html += '<input type="text" id="spend_proportion" name="'+$id+'[rank]" value="" class="spend_proportion">';
			html += '</label>';
			html += '</section>';
			html += '<section class="col col-2">';
			html += '<label class="label" for="proportion">所需积分（金币等）</label>';
			html += '<label class="input">';
			html += '<input type="text" id="spend_proportion" name="'+$id+'[coins]" value="0" class="spend_proportion">';
			html += '</label>';
			html += '</section>';
			html += '<section class="col col-3">';
			html += '<label class="label" for="status">操作</label>';
			html += '<div class="inline-group">';
			html += '<a class="btn btn-danger btn-xs" data-saved="0" href="javascript:delrow('+$id+');"> <i class="glyphicon glyphicon-trash"></i></a>';
			html += '</div>';
			html += '</section>';
			html += '</div>';
		$("[id^='del_id']").first().before(html);
	});
	function delrow(id){
		$("#del_id_"+id).remove();
	}
	nUI.ajaxCallbacks['delSuccess'] = function(data){
		delrow(data.id);
	};
</script>