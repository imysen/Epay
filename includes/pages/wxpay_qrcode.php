<?php
// 微信扫码支付页面
if(!defined('IN_PLUGIN'))exit();
define('IN_EPAY', true);
include_once ROOT.'includes/ep_ui.php';
$channel = 'wxpay';
$title = '微信扫码支付';
ep_pay_head($title, $channel);
?>
<div class="ep-pay-card" x-data="wxpayQrcode()" x-init="init()">
  <!-- 通道条 -->
  <div class="ep-channel-bar">
	<div class="ep-channel-name">
	  <span class="ep-channel-logo">
		<svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M8.7 13.3a.8.8 0 1 1 0-1.6.8.8 0 0 1 0 1.6m6.6 0a.8.8 0 1 1 0-1.6.8.8 0 0 1 0 1.6M9.1 4.2C4.5 4.9 1.3 8 1.3 11.6c0 1.9.9 3.6 2.5 4.9-.2.6-.7 1.8-.7 2 0 .2.1.3.3.3.1 0 2.2-1.2 3.2-1.8 1 .3 2 .4 3.1.4h.5c-.2-.5-.3-1-.3-1.5 0-3.4 3.2-6.1 7.3-6.1.3 0 .6 0 .9.1-.6-3.1-3.8-5.5-8-5.7M9 7.4a.9.9 0 1 1 0-1.8.9.9 0 0 1 0 1.8m6 0a.9.9 0 1 1 0-1.8.9.9 0 0 1 0 1.8"/></svg>
	  </span>
	  微信扫码支付
	</div>
	<div class="ep-countdown">
	  <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
	  <span x-text="countdown"></span>
	</div>
  </div>

  <!-- 金额 -->
  <div class="ep-amount-area">
	<div class="ep-amount"><span class="symbol">¥</span><?=htmlspecialchars($order['realmoney'])?></div>
	<div class="ep-subject"><?=htmlspecialchars($order['name'])?></div>
  </div>

  <!-- 二维码 -->
  <div class="ep-qr-area">
	<div class="ep-qr-box" id="qrcode"></div>
	<div class="ep-scan-hint">
	  <svg class="ch-icon" width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M8.7 13.3a.8.8 0 1 1 0-1.6.8.8 0 0 1 0 1.6m6.6 0a.8.8 0 1 1 0-1.6.8.8 0 0 1 0 1.6M9.1 4.2C4.5 4.9 1.3 8 1.3 11.6c0 1.9.9 3.6 2.5 4.9-.2.6-.7 1.8-.7 2 0 .2.1.3.3.3.1 0 2.2-1.2 3.2-1.8 1 .3 2 .4 3.1.4h.5c-.2-.5-.3-1-.3-1.5 0-3.4 3.2-6.1 7.3-6.1.3 0 .6 0 .9.1-.6-3.1-3.8-5.5-8-5.7M9 7.4a.9.9 0 1 1 0-1.8.9.9 0 0 1 0 1.8m6 0a.9.9 0 1 1 0-1.8.9.9 0 0 1 0 1.8"/></svg>
	  请使用微信扫一扫
	</div>
  </div>

  <!-- 状态条 -->
  <div class="ep-status-bar pending">
	<span class="ep-dot-pulse"></span>
	<span x-text="statusText">正在等待付款结果…</span>
  </div>

  <!-- 移动端复制(仅手机显示) -->
  <template x-if="isMobile">
	<div style="padding:0 20px 20px">
	  <button class="ep-copy-btn" @click="copyLink()">
		<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
		复制链接在微信中打开
	  </button>
	</div>
  </template>

  <!-- 订单详情 -->
  <div class="ep-detail" :class="detailOpen?'open':''">
	<div class="ep-detail-toggle" @click="detailOpen=!detailOpen">
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

  <!-- 轮询器 -->
  <div x-data="poller('/getshop.php', {type:'wxpay', trade_no:'<?=addslashes($order['trade_no'])?>'}, {interval:2000, delay:2000})"
	   @poll-ok.window="onOk()"
	   style="display:none"></div>
</div>

<!-- jQuery + jquery.qrcode 仅用于二维码生成(操作 #qrcode,不涉及 Alpine x-data 子树) -->
<script src="<?=$cdnpublic?>jquery/1.12.4/jquery.min.js"></script>
<script src="<?=$cdnpublic?>jquery.qrcode/1.0/jquery.qrcode.min.js"></script>
<script>
function wxpayQrcode(){
	return {
		statusText:'正在等待付款结果…',
		detailOpen:false,
		isMobile:/Android|iPhone|iPad|iPod|SymbianOS|Windows Phone/i.test(navigator.userAgent),
		countdown:'15:00',
		secs:900,
		init(){
			this.renderQR();
			this.startCountdown();
		},
		renderQR(){
			var code_url = <?=json_encode($code_url)?>;
			var code_type = code_url.indexOf('data:image/')>-1?1:0;
			if(code_type==0){
				$('#qrcode').qrcode({text:code_url,width:200,height:200,foreground:'#000000',background:'#ffffff',typeNumber:-1});
				// 微信内置浏览器:canvas 转 img(长按识别)
				if(navigator.userAgent.indexOf('MicroMessenger/')>0){
					var canvas=$('#qrcode canvas')[0];
					if(canvas){
						var img=new Image();
						img.src=canvas.toDataURL('image/png');
						$('#qrcode').empty().append(img);
					}
				}
			}else{
				$('#qrcode').html('<img src="'+code_url+'" width="200" height="200"/>');
			}
		},
		startCountdown(){
			const tick=()=>{this.secs--;if(this.secs<0){this.countdown='已超时';return;}const m=String(Math.floor(this.secs/60)).padStart(2,'0');const s=String(this.secs%60).padStart(2,'0');this.countdown=m+':'+s;};
			tick();setInterval(tick,1000);
		},
		async copyLink(){
			var code_url = <?=json_encode($code_url)?>;
			try{await navigator.clipboard.writeText(code_url);epToast('success','链接已复制,请到微信粘贴');}
			catch{epToast('info','请长按页面复制链接');}
		},
		onOk(){this.statusText='支付成功,正在跳转…';epToast('success','支付成功,正在跳转');}
	};
}
</script>
<?php echo '</body></html>'; ?>
