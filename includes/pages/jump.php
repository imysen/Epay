<?php
// 浏览器引导页(微信内提示用外部浏览器打开)
if(!defined('IN_PLUGIN'))exit();
$useragent=strtolower($_SERVER['HTTP_USER_AGENT']);
if(strpos($useragent,'iphone')!==false||strpos($useragent,'ipod')!==false){$app='Safari';}else{$app='浏览器';}
?><!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=no">
<title>请使用浏览器打开</title>
<link href="/assets/css/ep-ui.css?v=<?=filemtime(ROOT.'assets/css/ep-ui.css')?>" rel="stylesheet">
</head>
<body class="ep-app" style="background:var(--ep-gray-50);display:flex;align-items:center;justify-content:center;min-height:100vh;padding:16px">
<div style="max-width:360px;width:100%;background:#fff;border-radius:12px;box-shadow:var(--ep-shadow-sm);padding:48px 24px;text-align:center">
  <div style="width:64px;height:64px;border-radius:50%;background:var(--ep-brand-50);color:var(--ep-brand-600);margin:0 auto 20px;display:flex;align-items:center;justify-content:center">
    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h6v6"/><path d="M10 14 21 3"/><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/></svg>
  </div>
  <h2 style="font-size:20px;font-weight:600;color:var(--ep-gray-900);margin-bottom:4px">请使用<?=$app?>打开</h2>
  <p style="font-size:14px;color:var(--ep-gray-500);margin-bottom:24px">点击右上角选择在<?=$app?>中打开</p>
  <a class="ep-btn ep-btn-primary" style="height:44px;width:100%" id="J_BtnDowanloadApp">点此继续访问</a>
</div>
<a style="display:none" href="" id="vurl" rel="noreferrer"></a>
<script>
var u=window.location.href;document.body.addEventListener('touchmove',function(e){e.preventDefault();});
if(navigator.userAgent.indexOf('QQ/')>-1){
  ['ucbrowser://'+u,'mttbrowser://url='+u,'googlechrome://'+u].forEach(function(s){document.getElementById('vurl').href=s;document.getElementById('vurl').click()});
  document.addEventListener('click',function(){['ucbrowser://'+u,'mttbrowser://url='+u,'googlechrome://'+u].forEach(function(s){document.getElementById('vurl').href=s;document.getElementById('vurl').click()})});
}
</script>
</body>
</html>