<?php
// OpenID获取结果页
if(!defined('IN_CRONLITE'))exit();
?><!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0">
<title>获取<?=htmlspecialchars($openid_name)?></title>
<link href="/assets/css/ep-ui.css?v=<?=filemtime(ROOT.'assets/css/ep-ui.css')?>" rel="stylesheet">
<script defer src="/assets/js/alpine.min.js?v=<?=filemtime(ROOT.'assets/js/alpine.min.js')?>"></script>
</head>
<body class="ep-app" x-data="openidPage()" x-init="init()" style="background:var(--ep-gray-50);min-height:100vh;padding:16px">
<div style="max-width:400px;margin:0 auto;background:#fff;border-radius:12px;box-shadow:var(--ep-shadow-sm);padding:24px">
  <div style="text-align:center;margin-bottom:20px">
    <div style="width:56px;height:56px;border-radius:50%;background:var(--ep-success-50);color:var(--ep-success-600);margin:0 auto 12px;display:flex;align-items:center;justify-content:center">
      <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
    </div>
    <h2 style="font-size:18px;font-weight:600;color:var(--ep-gray-900)">获取<?=htmlspecialchars($openid_name)?>成功</h2>
  </div>
  <div style="margin-bottom:16px">
    <div style="font-size:13px;color:var(--ep-gray-500);margin-bottom:8px">如未自动填写，请手动复制下方<?=htmlspecialchars($openid_name)?>：</div>
    <textarea class="ep-input" rows="2" style="width:100%;text-align:center;font-family:var(--ep-font-mono);resize:none" readonly x-ref="oid"><?=htmlspecialchars($openid_content)?></textarea>
  </div>
  <div style="display:flex;flex-direction:column;gap:10px">
    <button class="ep-btn ep-btn-primary" style="height:44px;width:100%" @click="copy()">点击复制</button>
    <button class="ep-btn ep-btn-secondary" style="height:44px;width:100%" id="Close" @click="close()">关闭</button>
  </div>
  <div style="text-align:center;margin-top:16px;font-size:12px;color:var(--ep-gray-400)">Copyright © <?=date("Y")?> <?=htmlspecialchars($conf['sitename'])?></div>
</div>
<div class="ep-toasts" id="toasts"></div>
<script>
document.body.addEventListener('touchmove',function(e){e.preventDefault()},{passive:false});
function toast(msg){var el=document.createElement('div');el.className='ep-toast success';el.innerHTML='<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg><span>'+msg+'</span>';document.getElementById('toasts').appendChild(el);setTimeout(function(){el.remove()},2500);}
function openidPage(){
  return {
    _closeHandler:null,
    init(){this.setupBridge();},
    async copy(){try{await navigator.clipboard.writeText(this.$refs.oid.value);toast('复制成功');}catch{toast('复制失败，请长按手动复制');}},
    close(){if(this._closeHandler){this._closeHandler();}},
    setupBridge(){
      var ua=navigator.userAgent;
      var that=this;
      // QQ 浏览器:不显示关闭按钮
      if(ua.indexOf('QQ/')>-1){
        var btn=document.getElementById('Close');
        if(btn){btn.style.display='none';}
        return;
      }
      // 微信内:等待 WeixinJSBridge 就绪
      if(ua.indexOf('MicroMessenger')>-1){
        if(typeof WeixinJSBridge!=='undefined'){
          that._closeHandler=function(){WeixinJSBridge.call('closeWindow');};
        }else if(document.addEventListener){
          document.addEventListener('WeixinJSBridgeReady',function(){that._closeHandler=function(){WeixinJSBridge.call('closeWindow');};},false);
        }
        return;
      }
      // 支付宝内:等待 AlipayJSBridge 就绪
      if(ua.indexOf('AlipayClient')>-1){
        if(window.AlipayJSBridge){
          that._closeHandler=function(){AlipayJSBridge.call('popWindow');};
        }else{
          document.addEventListener('AlipayJSBridgeReady',function(){that._closeHandler=function(){AlipayJSBridge.call('popWindow');};},false);
        }
        return;
      }
      // 其他浏览器:直接关闭
      that._closeHandler=function(){window.opener=null;window.close();};
    }
  };
}
</script>
</body>
</html>