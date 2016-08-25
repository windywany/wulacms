<tbody>
	{foreach $nodes as $node}
	<tr rel="{$node.uri}" parent="{$parent}" data-parent="true">
		<td>{$node.name}{if $debuging}({$node.resId}){/if}</td>
		<td class="text-right">
			{if $node.defaultOp}
				<select name="acl[{$node.resId}]">
					{html_options options=$options selected=$acl[$node.resId]}	
				</select>				
			{/if}
		</td>		
	</tr>
	{/foreach}
	{foreach $ops as $o=>$n}
	<tr rel="" parent="{$parent}">
		<td>{$n.name}{if $debuging}({$n.resId}){/if}</td>
		<td class="text-right">
			<select name="acl[{$n.resId}]">
				{html_options options=$options selected=$acl[$n.resId]}	
			</select>
		</td>		
	</tr>
	{/foreach}	
</tbody>