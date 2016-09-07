{extends 'dashboard/views/blank.tpl'}
{block title}
	<i class="fa fa-fw fa-weixin txt-color-green"></i> 论坛
{/block}
{block toolbar}
	{if $canAdd}
		<a href="{'bbs/forum/add'|app}" target="tag" data-tag="#forum-editor" class="btn btn-success">
			<i class="fa fa-w fa-plus"></i> 添加
		</a>
	{/if}
{/block}
{block widget}
	<div class="col-sm-3">
		<div class="panel panel-default">
			<div class="panel-body" style="min-height: 400px">
				<table
					class="inbox-table"
					id="forum-table"
					data-widget="nuiTable"
					data-tree="true"
					data-hh="true"
					data-no-hover="true"
					data-source="{'bbs/forum/data'|app}">
					<thead>
						<tr>									
							<th>版块</th>	
						</tr>
					</thead>
					{include './data.tpl' is_root=1}
				</table>
			</div>
		</div>
	</div>
	<div class="col-sm-9" id="forum-editor">
		<p class="lead">请从左侧选择一个版块进行操作.</p>
	</div>
{/block}
{block js}
<script type="text/javascript">
	nUI.ajaxCallbacks.reloadForumTree = function(args){
		//$('#forum-table').data('treeObj').reloadNode();
		console.log(args);
		$('#id').val(args.id);
	}
</script>
{/block}