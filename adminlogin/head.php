<?php
@header('Content-Type: text/html; charset=UTF-8');

$admin_cdnpublic = 0;
if($admin_cdnpublic==1){
  $cdnpublic = '//lib.baomitu.com/';
}elseif($admin_cdnpublic==2){
  $cdnpublic = 'https://s4.zstatic.net/ajax/libs/';
}elseif($admin_cdnpublic==4){
  $cdnpublic = 'https://cdnjs.snrat.com/ajax/libs/';
}else{
  $cdnpublic = '/assets/vendor/';
}

if(!defined('IN_EPAY')) define('IN_EPAY', true);
include_once '../includes/ep_ui.php';

if($islogin==1){
  register_shutdown_function(function(){
    echo '</div>';
    ep_layout_foot();
    echo '</body></html>';
  });
  $legacyAssets = true;
  $pageName = basename($_SERVER['SCRIPT_NAME'], '.php');
  $activeMap = [
    'settle_batch'=>'settle',
    'transfer_add'=>'transfer', 'transfer_batch'=>'transfer', 'transfer_export'=>'transfer', 'transfer_red'=>'transfer',
    'glist'=>'ulist', 'gedit'=>'ulist', 'group'=>'ulist', 'record'=>'ulist', 'record_export'=>'ulist', 'uset'=>'ulist', 'ustat'=>'ulist', 'invitecode'=>'ulist',
    'pay_wework'=>'pay_weixin', 'plugin_page'=>'pay_plugin',
    'set_totp'=>'set', 'set_wxkf'=>'set',
  ];
  $activeNav = $activeMap[$pageName] ?? $pageName;
  $crumbs = ['首页', $title];
  ep_layout_head($legacyAssets);
  echo '<div class="ep-admin-legacy" data-page="'.htmlspecialchars($pageName).'">';
  $nativePages = ['index', 'onecode', 'order', 'ulist', 'pay_channel', 'set', 'transfer', 'slist', 'blacklist'];
  if(!in_array($pageName, $nativePages, true)){
    echo '<div class="ep-page-head ep-auto-page-head"><div><h1>'.htmlspecialchars($title).'</h1></div></div>';
  }
  echo '<script src="/assets/js/ep-admin-legacy.js?v='.filemtime('../assets/js/ep-admin-legacy.js').'" defer></script>';
}else{
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8"/>
  <meta name="renderer" content="webkit">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <title><?php echo htmlspecialchars($title) ?></title>
  <link href="<?php echo $cdnpublic?>twitter-bootstrap/3.4.1/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="<?php echo $cdnpublic?>font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet"/>
  <script src="<?php echo $cdnpublic?>jquery/3.4.1/jquery.min.js"></script>
  <script src="<?php echo $cdnpublic?>twitter-bootstrap/3.4.1/js/bootstrap.min.js"></script>
</head>
<body>
<?php }
