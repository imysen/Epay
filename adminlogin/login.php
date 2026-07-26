<?php
/**
 * 登录
**/
$verifycode = 1;
$login_limit_count = 5;
$login_limit_file = '@login.lock';

if(!function_exists("imagecreate") || !file_exists('code.php'))$verifycode=0;
include("../includes/common.php");

if(isset($_GET['act']) && $_GET['act']=='login'){
  if(!checkRefererHost())exit('{"code":403}');
  $username = trim($_POST['username']);
  $password = trim($_POST['password']);
  $code = trim($_POST['code']);
  $enc_type = isset($_POST['enc']) ? $_POST['enc'] : '0';
  if(empty($username) || empty($password)) exit(json_encode(['code'=>-1,'msg'=>'用户名或密码不能为空']));
  if($verifycode==1 && (!$code || strtolower($code) != $_SESSION['vc_code'])) exit(json_encode(['code'=>-1,'msg'=>'验证码错误']));
  $errcount = $DB->getColumn("SELECT count(*) FROM `pre_log` WHERE `ip`=:ip AND `date`>DATE_SUB(NOW(),INTERVAL 1 DAY) AND `uid`=0 AND `type`='登录失败'", [':ip'=>$clientip]);
  if($errcount >= $login_limit_count && file_exists($login_limit_file) && !$conf['totp_open']) exit(json_encode(['code'=>-1,'msg'=>'多次登录失败，暂时禁止登录。可删除@login.lock文件解除限制']));
  if($enc_type == '1'){
    $plain = '';
    $private_key = base64ToPem($conf['private_key'], 'PRIVATE KEY');
    $pkey = openssl_pkey_get_private($private_key);
    if(!openssl_private_decrypt(base64_decode($password), $plain, $pkey, OPENSSL_PKCS1_PADDING)) exit(json_encode(['code'=>-1,'msg'=>'密码解密失败']));
    $password = $plain;
  }
  if($username == $conf['admin_user'] && $password == $conf['admin_pwd']){
    if ($conf['totp_open'] == 1 && !empty($conf['totp_secret'])) {
      if (file_exists($login_limit_file)) unlink($login_limit_file);
      exit(json_encode(['code'=>-1, 'msg'=>'需要验证动态口令', 'vcode' => 2]));
    }
    $DB->insert('log', ['uid'=>0, 'type'=>'登录后台', 'date'=>'NOW()', 'ip'=>$clientip]);
    if (file_exists($login_limit_file)) unlink($login_limit_file);
    $session=md5($username.$password.$password_hash);
    $expiretime=time() + 2592000;
    $token=authcode("{$username}\t{$session}\t{$expiretime}", 'ENCODE', SYS_KEY);
    setcookie("admin_token", $token, $expiretime, null, null, null, true);
    unset($_SESSION['vc_code']);
    exit(json_encode(['code'=>0]));
  }
  $DB->insert('log', ['uid'=>0, 'type'=>'登录失败', 'date'=>'NOW()', 'ip'=>$clientip]);
  unset($_SESSION['vc_code']);
  $errcount++;
  $retry_times = max(0, $login_limit_count - $errcount);
  if($retry_times <= 0){
    file_put_contents($login_limit_file, '1');
    exit(json_encode(['code'=>-1,'msg'=>'多次登录失败，暂时禁止登录。可删除@login.lock文件解除限制','vcode'=>1]));
  }
  exit(json_encode(['code'=>-1,'msg'=>'用户名或密码错误，你还可以尝试'.$retry_times.'次','vcode'=>1]));
}elseif(isset($_GET['act']) && $_GET['act']=='totp'){
  if(!checkRefererHost())exit('{"code":403}');
  $code = trim($_POST['code']);
  if (empty($code)) exit(json_encode(['code'=>-1,'msg'=>'请输入动态口令']));
  if ($conf['totp_open'] != 1 || empty($conf['totp_secret'])) exit(json_encode(['code'=>-1,'msg'=>'未启用TOTP二次验证']));
  try{
    $totp = \lib\TOTP::create($conf['totp_secret']);
    if (!$totp->verify($code)) exit(json_encode(['code'=>-1,'msg'=>'动态口令错误']));
  }catch(Exception $e){exit(json_encode(['code'=>-1,'msg'=>$e->getMessage()]));}
  $DB->insert('log', ['uid'=>0, 'type'=>'登录后台', 'date'=>'NOW()', 'ip'=>$clientip]);
  $session=md5($conf['admin_user'].$conf['admin_pwd'].$password_hash);
  $expiretime=time() + 2592000;
  $token=authcode("{$conf['admin_user']}\t{$session}\t{$expiretime}", 'ENCODE', SYS_KEY);
  setcookie("admin_token", $token, $expiretime, null, null, null, true);
  exit(json_encode(['code'=>0]));
}elseif(isset($_GET['logout'])){
  if(!checkRefererHost())exit();
  setcookie("admin_token", "", time() - 2592000);
  exit("<script>window.location.href='./login.php';</script>");
}elseif($islogin==1){
  exit("<script>alert('您已登录！');window.location.href='./';</script>");
}

define('IN_EPAY', true);
include_once '../includes/ep_ui.php';
$title='管理员登录';
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=no">
<meta name="renderer" content="webkit">
<title>管理员登录 · 支付管理中心</title>
<link href="/assets/css/ep-ui.css?v=<?=filemtime(ROOT.'assets/css/ep-ui.css')?>" rel="stylesheet">
<script src="<?php echo $cdnpublic?>jquery/3.4.1/jquery.min.js"></script>
<script src="<?php echo $cdnpublic?>layer/3.1.1/layer.js"></script>
<script src="<?php echo $cdnpublic?>jsencrypt/3.5.4/jsencrypt.min.js"></script>
</head>
<body class="ep-app" style="min-height:100vh;display:flex;align-items:center;justify-content:center;padding:24px">
<div class="ep-card" style="width:min(400px,100%);padding:32px">
  <div style="text-align:center;margin-bottom:28px">
    <div style="width:48px;height:48px;border-radius:8px;background:var(--ep-brand-500);color:#fff;display:inline-flex;align-items:center;justify-content:center;margin-bottom:14px"><?=ep_icon('shield',24)?></div>
    <h1 style="font-size:20px;font-weight:600;color:var(--ep-gray-900)">支付管理中心</h1>
    <div style="font-size:13px;color:var(--ep-gray-500);margin-top:5px">管理员登录</div>
  </div>

  <form id="login-form" onsubmit="return submitlogin()">
    <div style="margin-bottom:16px"><label class="login-label" for="login-user">用户名</label><input id="login-user" name="user" class="ep-input login-input" autocomplete="username" placeholder="请输入用户名" required></div>
    <div style="margin-bottom:16px"><label class="login-label" for="login-pass">密码</label><input id="login-pass" name="pass" type="password" class="ep-input login-input" autocomplete="current-password" placeholder="请输入密码" required></div>
    <?php if($verifycode==1){?>
    <div style="margin-bottom:20px"><label class="login-label" for="login-code">验证码</label><div style="display:flex;gap:10px"><input id="login-code" name="code" class="ep-input login-input" style="flex:1;min-width:0" autocomplete="off" placeholder="输入验证码" required><button type="button" id="verifycode" class="login-captcha" title="点击更换验证码"><img src="./code.php?r=<?php echo time();?>" alt="验证码"></button></div></div>
    <?php }?>
    <button class="ep-btn ep-btn-primary" type="submit" style="width:100%;height:44px;font-size:15px">登录</button>
    <button class="login-link" type="button" onclick="findpwd()">忘记密码</button>
  </form>

  <form id="totp-form" onsubmit="return doTotp()" style="display:none">
    <div class="login-note">请输入身份验证器中的 6 位动态口令。</div>
    <div style="margin-bottom:20px"><label class="login-label" for="totp_code">动态口令</label><input id="totp_code" type="text" inputmode="numeric" maxlength="6" class="ep-input login-input" autocomplete="one-time-code" placeholder="输入动态口令" required></div>
    <button class="ep-btn ep-btn-primary" type="submit" style="width:100%;height:44px;font-size:15px">验证并登录</button>
    <button class="login-link" type="button" onclick="findpwd()">忘记密码</button>
  </form>
</div>

<div class="login-modal" id="modal-findpwd" hidden>
  <div class="ep-card" style="width:min(420px,100%);padding:24px">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px"><h2 style="font-size:16px;font-weight:600;color:var(--ep-gray-800)">找回管理员密码</h2><button type="button" class="ep-btn ep-btn-ghost ep-btn-icon" onclick="closeFindpwd()"><?=ep_icon('x',16)?></button></div>
    <p style="font-size:14px;line-height:1.7;color:var(--ep-gray-600)">进入数据库管理器（phpMyAdmin），查看当前网站所在数据库的 <code class="ep-code">pay_config</code> 表即可找回管理员密码。</p>
    <?php if($conf['totp_open'] == 1){?><p style="font-size:13px;line-height:1.7;color:var(--ep-gray-500);margin-top:12px">如需关闭 TOTP 二次验证，请执行：<code class="ep-code">UPDATE pay_config SET v='0' WHERE k='totp_open';</code></p><?php }?>
  </div>
</div>

<style>
.login-label{display:block;font-size:13px;color:var(--ep-gray-600);margin-bottom:6px}.login-input{width:100%;height:42px}.login-captcha{width:108px;height:42px;padding:0;border:1px solid var(--ep-gray-300);border-radius:6px;background:#fff;overflow:hidden;cursor:pointer}.login-captcha img{display:block;width:100%;height:100%;object-fit:cover}.login-link{display:block;border:none;background:none;color:var(--ep-brand-500);font-size:13px;margin:16px auto 0;cursor:pointer}.login-note{font-size:13px;color:var(--ep-gray-500);background:var(--ep-brand-50);border-radius:6px;padding:10px 12px;margin-bottom:16px}.login-modal{position:fixed;inset:0;z-index:100;background:rgba(17,24,39,.35);display:flex;align-items:center;justify-content:center;padding:20px}.login-modal[hidden]{display:none}
</style>
<script>
const PUBLIC_KEY_PEM=`<?php echo base64ToPem($conf['public_key'], 'PUBLIC KEY')?>`;
function showError(message){layer.alert(message,{icon:2});}
function submitlogin(){
  let pass=$('#login-pass').val();const user=$('#login-user').val();const code=$('#login-code').val();let encType='0';
  if(!user||!pass){showError('用户名或密码不能为空！');return false;}
  if(PUBLIC_KEY_PEM){const enc=new JSEncrypt();enc.setPublicKey(PUBLIC_KEY_PEM);const encrypted=enc.encrypt(pass);if(encrypted){pass=encrypted;encType='1';}}
  const loading=layer.load(2);
  $.ajax({type:'POST',url:'?act=login',data:{username:user,password:pass,code:code,enc:encType},dataType:'json',success:function(data){
    layer.close(loading);
    if(data.code==0){layer.msg('登录成功，正在跳转',{icon:1,shade:.01,time:15000});window.location.href='./';return;}
    if(data.vcode==1)$('#verifycode img').attr('src','./code.php?r='+Math.random());
    if(data.vcode==2){$('#login-form').hide();$('#totp-form').show();$('#totp_code').focus();return;}
    showError(data.msg);
  },error:function(){layer.close(loading);layer.msg('服务器错误');}});
  return false;
}
function doTotp(){
  const code=$('#totp_code').val();if(code.length!=6){layer.msg('动态口令格式错误',{icon:2});return false;}
  const loading=layer.load(2,{shade:[.1,'#fff']});
  $.post('?act=totp',{code:code},function(res){layer.close(loading);if(res.code==0){layer.msg('登录成功，正在跳转',{icon:1,shade:.01,time:15000});window.location.href='./';}else showError(res.msg);},'json');
  return false;
}
function findpwd(){document.getElementById('modal-findpwd').hidden=false;}function closeFindpwd(){document.getElementById('modal-findpwd').hidden=true;}
$('#verifycode').on('click',function(){$(this).find('img').attr('src','./code.php?r='+Math.random());});
$('#totp_code').on('input',function(){if(this.value.length===6)$('#totp-form').trigger('submit');});
</script>
</body>
</html>
