<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>{foreach $titles as $tid=>$title}{$labels[$tid]}{$title}-{/foreach}的测试文章</title>
</head>
<body>
	{if $searcher}
		<a href="{$searcher->channel()|url}">全部</a>
		<a href="{$searcher->channel('guochangame')|url}">国产{if $channel=='guochangame'}(o){/if}</a>
		<a href="{$searcher->channel('x')|url}">进口{if $channel=='x'}(o){/if}</a>
		<br/>
		<a href="{$searcher->sort('softype','a')|url}">按价格排序</a>
		<br/>
		{foreach $searcher->allFields as $fid=>$f}
			<h3>{$f.label}:</h3>
			<a href="{$searcher->search($fid)|url}">全部{if $f.value == ''}(o){/if}</a>
			{foreach $f.values as $vid=>$txt}
				<a href="{$searcher->search($fid,$vid)|url}">{$txt}{if $f.value == $vid}(o){/if}</a>
			{/foreach}
			<hr/>
		{/foreach}
	{/if}
	<ul>
		{foreach $searchResults as $pg}
			<li>
				<a href="{$pg.url|url}">{$pg.title}</a>
			</li>
		{/foreach}
	</ul>
	
	<br/>
	{ctsp var=p limit=$limit}
		{if $p@key == 'first'}
			<li><a href="{$p|url}">第一页</a></li>
		{/if}
		{if $p@key =='prev'}
		<li><a href="{$p|url}">上一页</a></li>
		{/if}
		{if is_numeric($p@key)}
		<li><a href="{$p|url}" class="{if  $_cpn == $p@key}active{/if}" >{$p@key}</a></li>
		{/if}
		{if $p@key =='next'}
		<li><a href="{$p|url}">下一页</a></li>
	{/if}
{/ctsp}
</body>
</html>