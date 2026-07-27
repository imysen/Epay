<?php
/**
 * Epay UI 辅助层
 * - ep_icon(): 内联 SVG 图标(无 emoji,无字体库)
 * - ep_layout_head(): 后台公共头(侧边栏 + 顶栏)
 * - ep_pay_head(): 支付收银页 HTML 头
 * - ep_alpine(): 引入 Alpine + 通用组件(本地,无 CDN)
 *
 * 设计规范见 design/DESIGN-SYSTEM.md
 */

if(!defined('IN_EPAY'))exit();

/**
 * 内联 SVG 图标。line 风格,1.5 描边,currentColor 继承。
 * @param string $name 图标名(home/list/users/credit-card/wallet/settings/shield/bell/log-out/plus/search/edit/trash/copy/qr-code/check/x/alert/info/chevron-down/chevron-right/external-link/refresh/filter/clock/trending-up/trending-down/package/menu/lock/key/eye/eye-off)
 * @param int $size 16/20/32
 * @param string $class 额外 class
 */
function ep_icon($name, $size=16, $class=''){
	$paths = [
		'home'=>'<path d="M3 12l9-9 9 9"/><path d="M5 10v10h14V10"/>',
		'list'=>'<path d="M8 6h13"/><path d="M8 12h13"/><path d="M8 18h13"/><circle cx="3.5" cy="6" r="1"/><circle cx="3.5" cy="12" r="1"/><circle cx="3.5" cy="18" r="1"/>',
		'users'=>'<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>',
		'credit-card'=>'<rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/>',
		'wallet'=>'<path d="M16 7a4 4 0 1 0-8 0"/><rect x="4" y="7" width="16" height="13" rx="2"/><path d="M9 13l2 2 4-4"/>',
		'settings'=>'<circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.6 1.6 0 0 0 .3 1.8l.1.1a2 2 0 1 1-2.8 2.8l-.1-.1a1.6 1.6 0 0 0-1.8-.3 1.6 1.6 0 0 0-1 1.5V21a2 2 0 1 1-4 0v-.1a1.6 1.6 0 0 0-1-1.5 1.6 1.6 0 0 0-1.8.3l-.1.1a2 2 0 1 1-2.8-2.8l.1-.1a1.6 1.6 0 0 0 .3-1.8 1.6 1.6 0 0 0-1.5-1H3a2 2 0 1 1 0-4h.1a1.6 1.6 0 0 0 1.5-1 1.6 1.6 0 0 0-.3-1.8l-.1-.1a2 2 0 1 1 2.8-2.8l.1.1a1.6 1.6 0 0 0 1.8.3H9a1.6 1.6 0 0 0 1-1.5V3a2 2 0 1 1 4 0v.1a1.6 1.6 0 0 0 1 1.5 1.6 1.6 0 0 0 1.8-.3l.1-.1a2 2 0 1 1 2.8 2.8l-.1.1a1.6 1.6 0 0 0-.3 1.8V9a1.6 1.6 0 0 0 1.5 1H21a2 2 0 1 1 0 4h-.1a1.6 1.6 0 0 0-1.5 1z"/>',
		'shield'=>'<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>',
		'bell'=>'<path d="M18 8a6 6 0 0 0-12 0c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.7 21a2 2 0 0 1-3.4 0"/>',
		'log-out'=>'<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="M16 17l5-5-5-5"/><path d="M21 12H9"/>',
		'plus'=>'<path d="M12 5v14M5 12h14"/>',
		'search'=>'<circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>',
		'edit'=>'<path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.1 2.1 0 0 1 3 3L12 15l-4 1 1-4z"/>',
		'trash'=>'<path d="M3 6h18"/><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>',
		'copy'=>'<rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/>',
		'qr-code'=>'<rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><path d="M14 17.5h7M17.5 14v7"/>',
		'check'=>'<path d="M20 6 9 17l-5-5"/>',
		'x'=>'<path d="M18 6 6 18M6 6l12 12"/>',
		'alert'=>'<path d="M10.3 3.9 1.8 18a2 2 0 0 0 1.7 3h17a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0z"/><path d="M12 9v4"/><path d="M12 17h.01"/>',
		'info'=>'<circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/>',
		'chevron-down'=>'<polyline points="6 9 12 15 18 9"/>',
		'chevron-right'=>'<polyline points="9 18 15 12 9 6"/>',
		'external-link'=>'<path d="M15 3h6v6"/><path d="M10 14 21 3"/><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>',
		'refresh'=>'<path d="M21 12a9 9 0 1 1-3-6.7"/><path d="M21 4v6h-6"/>',
		'filter'=>'<polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>',
		'clock'=>'<circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>',
		'trending-up'=>'<polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/>',
		'trending-down'=>'<polyline points="23 18 13.5 8.5 8.5 13.5 1 6"/><polyline points="17 18 23 18 23 12"/>',
		'package'=>'<path d="M16.5 9.4 7.5 4.21"/><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><path d="M3.27 6.96 12 12.01l8.73-5.05"/><path d="M12 22.08V12"/>',
		'menu'=>'<path d="M3 6h18M3 12h18M3 18h18"/>',
		'lock'=>'<rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>',
		'key'=>'<circle cx="7.5" cy="15.5" r="5.5"/><path d="m21 2-9.6 9.6"/><path d="m15.5 7.5 3 3L22 7l-3-3"/>',
		'eye'=>'<path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7z"/><circle cx="12" cy="12" r="3"/>',
		'eye-off'=>'<path d="M9.9 4.24A9.1 9.1 0 0 1 12 4c7 0 10 8 10 8a13.2 13.2 0 0 1-1.67 2.68"/><path d="M6.61 6.61A13.5 13.5 0 0 0 2 12s3 8 10 8a9.1 9.1 0 0 0 5.39-1.61"/><path d="M14.12 14.12A3 3 0 1 1 9.88 9.88"/><path d="m2 2 20 20"/>',
		'download'=>'<path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><path d="M7 10l5 5 5-5"/><path d="M12 15V3"/>',
		'send'=>'<path d="M22 2 11 13"/><path d="M22 2 15 22l-4-9-9-4z"/>',
		'merchant'=>'<path d="M2 7l1-4h18l1 4"/><path d="M4 7v13a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1V7"/><path d="M9 21v-6h6v6"/>',
	];
	$body = isset($paths[$name]) ? $paths[$name] : $paths['info'];
	return '<svg width="'.$size.'" height="'.$size.'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="'.$class.'">'.$body.'</svg>';
}

/**
 * 后台公共头:输出侧边栏 + 顶栏骨架。
 * 调用前页面需设置 $title、$activeNav(侧边栏 active 项标识)、可选 $crumbs(数组)。
 * 调用方在 include head 后输出 <main class="ep-content">...</main>。
 */
function ep_layout_head($legacyAssets=false){
	global $conf, $islogin, $title, $activeNav, $crumbs;
	if($islogin!=1){exit("<script>window.location.href='./login.php';</script>");}
	$crumbs = isset($crumbs) ? $crumbs : ['首页', $title];
		$activeNav = isset($activeNav) ? $activeNav : basename($_SERVER['SCRIPT_NAME'], '.php');
	// 侧边栏导航结构(标识 => [图标, 名称])
	$nav = [
		['group'=>'运营'],
		['item'=>'index', 'icon'=>'home', 'name'=>'平台首页', 'url'=>'./index.php'],
		['item'=>'order', 'icon'=>'list', 'name'=>'订单管理', 'url'=>'./order.php'],
		['item'=>'export', 'icon'=>'download', 'name'=>'订单导出', 'url'=>'./export.php'],
		['item'=>'buyerstat', 'icon'=>'trending-up', 'name'=>'支付用户统计', 'url'=>'./buyerstat.php'],
		['item'=>'blacklist', 'icon'=>'shield', 'name'=>'黑名单管理', 'url'=>'./blacklist.php'],
		['item'=>'ps_receiver', 'icon'=>'wallet', 'name'=>'分账规则', 'url'=>'./ps_receiver.php'],
		['item'=>'ps_order', 'icon'=>'list', 'name'=>'分账记录', 'url'=>'./ps_order.php'],
		['group'=>'付款'],
		['item'=>'slist', 'icon'=>'wallet', 'name'=>'结算管理', 'url'=>'./slist.php'],
		['item'=>'settle', 'icon'=>'credit-card', 'name'=>'批量结算', 'url'=>'./settle.php'],
		['item'=>'transfer', 'icon'=>'send', 'name'=>'付款记录', 'url'=>'./transfer.php'],
		['item'=>'transfer_add', 'icon'=>'plus', 'name'=>'新增付款', 'url'=>'./transfer_add.php'],
		['item'=>'transfer_stat', 'icon'=>'trending-up', 'name'=>'付款统计', 'url'=>'./transfer_stat.php'],
		['group'=>'商户管理'],
		['item'=>'ulist', 'icon'=>'users', 'name'=>'用户列表', 'url'=>'./ulist.php'],
		['item'=>'onecode', 'icon'=>'qr-code', 'name'=>'聚合收款码牌', 'url'=>'./onecode.php'],
		['group'=>'配置'],
		['item'=>'pay_channel', 'icon'=>'credit-card', 'name'=>'支付接口', 'url'=>'./pay_channel.php'],
		['item'=>'risk', 'icon'=>'shield', 'name'=>'风控与日志', 'url'=>'./risk.php'],
		['item'=>'set', 'icon'=>'settings', 'name'=>'系统设置', 'url'=>'./set.php?mod=site'],
	];
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="renderer" content="webkit">
<title><?=htmlspecialchars($title)?> · 支付管理中心</title>
<?php if($legacyAssets): ?>
<link href="/assets/vendor/twitter-bootstrap/3.4.1/css/bootstrap.min.css" rel="stylesheet">
<link href="/assets/css/bootstrap-table.css?v=1" rel="stylesheet">
<link href="/assets/vendor/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
<?php endif; ?>
<link href="/assets/css/ep-ui.css?v=<?=filemtime(ROOT.'assets/css/ep-ui.css')?>" rel="stylesheet">
<?php if($legacyAssets): ?>
<link href="/assets/css/ep-admin-legacy.css?v=<?=filemtime(ROOT.'assets/css/ep-admin-legacy.css')?>" rel="stylesheet">
<script src="/assets/vendor/jquery/3.4.1/jquery.min.js"></script>
<script src="/assets/vendor/twitter-bootstrap/3.4.1/js/bootstrap.min.js"></script>
<?php endif; ?>
<?=ep_alpine(true)?>
</head>
<body class="ep-app">
<div class="ep-layout">
  <aside class="ep-sidebar" id="epSidebar">
	<div class="ep-sidebar-brand">
	  <span class="logo"><?=ep_icon('credit-card',18)?></span>
	  <span class="name">支付管理中心</span>
	</div>
	<nav class="ep-nav-scroll">
<?php foreach($nav as $n):
	if(isset($n['group'])){ echo '<div class="ep-nav-group">'.htmlspecialchars($n['group']).'</div>'; continue; }
	$cls = ($activeNav===$n['item'])?' active':'';
?>
	  <a class="ep-nav-item<?=$cls?>" href="<?=$n['url']?>"><?=ep_icon($n['icon'],20)?><?=htmlspecialchars($n['name'])?></a>
<?php endforeach;?>
	</nav>
	<div style="padding:12px 16px;border-top:1px solid #2D3748;flex-shrink:0">
	  <a class="ep-nav-item" style="height:36px" href="./login.php?logout" onclick="return confirm('确定退出登录?')"><?=ep_icon('log-out',20)?>退出登录</a>
	</div>
  </aside>

  <div class="ep-main">
	<header class="ep-topbar">
	  <button class="ep-iconbtn ep-hamburger" onclick="document.getElementById('epSidebar').classList.toggle('open')"><?=ep_icon('menu',20)?></button>
	  <div class="ep-crumb">
<?php
	$last = array_key_last($crumbs);
	foreach($crumbs as $i=>$c){
		if($i>0) echo ep_icon('chevron-right',14);
		echo ($i===$last) ? '<b>'.htmlspecialchars($c).'</b>' : htmlspecialchars($c);
	}
?>
	  </div>
	  <div class="ep-topbar-right">
		<button class="ep-iconbtn" title="刷新" onclick="location.reload()"><?=ep_icon('refresh',18)?></button>
		<button class="ep-iconbtn" title="通知"><?=ep_icon('bell',18)?><span class="dot"></span></button>
		<div class="ep-divider-v"></div>
		<div class="ep-userchip">
		  <div class="avatar ep-avatar"><?php $adminInitial=(string)$conf['admin_user']; echo htmlspecialchars(function_exists('mb_substr')?mb_substr($adminInitial,0,1,'UTF-8'):substr($adminInitial,0,1), ENT_QUOTES, 'UTF-8'); ?></div>
		  <div class="ep-usermeta">
			<span class="name"><?=htmlspecialchars($conf['admin_user'])?></span>
			<span class="role">管理员</span>
		  </div>
		  <?=ep_icon('chevron-down',16)?>
		</div>
	  </div>
	</header>
	<main class="ep-content">
<?php
}

/**
 * 后台公共尾:闭合 main/layout + 全局 Toast/Modal 容器。
 */
function ep_layout_foot(){
?>
	</main>
  </div>
</div>
<!-- 全局 UI 总线容器(Alpine):toast / confirm / prompt / loading -->
<div x-data="epUI()" x-cloak @toast.window="openToast($event.detail)" @ep-confirm.window="openConfirm($event.detail)" @ep-prompt.window="openPrompt($event.detail)">
  <!-- loading -->
  <template x-if="loading>0">
	<div class="ep-mask" style="background:rgba(17,24,39,.25);display:flex;align-items:center;justify-content:center" x-show="loading>0">
	  <div class="ep-card" style="padding:16px 24px;display:flex;align-items:center;gap:10px;font-size:14px;color:var(--ep-gray-700)">
		<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="animation:ep-spin .8s linear infinite"><path d="M21 12a9 9 0 1 1-6.7-8.7" stroke-linecap="round"/></svg>
		<span x-text="loadingText||'处理中…'"></span>
	  </div>
	</div>
  </template>
  <!-- toast -->
  <div class="ep-toasts">
	<template x-for="t in toasts" :key="t.id">
	  <div class="ep-toast" :class="t.type" x-transition>
		<span x-html="t.icon"></span>
		<span x-text="t.msg"></span>
		<span class="close" @click="toasts=toasts.filter(x=>x.id!==t.id)"><?=ep_icon('x',14)?></span>
	  </div>
	</template>
  </div>
  <!-- confirm -->
  <template x-if="confirm">
	<div class="ep-mask show" x-show="confirm?.open" x-transition.opacity>
	  <div class="ep-modal" style="max-width:400px">
		<div class="ep-modal-body" style="padding:24px">
		  <div style="display:flex;gap:12px">
			<div style="width:40px;height:40px;border-radius:50%;background:var(--ep-warning-50);color:var(--ep-warning-600);display:flex;align-items:center;justify-content:center;flex-shrink:0"><?=ep_icon('alert',20)?></div>
			<div>
			  <div style="font-size:16px;font-weight:600;color:var(--ep-gray-800);margin-bottom:4px" x-text="confirm?.msg"></div>
			  <div style="font-size:13px;color:var(--ep-gray-500)" x-text="confirm?.desc||''"></div>
			</div>
		  </div>
		</div>
		<div class="ep-modal-foot">
		  <button class="ep-btn ep-btn-secondary" @click="confirm.resolve(false);confirm.open=false" x-text="confirm?.cancelText||'取消'"></button>
		  <button class="ep-btn ep-btn-danger" @click="confirm.resolve(true);confirm.open=false" x-text="confirm?.okText||'确定'"></button>
		</div>
	  </div>
	</div>
  </template>
  <!-- prompt -->
  <template x-if="prompt">
	<div class="ep-mask show" x-show="prompt?.open" x-transition.opacity>
	  <div class="ep-modal">
		<div class="ep-modal-head"><h3 x-text="prompt?.title"></h3><button class="ep-btn ep-btn-ghost ep-btn-icon" @click="prompt.resolve(null);prompt.open=false"><?=ep_icon('x',16)?></button></div>
		<div class="ep-modal-body">
		  <label x-text="prompt?.label||''"></label>
		  <input class="ep-input" style="width:100%" :value="prompt?.value" x-model="prompt.value" x-ref="promptInput">
		</div>
		<div class="ep-modal-foot">
		  <button class="ep-btn ep-btn-secondary" @click="prompt.resolve(null);prompt.open=false">取消</button>
		  <button class="ep-btn ep-btn-primary" @click="prompt.resolve(prompt.value);prompt.open=false">确定</button>
		</div>
	  </div>
	</div>
  </template>
</div>
<style>@keyframes ep-spin{to{transform:rotate(360deg)}}</style>
<?php
}

/**
 * 支付收银页 HTML 头。调用方需传入 $title,$channel(wxpay/alipay/...)。
 * @param string $title 页面标题
 * @param string $channel 支付渠道标识
 */
function ep_pay_head($title, $channel='wxpay'){
	$title = (string)$title;
	$channel = (string)$channel;
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=no">
<meta name="renderer" content="webkit">
<title><?=htmlspecialchars($title)?></title>
<link href="/assets/css/ep-ui.css?v=<?=filemtime(ROOT.'assets/css/ep-ui.css')?>" rel="stylesheet">
<?=ep_alpine(true)?>
</head>
<body class="ep-app" data-channel="<?=htmlspecialchars($channel)?>" style="display:flex;align-items:center;justify-content:center;min-height:100vh;padding:16px">
<?php
}

/**
 * 引入 Alpine.js + 通用组件。本地文件,无 CDN。
 * Alpine 核心 + 组件定义合并输出。$return=true 则返回字符串。
 */
function ep_alpine($return=false){
	$base = ROOT.'assets/js/';
	// 优先用打包产物,缺失时回退到 Alpine CDN(开发期)
	$alpineFile = $base.'alpine.min.js';
	$hasLocal = is_file($alpineFile);
	$v = $hasLocal ? filemtime($alpineFile) : '3.14.1';
	ob_start();
?>
<script>
// === Alpine 通用组件(对应替换 layer/httpGet/httpPost/poller)===
document.addEventListener('alpine:init',()=>{
	// $fetch magic:替代 httpGet/httpPost
	// GET 请求:body 自动拼到 URL query;POST 请求:body 作为请求体发送
	Alpine.magic('fetch',()=>async(url,opts={})=>{
		const method=(opts.method||'GET').toUpperCase();
		let target=url, body=undefined, headers={};
		if(opts.body){
			if(method==='GET'||method==='HEAD'){
				// GET 请求把参数拼到 URL query 上(对应原 $.ajax 的 data 行为)
				const qp=new URLSearchParams(opts.body).toString();
				target=url+(url.indexOf('?')>-1?'&':'?')+qp;
			}else{
				headers['X-Requested-With']='XMLHttpRequest';
				if(opts.body instanceof FormData){body=opts.body;}
				else{body=new URLSearchParams(opts.body);headers['Content-Type']='application/x-www-form-urlencoded;charset=UTF-8';}
			}
		}
		const res=await fetch(target,{method,headers,body,credentials:'same-origin'});
		if(!res.ok) throw new Error('HTTP '+res.status);
		return res.json();
	});
	// epUI 全局总线:替代 layer.msg/alert/confirm/prompt/load
	Alpine.data('epUI',()=>({
		toasts:[],loading:0,loadingText:'',confirm:null,prompt:null,
		openToast(t){t.id=Date.now()+Math.random();t.icon=this.toastIcon(t.type);this.toasts.push(t);if(t.time!==0)setTimeout(()=>this.toasts=this.toasts.filter(x=>x.id!==t.id),t.time||2500);},
		toastIcon(type){const m={success:'<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>',error:'<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 8v4"/><path d="M12 16h.01"/></svg>',info:'<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>'};return m[type]||m.info;},
		openConfirm(d){this.confirm={...d,resolve:d.resolve||(()=>{}),open:true};},
		openPrompt(d){this.prompt={...d,resolve:d.resolve||(()=>{}),open:true};this.$nextTick(()=>this.$refs.promptInput&&this.$refs.promptInput.focus());},
		withLoading(p,text){this.loading++;this.loadingText=text||'';return p.finally(()=>this.loading--);}
	}));
	// poller:封装 getshop.php 轮询(支付页核心)
	Alpine.data('poller',(url,params,opts={})=>({
		interval:opts.interval||2000,delay:opts.delay??0,_timer:null,_stopped:false,
		init(){this.delay>0?this._timer=setTimeout(()=>this._tick(),this.delay):this._tick();},
		async _tick(){
			if(this._stopped)return;
			try{
				const d=await this.$fetch(url,{method:'GET',body:params});
				if(d.code===1){this.$dispatch('poll-ok',{data:d});setTimeout(()=>{window.location.href=d.backurl;},300);return;}
				this.$dispatch('poll-pending',{data:d});
			}catch(e){this.$dispatch('poll-error',{err:e});}
			this._timer=setTimeout(()=>this._tick(),this.interval);
		},
		destroy(){this._stopped=true;clearTimeout(this._timer);}
	}));
});
window.epToast=(type,msg,time)=>window.dispatchEvent(new CustomEvent('toast',{detail:{type,msg,time}}));
window.epConfirm=(msg,desc)=>new Promise(r=>window.dispatchEvent(new CustomEvent('ep-confirm',{detail:{msg,desc,resolve:r}})));
window.epPrompt=(title,label='',value='')=>new Promise(r=>window.dispatchEvent(new CustomEvent('ep-prompt',{detail:{title,label,value,resolve:r}})));
window.epLoading=(p,text)=>{const root=document.querySelector('[x-data^="epUI"]')&&Alpine.$data(document.querySelector('[x-data^="epUI"]'));return root?root.withLoading(p,text):p;};
</script>
<?php if($hasLocal): ?>
<script defer src="/assets/js/alpine.min.js?v=<?=$v?>"></script>
<?php else: ?>
<script defer src="https://unpkg.com/alpinejs@3.14.1/dist/cdn.min.js"></script>
<?php endif;
	$out = ob_get_clean();
	if($return) return $out;
	echo $out;
}
