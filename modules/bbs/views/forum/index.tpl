{extends 'dashboard/views/blank.tpl'}
{block title}
	<i class="fa fa-fw fa-weixin txt-color-green"></i> 论坛
{/block}
{block toolbar}
	{if $canAdd}
		<a href="{'bbs/forum/add'|app}" target="tag" data-tag="#forum-editor" data-blockUI="false" class="btn btn-success">
			<i class="fa fa-w fa-plus"></i> 添加
		</a>
	{/if}
{/block}
{block widget}
	<div class="col-sm-3">
		<div class="panel panel-default">
			<div class="panel-body" style="min-height: 500px">
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
    <input type="hidden" id="forum_id" name="forum_id"/>
{/block}
{block name="js" nocache}
<script type="text/javascript">
    {minify type="js"}
    var optButtons = { target:'td.forumname',buttons:[] };
    optButtons.buttons.push({
        'html':'<a href="{'bbs/forum/edit'|app}$#forum_id$" data-blockUI="false" target="tag" data-tag="#forum-editor" class="txt-color-blue"><i class="fa fa-pencil fa-lg"></a>'
    });

    optButtons.buttons.push({
        'html':'<a href="{'bbs/forum/add'|app}$#forum_id$" data-blockUI="false" target="tag" data-tag="#forum-editor" class="txt-color-green"><i class="fa fa-plus fa-lg"></a>'
    });

    optButtons.buttons.push({
        'html':'<a href="{'bbs/forum/del'|app}$#forum_id$" target="ajax" data-confirm="版块删除后将无法恢复,你确定要删除这个版块吗?" class="txt-color-red"><i class="fa fa-trash-o fa-lg"></a>'
    });
    $('#forum-table').on('mouseover','tbody tr',function () {
        $('#forum_id').val($(this).attr('rel'));
        nUI.showButtons(optButtons,$(this));
    }).on('mouseout','tbody tr',function () {
        nUI.hideButtons(optButtons);
    });

	nUI.ajaxCallbacks.reloadForumTree = function(args){
		$('#forum-table').data('tableObj').reloadNode(args.upid);
        if(args.id>0) {
            $('#id').val(args.id);
            $('#url').val(args.url);
        }
	}
	{/minify}
</script>
{/block}