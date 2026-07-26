<?php
// 支付宝扫码支付页面
if(!defined('IN_PLUGIN'))exit();
define('IN_EPAY', true);
include_once ROOT.'includes/ep_ui.php';
$channel = 'alipay';
$title = '支付宝扫码支付';
ep_pay_head($title, $channel);
?>
<div class="ep-pay-card" x-data="alipayQR()" x-init="init()">
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
    <div class="ep-qr-box" id="qrcode"></div>
    <div class="ep-scan-hint">
      <svg class="ch-icon" width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20zm0 18a8 8 0 1 1 0-16 8 8 0 0 1 0 16z"/></svg>
      请使用支付宝扫一扫
    </div>
  </div>
  <!-- 打开APP(移动端) -->
  <template x-if="isMobile && !isDataImage">
    <div style="padding:0 20px 16px;display:flex;flex-direction:column;align-items:center;gap:10px">
      <template x-if="isWechat">
        <img src="/assets/img/guide1.png" alt="引导图" style="max-width:100%;margin:auto">
      </template>
      <a class="ep-btn ep-btn-primary" style="height:40px;width:100%" :href="isWechat?'javascript:void(0)':urlScheme" @click="isWechat?wxOpen():null">打开支付宝APP继续付款</a>
      <button class="ep-btn ep-btn-ghost ep-btn-sm" @click="checkResult()">我已付款，返回查看订单</button>
    </div>
  </template>
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
<script src="<?=$cdnpublic?>jquery/1.12.4/jquery.min.js"></script>
<script src="<?=$cdnpublic?>jquery.qrcode/1.0/jquery.qrcode.min.js"></script>
<script>
function alipayQR(){
  var code_url=<?=json_encode($code_url)?>;
  var isDataImage=code_url.indexOf('data:image/')>-1;
  var urlScheme=isDataImage?'':'alipays://platformapi/startapp?appId=20000067&url='+encodeURIComponent(code_url);
  return {
    statusText:'正在等待付款结果…',detailOpen:false,countdown:'15:00',secs:900,
    isMobile:/Android|iPhone|iPad|iPod|SymbianOS|Windows Phone/i.test(navigator.userAgent),
    isWechat:navigator.userAgent.indexOf('MicroMessenger/')>0,
    isDataImage:isDataImage,urlScheme:urlScheme,
    init(){
      this.renderQR();this.startCountdown();
      // 移动端支付宝自动跳转 + history 拦截
      if(this.isMobile){this.setupHistory();if(!isDataImage&&!this.isWechat&&navigator.userAgent.indexOf('EdgA/')==-1&&window.innerHeight>window.innerWidth){setTimeout(function(){window.location.href=urlScheme;},1000);}}
    },
    renderQR(){
      if(isDataImage){$('#qrcode').html('<img src="'+code_url+'" width="200" height="200"/>');return;}
      $('#qrcode').qrcode({text:code_url,width:200,height:200,foreground:'#000000',background:'#ffffff',typeNumber:-1});
      if(this.isWechat){var c=$('#qrcode canvas')[0];if(c){var i=new Image();i.src=c.toDataURL('image/png');$('#qrcode').empty().append(i);}}
    },
    startCountdown(){var t=()=>{this.secs--;if(this.secs<0){this.countdown='已超时';return;}var m=String(Math.floor(this.secs/60)).padStart(2,'0');var s=String(this.secs%60).padStart(2,'0');this.countdown=m+':'+s;};t();setInterval(t,1000);},
    setupHistory(){window.onpopstate=function(e){if(e.state=='forward'||confirm('是否取消支付并返回?')){window.history.back();}else{e.preventDefault();window.history.pushState('forward',null,'');}};window.history.pushState('forward',null,'');},
    wxOpen(){epToast('info','请点击屏幕右上角,在浏览器打开即可跳转支付');},
    async checkResult(){try{var d=await this.$fetch('/getshop.php',{method:'GET',body:{type:'alipay',trade_no:'<?=addslashes($order['trade_no'])?>'}});if(d.code===1){epToast('success','支付成功,正在跳转');setTimeout(function(){window.location.href=d.backurl;},300);}else{epToast('info','您还未完成付款,请继续付款');}}catch(e){epToast('error','服务器错误');}},
    onOk(){this.statusText='支付成功,正在跳转…';epToast('success','支付成功,正在跳转');}
  };
}
</script>
<?php echo '</body></html>'; ?>