{ctss var=ss limit=10 model='software'}
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>{foreach $ss.titles as $tid=>$title}{$ss.labels[$tid]}{$title}-{/foreach}的测试文章</title>
</head>
<body>
	{if $ss.searcher}	
		<h3>共找到:{$ss.total}个结果</h3>
		<div>	
		<h3>排序</h3>
		<a href="{$ss->sort('rank')}">按评论排序</a> &nbsp; <a href="{$ss->sort('price')}">按价格排序</a>
		</div>
		{foreach $ss->fields as $fid=>$f}
			<h3>{$f.label}:</h3>
			<a href="{$ss->search($fid)}">全部{if $f.value == ''}(o){/if}</a>
			{foreach $f.values as $vid=>$txt}
				<a href="{$ss->search($fid,$vid)}">{$txt}{if $f.value == $vid}(o){/if}</a>
			{/foreach}
			<hr/>
		{/foreach}
	
		<ul>
			{foreach $ss as $pg}
				<li>
					<a href="{$pg|url}">{$pg.title}</a>
				</li>
			{/foreach}
		</ul>	
		<br/>
		{ctsp var=p for='ss' limit=$ss.limit}
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
	{else}
		不支持搜索.
	{/if}
	<h3>查询条件abc:{$_KSGET->get('abc','1111')}</h3>
	<h3>生成本页共执行SQL:{0|sqlcnt}</h3>
	<h3>本页面可用数据</h3>
	{$mf_page_data|kk}
</body>
</html>
{/ctss}