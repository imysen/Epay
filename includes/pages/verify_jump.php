<?php
if (!defined('IN_CRONLITE')) exit();
$x = new \lib\hieroglyphy();
$key_enc = $x->hieroglyphyString($key);

$html = '<form id="dopay" action="'.htmlspecialchars($siteurl.'submit.php', ENT_QUOTES, 'UTF-8').'" method="post">';
foreach ($query_arr as $k=>$v) {
    $html.= '<input type="hidden" name="'.htmlspecialchars($k, ENT_QUOTES, 'UTF-8').'" value="'.htmlspecialchars($v, ENT_QUOTES, 'UTF-8').'"/>';
}
$html .= '<input type="submit" value="Loading" style="display:none"></form>';

define('IN_EPAY', true);
include_once ROOT.'includes/ep_ui.php';
$channel = 'wxpay';
$title = '支付环境安全验证';
ep_pay_head($title, $channel);
?>

<div class="ep-pay-card" style="width:min(420px,100%);text-align:center;padding:32px 24px">
  <div style="width:56px;height:56px;border-radius:50%;background:var(--ep-warning-50);color:var(--ep-warning-500);display:inline-flex;align-items:center;justify-content:center;margin-bottom:16px">
    <?=ep_icon('shield',28)?>
  </div>
  <div style="font-size:16px;font-weight:600;color:var(--ep-gray-900);margin-bottom:6px">正在进行支付安全验证</div>
  <div style="font-size:13px;color:var(--ep-gray-500);line-height:1.6">请稍候…</div>
  <div style="width:28px;height:28px;border:3px solid var(--ep-gray-200);border-top-color:var(--ep-brand-500);border-radius:50%;animation:ep-spin .8s linear infinite;margin:20px auto 0"></div>
</div>
<style>@keyframes ep-spin{to{transform:rotate(360deg)}}</style>

<?php echo $html?>
<script>
    var key = <?php echo $key_enc;?>;
    window.onload=function(){
        var elem = document.getElementById("dopay");
        var input=document.createElement("input");
        input.type="hidden";
        input.name="__defend";
        input.value=key;
        elem.appendChild(input);
        elem.submit();
    }
</script>

<?php echo '</body></html>'; ?>
