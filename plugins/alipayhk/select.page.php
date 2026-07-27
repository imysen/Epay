<?php
if(!defined('IN_PLUGIN'))exit();
define('IN_EPAY', true);
include_once ROOT.'includes/ep_ui.php';
$channel = 'alipay';
$title = '支付宝支付';
ep_pay_head($title, $channel);
?>

<div class="ep-pay-card" style="width:min(420px,100%);text-align:center;padding:32px 24px">
  <div style="width:52px;height:52px;border-radius:8px;background:var(--ep-channel-bg);color:var(--ep-channel);display:inline-flex;align-items:center;justify-content:center;margin-bottom:16px"><?=ep_icon('wallet',26)?></div>
  <div style="font-size:16px;font-weight:600;color:var(--ep-gray-900)">支付宝支付</div>
  <div style="font-size:32px;font-weight:600;color:var(--ep-gray-900);margin-top:12px">¥<?=htmlspecialchars($order['realmoney'])?></div>
  <div style="font-size:13px;color:var(--ep-gray-500);margin-top:6px"><?=htmlspecialchars($order['name'])?></div>

  <div style="border-top:1px solid var(--ep-gray-100);margin:24px -24px 20px"></div>
  <div style="font-size:15px;font-weight:500;color:var(--ep-gray-800)">请选择付款 APP</div>
  <div style="font-size:12px;color:var(--ep-gray-400);margin-top:4px">Please select your wallet region</div>
  <div class="wallet-options">
    <a class="wallet-option" href="?type=ALIPAYCN"><img src="https://payment.pa-sys.com/imgs/alipay-cn-20240905.png" alt="支付宝中国"><span>支付宝（中国）</span></a>
    <a class="wallet-option" href="?type=ALIPAYHK"><img src="https://payment.pa-sys.com/imgs/alipay-hk-20240905.png" alt="AlipayHK"><span>AlipayHK</span></a>
  </div>

  <div class="ep-detail open" style="text-align:left;margin:0 -24px">
    <div class="ep-detail-body" style="max-height:240px"><div class="ep-detail-grid">
      <span class="k">商品名称</span><span class="v"><?=htmlspecialchars($order['name'])?></span>
      <span class="k">系统订单号</span><span class="v ep-mono"><?=htmlspecialchars($order['trade_no'])?></span>
      <span class="k">创建时间</span><span class="v"><?=htmlspecialchars($order['addtime'])?></span>
    </div></div>
  </div>
</div>
<style>
.wallet-options{display:grid;grid-template-columns:1fr 1fr;gap:12px;margin:20px 0 24px}.wallet-option{display:flex;flex-direction:column;align-items:center;gap:8px;padding:14px 8px;border:1px solid var(--ep-gray-200);border-radius:8px;color:var(--ep-gray-700);font-size:13px;text-decoration:none}.wallet-option:hover{border-color:var(--ep-brand-500);background:var(--ep-brand-50);text-decoration:none}.wallet-option img{width:68px;height:68px;object-fit:contain}
</style>
<script>
(function poll(){
  fetch('/getshop.php?'+new URLSearchParams({type:'alipay',trade_no:<?=json_encode($order['trade_no'])?>}),{credentials:'same-origin'}).then(r=>r.json()).then(d=>{if(d.code===1){window.location.href=d.backurl;return;}setTimeout(poll,2000);}).catch(()=>setTimeout(poll,2000));
})();
</script>
<?php echo '</body></html>'; ?>
