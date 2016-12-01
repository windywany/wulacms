<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0;" name="viewport">
<title>微信分享</title>
</head>

{literal}
<style>
h3{ text-align:center; border-bottom:1px solid #ccc; width:40%; margin: 2% 30%; padding-bottom:0.1rem;}
.weixin_id{text-align:center;  width:40%; margin: 0 30%;  padding-bottom:0.3rem; margin-bottom:1rem; margin-top:-0.2rem;}
.weixin_shibie{ text-align:center; color:#F00; font-size:0.8rem;}
p{ font-size:0.8rem; color:#666;}
img{ width:60%; height:auto; margin-left:20%;}
</style>
{/literal}

<body>
<!-- <h3>P2P</h3>
<p class="weixin_id">ID:{$user}</p> -->
<img src="{$QRImage|media}" alt="图片加载失败，请刷新重试！"/>
<p class="weixin_shibie">长按二维码“识别”关注</p>
<!-- <p>欢迎进入P2P！</p> -->
</body>
</html>
