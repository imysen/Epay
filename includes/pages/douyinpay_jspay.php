<?php
// 抖音支付JSAPI页面
if(!defined('IN_PLUGIN'))exit();
define('IN_EPAY', true);
include_once ROOT.'includes/ep_ui.php';
$channel = 'douyinpay';
$title = '抖音支付';
ep_pay_head($title, $channel);
?>
<div class="ep-pay-card" x-data="jspayDy()" x-init="init()">
  <div class="ep-channel-bar"><div class="ep-channel-name"><span class="ep-channel-logo"><svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="12" r="10"/></svg></span>抖音支付</div></div>
  <div class="ep-amount-area" style="padding-top:40px"><div class="ep-amount"><span class="symbol">¥</span><?=htmlspecialchars($order['realmoney'])?></div><div class="ep-subject"><?=htmlspecialchars($order['name'])?></div></div>
  <div class="ep-status-bar pending"><span class="ep-dot-pulse"></span><span x-text="statusText">正在跳转支付…</span></div>
  <div class="ep-detail" :class="detailOpen?'open':''">
    <div class="ep-detail-toggle" @click="detailOpen=!detailOpen"><span class="label"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/></svg>订单详情</span><svg class="chev" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg></div>
    <div class="ep-detail-body"><div class="ep-detail-grid"><span class="k">商品名称</span><span class="v"><?=htmlspecialchars($order['name'])?></span><span class="k">系统订单号</span><span class="v mono"><?=htmlspecialchars($order['trade_no'])?></span></div></div>
  </div>
  <div class="ep-pay-foot">支付安全由中国人民财产保险股份有限公司承保</div>
  <div x-data="poller('/getshop.php', {type:'douyinpay', trade_no:'<?=addslashes(TRADE_NO)?>'}, {interval:2000, delay:0})" @poll-ok.window="onOk()" style="display:none"></div>
</div>
<script>
document.body.addEventListener('touchmove',function(e){e.preventDefault();},{passive:false});
function jspayDy(){return {statusText:'正在跳转支付…',detailOpen:false,init(){var s=window.DouyinOpenJSBridge;s.config({});s.ready(()=>{s.ttcjpay.dypay({sdk_info:<?=$jsApiParameters?>,success:r=>{if(r&&r.code==='0'){window.dispatchEvent(new CustomEvent('poll-ok',{detail:{}}));}},fail:r=>{if(r&&r.code===-2)epToast('error','请升级抖音APP');}});});},onOk(){this.statusText='支付成功,正在跳转…';epToast('success','支付成功,正在跳转');}};}
</script>
<?php echo '</body></html>'; ?>