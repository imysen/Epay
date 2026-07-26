<?php
// 微信引导页 - 提示用户在浏览器中打开完成支付
if(!defined('IN_PLUGIN'))exit();
define('IN_EPAY', true);
include_once ROOT.'includes/ep_ui.php';
$useragent = strtolower($_SERVER['HTTP_USER_AGENT']);
$bg_img = (strpos($useragent,'iphone')!==false||strpos($useragent,'ipod')!==false) ? '/assets/img/ios.png' : '/assets/img/android.png';
$channel = 'wxpay';
$title = '支付提示';
ep_pay_head($title, $channel);
?>
<div class="ep-pay-card" x-data="wxOpen()" x-init="init()" style="text-align:center">
  <div class="ep-channel-bar">
    <div class="ep-channel-name">
      <span class="ep-channel-logo">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M8.7 13.3a.8.8 0 1 1 0-1.6.8.8 0 0 1 0 1.6m6.6 0a.8.8 0 1 1 0-1.6.8.8 0 0 1 0 1.6M9.1 4.2C4.5 4.9 1.3 8 1.3 11.6c0 1.9.9 3.6 2.5 4.9-.2.6-.7 1.8-.7 2 0 .2.1.3.3.3.1 0 2.2-1.2 3.2-1.8 1 .3 2 .4 3.1.4h.5c-.2-.5-.3-1-.3-1.5 0-3.4 3.2-6.1 7.3-6.1.3 0 .6 0 .9.1-.6-3.1-3.8-5.5-8-5.7M9 7.4a.9.9 0 1 1 0-1.8.9.9 0 0 1 0 1.8m6 0a.9.9 0 1 1 0-1.8.9.9 0 0 1 0 1.8"/></svg>
      </span>
      支付提示
    </div>
  </div>

  <!-- 引导区 -->
  <div style="padding:24px 20px 8px;display:flex;flex-direction:column;align-items:center;gap:16px">
    <img src="<?=htmlspecialchars($bg_img)?>" alt="引导" style="width:120px;height:120px;object-fit:contain">
    <div style="font-size:14px;color:var(--ep-gray-600);line-height:1.8">
      请点击右上角<br>
      <strong>在浏览器中打开</strong><br>
      以完成支付
    </div>
  </div>

  <!-- 订单金额 -->
  <div class="ep-amount-area" style="padding-top:12px">
    <div class="ep-amount"><span class="symbol">¥</span><?=htmlspecialchars($order['realmoney'])?></div>
    <div class="ep-subject"><?=htmlspecialchars($order['name'])?></div>
  </div>

  <div class="ep-status-bar pending"><span class="ep-dot-pulse"></span><span x-text="statusText">正在等待付款结果…</span></div>

  <div class="ep-detail" :class="detailOpen?'open':''">
    <div class="ep-detail-toggle" @click="detailOpen=!detailOpen"><span class="label"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/></svg>订单详情</span><svg class="chev" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg></div>
    <div class="ep-detail-body"><div class="ep-detail-grid"><span class="k">商品名称</span><span class="v"><?=htmlspecialchars($order['name'])?></span><span class="k">系统订单号</span><span class="v mono"><?=htmlspecialchars($order['trade_no'])?></span><span class="k">创建时间</span><span class="v"><?=htmlspecialchars($order['addtime'])?></span></div></div>
  </div>

  <div class="ep-pay-foot">支付完成后页面将自动跳转</div>
  <div x-data="poller('/getshop.php', {type:'alipay', trade_no:'<?=addslashes($order['trade_no'])?>'}, {interval:2000, delay:5000})" @poll-ok.window="onOk()" style="display:none"></div>
</div>
<script>
function wxOpen(){
  return {
    statusText:'正在等待付款结果…',detailOpen:false,
    init(){},
    onOk(){this.statusText='支付成功,正在跳转…';epToast('success','支付成功,正在跳转');}
  };
}
</script>
<?php echo '</body></html>'; ?>