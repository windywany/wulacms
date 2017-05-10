<tbody data-total="{$total}">
	{foreach $rows as $row}
	<tr name="comment" rel="{$row.id}" id="comment-id-{$row.id}" parent="0">
		<td></td>		
		<td class="{$status_cls[$row.status]}"><input type="checkbox" value="{$row.id}" class="grp" /></td>
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
			
			{if $row.author_phone}
				<br/>
				<abbr>电话:</abbr><a href="javascript:;" onclick="comment_scontact('{$row.author_phone}')">{$row.author_phone}</a>
			{/if}
			{if $row.author_qq}
				<br/>
				<abbr>QQ:</abbr><a href="javascript:;" onclick="comment_scontact('{$row.author_qq}')">{$row.author_qq}</a>
			{/if}
			{if $row.author_weixin}
				<br/>
				<abbr>微信:</abbr><a href="javascript:;" onclick="comment_scontact('{$row.author_weixin}')">{$row.author_weixin}</a>
			{/if}
			
			{if $row.author_weibo}
				<br/>
				<abbr>微博:</abbr><a href="javascript:;" onclick="comment_scontact('{$row.author_weibo}')">{$row.author_weibo}</a>
			{/if}
			
			{if $row.author_ip}
				<br/>
				<a href="javascript:;" onclick="comment_select_ip('{$row.author_ip}')">{$row.author_ip}</a>
			{/if}
			
		</td>
		<td>
			<p> 			
			提交于 {$row.create_time|date_format:'Y-m-d H:i'}
			</p>
			{if $row.author_address}
				<address><abbr title="联系地址">联系地址:</abbr>{$row.author_address}</address>
			{/if}			
			<div class="msg-ask">
				{if $row.subject}<span class="label bg-color-blueLight pull-left">{$row.subject}</span>{/if}
				{$row.content|nl2br}
			</div>			
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
				<a href="#{'comment/msg/edit'|app:0}{$row.id}" class="btn btn-primary btn-xs">
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
					<li><a href="#{'comment/msg/edit'|app:0}{$row.id}">
						<i class="fa fa-pencil-square-o"></i> 编辑</a></li>
				{/if}
				{if $canApproveComment}					
					<li><a href="{'comment/msg/status'|app}0/{$row.id}"
						target="ajax"  					    
						data-confirm="你真的要修改此留言的状态为处理中吗?"
						><i class="fa fa-check-square-o txt-color-blue"></i> 标记为待处理</a></li>						
					<li><a href="{'comment/msg/status'|app}3/{$row.id}"
						target="ajax"  					    
						data-confirm="你真的要修改此留言的状态为处理中吗?"
						><i class="fa fa-check-square-o txt-color-treal"></i> 标记为处理中</a></li>					
					<li><a href="{'comment/msg/status'|app}1/{$row.id}"
						target="ajax"  					    
						data-confirm="你真的要修改此留言的状态为已处理吗?"
						><i class="fa fa-check-square-o txt-color-grean"></i> 标记为已处理</a></li>
					
					<li><a href="{'comment/msg/status'|app}2/{$row.id}"
						target="ajax"  					    
						data-confirm="你真的要标记此留言为垃圾留言吗?"
						><i class="fa fa-bug txt-color-orange"></i> 标记为垃圾留言</a></li>
					<li class="divider"></li>
				{/if}
				{if $canDelComment}					
					<li><a href="{'comment/msg/del'|app}{$row.id}" target="ajax" 
					data-confirm="你真的要删除此留言吗?"><i class="fa fa-trash-o txt-color-red"></i> 删除</a></li>
				{/if}
				</ul>
			</div>			
		</td>	
	</tr>
	{if $row.replies}
	{foreach $row.replies as $rp}
	<tr name="comment" rel="{$rp.id}" id="comment-id-{$rp.id}" parent="{$row.id}">
		<td colspan="2"></td>
		<td>
			<strong><img class="online comment-user-avatar" src="{'avatars/male.png'|assets}"/>{$rp.author}</strong>
			{if $rp.author_url}
				<br/>
				<a href="{$rp.author_url}" title="{$rp.author_url}" target="_blank">{$rp.author_url|truncate:32}</a>
			{/if}	
			{if $rp.author_email}
				<br/>
				<a href="mailto:{$rp.author_email}" title="{$rp.author_email}">{$rp.author_email|truncate:32}</a>
			{/if}
			{if $rp.author_ip}
				<br/>
				<a href="javascript:;" onclick="comment_select_ip('{$rp.author_ip}')">{$rp.author_ip}</a>
			{/if}			
		</td>
		<td>
			<p> 			
			回复于 {$rp.create_time|date_format:'Y-m-d H:i'}
			</p>			
			<div class="msg-ask">
				{if $rp.subject}<span class="label bg-color-blue pull-left">{$rp.subject}</span>{/if}
				{$rp.content|nl2br}
			</div>
		</td>
		<td></td>
		<td class="text-right">
			<div class="btn-group">				
				{if $canEditComment}
				<a href="#{'comment/msg/edit'|app:0}{$rp.id}" class="btn btn-primary btn-xs">
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
				{if $canDelComment}					
					<li><a href="{'comment/msg/del'|app}{$rp.id}" target="ajax" 
					data-confirm="你真的要删除此留言吗?"><i class="fa fa-trash-o txt-color-red"></i> 删除</a></li>
				{/if}
				</ul>
			</div>			
		</td>
	</tr>
	{/foreach}
	{/if}
	{foreachelse}
	<tr><td colspan="6">无结果</td></tr>
	{/foreach}
</tbody>