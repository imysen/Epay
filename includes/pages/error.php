<?php
// 支付失败/超时提示页
if(!defined('IN_PLUGIN'))exit();
?><!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0">
<title>支付失败</title>
<link href="/assets/css/ep-ui.css?v=<?=filemtime(ROOT.'assets/css/ep-ui.css')?>" rel="stylesheet">
</head>
<body class="ep-app" style="background:var(--ep-gray-50);display:flex;align-items:center;justify-content:center;min-height:100vh;padding:16px">
<div style="max-width:360px;width:100%;background:#fff;border-radius:12px;box-shadow:var(--ep-shadow-sm);padding:48px 24px;text-align:center">
  <div style="width:64px;height:64px;border-radius:50%;background:var(--ep-danger-50);color:var(--ep-danger-600);margin:0 auto 20px;display:flex;align-items:center;justify-content:center">
    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 8v4"/><path d="M12 16h.01"/></svg>
  </div>
  <h2 style="font-size:20px;font-weight:600;color:var(--ep-gray-900);margin-bottom:8px">支付失败</h2>
  <p style="font-size:14px;color:var(--ep-gray-500)">支付失败或支付超时，请返回重新发起支付</p>
</div>
<script>document.body.addEventListener('touchmove',function(e){e.preventDefault()},{passive:false})</script>
</body>
</html>