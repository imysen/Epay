<?php
// 支付成功页面
if(!defined('IN_PLUGIN'))exit();
?><!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0">
<title>支付成功</title>
<link href="/assets/css/ep-ui.css?v=<?=filemtime(ROOT.'assets/css/ep-ui.css')?>" rel="stylesheet">
</head>
<body class="ep-app" style="background:var(--ep-gray-50);display:flex;align-items:center;justify-content:center;min-height:100vh;padding:16px">
<div style="max-width:360px;width:100%;background:#fff;border-radius:12px;box-shadow:var(--ep-shadow-sm);padding:48px 24px;text-align:center">
  <div style="width:64px;height:64px;border-radius:50%;background:var(--ep-success-50);color:var(--ep-success-600);margin:0 auto 20px;display:flex;align-items:center;justify-content:center">
    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
  </div>
  <h2 style="font-size:20px;font-weight:600;color:var(--ep-gray-900);margin-bottom:8px">支付成功</h2>
  <p style="font-size:14px;color:var(--ep-gray-500);margin-bottom:24px">支付成功，请回到浏览器查看订单</p>
  <button id="Close" class="ep-btn ep-btn-primary" style="height:44px;width:100%">关闭</button>
</div>
<script>
document.body.addEventListener('touchmove',function(e){e.preventDefault()},{passive:false});
var ua=navigator.userAgent;
if(ua.indexOf("AlipayClient")>-1){function A(f){window.AlipayJSBridge?f():document.addEventListener('AlipayJSBridgeReady',f,false)}A(function(){document.getElementById('Close').addEventListener('click',function(){AlipayJSBridge.call('popWindow')})})}
else if(ua.indexOf("MicroMessenger")>-1){function W(f){"undefined"==typeof WeixinJSBridge?document.addEventListener?document.addEventListener('WeixinJSBridgeReady',f,!1):document.attachEvent&&(document.attachEvent('WeixinJSBridgeReady',f),document.attachEvent('onWeixinJSBridgeReady',f)):f()}W(function(){document.getElementById('Close').addEventListener('click',function(){WeixinJSBridge.call('closeWindow')})})}
else{document.getElementById('Close').addEventListener('click',function(){window.opener=null;window.close()})}
</script>
</body>
</html>