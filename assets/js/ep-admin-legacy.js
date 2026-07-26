(function(){
  var pageMeta={
    buyerstat:['支付用户统计','分析支付账号、IP 与手机号的交易表现。'],
    ps_receiver:['分账规则','配置支付订单的分账接收方与比例。'],
    ps_order:['分账记录','查询分账执行状态并处理异常记录。'],
    record:['资金明细','查看商户资金变动与结算流水。'],
    ustat:['支付统计','按商户维度汇总支付金额和订单。'],
    glist:['用户组设置','管理商户用户组、费率和权限规则。'],
    gedit:['编辑用户组','维护用户组费率、支付方式和高级配置。'],
    group:['用户组购买','管理用户组购买与展示设置。'],
    domain:['授权支付域名','审核、管理商户支付授权域名。'],
    invitecode:['邀请码管理','生成、检索和清理邀请码。'],
    pay_type:['支付方式','管理前台可用的支付方式与显示信息。'],
    pay_plugin:['支付插件','查看和刷新已安装的支付插件。'],
    pay_roll:['支付通道轮询','配置支付通道轮询分组和规则。'],
    pay_weixin:['公众号与小程序','配置微信支付的公众号和小程序。'],
    pay_wework:['企业微信配置','管理企业微信支付与客服配置。'],
    settle:['批量结算','创建并执行商户批量结算。'],
    settle_batch:['结算执行','处理当前结算批次的付款步骤。'],
    transfer_add:['新增付款','向指定收款账号发起单笔付款。'],
    transfer_batch:['批量付款','导入并处理多笔付款记录。'],
    transfer_export:['导出付款记录','按条件导出付款流水。'],
    transfer_red:['创建红包','创建微信或支付宝红包收款链接。'],
    transfer_stat:['付款统计','按收款账号汇总付款情况。'],
    set_totp:['双重验证','启用、重置或关闭管理员 TOTP 验证。'],
    set_wxkf:['微信客服支付','配置微信客服与小程序客服支付。'],
    risk:['风控记录','查看支付风控命中与处理记录。'],
    log:['登录日志','查看管理员登录历史和来源 IP。'],
    clean:['数据清理','执行订单、日志等历史数据清理操作。'],
    gettoken:['获取用户标识','辅助获取支付授权用户标识。'],
    gonggao:['公告管理','发布和维护平台公告内容。'],
    export:['订单导出','按条件导出收款订单。'],
    record_export:['资金明细导出','按条件导出商户资金明细。'],
    uset:['商户设置','新增、编辑商户及其支付配置。'],
    plugin_page:['支付插件配置','配置当前支付通道的插件参数。']
  };

  function addFormClass(form){
    form.classList.add('ep-legacy-form');
    if(form.id==='searchToolbar')form.classList.add('ep-toolbar','ep-legacy-search');
  }

  function insertPageDescription(root){
    var key=root.dataset.page;
    var meta=pageMeta[key];
    var heading=root.querySelector('.ep-auto-page-head');
    if(!meta||!heading)return;
    heading.querySelector('h1').textContent=meta[0];
    var desc=document.createElement('div');
    desc.className='desc';
    desc.textContent=meta[1];
    heading.querySelector('div').appendChild(desc);
  }

  function init(){
    var root=document.querySelector('.ep-admin-legacy');
    if(!root)return;
    insertPageDescription(root);
    root.querySelectorAll('#searchToolbar').forEach(addFormClass);
    root.querySelectorAll('form.form-horizontal, form[role="form"]').forEach(addFormClass);
    root.querySelectorAll('table#listTable').forEach(function(table){table.classList.add('ep-legacy-list-table','ep-table');});
    root.querySelectorAll('.bootstrap-table').forEach(function(table){table.classList.add('ep-legacy-bootstrap-table');});
    root.querySelectorAll('.panel').forEach(function(panel){panel.classList.add('ep-legacy-panel');});
    root.querySelectorAll('.nav-pills,.nav-tabs').forEach(function(nav){nav.classList.add('ep-legacy-tabs');});
    root.querySelectorAll('.modal').forEach(function(modal){modal.classList.add('ep-legacy-modal');});
    root.querySelectorAll('.well').forEach(function(well){well.classList.add('ep-legacy-well');});
  }

  if(document.readyState==='loading')document.addEventListener('DOMContentLoaded',init);
  else init();
})();
