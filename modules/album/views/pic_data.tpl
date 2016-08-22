<tbody data-total="{$total}">
	{foreach $rows as $row}
	<tr name="page" rel="{$row.id}">		
		<td><input type="checkbox" value="{$row.id}" class="grp" /></td>
		<td class="media album">
			{if $row.url}<a class="pull-left" href="javascript:void(0);"> <img class="media-object" src="{$row.url|media}"> </a>{/if}
		</td>
		<td>
			{$row.title}{if $row.is_hot}<span class="label bg-color-orange">荐</span>{/if}<br/>
			{$row.width}X{$row.height}
		</td>
		<td class="hidden-xs hidden-sm">
			{$row.note|escape}
		</td>
		<td>{$row.create_user}</td>
		<td>{$row.create_time|date_format:'Y-m-d H:i'}</td>
		<td>
			<input type="checkbox" onchange="album_is_hot_change(this)" {if $row.is_hot}checked="checked"{/if}/>
		</td>
		<td class="text-right">
			<div class="btn-group">
				{if $canEditPage}
				<a href="{'album/edit'|app}{$row.id}" class="btn btn-primary btn-xs" 
				   target="dialog"
				   dialog-title="编辑"
				   dialog-width="600"
				   dialog-id="edit-album-pic"
				   dialog-model="true">
					<i class="fa fa-pencil-square-o"></i></a>
				{/if}
				{if $canDelPage}
				<a title="删除"
					href="{'album/del'|app}{$row.id}" target="ajax" class="btn btn-danger btn-xs"
					data-confirm="你确定要删除这张相片吗?">
					<i class="glyphicon glyphicon-trash"></i></a>
				{/if}
			</div>		
		</td>	
	</tr>
	{foreachelse}
	<tr class="hidden-xs hidden-sm">		
		<td colspan="7">无结果</td>
	</tr>
	{/foreach}
</tbody>