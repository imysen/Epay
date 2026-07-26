<?php
if (!defined('IN_PLUGIN')) exit();
define('IN_EPAY', true);
include_once ROOT.'includes/ep_ui.php';
$channel = 'alipay';
$title = '支付宝扫码支付';
ep_pay_head($title, $channel);
?>

<div class="ep-pay-card" x-data="alipayCodePay()" x-init="init()">
  <div class="ep-channel-bar">
    <div class="ep-channel-name"><span class="ep-channel-logo"><?=ep_icon('credit-card',16)?></span>支付宝扫码支付</div>
    <div class="ep-countdown"><?=ep_icon('clock',13)?><span x-text="countdown"></span></div>
  </div>
  <div class="ep-amount-area">
    <div class="ep-amount"><span class="symbol">¥</span><?=htmlspecialchars($order['realmoney'])?></div>
    <div class="ep-subject"><?=htmlspecialchars($order['name'])?></div>
  </div>
  <div class="ep-qr-area">
    <div class="ep-qr-box" style="position:relative">
      <div id="qrcode"></div>
      <div x-show="expired" x-cloak style="position:absolute;inset:0;background:rgba(17,24,39,.78);display:flex;flex-direction:column;align-items:center;justify-content:center;color:#fff;border-radius:8px;gap:8px">
        <?=ep_icon('alert',28)?><strong>二维码已失效</strong><span style="font-size:12px">请返回重新发起支付</span>
      </div>
    </div>
    <div class="ep-scan-hint"><span class="ch-icon"><?=ep_icon('qr-code',14)?></span>请使用支付宝扫一扫</div>
  </div>
  <template x-if="isMobile && !isDataImage && !expired">
    <div style="padding:0 20px 12px;display:flex;flex-direction:column;gap:8px">
      <button class="ep-btn ep-btn-primary" style="height:42px;width:100%" @click="openApp()">打开支付宝 APP 继续付款</button>
      <button class="ep-btn ep-btn-secondary" style="height:38px;width:100%" @click="checkResult()">我已付款，返回查看订单</button>
    </div>
  </template>
  <div class="ep-status-bar pending"><span class="ep-dot-pulse"></span><span x-text="expired?'订单已过期':'正在等待付款结果…'"></span></div>
  <div class="ep-detail" :class="detailOpen?'open':''">
    <div class="ep-detail-toggle" @click="detailOpen=!detailOpen"><span class="label"><?=ep_icon('list',16)?>订单详情</span><?=ep_icon('chevron-down',16,'chev')?></div>
    <div class="ep-detail-body"><div class="ep-detail-grid">
      <span class="k">商品名称</span><span class="v"><?=htmlspecialchars($order['name'])?></span>
      <span class="k">系统订单号</span><span class="v ep-mono"><?=htmlspecialchars($order['trade_no'])?></span>
      <span class="k">创建时间</span><span class="v"><?=htmlspecialchars($order['addtime'])?></span>
    </div></div>
  </div>
  <div class="ep-pay-foot">支付完成后页面将自动跳转</div>
</div>

<div id="wechat-guide" x-data="{open:false}" x-show="open" x-cloak @show-guide.window="open=true" @click="open=false" style="position:fixed;inset:0;background:rgba(17,24,39,.72);z-index:100"><img src="/assets/img/guide1.png" alt="请在浏览器打开" style="width:min(96%,420px);float:right;margin:12px"></div>
<script src="<?php echo $cdnpublic ?>jquery/1.12.4/jquery.min.js"></script>
<script src="<?php echo $cdnpublic ?>jquery.qrcode/1.0/jquery.qrcode.min.js"></script>
<script>
function alipayCodePay(){
  const codeUrl=<?=json_encode($code_url)?>;
  const tradeNo=<?=json_encode($order['trade_no'])?>;
  const initialSeconds=<?=json_encode((int)$paytime)?>;
  return {
    detailOpen:false,expired:false,isDataImage:codeUrl.indexOf('data:image/')>-1,
    isMobile:/Android|iPhone|iPad|iPod|Windows Phone/i.test(navigator.userAgent),
    isWechat:navigator.userAgent.indexOf('MicroMessenger/')>-1,
    seconds:initialSeconds,countdown:'—',
    init(){
      this.renderQR();this.tick();
      if(this.seconds>0)this.timer=setInterval(()=>this.tick(),1000);
      if(this.isMobile) this.protectHistory();
      if(this.isMobile&&!this.isDataImage&&!this.isWechat&&navigator.userAgent.indexOf('EdgA/')===-1) setTimeout(()=>this.openApp(),1000);
      this.poll();
    },
    renderQR(){
      if(this.isDataImage){$('#qrcode').html('<img src="'+codeUrl+'" width="200" height="200" alt="支付宝付款码">');return;}
      $('#qrcode').qrcode({text:codeUrl,width:200,height:200,foreground:'#000',background:'#fff',typeNumber:-1});
      if(this.isWechat){const canvas=$('#qrcode canvas')[0];if(canvas){const image=new Image();image.src=canvas.toDataURL('image/png');$('#qrcode').empty().append(image);}}
    },
    tick(){
      if(this.seconds<=0){this.expired=true;this.countdown='已过期';clearInterval(this.timer);return;}
      const h=String(Math.floor(this.seconds/3600)).padStart(2,'0');const m=String(Math.floor(this.seconds%3600/60)).padStart(2,'0');const s=String(this.seconds%60).padStart(2,'0');this.countdown=h+':'+m+':'+s;this.seconds--;
    },
    protectHistory(){window.history.pushState('forward',null,'');window.onpopstate=e=>{if(e.state==='forward'||confirm('是否取消支付并返回？'))window.history.back();else window.history.pushState('forward',null,'');};},
    openApp(){if(this.isWechat){window.dispatchEvent(new CustomEvent('show-guide'));return;}window.location.href='alipays://platformapi/startapp?appId=20000067&url='+encodeURIComponent(codeUrl);},
    async checkResult(){try{const d=await this.$fetch('/getshop.php',{method:'GET',body:{type:'alipay',trade_no:tradeNo}});if(d.code===1)window.location.href=d.backurl;else epToast('info','您还未完成付款，请继续付款');}catch(e){epToast('error','服务器错误');}},
    async poll(){if(this.expired)return;try{const d=await this.$fetch('/getshop.php',{method:'GET',body:{type:'alipay',trade_no:tradeNo}});if(d.code===1){window.location.href=d.backurl;return;}}catch(e){}setTimeout(()=>this.poll(),2000);}
  };
}
</script>
<?php echo '</body></html>'; ?>
