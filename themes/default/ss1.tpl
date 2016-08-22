{ctss var=ss}
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>{$title}</title>
</head>
<body>
<div>	
	<h3>排序</h3>
	<a href="{$ss->sort('id')}">
	按编号{if $ss->sf == 'id' && $ss->sd=='d'}降{else}升{/if}序排序
	</a> &nbsp; <a href="{$ss->sort('update_time')}">
	按更新时间{if $ss->sf == 'update_time' && $ss->sd=='d'}降{else}升{/if}排序
	</a>
	</div>
<ul>
{cts var=a1 from=pages model='news' limit='10' sortby=$ss->sf order=$ss->sd}
	<li><a href="{$a1.url|url}">{$a1.title}</a></li>
{/cts}
</ul>	
		{ctsp var=p for='a1' limit=$ss.limit}
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
	
	<h3>生成本页共执行SQL:{0|sqlcnt}</h3>
	
</body>
</html>
{/ctss}