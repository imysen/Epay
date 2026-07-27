<?php
// 支付环境异常提示页(防诈骗提醒)
if(!defined('IN_CRONLITE'))exit();
?><!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0">
<title>支付提示</title>
<link href="/assets/css/ep-ui.css?v=<?=filemtime(ROOT.'assets/css/ep-ui.css')?>" rel="stylesheet">
</head>
<body class="ep-app" style="background:var(--ep-gray-50);display:flex;align-items:center;justify-content:center;min-height:100vh;padding:16px">
<div style="max-width:360px;width:100%;background:#fff;border-radius:12px;box-shadow:var(--ep-shadow-sm);padding:32px 24px 24px;text-align:center">
  <div style="width:64px;height:64px;border-radius:50%;background:var(--ep-warning-50);color:var(--ep-warning-600);margin:0 auto 20px;display:flex;align-items:center;justify-content:center">
    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.3 3.9 1.8 18a2 2 0 0 0 1.7 3h17a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0z"/><path d="M12 9v4"/><path d="M12 17h.01"/></svg>
  </div>
  <h2 style="font-size:20px;font-weight:600;color:var(--ep-gray-900);margin-bottom:8px">防诈骗提醒</h2>
  <p style="font-size:14px;color:var(--ep-gray-500);line-height:1.6;margin-bottom:8px">您当前支付的商品为</p>
  <p style="font-size:15px;font-weight:600;color:var(--ep-warning-600);margin-bottom:16px"><?=htmlspecialchars($order['name'])?></p>
  <p style="font-size:13px;color:var(--ep-gray-500);line-height:1.6;margin-bottom:24px">请勿使用他人发过来的二维码或链接进行支付，以防资金损失！</p>
  <div style="display:flex;flex-direction:column;gap:10px">
    <a class="ep-btn ep-btn-primary" style="height:44px;width:100%" href="<?=htmlspecialchars($order['payurl'])?>">继续支付</a>
    <button class="ep-btn ep-btn-secondary" style="height:44px;width:100%" id="Close">关闭</button>
  </div>
  <div style="text-align:center;margin-top:16px;font-size:12px;color:var(--ep-gray-400)">Copyright © <?=date("Y")?> <?=htmlspecialchars($conf['sitename'])?></div>
</div>
<script>
document.body.addEventListener('touchmove',function(e){e.preventDefault()},{passive:false});
(function(){
  var ua=navigator.userAgent;
  var btn=document.getElementById('Close');
  if(ua.indexOf('AlipayClient')>-1){
    function A(f){window.AlipayJSBridge?f():document.addEventListener('AlipayJSBridgeReady',f,false)}
    A(function(){btn.addEventListener('click',function(){AlipayJSBridge.call('popWindow')})});
  }else if(ua.indexOf('MicroMessenger')>-1){
    function W(f){"undefined"==typeof WeixinJSBridge?(document.addEventListener?document.addEventListener('WeixinJSBridgeReady',f,!1):document.attachEvent&&(document.attachEvent('WeixinJSBridgeReady',f),document.attachEvent('onWeixinJSBridgeReady',f))):f()}
    W(function(){btn.addEventListener('click',function(){WeixinJSBridge.call('closeWindow')})});
  }else{
    btn.addEventListener('click',function(){window.opener=null;window.close()});
  }
})();
</script>
</body>
</html>
