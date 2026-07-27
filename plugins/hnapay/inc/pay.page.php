<?php
if(!defined('IN_PLUGIN'))exit();
define('IN_EPAY', true);
include_once ROOT.'includes/ep_ui.php';
$channel = 'bank';
$title = '快捷支付';
ep_pay_head($title, $channel);
?>
<script src="<?php echo $cdnpublic ?>jquery/3.4.1/jquery.min.js"></script>
<script src="<?php echo $cdnpublic ?>bootstrap-datepicker/1.10.0/js/bootstrap-datepicker.min.js"></script>
<script src="<?php echo $cdnpublic ?>bootstrap-datepicker/1.10.0/locales/bootstrap-datepicker.zh-CN.min.js"></script>
<script src="<?php echo $cdnpublic ?>jquery-cookie/1.4.1/jquery.cookie.min.js"></script>
<script src="<?php echo $cdnpublic ?>layer/3.1.1/layer.js"></script>
<link href="/assets/css/datepicker.css" rel="stylesheet">

<div class="ep-pay-card ep-pay-card-form" style="width:min(560px,100%);max-width:560px;text-align:left;padding:0">
  <div style="padding:20px 24px;border-bottom:1px solid var(--ep-gray-100);display:flex;align-items:center;gap:8px">
    <span style="color:var(--ep-brand-500)"><?=ep_icon('credit-card',20)?></span>
    <span style="font-size:16px;font-weight:600;color:var(--ep-gray-800)">快捷支付</span>
  </div>

  <form id="paymentForm" style="padding:20px 24px">
    <div class="ep-card" style="margin-bottom:20px;padding:12px 16px;border-left:3px solid var(--ep-brand-500);border-radius:0 8px 8px 0">
      <div style="display:flex;gap:24px;flex-wrap:wrap">
        <div>
          <div style="font-size:12px;color:var(--ep-gray-400)">订单号</div>
          <div style="font-size:13px;color:var(--ep-gray-700);font-family:var(--ep-font-mono)"><?php echo TRADE_NO ?></div>
        </div>
        <div>
          <div style="font-size:12px;color:var(--ep-gray-400)">订单金额</div>
          <div style="font-size:15px;font-weight:600;color:var(--ep-gray-900)">¥<?php echo $order['realmoney'] ?></div>
        </div>
      </div>
    </div>

    <div style="font-size:14px;font-weight:500;color:var(--ep-gray-700);margin-bottom:16px">请输入快捷支付绑卡信息</div>

    <div style="margin-bottom:16px">
      <label style="display:block;font-size:13px;color:var(--ep-gray-600);margin-bottom:6px">银行卡号</label>
      <input class="ep-input" style="width:100%;height:44px;font-size:16px" id="card-number" placeholder="请输入银行卡号" required pattern="\d{15,19}" maxlength="19">
      <div id="bank-info-display" style="display:none;margin-top:8px;align-items:center;gap:8px">
        <span id="bank-name-display" style="font-weight:500;color:var(--ep-brand-500)">-</span>
        <span id="card-type-display" style="background:var(--ep-info-50,#F0F9FF);color:var(--ep-info-500,#0EA5E9);padding:2px 8px;border-radius:4px;font-size:12px">-</span>
      </div>
    </div>

    <div style="margin-bottom:16px">
      <label style="display:block;font-size:13px;color:var(--ep-gray-600);margin-bottom:6px">手机号码</label>
      <input class="ep-input" style="width:100%;height:44px;font-size:16px" id="phone-number" placeholder="银行卡绑定的手机号码" required pattern="1[3-9]\d{9}" maxlength="11">
    </div>

    <div style="display:flex;gap:12px;margin-bottom:16px">
      <div style="flex:1">
        <label style="display:block;font-size:13px;color:var(--ep-gray-600);margin-bottom:6px">持卡人姓名</label>
        <input class="ep-input" style="width:100%;height:44px;font-size:16px" id="cardholder-name" placeholder="请输入姓名" required pattern="[一-龥]{2,20}" maxlength="20">
      </div>
      <div style="flex:1">
        <label style="display:block;font-size:13px;color:var(--ep-gray-600);margin-bottom:6px">身份证号码</label>
        <input class="ep-input" style="width:100%;height:44px;font-size:16px" id="id-number" placeholder="请输入身份证号" required pattern="\d{17}[\dXx]" maxlength="18">
      </div>
    </div>

    <div id="credit-card-fields" style="display:none;margin-bottom:16px">
      <label style="display:block;font-size:13px;color:var(--ep-gray-600);margin-bottom:6px">信用卡信息</label>
      <div style="display:flex;gap:12px">
        <input class="ep-input" style="flex:1;height:44px;font-size:16px" id="expiry-date" placeholder="到期时间" required>
        <input class="ep-input" style="flex:1;height:44px;font-size:16px" id="cvv" placeholder="CVV码" maxlength="3" required pattern="\d{3}">
      </div>
    </div>

    <div style="margin-bottom:20px">
      <label style="display:block;font-size:13px;color:var(--ep-gray-600);margin-bottom:6px">短信验证码</label>
      <div style="display:flex;gap:12px">
        <input class="ep-input" style="flex:1;height:44px;font-size:16px" id="bind-sms-code" placeholder="请输入短信验证码" required>
        <button class="ep-btn ep-btn-secondary" style="height:44px;white-space:nowrap" id="get-sms-code">获取验证码</button>
      </div>
    </div>

    <div style="margin-bottom:20px;display:flex;align-items:center;gap:8px">
      <input type="checkbox" id="agree-protocol" required style="width:16px;height:16px">
      <label for="agree-protocol" style="font-size:13px;color:var(--ep-gray-600)">我已阅读并同意<a href="/pay/agreement/<?php echo TRADE_NO?>/" target="_blank" style="color:var(--ep-brand-500)">《新生支付服务协议》</a></label>
    </div>

    <button class="ep-btn ep-btn-primary" style="width:100%;height:48px;font-size:16px" id="confirm-payment" disabled>确认支付</button>
  </form>
</div>

<style>
body.ep-app{align-items:flex-start!important;overflow-y:auto}
.ep-pay-card-form{margin:auto 0}
.ep-pay-card-form .ep-input.is-invalid{border-color:var(--ep-danger-500);box-shadow:0 0 0 3px var(--ep-danger-50)}
.ep-pay-card-form .ep-btn:disabled{opacity:.5;cursor:not-allowed}
@media(max-width:520px){
  .ep-pay-card-form form>div[style*="display:flex"]{flex-direction:column}
  .ep-pay-card-form{margin:0}
}
</style>

<script>
$(document).ready(function() {
    let smsToken = null;
    let bankCardType = null;

    $('input').on('input', function() { $(this).removeClass('is-invalid'); });
    $('#phone-number').on('input', function() { this.value = this.value.replace(/\D/g, ''); });
    $('#card-number').on('input', function() { this.value = this.value.replace(/\D/g, ''); });

    $('#expiry-date').datepicker({format:'mm/yy',startView:"months",minViewMode:"months",autoclose:true,clearBtn:true,language:'zh-CN'});

    $('#card-number').on('change', function() {
        let cardNumber = $(this).val().replace(/\s/g, '');
        if (/^\d{16,19}$/.test(cardNumber)) {
            var ii = layer.load(2, {shade:[0.1,'#fff']});
            $.ajax({url:'?',method:'POST',data:{action:'query_card',cardno:cardNumber},dataType:'json',
                success: function(r) {
                    layer.close(ii);
                    if (r.code == 0) {
                        $('#bank-name-display').text(r.data.bank_name);
                        var t = {'DC':'储蓄卡','CC':'信用卡','SCC':'准贷记卡','PC':'预付费卡'};
                        $('#card-type-display').text(t[r.data.card_type]);
                        $('#bank-info-display').css('display','flex');
                        bankCardType = r.data.card_type == 'CC' ? 'credit' : 'debit';
                        if(r.data.card_type == 'CC') $('#credit-card-fields').show(); else $('#credit-card-fields').hide();
                    } else { $('#bank-info-display').hide(); layer.alert(r.msg, {icon:2}); }
                },
                error: function() { layer.close(ii); alert('网络请求异常，请检查网络连接'); }
            });
        } else { $('#bank-info-display').hide(); $('#credit-card-fields').hide(); }
    });

    $('#get-sms-code').click(function(e){
        e.preventDefault();
        let isValid = true;
        $('#card-number,#phone-number,#cardholder-name,#id-number,#credit-card-fields input:visible').each(function() {
            const $input = $(this);
            const value = $input.val();
            const pattern = $input.attr('pattern');
            if (!value || pattern && !new RegExp('^(?:'+pattern+')$').test(value)) {
                $input.addClass('is-invalid');
                isValid = false;
            }
        });
        if (!bankCardType) {
            $('#card-number').addClass('is-invalid');
            isValid = false;
        }
        if (!isValid) return;
        const $btn = $(this).prop('disabled', true);
        var ii = layer.load(2, {shade:[0.1,'#fff']});
        $.ajax({url:'?',method:'POST',data:{action:'request',phone:$('#phone-number').val(),cardno:$('#card-number').val(),cardtype:bankCardType,name:$('#cardholder-name').val(),idcard:$('#id-number').val(),expiry:$('#expiry-date').val(),cvv:$('#cvv').val()},dataType:'json',
            success: function(r) {
                layer.close(ii); $btn.prop('disabled', false);
                if (r.code == 0) { smsToken = r.token; layer.msg('短信验证码已发送，请注意查收', {icon:1,time:1500}); $('#confirm-payment').prop('disabled', false); startBindSmsCountdown(); }
                else layer.alert(r.msg, {icon:2});
            },
            error: function() { layer.close(ii); $btn.prop('disabled', false); alert('网络请求异常，请检查网络连接'); }
        });
    });

    $('#confirm-payment').click(function(e){
        e.preventDefault();
        if (!smsToken) { layer.alert('请先获取短信验证码'); return; }
        const smsCode = $('#bind-sms-code').val();
        if (smsCode == '') { layer.alert('请输入短信验证码'); return; }
        if(!$('#agree-protocol').is(':checked')) { layer.alert('请阅读并同意支付服务协议'); return; }
        const $btn = $(this).prop('disabled', true);
        var ii = layer.load(2, {shade:[0.1,'#fff']});
        $.ajax({url:'?',method:'POST',data:{action:'confirm',phone:$('#phone-number').val(),token:smsToken,smscode:smsCode},dataType:'json',
            success: function(r) {
                layer.close(ii); $btn.prop('disabled', false);
                if (r.code == 0) {
                    $.cookie('fastpay_phone', $('#phone-number').val(), {expires:365,path:'/'});
                    $.cookie('fastpay_cardno', $('#card-number').val(), {expires:365,path:'/'});
                    $.cookie('fastpay_name', $('#cardholder-name').val(), {expires:365,path:'/'});
                    $.cookie('fastpay_idcard', $('#id-number').val(), {expires:365,path:'/'});
                    layer.msg('支付成功，正在跳转中...', {icon:16,shade:0.1,time:15000});
                    setTimeout(function(){ window.location.href=r.backurl; }, 1000);
                } else layer.alert(r.msg, {icon:2});
            },
            error: function() { layer.close(ii); $btn.prop('disabled', false); alert('网络请求异常，请检查网络连接'); }
        });
    });

    function startBindSmsCountdown() {
        let timeLeft = 60; const button = $('#get-sms-code'); const originalHtml = button.html();
        button.prop('disabled', true);
        const countdown = setInterval(function() {
            timeLeft--; button.html('重新发送('+timeLeft+')');
            if (timeLeft <= 0) { clearInterval(countdown); button.html(originalHtml); button.prop('disabled', false); }
        }, 1000);
    }

    if($.cookie('fastpay_phone') && $.cookie('fastpay_cardno')) {
        $('#phone-number').val($.cookie('fastpay_phone'));
        $('#card-number').val($.cookie('fastpay_cardno'));
        $('#cardholder-name').val($.cookie('fastpay_name'));
        $('#id-number').val($.cookie('fastpay_idcard'));
        $('#card-number').change();
    }

    $.ajax({type:"GET",dataType:"json",url:"/getshop.php",data:{type:"bank",trade_no:"<?php echo $order['trade_no']?>"},
        success: function(data) { if (data.code == 1) { layer.msg('支付成功，正在跳转中...', {icon:16,shade:0.1,time:15000}); setTimeout(function(){ window.location.href=data.backurl; }, 1000); } }
    });
});
</script>
<?php echo '</body></html>'; ?>
