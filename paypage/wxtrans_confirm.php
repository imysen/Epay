<?php
if(!defined('IN_CRONLITE'))exit();
define('IN_EPAY', true);
include_once ROOT.'includes/ep_ui.php';
$channel = 'wxpay';
$title = '确认收款';
ep_pay_head($title, $channel);
?>

<div class="ep-pay-card" style="width:min(420px,100%);text-align:center;padding:40px 24px 32px">
  <div style="width:64px;height:64px;border-radius:50%;background:var(--ep-warning-50);color:var(--ep-warning-500);display:inline-flex;align-items:center;justify-content:center;margin-bottom:20px">
    <?=ep_icon('wallet',32)?>
  </div>
  <div style="font-size:16px;font-weight:600;color:var(--ep-gray-900);margin-bottom:8px">待你收款</div>
  <div style="font-size:32px;font-weight:600;color:var(--ep-gray-900);font-variant-numeric:tabular-nums">¥<?=number_format((float)$money, 2)?></div>

  <div class="ep-card" style="text-align:left;margin-top:24px;margin-bottom:24px">
    <div class="ep-detail-grid" style="margin:0;padding-top:16px">
      <span class="k">转账时间</span><span class="v"><?=htmlspecialchars($addtime)?></span>
    </div>
  </div>

  <button class="ep-btn ep-btn-primary" id="Confirm" style="width:100%;height:44px" disabled>收款</button>
  <div style="font-size:12px;color:var(--ep-gray-400);margin-top:14px">1天内未确认，将退还给商家</div>
  <div style="font-size:12px;color:var(--ep-gray-400);margin-top:20px">Copyright © <?=date('Y')?> <?=htmlspecialchars($conf['sitename'])?></div>
</div>

<div class="paypage-mask" id="loading"><div class="paypage-loading"><span></span>正在加载…</div></div>
<div class="paypage-mask" id="dialog" hidden><div class="paypage-dialog"><div id="dialog-content" style="font-size:14px;color:var(--ep-gray-700);line-height:1.6"></div><button class="ep-btn ep-btn-primary" id="dialog-close" style="width:100%;margin-top:20px">关闭</button></div></div>
<style>
.paypage-mask{position:fixed;inset:0;background:rgba(17,24,39,.28);z-index:80;display:flex;align-items:center;justify-content:center;padding:24px}
.paypage-mask[hidden]{display:none}
.paypage-loading,.paypage-dialog{background:#fff;border-radius:8px;box-shadow:var(--ep-shadow-md);padding:20px 24px;color:var(--ep-gray-700);font-size:14px}
.paypage-loading{display:flex;align-items:center;gap:10px}.paypage-loading span{width:18px;height:18px;border:2px solid var(--ep-gray-200);border-top-color:var(--ep-brand-500);border-radius:50%;animation:paypage-spin .8s linear infinite}
.paypage-dialog{width:min(320px,100%)}@keyframes paypage-spin{to{transform:rotate(360deg)}}
</style>
<script src="//res.wx.qq.com/open/js/jweixin-1.6.0.js"></script>
<script>
const confirmButton=document.getElementById('Confirm');
const loading=document.getElementById('loading');
function showDialog(message){document.getElementById('dialog-content').textContent=message;document.getElementById('dialog').hidden=false;}
document.getElementById('dialog-close').addEventListener('click',()=>document.getElementById('dialog').hidden=true);
wx.config(<?php echo $wxconfig?>);
wx.ready(function(){
  wx.checkJsApi({jsApiList:['requestMerchantTransfer'],success:function(res){
    loading.hidden=true;
    if(res.checkResult.requestMerchantTransfer)confirmButton.disabled=false;
    else showDialog('你的微信版本过低，请更新至最新版本。');
  }});
});
wx.error(function(){loading.hidden=true;showDialog('微信功能加载失败，请稍后重试。');});
confirmButton.addEventListener('click',function(){
  if(confirmButton.disabled)return;
  WeixinJSBridge.invoke('requestMerchantTransfer',<?php echo $wxtransfer?>,function(res){
    if(res.err_msg==='requestMerchantTransfer:ok')window.location.href=<?=json_encode($url.'&do=success')?>;
  });
});
</script>
<?php echo '</body></html>'; ?>
