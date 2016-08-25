<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Insert title here</title>
</head>
<body>
Hi~drrd adfasdfasdfa sadfasdf
<h1>本栏目下的文章列表：</h1>
<ul>
{cts var=bb from=pages limit='2' channel=$channel}
	<li> <a href="{$bb.url|url}">{$bb.title}</a> </li>
{/cts}
</ul>


<h2>分页</h2>
<ul>
{ctsp var=p limit=2 for='bb'}
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
</ul>

</body>
</html>