<?php
// 微信WAP扫码支付页面
if(!defined('IN_PLUGIN'))exit();
define('IN_EPAY', true);
include_once ROOT.'includes/ep_ui.php';
$channel = 'wxpay';
$title = '微信支付';
ep_pay_head($title, $channel);
?>
<div class="ep-pay-card" x-data="wapPay()" x-init="init()">
  <div class="ep-channel-bar">
    <div class="ep-channel-name">
      <span class="ep-channel-logo">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M8.7 13.3a.8.8 0 1 1 0-1.6.8.8 0 0 1 0 1.6m6.6 0a.8.8 0 1 1 0-1.6.8.8 0 0 1 0 1.6M9.1 4.2C4.5 4.9 1.3 8 1.3 11.6c0 1.9.9 3.6 2.5 4.9-.2.6-.7 1.8-.7 2 0 .2.1.3.3.3.1 0 2.2-1.2 3.2-1.8 1 .3 2 .4 3.1.4h.5c-.2-.5-.3-1-.3-1.5 0-3.4 3.2-6.1 7.3-6.1.3 0 .6 0 .9.1-.6-3.1-3.8-5.5-8-5.7M9 7.4a.9.9 0 1 1 0-1.8.9.9 0 0 1 0 1.8m6 0a.9.9 0 1 1 0-1.8.9.9 0 0 1 0 1.8"/></svg>
      </span>
      微信支付
    </div>
  </div>
  <div class="ep-amount-area">
    <div class="ep-amount"><span class="symbol">¥</span><?=htmlspecialchars($order['realmoney'])?></div>
    <div class="ep-subject"><?=htmlspecialchars($order['name'])?></div>
  </div>
  <div class="ep-qr-area">
    <div class="ep-qr-box" id="qrcode"></div>
    <div class="ep-scan-hint" style="margin-top:8px;flex-direction:column;gap:4px">
      <span class="ch-icon">请使用微信APP扫描二维码支付</span>
    </div>
  </div>
  <div style="padding:0 20px 12px;display:flex;flex-direction:column;gap:8px">
    <a class="ep-btn ep-btn-primary" style="height:40px;width:100%" href="weixin://">打开微信APP</a>
    <div style="display:flex;gap:8px">
      <button class="ep-btn ep-btn-secondary" style="flex:1" @click="checkResult()">检测支付状态</button>
      <button class="ep-btn ep-btn-secondary" style="flex:1" @click="downloadQR()">保存二维码</button>
    </div>
    <div style="display:flex;gap:8px;align-items:center">
      <input class="ep-input ep-mini" style="flex:1;background:var(--ep-gray-50)" :value="codeUrl" readonly>
      <button class="ep-btn ep-btn-secondary ep-btn-sm" @click="copyLink()">复制</button>
    </div>
    <div style="font-size:12px;color:var(--ep-gray-400)">提示：可将链接发到微信聊天框，点击即可支付</div>
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
<script src="<?=$cdnpublic?>jquery/1.12.4/jquery.min.js"></script>
<script src="<?=$cdnpublic?>jquery.qrcode/1.0/jquery.qrcode.min.js"></script>
<script>
function wapPay(){
  var u=<?=json_encode($code_url)?>;
  return {
    statusText:'正在等待付款结果…',detailOpen:false,codeUrl:u,
    init(){this.setupHistory();$('#qrcode').qrcode({text:u,width:200,height:200,foreground:'#000000',background:'#ffffff',typeNumber:-1});},
    setupHistory(){window.onpopstate=function(e){if(e.state=='forward'||confirm('是否取消支付并返回?')){window.history.back();}else{e.preventDefault();window.history.pushState('forward',null,'');}};window.history.pushState('forward',null,'');},
    downloadQR(){var c=$('#qrcode canvas')[0];if(c){var a=document.createElement('a');a.download='微信支付二维码.png';a.href=c.toDataURL('image/png');a.click();}},
    async copyLink(){try{await navigator.clipboard.writeText(u);epToast('success','链接已复制');}catch{epToast('info','请长按手动复制');}},
    async checkResult(){try{var d=await this.$fetch('/getshop.php',{method:'GET',body:{type:'wxpay',trade_no:'<?=addslashes($order['trade_no'])?>'}});if(d.code===1){epToast('success','支付成功,正在跳转');setTimeout(function(){window.location.href=d.backurl;},300);}else{epToast('info','您还未完成付款');}}catch(e){epToast('error','服务器错误');}},
    onOk(){this.statusText='支付成功,正在跳转…';epToast('success','支付成功,正在跳转');}
  };
}
</script>
<?php echo '</body></html>'; ?>