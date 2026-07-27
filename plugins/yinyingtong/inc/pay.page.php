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
        <div style="min-width:0;flex:1">
          <div style="font-size:12px;color:var(--ep-gray-400)">订单号</div>
          <div style="font-size:13px;color:var(--ep-gray-700);font-family:var(--ep-font-mono);overflow-wrap:anywhere"><?php echo TRADE_NO ?></div>
        </div>
        <div>
          <div style="font-size:12px;color:var(--ep-gray-400)">订单金额</div>
          <div style="font-size:15px;font-weight:600;color:var(--ep-gray-900)">¥<?php echo $order['realmoney'] ?></div>
        </div>
      </div>
    </div>

    <div style="font-size:14px;font-weight:500;color:var(--ep-gray-700);margin-bottom:16px">请输入快捷支付绑卡信息</div>

    <div style="margin-bottom:16px">
      <label class="fastpay-label" for="card-number">银行卡号</label>
      <input class="ep-input fastpay-input" id="card-number" inputmode="numeric" autocomplete="cc-number" placeholder="请输入银行卡号" required pattern="\d{15,19}" maxlength="19">
      <div class="fastpay-error">请输入有效的银行卡号</div>
      <div id="bank-info-display" style="display:none;margin-top:8px;align-items:center;gap:8px">
        <span id="bank-name-display" style="font-weight:500;color:var(--ep-brand-500)">-</span>
        <span id="card-type-display" style="background:var(--ep-info-50,#F0F9FF);color:var(--ep-info-500,#0EA5E9);padding:2px 8px;border-radius:4px;font-size:12px">-</span>
      </div>
    </div>

    <div style="margin-bottom:16px">
      <label class="fastpay-label" for="phone-number">手机号码</label>
      <input class="ep-input fastpay-input" type="tel" inputmode="numeric" autocomplete="tel" id="phone-number" placeholder="银行卡绑定的手机号码" required pattern="1[3-9]\d{9}" maxlength="11">
      <div class="fastpay-error">请输入有效的手机号码</div>
    </div>

    <div class="fastpay-grid">
      <div>
        <label class="fastpay-label" for="cardholder-name">持卡人姓名</label>
        <input class="ep-input fastpay-input" autocomplete="name" id="cardholder-name" placeholder="请输入姓名" required pattern="[一-龥]{2,20}" maxlength="20">
        <div class="fastpay-error">请输入正确的姓名</div>
      </div>
      <div>
        <label class="fastpay-label" for="id-number">身份证号码</label>
        <input class="ep-input fastpay-input" id="id-number" placeholder="请输入身份证号" required pattern="\d{17}[\dXx]" maxlength="18">
        <div class="fastpay-error">请输入有效的身份证号码</div>
      </div>
    </div>

    <div id="credit-card-fields" style="display:none;margin-bottom:16px">
      <label class="fastpay-label">信用卡信息</label>
      <div class="fastpay-grid" style="margin-bottom:0">
        <div>
          <input class="ep-input fastpay-input" id="expiry-date" autocomplete="cc-exp" placeholder="到期时间（月/年）">
          <div class="fastpay-error">请选择有效期</div>
        </div>
        <div>
          <input class="ep-input fastpay-input" id="cvv" inputmode="numeric" autocomplete="cc-csc" placeholder="CVV码" maxlength="3" pattern="\d{3}">
          <div class="fastpay-error">请输入有效的CVV码</div>
        </div>
      </div>
    </div>

    <div style="margin-bottom:20px">
      <label class="fastpay-label" for="bind-sms-code">短信验证码</label>
      <div style="display:flex;gap:12px">
        <input class="ep-input fastpay-input" style="flex:1;min-width:0" id="bind-sms-code" inputmode="numeric" autocomplete="one-time-code" placeholder="请输入短信验证码" required>
        <button type="button" class="ep-btn ep-btn-secondary" style="height:44px;white-space:nowrap" id="get-sms-code">获取验证码</button>
      </div>
    </div>

    <button type="button" class="ep-btn ep-btn-primary" style="width:100%;height:48px;font-size:16px" id="confirm-payment" disabled>确认支付</button>
  </form>
</div>

<style>
.fastpay-label{display:block;font-size:13px;color:var(--ep-gray-600);margin-bottom:6px}
.fastpay-input{width:100%;height:44px;font-size:16px}
.fastpay-grid{display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:16px}
.fastpay-error{display:none;color:var(--ep-danger-500);font-size:12px;margin-top:5px}
.fastpay-input.is-invalid{border-color:var(--ep-danger-500);box-shadow:0 0 0 3px var(--ep-danger-50)}
.fastpay-input.is-invalid+.fastpay-error{display:block}
.ep-btn:disabled{opacity:.5;cursor:not-allowed}
body.ep-app{align-items:flex-start!important;overflow-y:auto}
.ep-pay-card-form{margin:auto 0}
@media(max-width:520px){.fastpay-grid{grid-template-columns:1fr}.ep-pay-card-form{margin:0}}
</style>

<script>
$(document).ready(function() {
    let smsToken = null;
    let bankCardType = null;

    function showError(message) {
        if (window.layer) layer.alert(message, {icon:2});
        else alert(message);
    }
    function loading() {
        return window.layer ? layer.load(2, {shade:[0.1,'#fff']}) : null;
    }
    function closeLoading(index) {
        if (window.layer && index !== null) layer.close(index);
    }
    function validateInput($input) {
        if (!$input.is(':visible') || $input.attr('type') === 'checkbox') return true;
        const value = $input.val();
        const pattern = $input.attr('pattern');
        const valid = !$input.prop('required') && value === '' || value !== '' && (!pattern || new RegExp('^(?:'+pattern+')$').test(value));
        $input.toggleClass('is-invalid', !valid);
        return valid;
    }
    function validateCardFields() {
        let valid = true;
        $('#card-number,#phone-number,#cardholder-name,#id-number,#credit-card-fields input:visible').each(function(){ if(!validateInput($(this))) valid=false; });
        if (!bankCardType) { $('#card-number').addClass('is-invalid'); valid=false; }
        return valid;
    }

    $('input').on('input change', function() { $(this).removeClass('is-invalid'); });
    $('#phone-number,#card-number,#cvv,#bind-sms-code').on('input', function() { this.value = this.value.replace(/\D/g, ''); });
    $('#id-number').on('input', function() { this.value = this.value.replace(/[^\dXx]/g, '').toUpperCase(); });

    $('#expiry-date').datepicker({format:'mm/yy',startView:'months',minViewMode:'months',autoclose:true,clearBtn:true,language:'zh-CN'});

    $('#card-number').on('change', function() {
        const cardNumber = $(this).val().replace(/\s/g, '');
        bankCardType = null;
        if (!/^\d{16,19}$/.test(cardNumber)) {
            $('#bank-info-display,#credit-card-fields').hide();
            return;
        }
        const ii = loading();
        $.ajax({url:'?',method:'POST',data:{action:'query_card',cardno:cardNumber},dataType:'json',
            success:function(response){
                closeLoading(ii);
                if(response.code==0){
                    const cardTypeNames={'DC':'储蓄卡','CC':'信用卡','SCC':'准贷记卡','PC':'预付费卡'};
                    $('#bank-name-display').text(response.data.bank_name);
                    $('#card-type-display').text(cardTypeNames[response.data.card_type] || '银行卡');
                    $('#bank-info-display').css('display','flex');
                    bankCardType=response.data.card_type==='CC'?'credit':'debit';
                    $('#credit-card-fields').toggle(response.data.card_type==='CC');
                    $('#expiry-date,#cvv').prop('required',response.data.card_type==='CC');
                }else{
                    $('#bank-info-display,#credit-card-fields').hide();
                    showError(response.msg);
                }
            },
            error:function(){closeLoading(ii);showError('网络请求异常，请检查网络连接');}
        });
    });

    $('#get-sms-code').on('click', function() {
        if(!validateCardFields()) return;
        const $btn=$(this).prop('disabled',true);
        const ii=loading();
        $.ajax({url:'?',method:'POST',data:{action:'request',phone:$('#phone-number').val(),cardno:$('#card-number').val(),cardtype:bankCardType,name:$('#cardholder-name').val(),idcard:$('#id-number').val(),expiry:$('#expiry-date').val(),cvv:$('#cvv').val()},dataType:'json',
            success:function(response){
                closeLoading(ii);
                if(response.code==0){
                    smsToken=response.token;
                    if(window.layer) layer.msg('短信验证码已发送，请注意查收',{icon:1,time:1500});
                    $('#confirm-payment').prop('disabled',false);
                    startSmsCountdown($btn);
                }else{$btn.prop('disabled',false);showError(response.msg);}
            },
            error:function(){closeLoading(ii);$btn.prop('disabled',false);showError('网络请求异常，请检查网络连接');}
        });
    });

    $('#confirm-payment').on('click', function() {
        if(!smsToken){showError('请先获取短信验证码');return;}
        const smsCode=$('#bind-sms-code').val();
        if(!smsCode){$('#bind-sms-code').addClass('is-invalid');showError('请输入短信验证码');return;}
        const $btn=$(this).prop('disabled',true);
        const ii=loading();
        $.ajax({url:'?',method:'POST',data:{action:'confirm',phone:$('#phone-number').val(),cardno:$('#card-number').val(),cardtype:bankCardType,name:$('#cardholder-name').val(),idcard:$('#id-number').val(),token:smsToken,smscode:smsCode},dataType:'json',
            success:function(response){
                closeLoading(ii);
                if(response.code==0){
                    $.cookie('fastpay_phone',$('#phone-number').val(),{expires:365,path:'/'});
                    $.cookie('fastpay_cardno',$('#card-number').val(),{expires:365,path:'/'});
                    $.cookie('fastpay_name',$('#cardholder-name').val(),{expires:365,path:'/'});
                    $.cookie('fastpay_idcard',$('#id-number').val(),{expires:365,path:'/'});
                    if(window.layer) layer.msg('支付成功，正在跳转中...',{icon:16,shade:0.1,time:15000});
                    setTimeout(function(){window.location.href=response.backurl;},1000);
                }else{$btn.prop('disabled',false);showError(response.msg);}
            },
            error:function(){closeLoading(ii);$btn.prop('disabled',false);showError('网络请求异常，请检查网络连接');}
        });
    });

    function startSmsCountdown(button){
        let remaining=60;
        const original=button.text();
        button.prop('disabled',true).text('重新发送('+remaining+')');
        const timer=setInterval(function(){
            remaining--;
            if(remaining<=0){clearInterval(timer);button.text(original).prop('disabled',false);}
            else button.text('重新发送('+remaining+')');
        },1000);
    }

    if($.cookie('fastpay_phone') && $.cookie('fastpay_cardno')){
        $('#phone-number').val($.cookie('fastpay_phone'));
        $('#card-number').val($.cookie('fastpay_cardno'));
        $('#cardholder-name').val($.cookie('fastpay_name'));
        $('#id-number').val($.cookie('fastpay_idcard'));
        $('#card-number').trigger('change');
    }

    $.ajax({type:'GET',dataType:'json',url:'/getshop.php',data:{type:'bank',trade_no:'<?php echo $order['trade_no']?>'},success:function(data){
        if(data.code==1){
            if(window.layer) layer.msg('支付成功，正在跳转中...',{icon:16,shade:0.1,time:15000});
            setTimeout(function(){window.location.href=data.backurl;},1000);
        }
    }});
});
</script>
<?php echo '</body></html>'; ?>
