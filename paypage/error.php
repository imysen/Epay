<?php
if(!defined('IN_CRONLITE'))exit();
define('IN_EPAY', true);
include_once ROOT.'includes/ep_ui.php';
$channel = 'wxpay';
$title = '错误提示';
ep_pay_head($title, $channel);
?>

<div class="ep-pay-card" style="width:min(420px,100%);text-align:center;padding:40px 24px 32px">
  <div style="width:64px;height:64px;border-radius:50%;background:var(--ep-danger-50);color:var(--ep-danger-500);display:inline-flex;align-items:center;justify-content:center;margin-bottom:20px">
    <?=ep_icon('alert',32)?>
  </div>
  <div style="font-size:16px;font-weight:600;color:var(--ep-gray-900);margin-bottom:8px"><?=htmlspecialchars($msg)?></div>
  <button class="ep-btn ep-btn-secondary" id="Close" style="width:100%;height:44px;margin-top:24px">关闭</button>
</div>

<script>
document.getElementById('Close').addEventListener('click',function(){
  var ua=navigator.userAgent;
  if(ua.indexOf('AlipayClient')>-1 && window.AlipayJSBridge){AlipayJSBridge.call('popWindow');}
  else if(ua.indexOf('MicroMessenger')>-1 && typeof WeixinJSBridge!=='undefined'){WeixinJSBridge.call('closeWindow');}
  else{window.opener=null;window.close();if(window.location.href.indexOf('close=1')===-1)window.location.href=window.location.href+'&close=1';}
});
document.body.addEventListener('touchmove',function(e){e.preventDefault();},{passive:false});
</script>

<?php echo '</body></html>'; ?>
