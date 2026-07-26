<?php
if(!defined('IN_PLUGIN'))exit();
define('IN_EPAY', true);
include_once ROOT.'includes/ep_ui.php';
$payChannel = $channel;
$channel = 'alipay';
$title = '收银台';
ep_pay_head($title, $channel);
?>
<div style="text-align:center;padding:40px 24px">
  <div style="width:48px;height:48px;border-radius:50%;background:var(--ep-channel-bg,#E8F2FF);color:var(--ep-channel,#1677FF);display:inline-flex;align-items:center;justify-content:center;margin-bottom:16px">
    <?=ep_icon('credit-card',24)?>
  </div>
  <div style="font-size:14px;color:var(--ep-gray-500)">正在跳转到支付宝…</div>
</div>
<script src="//gw.alipayobjects.com/as/g/h5-lib/alipayjsapi/3.1.1/alipayjsapi.min.js"></script>
<script>
    var userId = <?=json_encode($payChannel['appmchid'])?>;
    var money = <?=json_encode((string)$order['realmoney'])?>;
    var remark = "请勿添加备注-<?php echo $order['trade_no']?>";

    function returnApp() {
        AlipayJSBridge.call("exitApp")
    }

    function ready(a) {
        window.AlipayJSBridge ? a && a() : document.addEventListener("AlipayJSBridgeReady", a, !1)
    }

    ready(function () {
        try {
            var a = {
                actionType: "scan",
                u: userId,
                a: money,
                m: remark,
                biz_data: {
                    s: "money",
                    u: userId,
                    a: money,
                    m: remark
                }
            }
        } catch (b) {
            returnApp()
        }
        AlipayJSBridge.call("startApp", {
            appId: "20000123",
            param: a
        }, function (a) { })
    });
    document.addEventListener("resume", function (a) {
        returnApp()
    });
</script>
<?php echo '</body></html>'; ?>
