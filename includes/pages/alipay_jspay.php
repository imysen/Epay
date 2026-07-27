<?php
// 支付宝JS支付页面
if(!defined('IN_PLUGIN'))exit();
define('IN_EPAY', true);
include_once ROOT.'includes/ep_ui.php';
$channel = 'alipay';
$title = '支付宝支付';
ep_pay_head($title, $channel);
?>
<div class="ep-pay-card" x-data="jspayAli()" x-init="init()">
  <div class="ep-channel-bar"><div class="ep-channel-name"><span class="ep-channel-logo"><svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20zm0 18a8 8 0 1 1 0-16 8 8 0 0 1 0 16z"/><path d="M12 6v6l4 2"/></svg></span>支付宝支付</div></div>
  <div class="ep-amount-area" style="padding-top:40px"><div class="ep-amount"><span class="symbol">¥</span><?=htmlspecialchars($order['realmoney'])?></div><div class="ep-subject"><?=htmlspecialchars($order['name'])?></div></div>
  <div class="ep-status-bar pending"><span class="ep-dot-pulse"></span><span x-text="statusText">正在跳转支付…</span></div>
  <div class="ep-detail" :class="detailOpen?'open':''">
    <div class="ep-detail-toggle" @click="detailOpen=!detailOpen"><span class="label"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/></svg>订单详情</span><svg class="chev" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg></div>
    <div class="ep-detail-body"><div class="ep-detail-grid"><span class="k">商品名称</span><span class="v"><?=htmlspecialchars($order['name'])?></span><span class="k">系统订单号</span><span class="v mono"><?=htmlspecialchars($order['trade_no'])?></span></div></div>
  </div>
  <div class="ep-pay-foot">支付安全由中国人民财产保险股份有限公司承保</div>
  <div x-data="poller('/getshop.php', {type:'wxpay', trade_no:'<?=addslashes(TRADE_NO)?>'}, {interval:2000, delay:0})" @poll-ok.window="onOk()" style="display:none"></div>
</div>
<script src="<?=$cdnpublic?>jquery/1.12.4/jquery.min.js"></script>
<script>
document.body.addEventListener('touchmove',function(e){e.preventDefault();},{passive:false});
function jspayAli(){
  return {
    statusText:'正在跳转支付…',detailOpen:false,
    init(){this.alipayPay();},
    alipayReady(cb){if(window.AlipayJSBridge){cb&&cb();}else{document.addEventListener('AlipayJSBridgeReady',cb,false);}},
    alipayPay(){
      var self=this;
      this.alipayReady(function(){
        AlipayJSBridge.call('tradePay',{tradeNO:'<?=addslashes($alipay_trade_no)?>'},function(r){
          if(r.resultCode=='9000'){self.statusText='支付成功,正在跳转…';window.dispatchEvent(new CustomEvent('poll-ok',{detail:{}}));}
          else if(r.resultCode=='8000'){self.statusText='正在处理中…';}
          else if(r.resultCode=='4000'){self.statusText='订单支付失败';}
          else if(r.resultCode=='6002'){self.statusText='网络连接出错';}
        });
      });
    },
    onOk(){this.statusText='支付成功,正在跳转…';epToast('success','支付成功,正在跳转');}
  };
}
</script>
<?php echo '</body></html>'; ?>