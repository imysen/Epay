<?php
$is_defend = true;
include("./inc.php");
if(isset($_GET['ucode'])){
    $code=strtoupper(trim($_GET['ucode']));
    if(!preg_match('/^[A-Z0-9]{1,32}$/',$code)) showerror('参数错误');
    $uid = $DB->findColumn('onecode', 'uid', ['code' => $code]);
    if(!$uid) showerror('当前码牌尚未绑定收款商户，请联系管理员');
}elseif(isset($_GET['merchant'])){
    $merchant=trim($_GET['merchant']);
    $uid = authcode($merchant, 'DECODE', SYS_KEY);
    if(!$uid || !is_numeric($uid))showerror('参数错误');
}elseif(isset($_SESSION['paypage_uid'])){
    $uid = intval($_SESSION['paypage_uid']);
}else{
    showerror('参数不完整');
}
$userrow = $DB->getRow("SELECT `uid`,`gid`,`money`,`mode`,`pay`,`cert`,`status`,`username`,`channelinfo`,`qq`,`codename`,`deposit` FROM `pre_user` WHERE `uid`='{$uid}' LIMIT 1");
if(!$userrow || $userrow['status']==0 || $userrow['pay']==0)showerror('当前商户不存在或已被封禁');
if($userrow['pay']==2 && $conf['user_review']==1)showerror('商户没通过审核，请联系官方客服进行审核');
$groupconfig = getGroupConfig($userrow['gid']);
$conf = array_merge($conf, $groupconfig);
if($conf['cert_force']==1 && $userrow['cert']==0){
    showerror('当前商户未完成实名认证，无法收款');
}
if($conf['forceqq']==1 && empty($userrow['qq'])){
    showerror('当前商户未填写联系QQ，无法收款');
}
if($conf['user_deposit']==1 && $conf['user_deposit_min'] > 0 && $conf['user_deposit_min'] > $userrow['deposit']){
    showerror('商户保证金不足，请前往支付平台充值保证金后再发起支付');
}
if(!empty($conf['pay_region_block'])){
    $ipregion = get_ip_region($clientip);
    if($ipregion){
        foreach(explode('|',$conf['pay_region_block']) as $rows){
            if(strpos($ipregion, $rows) !== false){
                showerror('您所在的地区无法发起支付，请更换其他支付方式');
            }
        }
    }
}

$_SESSION['paypage_uid'] = $uid;

$direct = '0';
$checktype = check_paytype();
$type = isset($_GET['type'])?trim($_GET['type']):$checktype;
if($type){
    if((isset($_GET['code']) || isset($_GET['auth_code']) || isset($_GET['userAuthCode'])) && $_SESSION['paypage_channel']){
        $submitData = \lib\Channel::info($_SESSION['paypage_channel'], $userrow['gid']);
        if($_SESSION['paypage_subchannel'] > 0) $submitData['subchannel'] = $_SESSION['paypage_subchannel'];
    }else{
        $submitData = \lib\Channel::submit($type, $uid, $userrow['gid']);
        $_SESSION['paypage_subchannel'] = $submitData['subchannel'];
    }
    $_SESSION['paypage_typeid'] = $submitData['typeid'];
    $_SESSION['paypage_channel'] = $submitData['channel'];
    $_SESSION['paypage_rate'] = $submitData['rate'];
    $_SESSION['paypage_paymax'] = $submitData['paymax'];
    $_SESSION['paypage_paymin'] = $submitData['paymin'];
    $_SESSION['paypage_mode'] = $submitData['mode'];

    $channel = $submitData['subchannel'] > 0 ? \lib\Channel::getSub($submitData['subchannel']) : \lib\Channel::get($submitData['channel'], $userrow['channelinfo']);
    if(!$channel)showerror('支付通道不存在');

    $apptype = explode(',',$channel['apptype']);
    if($checktype == 'alipay' && $type == 'alipay' && (
        ($submitData['plugin']=='alipay' || $submitData['plugin']=='alipaysl' || $submitData['plugin']=='alipayd') && in_array('4',$apptype)
        || $submitData['plugin']=='lakala' && in_array('2',$apptype)
        || $submitData['plugin']=='huifu' && in_array('4',$apptype)
        || $submitData['plugin']=='xsy' && in_array('2',$apptype)
        || $submitData['plugin']=='baofu' && in_array('2',$apptype)
        || $submitData['plugin']=='adapay' && in_array('2',$apptype)
        || $submitData['plugin']=='allinpay' && in_array('2',$apptype)
        || $submitData['plugin']=='dinpay' && in_array('3',$apptype)
        || $submitData['plugin']=='duolabao' && in_array('2',$apptype)
        || $submitData['plugin']=='fubei'
        || $submitData['plugin']=='fuiou2' && in_array('2',$apptype)
        || $submitData['plugin']=='haipay' && in_array('2',$apptype)
        || $submitData['plugin']=='hlpay' && in_array('2',$apptype)
        || $submitData['plugin']=='huishouqian' && in_array('2',$apptype)
        || $submitData['plugin']=='jindd' && in_array('2',$apptype)
        || $submitData['plugin']=='jlpay' && in_array('2',$apptype)
        || $submitData['plugin']=='joinpay' && in_array('3',$apptype)
        || $submitData['plugin']=='leshua' && in_array('2',$apptype)
        || $submitData['plugin']=='llianpay' && in_array('2',$apptype)
        || $submitData['plugin']=='sandpay' && in_array('2',$apptype)
        || $submitData['plugin']=='shengpay' && in_array('4',$apptype)
        || $submitData['plugin']=='suixingpay' && in_array('2',$apptype)
        || $submitData['plugin']=='unionpay' && in_array('2',$apptype)
        || $submitData['plugin']=='ysepay' && in_array('3',$apptype)
        || $submitData['plugin']=='yseqt' && in_array('2',$apptype)
        || $submitData['plugin']=='yeepay' && in_array('2',$apptype)
        )){
        if($conf['alipay_web_login_all'] == 1 && $conf['alipay_web_login'] > 0 || $submitData['plugin']!='alipay' && $submitData['plugin']!='alipaysl' && $submitData['plugin']!='alipayd'){
            if(!$conf['alipay_web_login']) showerror('未配置支付宝网页快捷登录通道');
            $channel = \lib\Channel::get($conf['alipay_web_login']);
        }
        $openId = alipayOpenId($channel);
        $direct = '1';
    }elseif($checktype == 'wxpay' && $type == 'wxpay' && $channel['appwxmp']>0 && (
        ($submitData['plugin']=='wxpay' || $submitData['plugin']=='wxpaysl' || $submitData['plugin']=='wxpayn' || $submitData['plugin']=='wxpaynp') && in_array('2',$apptype)
        || $submitData['plugin']=='lakala'
        || $submitData['plugin']=='huifu' && in_array('1',$apptype)
        || $submitData['plugin']=='xsy'
        || $submitData['plugin']=='baofu' && in_array('2',$apptype)
        || $submitData['plugin']=='adapay' && in_array('1',$apptype)
        || $submitData['plugin']=='allinpay' && in_array('2',$apptype)
        || $submitData['plugin']=='dinpay' && in_array('3',$apptype)
        || $submitData['plugin']=='duolabao' && in_array('2',$apptype)
        || $submitData['plugin']=='fubei'
        || $submitData['plugin']=='fuiou2' && in_array('2',$apptype)
        || $submitData['plugin']=='haipay'
        || $submitData['plugin']=='hlpay' && in_array('2',$apptype)
        || $submitData['plugin']=='huishouqian' && in_array('2',$apptype)
        || $submitData['plugin']=='jindd' && in_array('1',$apptype)
        || $submitData['plugin']=='jlpay' && in_array('2',$apptype)
        || $submitData['plugin']=='joinpay' && in_array('3',$apptype)
        || $submitData['plugin']=='leshua' && in_array('2',$apptype)
        || $submitData['plugin']=='llianpay' && in_array('2',$apptype)
        || $submitData['plugin']=='passpay' && in_array('2',$apptype)
        || $submitData['plugin']=='sandpay' && in_array('2',$apptype)
        || $submitData['plugin']=='shengpay' && in_array('1',$apptype)
        || $submitData['plugin']=='suixingpay' && in_array('2',$apptype)
        || $submitData['plugin']=='unionpay' && in_array('2',$apptype)
        || $submitData['plugin']=='ysepay' && in_array('2',$apptype)
        || $submitData['plugin']=='yseqt' && in_array('3',$apptype)
        || $submitData['plugin']=='yeepay' && in_array('2',$apptype)
        )){
        $openId = weixinOpenId($channel);
        $direct = '1';
    }elseif($checktype == 'bank' && $type == 'bank' && (
        $submitData['plugin']=='lakala' && in_array('2',$apptype)
        || $submitData['plugin']=='huifu' && in_array('4',$apptype)
        || $submitData['plugin']=='xsy' && in_array('2',$apptype)
        || $submitData['plugin']=='baofu' && in_array('2',$apptype)
        || $submitData['plugin']=='allinpay' && in_array('2',$apptype)
        || $submitData['plugin']=='jlpay' && in_array('2',$apptype)
        || $submitData['plugin']=='yseqt' && in_array('2',$apptype)
        )){
        $openId = unionpayOpenId($channel);
        $direct = '1';
    }elseif($checktype == 'qqpay' && $type == 'qqpay' && $submitData['plugin']=='qqpay' && in_array('2',$apptype)){
        $direct = '1';
    }
}

$money = isset($_GET['money'])?$_GET['money']:null;
if($money<=0 || !is_numeric($money) || !preg_match('/^[0-9.]+$/',$money))$money = null;
$codename = !empty($userrow['codename'])?$userrow['codename']:$userrow['username'];
$csrf_token = md5(mt_rand(0,999).time());
$_SESSION['paypage_token'] = $csrf_token;

define('IN_EPAY', true);
include_once ROOT.'includes/ep_ui.php';
$channel = $type ?: 'wxpay';
$title = '向'.htmlspecialchars($codename).'付款';
ep_pay_head($title, $channel);
?>

<div x-data="paypage()" x-init="init()" style="width:min(420px,100%)">
<div class="ep-pay-card" style="padding:0">
  <div style="padding:28px 24px 16px;text-align:center;border-bottom:1px solid var(--ep-gray-100)">
    <div style="width:48px;height:48px;border-radius:50%;background:var(--ep-brand-500);color:#fff;display:inline-flex;align-items:center;justify-content:center;margin-bottom:12px">
      <?=ep_icon('merchant',24)?>
    </div>
    <div style="font-size:16px;font-weight:600;color:var(--ep-gray-900)"><?=htmlspecialchars($codename)?></div>
    <div style="font-size:12px;color:var(--ep-gray-400);margin-top:4px">扫码付款</div>
  </div>

  <form name="payForm" action="dopay" method="post" style="display:none">
    <input type="hidden" name="uid" id="uid" value="<?=htmlspecialchars((string)$uid, ENT_QUOTES, 'UTF-8')?>">
    <input type="hidden" name="token" id="token" value="<?=htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8')?>">
    <input type="hidden" name="paytype" id="paytype" value="<?=htmlspecialchars((string)$type, ENT_QUOTES, 'UTF-8')?>">
    <input type="hidden" name="direct" id="direct" value="<?=htmlspecialchars($direct, ENT_QUOTES, 'UTF-8')?>">
    <input type="hidden" name="payer" id="payer" value="<?=htmlspecialchars((string)($openId ?? ''), ENT_QUOTES, 'UTF-8')?>">
    <input type="hidden" name="trade_no" id="trade_no" value="">
    <?php if($money){?><input type="hidden" name="txAmount" id="txAmount" value="<?=htmlspecialchars((string)$money, ENT_QUOTES, 'UTF-8')?>"><?php }?>
  </form>

  <div style="padding:28px 24px 20px">
    <div style="font-size:13px;color:var(--ep-gray-500);margin-bottom:8px">请输入付款金额</div>
    <div style="display:flex;align-items:baseline;gap:6px;padding:12px 0;border-bottom:1px solid var(--ep-gray-200);margin-bottom:16px">
      <span style="font-size:24px;font-weight:600;color:var(--ep-gray-900)">¥</span>
      <span x-text="amountFormat" style="font-size:32px;font-weight:600;color:var(--ep-gray-900);min-height:40px;flex:1"></span>
      <span x-show="cursorVisible" style="width:2px;height:32px;background:var(--ep-brand-500);animation:ep-cursor 1s infinite"></span>
      <button x-show="valueCur.length>0" @click="clear()" class="ep-btn ep-btn-ghost ep-btn-icon" style="color:var(--ep-gray-400)"><?=ep_icon('x',16)?></button>
    </div>

    <div style="display:flex;align-items:center;justify-content:space-between;font-size:13px;margin-bottom:24px;min-height:20px">
      <span style="color:var(--ep-gray-500)">备注：<span x-text="remark||'无'" style="color:var(--ep-gray-700)"></span></span>
      <a href="javascript:void(0)" @click="openRemark()" style="font-size:13px" x-text="remark?'编辑':'添加备注'"></a>
    </div>

    <button class="ep-btn ep-btn-primary" :class="payable?'':'saving'" @click="submit()" style="width:100%;height:48px;font-size:16px" x-text="btnText"></button>
  </div>
</div>

<!-- 备注弹层 -->
<div x-show="remarkOpen" :class="remarkOpen?'show':''" x-cloak class="ep-mask" style="align-items:flex-end;justify-content:center;padding:0 16px 40px">
  <div class="ep-card" style="width:min(360px,100%);padding:20px" @click.stop>
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px">
      <h3 style="font-size:16px;font-weight:600;color:var(--ep-gray-800)">添加备注</h3>
      <button class="ep-btn ep-btn-ghost ep-btn-icon" @click="remarkOpen=false"><?=ep_icon('x',16)?></button>
    </div>
    <textarea x-ref="remarkInput" x-model="remarkText" placeholder="请输入备注内容，30个字以内" rows="3" style="width:100%;padding:10px 12px;border:1px solid var(--ep-gray-300);border-radius:6px;font-size:14px;resize:none;outline:none;min-height:80px"></textarea>
    <div x-show="remarkText.length>30" style="color:var(--ep-danger-500);font-size:12px;margin-top:6px">备注内容不能超过30个字</div>
    <div style="display:flex;justify-content:flex-end;gap:8px;margin-top:16px">
      <button class="ep-btn ep-btn-secondary" @click="remarkOpen=false">取消</button>
      <button class="ep-btn ep-btn-primary" :class="remarkText.length>30?'saving':''" @click="saveRemark()">确定</button>
    </div>
  </div>
</div>

<style>
@keyframes ep-cursor{0%,100%{opacity:0}50%{opacity:1}}
.ep-pay-keyboard{display:grid;grid-template-columns:repeat(4,1fr);background:#fff;border-top:1px solid var(--ep-gray-200);user-select:none;-webkit-user-select:none}
.ep-pay-key{height:64px;display:flex;align-items:center;justify-content:center;font-size:24px;color:var(--ep-gray-800);border-right:1px solid var(--ep-gray-200);border-bottom:1px solid var(--ep-gray-200);background:#fff;cursor:pointer}
.ep-pay-key:nth-child(4n){border-right:none}
.ep-pay-key:active{background:var(--ep-gray-100)}
.ep-pay-key.clear{font-size:13px;color:var(--ep-gray-500)}
.ep-pay-key.pay{background:var(--ep-brand-500);color:#fff;font-size:16px;font-weight:500;grid-row:span 3;border-right:none;border-bottom:none}
.ep-pay-key.pay:active{background:var(--ep-brand-600)}
.ep-pay-key.pay.disabled{background:var(--ep-gray-300);pointer-events:none}
@media(max-width:360px){.ep-pay-key{height:56px;font-size:22px}}
</style>

<!-- 数字键盘 -->
<div class="ep-pay-keyboard" @click="press($event.target.dataset.value)">
  <div class="ep-pay-key" data-value="1">1</div>
  <div class="ep-pay-key" data-value="2">2</div>
  <div class="ep-pay-key" data-value="3">3</div>
  <div class="ep-pay-key clear" data-value="delete">删除</div>
  <div class="ep-pay-key" data-value="4">4</div>
  <div class="ep-pay-key" data-value="5">5</div>
  <div class="ep-pay-key" data-value="6">6</div>
  <div class="ep-pay-key pay" :class="payable?'':'disabled'" data-value="pay" @click.stop="submit()">确认<br>支付</div>
  <div class="ep-pay-key" data-value="7">7</div>
  <div class="ep-pay-key" data-value="8">8</div>
  <div class="ep-pay-key" data-value="9">9</div>
  <div class="ep-pay-key" data-value=".">.</div>
  <div class="ep-pay-key" data-value="0" style="grid-column:span 2">0</div>
</div>
</div>

<script>
function paypage(){
  const txAmount = document.getElementById('txAmount')?.value;
  const uid = document.getElementById('uid').value;
  const paytype = document.getElementById('paytype').value;
  const token = document.getElementById('token').value;
  const direct = document.getElementById('direct').value;
  const payer = document.getElementById('payer').value;
  return {
    valueCur:'', valueFormat:'', valueFinal:0, amountFormat:'', cursorVisible:true, submitting:false,
    remark:'', remarkOpen:false, remarkText:'',
    get payable(){return this.valueFinal>0 && !this.valueCur.match(/\.$/) && !this.submitting;},
    get btnText(){return this.submitting?'支付中…':'确认支付';},
    init(){
      setInterval(()=>{this.cursorVisible=!this.cursorVisible;},500);
      if(txAmount && parseFloat(txAmount)>0) this.submit();
    },
    press(v){
      if(!v) return;
      if(v==='pay'){this.submit();return;}
      if(v==='delete'){this.valueCur=this.valueCur.slice(0,-1);this.format();return;}
      if(v==='.' && this.valueCur==='') return;
      if(v==='.' && this.valueCur.indexOf('.')>-1) return;
      if(v==='0' && this.valueCur==='0') return;
      if(this.valueCur==='0' && v!=='.'){this.valueCur=v;this.format();return;}
      let next = this.valueCur + v;
      if(next.indexOf('.')>-1 && !/^\d{1,9}(\.\d{0,2})?$/.test(next)) return;
      if(next.indexOf('.')===-1 && next.length>9) return;
      this.valueCur=next;this.format();
    },
    format(){
      if(!this.valueCur){this.valueFormat='';this.valueFinal=0;this.amountFormat='';return;}
      const parts=this.valueCur.split('.');
      let intPart=parts[0].replace(/\B(?=(\d{3})+(?!\d))/g,',');
      this.valueFormat=intPart+(parts.length>1?'.'+parts[1]:'');
      this.valueFinal=parseFloat(this.valueCur)||0;
      this.amountFormat=this.valueFormat;
    },
    clear(){this.valueCur='';this.format();},
    openRemark(){this.remarkText=this.remark;this.remarkOpen=true;this.$nextTick(()=>this.$refs.remarkInput&&this.$refs.remarkInput.focus());},
    saveRemark(){if(this.remarkText.length>30)return;this.remark=this.remarkText.trim();this.remarkOpen=false;},
    async submit(){
      if(this.submitting || !this.payable && !(txAmount && parseFloat(txAmount)>0)) return;
      let amount = txAmount && parseFloat(txAmount)>0 ? parseFloat(txAmount) : this.valueFinal;
      if(amount<=0){epToast('info','请输入金额');return;}
      this.submitting=true;
      try{
        const d=await fetch('ajax.php',{
          method:'POST',
          headers:{'Content-Type':'application/x-www-form-urlencoded;charset=UTF-8','X-Requested-With':'XMLHttpRequest'},
          body:new URLSearchParams({money:amount,payer:payer,uid:uid,paytype:paytype,direct:direct,remark:this.remark,token:token})
        }).then(r=>r.json());
        if(d.code==0){
          document.getElementById('trade_no').value=d.trade_no;
          if(d.direct==1){
            if(paytype==='wxpay') WxpayJsPay(d.paydata);
            else if(paytype==='alipay') AlipayJsPay(d.paydata);
            else if(paytype==='qqpay') QQJsPay(d.paydata);
          }else{
            window.location.href=d.url;
          }
        }else{
          epToast('error',d.msg||'支付失败');
        }
      }catch(e){
        epToast('error','网络异常，请重新发起支付');
      }
      this.submitting=false;
    }
  };
}

function WxpayJsPay(payStr){
  const json=typeof payStr==='string'?JSON.parse(payStr):payStr;
  WeixinJSBridge.invoke('getBrandWCPayRequest',json,function(res){
    if(res.err_msg==='get_brand_wcpay_request:ok'){
      window.location.href='./success.php?trade_no='+document.getElementById('trade_no').value;
    }
  });
}
function AlipayJsPay(payStr){
  if(window.AlipayJSBridge) callAli(payStr);
  else document.addEventListener('AlipayJSBridgeReady',()=>callAli(payStr),false);
}
function callAli(payStr){
  AlipayJSBridge.call('tradePay',{tradeNO:payStr},function(result){
    if(result.resultCode==='9000') window.location.href='./success.php?trade_no='+document.getElementById('trade_no').value;
  });
}
function QQJsPay(payStr){
  const json=typeof payStr==='string'?JSON.parse(payStr):payStr;
  mqq.tenpay.pay({tokenId:json.tokenId,appInfo:json.appInfo},function(result,resultCode){
    if(resultCode==0) window.location.href='./success.php?trade_no='+document.getElementById('trade_no').value;
  });
}
</script>

<?php echo '</body></html>'; ?>
