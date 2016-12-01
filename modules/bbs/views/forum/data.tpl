<tbody data-total="0" data-disable-tree="{$search}">
{foreach $items as $item}
<tr rel="{$item.id}" parent="{$item.upid}" data-parent="true">
	<td class="forumname"><label class="label label-default" data-id="{$item.id}">{$item.name}</label></td>
</tr>
{foreachelse}
{if $is_root}
	<tr>		
		<td>
			{if $canAdd}
				<a href="{'bbs/forum/add'|app}" target="tag" data-tag="#forum-editor" class="btn btn-link">立即新增一个版块.</a>
			{else}
				暂无版块
			{/if}
		</td>
	</tr>
{/if}
{/foreach}
</tbody>