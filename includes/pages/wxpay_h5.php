<?php
// 微信H5支付页面
if(!defined('IN_PLUGIN'))exit();
define('IN_EPAY', true);
include_once ROOT.'includes/ep_ui.php';
$channel = 'wxpay';
$title = '微信支付';
ep_pay_head($title, $channel);
?>
<div class="ep-pay-card" x-data="h5Pay()" x-init="init()">
  <div class="ep-channel-bar">
    <div class="ep-channel-name">
      <span class="ep-channel-logo">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M8.7 13.3a.8.8 0 1 1 0-1.6.8.8 0 0 1 0 1.6m6.6 0a.8.8 0 1 1 0-1.6.8.8 0 0 1 0 1.6M9.1 4.2C4.5 4.9 1.3 8 1.3 11.6c0 1.9.9 3.6 2.5 4.9-.2.6-.7 1.8-.7 2 0 .2.1.3.3.3.1 0 2.2-1.2 3.2-1.8 1 .3 2 .4 3.1.4h.5c-.2-.5-.3-1-.3-1.5 0-3.4 3.2-6.1 7.3-6.1.3 0 .6 0 .9.1-.6-3.1-3.8-5.5-8-5.7M9 7.4a.9.9 0 1 1 0-1.8.9.9 0 0 1 0 1.8m6 0a.9.9 0 1 1 0-1.8.9.9 0 0 1 0 1.8"/></svg>
      </span>
      微信支付
    </div>
  </div>
  <div class="ep-amount-area" style="padding-top:32px">
    <div class="ep-amount"><span class="symbol">¥</span><?=htmlspecialchars($order['realmoney'])?></div>
    <div class="ep-subject"><?=htmlspecialchars($order['name'])?></div>
  </div>
  <div style="padding:20px;display:flex;flex-direction:column;gap:12px">
    <a class="ep-btn ep-btn-primary" style="height:44px;width:100%" :href="urlScheme" x-text="btnText">跳转到微信支付</a>
    <button class="ep-btn ep-btn-secondary" style="height:44px;width:100%" @click="checkResult()">检测支付状态</button>
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
  <div x-data="poller('/getshop.php', {type:'wxpay', trade_no:'<?=addslashes($order['trade_no'])?>'}, {interval:2000, delay:3000})" @poll-ok.window="onOk()" style="display:none"></div>
</div>
<script>
function h5Pay(){
  var urlScheme=<?=json_encode($code_url)?>;
  var isHttp=urlScheme.match(/^https?:\/\//);
  return {
    statusText:'正在等待付款结果…',detailOpen:false,urlScheme:urlScheme,btnText:'跳转到微信支付',
    init(){this.setupHistory();if(!isHttp&&navigator.userAgent.indexOf('EdgA/')==-1){window.location.href=urlScheme;}},
    setupHistory(){window.onpopstate=function(e){if(e.state=='forward'||confirm('是否取消支付并返回?')){window.history.back();}else{e.preventDefault();window.history.pushState('forward',null,'');}};window.history.pushState('forward',null,'');},
    async checkResult(){try{var d=await this.$fetch('/getshop.php',{method:'GET',body:{type:'wxpay',trade_no:'<?=addslashes($order['trade_no'])?>'}});if(d.code===1){epToast('success','支付成功,正在跳转');setTimeout(function(){window.location.href=d.backurl;},300);}else{epToast('info','您还未完成付款');}}catch(e){epToast('error','服务器错误');}},
    onOk(){this.statusText='支付成功,正在跳转…';epToast('success','支付成功,正在跳转');}
  };
}
</script>
<?php echo '</body></html>'; ?>