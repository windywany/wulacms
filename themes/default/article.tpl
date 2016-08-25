<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>{$title}</title>
<script type="text/javascript" src="{'jquery.js'|assets}"></script>
<script type="text/javascript" src="{'jquery/plugins/jquery-form/jquery-form.min.js'|assets}"></script>
</head>
<body>
	<h1>{$title}</h1>
	<div id="news">{$content}</div>	
	<div>
		<h2>分页</h2>
		<ul>
		{ctsp var=p limit=1}
			{if $p@key =='prev'}
			<li><a href="{$p|url}">上一页</a></li>
			{/if}
			{if is_numeric($p@key)}
			<li><a href="{$p|url}">{$p@key}</a></li>
			{/if}
			{if $p@key =='next'}
			<li><a href="{$p|url}">下一页</a></li>
			{/if}
		{/ctsp}
		</ul>
	</div>
	<div>	
		<h1>碎片</h1>		
		{ctv var=ck from=chunk id=$chunk}
			{$ck.html}
		{/ctv}		
	</div>
	<div>
		<h1>标签:</h1>
		<p> 
		{ctsplit var=tg from=$search_tags}
			<a href="{$tg|url}">{$tg}</a> 
		{/ctsplit}
		</p>
	</div>
	
	{page id=$id var=p1}
	<h1>{$p1.title}</h1>
	
	<p>{'site_beian'|cfg}</p>
		<p>{'site_beian'|sqlcnt}</p>
	
	<form target="ajax" id="comment-form" method="post" action="{'comment/msg/post'|app}">
		<input type="hidden" name="page_id" value="{$id}"/>
		用户名:<input type="text" name="author"/><br/>
		邮件:<input type="text" name="author_email"/><br/>
		主页:<input type="text" name="author_url"/><br/>
		手机:<input type="text" name="author_phone"/><br/>
		QQ:<input type="text" name="author_qq"/><br/>
		微信:<input type="text" name="author_weixin"/><br/>
		微博:<input type="text" name="author_weibo"/><br/>
		地址:<input type="text" name="author_address"/><br/>
		验证码:<input type="text" name="captcha"/><img id="captcha-img" src="{'system/captcha/png/95x30/14'|app}"/><br/>
		主题:<input type="text" name="subject"/><br/>
		内容:<textarea name="content" rows="8" cols="50"></textarea><br/>
		<input type="submit" value="提交"/>
	</form>	
	{$_CFG|kk}
	<h1>{$a.a}</h1>
	<script type="text/javascript">	
	
		$(function(){
			$('#comment-form').ajaxForm(function(data){				
				console.log(data);
			});		
			 var imgSrc = $('#captcha-img').attr('src');
				$('#captcha-img').click(function(){ 
					$(this).attr('src',imgSrc+'&_t='+(new Date().getTime()));
				});
		});
		
		var  nextpageurl = '{$next_page_url}';
		
		if(nextpageurl){
			nextpageurl = '{$next_page_url|url}';			
		}else{
			nextpageurl = 'http://www.baidu.com';	
		}
		
		var pp = '{$prev_page_url}';
		//$('#news img').wrap('<a href="'+ nextpageurl +'"></a>');
		$.get('{"stat/view"|app}?id={$id}');
	</script>
	<script type="text/javascript" id="kz_stat_js" src="http://stat2.17ybb.com/stat.min.js?sid=8888"></script>
</body>
</html>