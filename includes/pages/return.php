<?php
// 支付返回页面(轮询订单结果)
if(!defined('IN_PLUGIN'))exit();
define('IN_EPAY', true);
// 支付插件 include 时已加载 common.php,$order/$channel 可用
include_once ROOT.'includes/ep_ui.php';
$channel = isset($channel) ? $channel : 'wxpay';
$title = '支付结果';
ep_pay_head($title, $channel);
?>
<div class="ep-pay-card" x-data="payReturn()">
  <!-- 状态区 -->
  <div class="ep-status-bar" :class="status.cls" style="margin:20px">
	<template x-if="status.cls==='pending'"><span class="ep-dot-pulse"></span></template>
	<template x-if="status.cls==='success'">
	  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
	</template>
	<template x-if="status.cls==='error'">
	  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M15 9l-6 6M9 9l6 6"/></svg>
	</template>
	<span x-text="status.text"></span>
  </div>

  <!-- 订单详情 -->
  <div class="ep-detail open" id="detail">
	<div class="ep-detail-toggle" onclick="document.getElementById('detail').classList.toggle('open')">
	  <span class="label">
		<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/></svg>
		订单详情
	  </span>
	  <svg class="chev" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
	</div>
	<div class="ep-detail-body">
	  <div class="ep-detail-grid">
		<span class="k">收款商户</span><span class="v"><?=htmlspecialchars($sitename)?></span>
		<span class="k">商品名称</span><span class="v"><?=htmlspecialchars($order['name'])?></span>
		<span class="k">系统订单号</span><span class="v mono"><?=htmlspecialchars($order['trade_no'])?></span>
		<span class="k">创建时间</span><span class="v"><?=htmlspecialchars($order['addtime'])?></span>
	  </div>
	</div>
  </div>

  <div class="ep-pay-foot">支付完成后页面将自动跳转</div>

  <!-- 轮询器(getshop.php)-->
  <div x-data="poller('/getshop.php', {type:'<?=addslashes($channel)?>', trade_no:'<?=addslashes($order['trade_no'])?>'}, {interval:2000, delay:0})"
	   @poll-ok.window="onOk()"
	   @poll-error.window=""
	   style="display:none"></div>
</div>
<script>
function payReturn(){
	return {
		status:{cls:'pending',text:'正在检测付款结果…'},
		onOk(){this.status={cls:'success',text:'支付成功,正在跳转…'};epToast('success','支付成功,正在跳转');}
	};
}
</script>
<?php
// 闭合 body
echo '</body></html>';

