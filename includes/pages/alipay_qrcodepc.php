<?php
// 支付宝扫码支付页面(PC端iframe版)
if(!defined('IN_PLUGIN'))exit();
define('IN_EPAY', true);
include_once ROOT.'includes/ep_ui.php';
$channel = 'alipay';
$title = '支付宝扫码支付';
ep_pay_head($title, $channel);
?>
<div class="ep-pay-card" x-data="alipayPC()" x-init="init()">
  <div class="ep-channel-bar">
    <div class="ep-channel-name">
      <span class="ep-channel-logo">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20zm0 18a8 8 0 1 1 0-16 8 8 0 0 1 0 16z"/><path d="M12 6v6l4 2"/></svg>
      </span>
      支付宝扫码支付
    </div>
    <div class="ep-countdown">
      <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
      <span x-text="countdown"></span>
    </div>
  </div>
  <div class="ep-amount-area">
    <div class="ep-amount"><span class="symbol">¥</span><?=htmlspecialchars($order['realmoney'])?></div>
    <div class="ep-subject"><?=htmlspecialchars($order['name'])?></div>
  </div>
  <div class="ep-qr-area">
    <div class="ep-qr-box" style="padding:8px"><iframe src="<?=htmlspecialchars($code_url)?>" width="200" height="200" frameborder="0" scrolling="no" seamless style="border:none"></iframe></div>
    <div class="ep-scan-hint">
      <svg class="ch-icon" width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20zm0 18a8 8 0 1 1 0-16 8 8 0 0 1 0 16z"/></svg>
      请使用支付宝扫一扫
    </div>
  </div>
  <div class="ep-status-bar pending"><span class="ep-dot-pulse"></span><span x-text="statusText">正在等待付款结果…</span></div>
  <div class="ep-detail" :class="detailOpen?'open':''">
    <div class="ep-detail-toggle" @click="detailOpen=!detailOpen">
      <span class="label"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/></svg>订单详情</span>
      <svg class="chev" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
    </div>
    <div class="ep-detail-body">
      <div class="ep-detail-grid">
        <span class="k">商品名称</span><span class="v"><?=htmlspecialchars($order['name'])?></span>
        <span class="k">系统订单号</span><span class="v mono"><?=htmlspecialchars($order['trade_no'])?></span>
        <span class="k">创建时间</span><span class="v"><?=htmlspecialchars($order['addtime'])?></span>
      </div>
    </div>
  </div>
  <div class="ep-pay-foot">支付完成后页面将自动跳转</div>
  <div x-data="poller('/getshop.php', {type:'alipay', trade_no:'<?=addslashes($order['trade_no'])?>'}, {interval:2000, delay:2000})" @poll-ok.window="onOk()" style="display:none"></div>
</div>
<script>
function alipayPC(){
  return {
    statusText:'正在等待付款结果…',detailOpen:false,countdown:'15:00',secs:900,
    init(){this.startCountdown();},
    startCountdown(){var t=()=>{this.secs--;if(this.secs<0){this.countdown='已超时';return;}var m=String(Math.floor(this.secs/60)).padStart(2,'0');var s=String(this.secs%60).padStart(2,'0');this.countdown=m+':'+s;};t();setInterval(t,1000);},
    onOk(){this.statusText='支付成功,正在跳转…';epToast('success','支付成功,正在跳转');}
  };
}
</script>
<?php echo '</body></html>'; ?>