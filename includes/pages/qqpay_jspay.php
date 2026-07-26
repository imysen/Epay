<?php
// QQ钱包JSAPI支付页面
if(!defined('IN_PLUGIN'))exit();
define('IN_EPAY', true);
include_once ROOT.'includes/ep_ui.php';
$channel = 'qqpay';
$title = 'QQ钱包支付';
ep_pay_head($title, $channel);
?>
<div class="ep-pay-card" x-data="jspayQQ()" x-init="init()">
  <div class="ep-channel-bar"><div class="ep-channel-name"><span class="ep-channel-logo"><svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="12" r="10"/></svg></span>QQ钱包支付</div></div>
  <div class="ep-amount-area" style="padding-top:40px"><div class="ep-amount"><span class="symbol">¥</span><?=htmlspecialchars($order['realmoney'])?></div><div class="ep-subject"><?=htmlspecialchars($order['name'])?></div></div>
  <div class="ep-status-bar pending"><span class="ep-dot-pulse"></span><span x-text="statusText">正在跳转支付…</span></div>
  <div class="ep-detail" :class="detailOpen?'open':''">
    <div class="ep-detail-toggle" @click="detailOpen=!detailOpen"><span class="label"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/></svg>订单详情</span><svg class="chev" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg></div>
    <div class="ep-detail-body"><div class="ep-detail-grid"><span class="k">商品名称</span><span class="v"><?=htmlspecialchars($order['name'])?></span><span class="k">系统订单号</span><span class="v mono"><?=htmlspecialchars($order['trade_no'])?></span></div></div>
  </div>
  <div class="ep-pay-foot">支付安全由中国人民财产保险股份有限公司承保</div>
  <div x-data="poller('/getshop.php', {type:'qqpay', trade_no:'<?=addslashes($order['trade_no'])?>'}, {interval:2000, delay:0})" @poll-ok.window="onOk()" style="display:none"></div>
</div>
<script src="//open.mobile.qq.com/sdk/qqapi.js?_bid=152"></script>
<script>
document.body.addEventListener('touchmove',function(e){e.preventDefault();},{passive:false});
function jspayQQ(){return {statusText:'正在跳转支付…',detailOpen:false,init(){mqq.tenpay.pay({tokenId:'<?=addslashes($tokenId)?>',appInfo:'<?=addslashes($appInfo)?>'},function(result){if(result.resultCode==0){window.dispatchEvent(new CustomEvent('poll-ok',{detail:{}}));}});},onOk(){this.statusText='支付成功,正在跳转…';epToast('success','支付成功,正在跳转');}};}
</script>
<?php echo '</body></html>'; ?>