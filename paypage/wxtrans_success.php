<?php
if(!defined('IN_CRONLITE'))exit();
define('IN_EPAY', true);
include_once ROOT.'includes/ep_ui.php';
$channel = 'wxpay';
$title = '收款成功';
ep_pay_head($title, $channel);
?>

<div class="ep-pay-card" style="width:min(420px,100%);text-align:center;padding:40px 24px 32px">
  <div style="width:64px;height:64px;border-radius:50%;background:var(--ep-success-50);color:var(--ep-success-500);display:inline-flex;align-items:center;justify-content:center;margin-bottom:20px">
    <?=ep_icon('check',32)?>
  </div>
  <div style="font-size:16px;font-weight:600;color:var(--ep-gray-900);margin-bottom:8px">你已收款，资金已存入零钱</div>
  <div style="font-size:32px;font-weight:600;color:var(--ep-gray-900);font-variant-numeric:tabular-nums">¥<?=number_format((float)$money, 2)?></div>

  <div class="ep-card" style="text-align:left;margin-top:24px;margin-bottom:24px">
    <div class="ep-detail-grid" style="margin:0;padding-top:16px">
      <span class="k">转账时间</span><span class="v"><?=htmlspecialchars($addtime)?></span>
      <span class="k">收款时间</span><span class="v"><?=htmlspecialchars($paytime)?></span>
    </div>
  </div>

  <button class="ep-btn ep-btn-secondary" id="Close" style="width:100%;height:44px">关闭</button>
  <div style="font-size:12px;color:var(--ep-gray-400);margin-top:20px">Copyright © <?=date('Y')?> <?=htmlspecialchars($conf['sitename'])?></div>
</div>

<script>
function closeWin(){
  if(typeof WeixinJSBridge!=='undefined')WeixinJSBridge.call('closeWindow');
  else {window.opener=null;window.close();}
}
document.getElementById('Close').addEventListener('click',closeWin);
</script>
<?php echo '</body></html>'; ?>
