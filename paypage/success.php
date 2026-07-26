<?php
$is_defend = true;
include("./inc.php");
@header('Content-Type: text/html; charset=UTF-8');
$trade_no=daddslashes($_GET['trade_no']);
$row=$DB->getRow("SELECT * FROM pre_order WHERE trade_no='{$trade_no}' limit 1");
if(!$row)showerror('订单号不存在');
if($row['status']!=1)showerror('订单未完成支付');
if(!isset($_SESSION['paypage_trade_no']) || $_SESSION['paypage_trade_no']!=$trade_no)showerror('订单校验失败');
$userrow=$DB->getRow("select codename,username from pre_user where uid='{$row['uid']}' limit 1");
$codename = !empty($userrow['codename'])?$userrow['codename']:$userrow['username'];

define('IN_EPAY', true);
include_once ROOT.'includes/ep_ui.php';
$channel = 'wxpay';
$title = '支付成功';
ep_pay_head($title, $channel);
?>

<div class="ep-pay-card" style="width:min(420px,100%);text-align:center;padding:40px 24px 32px">
  <div style="width:64px;height:64px;border-radius:50%;background:var(--ep-success-50);color:var(--ep-success-500);display:inline-flex;align-items:center;justify-content:center;margin-bottom:20px">
    <?=ep_icon('check',32)?>
  </div>
  <div style="font-size:16px;font-weight:600;color:var(--ep-gray-900);margin-bottom:8px">支付成功</div>
  <div style="font-size:32px;font-weight:600;color:var(--ep-gray-900);margin-bottom:8px">¥<?=number_format($row['money'],2)?></div>

  <div class="ep-card" style="text-align:left;margin-top:24px;margin-bottom:24px">
    <div class="ep-detail-grid" style="margin:0">
      <span class="k">收款方</span><span class="v"><?=htmlspecialchars($codename)?></span>
      <span class="k">完成时间</span><span class="v"><?=$row['endtime']?></span>
      <span class="k">订单号</span><span class="v mono"><?=$trade_no?></span>
    </div>
  </div>

  <button class="ep-btn ep-btn-secondary" id="Close" style="width:100%;height:44px">关闭</button>
  <div style="font-size:12px;color:var(--ep-gray-400);margin-top:20px">Copyright © <?=date("Y")?> <?=htmlspecialchars($conf['sitename'])?></div>
</div>

<script>
function closeWin(){
  var ua=navigator.userAgent;
  if(ua.indexOf('AlipayClient')>-1 && window.AlipayJSBridge){AlipayJSBridge.call('popWindow');}
  else if(ua.indexOf('MicroMessenger')>-1 && typeof WeixinJSBridge!=='undefined'){WeixinJSBridge.call('closeWindow');}
  else{window.opener=null;window.close();if(window.location.href.indexOf('close=1')===-1)window.location.href=window.location.href+'&close=1';}
}
document.getElementById('Close').addEventListener('click',closeWin);
document.body.addEventListener('touchmove',function(e){e.preventDefault();},{passive:false});
</script>

<?php echo '</body></html>'; ?>
