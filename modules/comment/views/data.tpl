<tbody data-total="{$total}">
	{foreach $rows as $row}
	<tr name="comment" rel="{$row.id}" id="comment-id-{$row.id}">		
		<td {if $row.status==0}class="warn-td"{/if}><input type="checkbox" value="{$row.id}" class="grp" /></td>
		<td>
			<strong><img class="online comment-user-avatar" src="{'avatars/male.png'|assets}"/>{$row.author}</strong>
			{if $row.author_url}
				<br/>
				<a href="{$row.author_url}" title="{$row.author_url}" target="_blank">{$row.author_url|truncate:32}</a>
			{/if}	
			{if $row.author_email}
				<br/>
				<a href="mailto:{$row.author_email}" title="{$row.author_email}">{$row.author_email|truncate:32}</a>
			{/if}	
			{if $row.author_ip}
				<br/>
				<a href="javascript:;" onclick="comment_select_ip('{$row.author_ip}')">{$row.author_ip}</a>
			{/if}
		</td>
		<td>
			<p>提交于<a href="{$row|url}#comment-{$row.id}" target="_blank">{$row.create_time|date_format:'Y-m-d H:i:s'}</a>
			{if $row.parent}&nbsp;|&nbsp;回复给<a href="{$row|url}#comment-{$row.parent}" target="_blank">{$row.pauthor}</a>{/if}</p>
			{$row.content|nl2br}
		</td>
		<td>
			{if $row.page_id}
				<a href="javascript:;" onclick="comment_setpage_id('{$row.page_id}')"><span class="badge bg-color-blueLight">{$row.comment_count}</span></a>
				<a href="{$row|url}#comment-{$row.id}" target="_blank">{if $row.title}{$row.title}{else}{$row.title2}{/if}</a>				
			{/if}
		</td>
		<td class="text-right">
			<div class="btn-group">				
				{if $canReplyComment}
				<a href="javascript:;" onclick="reply_comment({$row.id})" class="btn btn-primary btn-xs">
					<i class="fa fa-mail-reply"></i></a>									
				{elseif $canEditComment}
				<a href="#{'comment/edit'|app:0}{$row.id}" class="btn btn-primary btn-xs">
					<i class="fa fa-pencil-square-o"></i></a>
				{else}				
				<button data-toggle="dropdown" class="btn btn-primary btn-xs dropdown-toggle">
					<i class="fa fa-list"></i>
				</button>				
				{/if}
				<button data-toggle="dropdown" class="btn btn-default btn-xs dropdown-toggle">
					<span class="caret"></span>
				</button>
				<ul class="dropdown-menu pull-right">
				{if $canReplyComment && $canEditComment}
					<li><a href="#{'comment/edit'|app:0}{$row.id}">
						<i class="fa fa-pencil-square-o"></i> 编辑</a></li>
				{/if}
				{if $canApproveComment}									
					<li><a href="{'comment/status'|app}0/{$row.id}"
						target="ajax"						
						data-confirm="你真的要驳回此评论吗?"
						><i class="fa fa-thumbs-o-down txt-color-blue"></i> 驳回</a></li>						
					<li><a href="{'comment/status'|app}1/{$row.id}"
						target="ajax" 
						data-confirm="你真的要批准此评论吗?"
						><i class="fa fa-thumbs-o-up txt-color-green"></i> 批准</a></li>
					<li><a href="{'comment/status'|app}2/{$row.id}"
						target="ajax"  					    
						data-confirm="你真的要标记此评论为垃圾评论吗?"
						><i class="fa fa-bug txt-color-orange"></i> 标记为垃圾评论</a></li>
					<li class="divider"></li>
				{/if}
				{if $canDelComment}					
					<li><a href="{'comment/del'|app}{$row.id}" target="ajax" 
					data-confirm="你真的要删除此评论吗?"><i class="fa fa-trash-o txt-color-red"></i> 删除</a></li>
				{/if}
				</ul>
			</div>			
		</td>	
	</tr>
	{foreachelse}
	<tr><td colspan="5">无结果</td></tr>
	{/foreach}
</tbody>