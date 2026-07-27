<?php
/**
 * 聚合收款码牌
 */
define('IN_EPAY', true);
include("../includes/common.php");
include("../includes/ep_ui.php");
$title='聚合收款码牌';
$activeNav='onecode';
$crumbs=['首页','商户管理','聚合收款码牌'];
ep_layout_head();
?>
<div x-data="platePage()" x-init="load()">
<div class="ep-page-head">
  <div>
	<h1>聚合收款码牌</h1>
	<div class="desc">码牌是预先印制的固定二维码。顾客扫码访问 <code class="ep-code">/paypage/?ucode=码牌编号</code>,系统按绑定的商户发起聚合收款。</div>
  </div>
  <button class="ep-btn ep-btn-primary" @click="openAdd()">
	<?=ep_icon('plus',16)?>新增码牌
  </button>
</div>

<!-- 列表 -->
<div class="ep-card">
  <div class="ep-toolbar">
	<div class="ep-field">
	  <?=ep_icon('search',16)?>
	  <input class="ep-input has-icon ep-mini" style="width:240px" placeholder="搜索码牌编号或商户" x-model="kw">
	</div>
	<select class="ep-select ep-mini" x-model="filter">
	  <option value="">全部状态</option>
	  <option value="bound">已绑定</option>
	  <option value="unbound">未绑定</option>
	</select>
	<div class="spacer"></div>
	<button class="ep-btn ep-btn-secondary ep-btn-sm" @click="kw='';filter=''"><?=ep_icon('refresh',14)?>重置</button>
  </div>
  <div class="ep-table-wrap">
	<table class="ep-table">
	  <thead><tr>
		<th style="width:40px"><input type="checkbox" class="ep-ck" @change="toggleAll($event)"></th>
		<th>码牌编号</th><th>绑定商户</th><th>收款链接</th><th>状态</th><th>绑定时间</th>
		<th style="text-align:right">操作</th>
	  </tr></thead>
	  <tbody>
		<template x-if="loading">
		  <tr><td colspan="7" class="ep-empty" style="padding:32px">正在加载…</td></tr>
		</template>
		<template x-if="!loading && filtered.length===0">
		  <tr><td colspan="7">
			<div class="ep-empty"><?=ep_icon('qr-code',40)?><div class="t">暂无码牌</div><div class="s">点击右上角"新增码牌"创建第一张</div></div>
		  </td></tr>
		</template>
		<template x-for="row in filtered" :key="row.code">
		  <tr :class="selected.includes(row.code)?'selected':''">
			<td><input type="checkbox" class="ep-ck" :value="row.code" x-model="selected"></td>
			<td><span class="ep-mono" x-text="row.code"></span></td>
			<td>
			  <template x-if="row.uid"><span><span class="ep-mono" x-text="row.uid"></span> · <span x-text="row.username||'-'"></span></span></template>
			  <template x-if="!row.uid"><span style="color:var(--ep-gray-400)">—</span></template>
			</td>
			<td><input class="ep-input ep-mini" style="width:240px;background:var(--ep-gray-50)" :value="payUrl(row.code)" readonly @focus="$event.target.select()"></td>
			<td>
			  <template x-if="row.uid"><span class="ep-badge success"><span class="pulse"></span>已绑定</span></template>
			  <template x-if="!row.uid"><span class="ep-badge neutral">未绑定</span></template>
			</td>
			<td x-text="row.bindtime?row.bindtime.slice(0,16).replace('T',' '):'—'" style="color:var(--ep-gray-500)"></td>
			<td class="ops">
			  <button class="ep-btn ep-btn-ghost ep-btn-icon" title="复制链接" @click="copyLink(row.code)"><?=ep_icon('copy',15)?></button>
			  <button class="ep-btn ep-btn-ghost ep-btn-icon" :title="row.uid?'换绑':'绑定'" @click="openRebind(row.code,row.uid)" style="color:var(--ep-brand-600)"><?=ep_icon('edit',15)?></button>
			  <template x-if="row.uid"><button class="ep-btn ep-btn-ghost ep-btn-icon" title="解绑" @click="doUnbind(row.code)" style="color:var(--ep-danger-600)"><?=ep_icon('trash',15)?></button></template>
			  <template x-if="!row.uid"><button class="ep-btn ep-btn-ghost ep-btn-icon" title="删除" @click="doDelete(row.code)" style="color:var(--ep-danger-600)"><?=ep_icon('trash',15)?></button></template>
			</td>
		  </tr>
		</template>
	  </tbody>
	</table>
  </div>
  <div class="ep-pager">
	<div class="count">共 <span x-text="filtered.length"></span> 张码牌</div>
	<div class="pages">
	  <span class="ep-badge neutral" x-show="selected.length">已选 <span x-text="selected.length"></span> 张</span>
	  <button class="ep-btn ep-btn-secondary ep-btn-sm" x-show="selected.length" @click="selected=[]">清空选择</button>
	</div>
  </div>
</div>

<!-- 新增弹层 -->
<div class="ep-mask" :class="addOpen?'show':''" x-cloak>
  <div class="ep-modal">
	<div class="ep-modal-head"><h3>新增码牌</h3><button class="ep-btn ep-btn-ghost ep-btn-icon" @click="addOpen=false"><?=ep_icon('x',16)?></button></div>
	<div class="ep-modal-body">
	  <div class="field-row"><label>码牌编号</label><input class="ep-input" style="width:100%" placeholder="1-32 位字母或数字,如 SHOP005" x-model="form.code"></div>
	  <div class="field-row"><label>绑定商户 ID</label><input class="ep-input" type="number" placeholder="输入要绑定的商户 ID" x-model.number="form.uid"></div>
	</div>
	<div class="ep-modal-foot">
	  <button class="ep-btn ep-btn-secondary" @click="addOpen=false">取消</button>
	  <button class="ep-btn ep-btn-primary" @click="submitAdd()" :class="saving?'saving':''">确认新增</button>
	</div>
  </div>
</div>
</div>

<script>
function payUrl(code){return <?=json_encode($siteurl.'paypage/?ucode=')?>+encodeURIComponent(code);}
function platePage(){
	return {
		rows:[],loading:true,kw:'',filter:'',selected:[],
		addOpen:false,form:{code:'',uid:''},saving:false,
		get filtered(){return this.rows.filter(r=>{if(this.filter==='bound'&&!r.uid)return false;if(this.filter==='unbound'&&r.uid)return false;if(this.kw&&!String(r.code).toLowerCase().includes(this.kw.toLowerCase())&&!(r.username||'').toLowerCase().includes(this.kw.toLowerCase()))return false;return true;});},
		async load(){this.loading=true;try{const d=await this.$fetch('ajax_onecode.php?act=list');if(d.code===0)this.rows=d.data;else epToast('error',d.msg);}catch(e){epToast('error','加载失败');}this.loading=false;},
		openAdd(){this.form={code:'',uid:''};this.addOpen=true;},
		async submitAdd(){if(!this.form.code){epToast('error','请填写码牌编号');return;}if(this.saving)return;this.saving=true;try{const d=await this.$fetch('ajax_onecode.php?act=save',{method:'POST',body:{code:this.form.code,uid:this.form.uid}});if(d.code===0){epToast('success',d.msg);this.addOpen=false;await this.load();}else epToast('error',d.msg);}catch(e){epToast('error','操作失败');}this.saving=false;},
		async openRebind(code,uid){const v=await epPrompt('换绑商户','绑定商户 ID',uid||'');if(v===null)return;if(!v){epToast('error','请输入商户 ID');return;}try{const d=await this.$fetch('ajax_onecode.php?act=save',{method:'POST',body:{code:code,uid:v}});if(d.code===0){epToast('success',d.msg);await this.load();}else epToast('error',d.msg);}catch(e){epToast('error','操作失败');}},
		async doUnbind(code){const ok=await epConfirm('确定解绑码牌 '+code+'?','解绑后该二维码将不再关联商户,可重新绑定其他商户。');if(!ok)return;try{const d=await this.$fetch('ajax_onecode.php?act=unbind',{method:'POST',body:{code:code}});if(d.code===0){epToast('success',d.msg);await this.load();}else epToast('error',d.msg);}catch(e){epToast('error','操作失败');}},
		async doDelete(code){const ok=await epConfirm('删除后该二维码将失效,确定删除 '+code+' 吗?');if(!ok)return;try{const d=await this.$fetch('ajax_onecode.php?act=delete',{method:'POST',body:{code:code}});if(d.code===0){epToast('success',d.msg);await this.load();}else epToast('error',d.msg);}catch(e){epToast('error','操作失败');}},
		async copyLink(code){try{await navigator.clipboard.writeText(payUrl(code));epToast('success','收款链接已复制');}catch(e){epToast('info','请手动复制链接');}},
		toggleAll(e){this.selected=e.target.checked?this.filtered.map(r=>r.code):[];}
	};
}
</script>
<?php
ep_layout_foot();
