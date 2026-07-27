<?php
if (!defined('IN_CRONLITE')) exit();

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
  <div style="font-size:16px;font-weight:600;color:var(--ep-gray-900);margin-bottom:6px">支付环境安全验证</div>
  <div style="font-size:13px;color:var(--ep-gray-500);line-height:1.6">系统检测到异常流量，请完成<strong style="color:var(--ep-gray-700);font-weight:600">滑动验证</strong>后继续支付</div>
  <div id="geetest-area" style="margin-top:20px;border:1px dashed var(--ep-gray-300);border-radius:6px;padding:20px 16px;min-height:44px;display:flex;align-items:center;justify-content:center;color:var(--ep-gray-400);font-size:12px">正在加载验证…</div>
</div>

<?php echo $html?>
<script src="<?php echo $cdnpublic?>jquery/1.12.4/jquery.min.js"></script>
<script src="https://static.geetest.com/v4/gt4.js"></script>
<script>
window.appendChildOrg = Element.prototype.appendChild;
Element.prototype.appendChild = function() {
    if(arguments[0].tagName == 'SCRIPT'){
        arguments[0].setAttribute('referrerpolicy', 'no-referrer');
    }
    return window.appendChildOrg.apply(this, arguments);
};
initGeetest4({
    captchaId: "54088bb07d2df3c46b79f80300b0abbe",
    product: 'bind',
    protocol: 'https://',
    riskType: 'slide',
    hideSuccess: true
},function (captcha) {
    captcha.onReady(function(){
        captcha.showCaptcha();
        document.getElementById('geetest-area').style.display='none';
    }).onSuccess(function(){
        var result = captcha.getValidate();
        result.pid = <?php echo json_encode((string)$query_arr['pid'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)?>;
        result.trade_no = <?php echo json_encode((string)$query_arr['out_trade_no'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)?>;
        $.ajax({
            url: 'getshop.php?act=captcha_verify',
            type: 'post',
            dataType: 'json',
            data: result,
            cache: false,
            success: function (data) {
                if(data.code == 0){
                    var elem = document.getElementById("dopay");
                    var input = document.createElement("input");
                    input.type="hidden";
                    input.name="__defend";
                    input.value=data.key;
                    elem.appendChild(input);
                    elem.submit();
                }else{
                    alert(data.msg);
                }
            },
            error: function () {
                alert('服务器错误');
            }
        });
    }).onError(function(){
        alert('验证码加载失败，请刷新页面重试');
    })
});
</script>

<?php echo '</body></html>'; ?>
