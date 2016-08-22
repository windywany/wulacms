{if $widget_data}
<div class="widget {$widget_cls}">
	{$viewCls->getTitle()}
	<ul class="tow-menu-wrap {$ul1_cls}">
		{foreach $widget_data as $mid => $m}
			<li class="menu-li1 {$li1_cls} {if $start_open}{$open_cls}{/if} {$viewCls->activeCls($m.id,1)}" data-menu-id="{$m.id}">
				{$viewCls->getTopItem($m)}				
				{if $level == '1' && $m.items}
					<ul class="sub-menu-wrap {$ul2_cls}">
						{foreach $m.items as $smid =>$sm}
							<li class="menu-li2 {$li2_cls} {$viewCls->activeCls($sm.id,2)}"  data-menu-id="{$sm.id}">{$viewCls->getSubItem($sm)}</li>
						{/foreach}
					</ul>
				{/if}
			</li>
		{/foreach}
	</ul>
</div>
{/if}