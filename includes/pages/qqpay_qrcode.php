<?php
// QQ钱包扫码支付页面
if(!defined('IN_PLUGIN'))exit();
define('IN_EPAY', true);
include_once ROOT.'includes/ep_ui.php';
$channel = 'qqpay';
$title = 'QQ钱包扫码支付';
ep_pay_head($title, $channel);
?>
<div class="ep-pay-card" x-data="qrcodePay()" x-init="init()">
  <div class="ep-channel-bar">
    <div class="ep-channel-name">
      <span class="ep-channel-logo">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="12" r="10"/><path d="M8 12h8M12 8v8" stroke="#fff" stroke-width="2" stroke-linecap="round"/></svg>
      </span>
      QQ钱包扫码支付
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
    <div class="ep-qr-box" id="qrcode"></div>
    <div class="ep-scan-hint">
      <svg class="ch-icon" width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="12" r="10"/></svg>
      请使用手机QQ扫一扫
    </div>
  </div>
  <div class="ep-status-bar pending"><span class="ep-dot-pulse"></span><span x-text="statusText">正在等待付款结果…</span></div>
  <template x-if="isMobile">
    <div style="padding:0 20px 20px">
      <button class="ep-copy-btn" @click="copyLink()">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
        复制链接在QQ中打开
      </button>
    </div>
  </template>
  <div class="ep-detail" :class="detailOpen?'open':''">
    <div class="ep-detail-toggle" @click="detailOpen=!detailOpen">
      <span class="label"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/></svg>订单详情</span>
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
  <div x-data="poller('/getshop.php', {type:'qqpay', trade_no:'<?=addslashes($order['trade_no'])?>'}, {interval:2000, delay:0})" @poll-ok.window="onOk()" style="display:none"></div>
</div>
<script src="<?=$cdnpublic?>jquery/1.12.4/jquery.min.js"></script>
<script src="<?=$cdnpublic?>jquery.qrcode/1.0/jquery.qrcode.min.js"></script>
<script>
function qrcodePay(){
  return {
    statusText:'正在等待付款结果…',detailOpen:false,isMobile:/Android|iPhone|iPad|iPod|SymbianOS|Windows Phone/i.test(navigator.userAgent),countdown:'15:00',secs:900,
    init(){this.renderQR();this.startCountdown();},
    renderQR(){
      var code_url=<?=json_encode($code_url)?>;var ct=code_url.indexOf('data:image/')>-1?1:0;
      if(ct==0){$('#qrcode').qrcode({text:code_url,width:200,height:200,foreground:'#000000',background:'#ffffff',typeNumber:-1});
        if(navigator.userAgent.indexOf('QQ/')>0){var c=$('#qrcode canvas')[0];if(c){var i=new Image();i.src=c.toDataURL('image/png');$('#qrcode').empty().append(i);}}
      }else{$('#qrcode').html('<img src="'+code_url+'" width="200" height="200"/>');}
    },
    startCountdown(){var t=()=>{this.secs--;if(this.secs<0){this.countdown='已超时';return;}var m=String(Math.floor(this.secs/60)).padStart(2,'0');var s=String(this.secs%60).padStart(2,'0');this.countdown=m+':'+s;};t();setInterval(t,1000);},
    async copyLink(){var u=<?=json_encode($code_url)?>;try{await navigator.clipboard.writeText(u);epToast('success','链接已复制');}catch{epToast('info','请长按复制链接');}},
    onOk(){this.statusText='支付成功,正在跳转…';epToast('success','支付成功,正在跳转');}
  };
}
</script>
<?php echo '</body></html>'; ?>