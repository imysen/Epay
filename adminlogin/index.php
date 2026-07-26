<?php
define('IN_EPAY', true);
include("../includes/common.php");
include("../includes/ep_ui.php");
$title='支付管理中心';
$activeNav='index';
$crumbs=['首页','平台首页'];
ep_layout_head(true);

$messages=[];
if($conf['admin_pwd']==='123456'){
  $messages[]='请及时修改网站默认管理员密码。';
}elseif(strlen($conf['admin_pwd'])<6 || is_numeric($conf['admin_pwd']) && strlen($conf['admin_pwd'])<=10 || $conf['admin_pwd']===$conf['kfqq'] || $conf['admin_user']===$conf['admin_pwd']){
  $messages[]='网站管理员密码过于简单，请及时修改密码。';
}
?>
<div class="ep-page-head">
  <div>
    <h1 id="dashboard-title">后台管理首页</h1>
    <div class="desc">查看收款、商户与结算的实时汇总数据。</div>
  </div>
  <button class="ep-btn ep-btn-secondary" id="refresh-dashboard"><?=ep_icon('refresh',16)?>刷新数据</button>
</div>

<?php foreach($messages as $message): ?>
<div class="ep-card" style="border-color:var(--ep-danger-500);background:var(--ep-danger-50);padding:14px 16px;color:var(--ep-danger-600);display:flex;align-items:center;gap:8px">
  <?=ep_icon('alert',18)?><?=htmlspecialchars($message)?>
</div>
<?php endforeach; ?>

<div class="ep-stat-grid">
  <a class="ep-stat" href="./order.php">
    <span class="icon" style="background:var(--ep-brand-50);color:var(--ep-brand-500)"><?=ep_icon('list',18)?></span>
    <div class="label">订单总数</div>
    <div class="value" id="count1">—</div>
  </a>
  <a class="ep-stat" href="./ulist.php">
    <span class="icon" style="background:var(--ep-info-50);color:var(--ep-info-500)"><?=ep_icon('users',18)?></span>
    <div class="label">商户数量</div>
    <div class="value" id="count2">—</div>
  </a>
  <div class="ep-stat">
    <span class="icon" style="background:var(--ep-success-50);color:var(--ep-success-500)"><?=ep_icon('wallet',18)?></span>
    <div class="label">总计余额</div>
    <div class="value">¥<span id="usermoney">—</span></div>
  </div>
  <div class="ep-stat">
    <span class="icon" style="background:var(--ep-warning-50);color:var(--ep-warning-500)"><?=ep_icon('credit-card',18)?></span>
    <div class="label">结算总额</div>
    <div class="value">¥<span id="settlemoney">—</span></div>
  </div>
  <div class="ep-stat">
    <span class="icon" style="background:var(--ep-success-50);color:var(--ep-success-500)"><?=ep_icon('trending-up',18)?></span>
    <div class="label">今日订单成功率</div>
    <div class="value"><span id="success_rate">—</span>%</div>
  </div>
  <div class="ep-stat">
    <span class="icon" style="background:var(--ep-gray-100);color:var(--ep-gray-600)"><?=ep_icon('clock',18)?></span>
    <div class="label">当前时间</div>
    <div class="value" style="font-size:16px;margin-top:11px"><?=htmlspecialchars($date)?></div>
  </div>
</div>

<div class="ep-card">
  <div class="ep-card-head">
    <h2>管理员信息</h2>
    <a class="ep-btn ep-btn-secondary ep-btn-sm" href="./set.php?mod=account"><?=ep_icon('settings',14)?>账户设置</a>
  </div>
  <div class="ep-card-body" style="display:flex;align-items:center;gap:16px;flex-wrap:wrap">
    <div class="ep-avatar" style="width:48px;height:48px;font-size:18px"><?php $adminInitial=(string)$conf['admin_user']; echo htmlspecialchars(function_exists('mb_substr')?mb_substr($adminInitial,0,1,'UTF-8'):substr($adminInitial,0,1), ENT_QUOTES, 'UTF-8'); ?></div>
    <div style="flex:1;min-width:160px">
      <div style="font-weight:600;color:var(--ep-gray-800)"><?=htmlspecialchars($conf['admin_user'])?></div>
      <div style="font-size:13px;color:var(--ep-gray-500);margin-top:3px">管理员</div>
    </div>
    <div style="display:flex;gap:8px">
      <a class="ep-btn ep-btn-secondary" href="../" target="_blank"><?=ep_icon('external-link',14)?>访问首页</a>
      <a class="ep-btn ep-btn-danger" href="./login.php?logout"><?=ep_icon('log-out',14)?>退出登录</a>
    </div>
  </div>
</div>

<div class="ep-card">
  <div class="ep-card-head">
    <h2>支付方式收入统计</h2>
    <span style="font-size:12px;color:var(--ep-gray-400)">每小时更新</span>
  </div>
  <div class="ep-table-wrap"><table class="ep-table"><thead><tr id="paytype_head"><th>日期</th></tr></thead><tbody id="paytype_list"></tbody></table></div>
</div>

<div class="ep-card">
  <div class="ep-card-head">
    <h2>支付通道收入统计</h2>
    <span style="font-size:12px;color:var(--ep-gray-400)">每小时更新</span>
  </div>
  <div class="ep-table-wrap"><table class="ep-table"><thead><tr id="channel_head"><th>日期</th></tr></thead><tbody id="channel_list"></tbody></table></div>
</div>

<div class="ep-card">
  <div class="ep-card-head">
    <h2>支付方式手续费利润</h2>
    <span style="font-size:12px;color:var(--ep-gray-400)">已扣除通道成本，每小时更新</span>
  </div>
  <div class="ep-table-wrap"><table class="ep-table"><thead><tr id="profit_paytype_head"><th>日期</th></tr></thead><tbody id="profit_paytype_list"></tbody></table></div>
</div>

<script>
(function(){
  function cell(value){return $('<td>').text(value == null ? '0' : value);}
  function renderTable(headId,listId,labels,rows,today,field,totalField){
    const $head=$(headId).empty().append($('<th>').text('日期'));
    const $list=$(listId).empty();
    const keys=[];
    $.each(labels,function(key,label){keys.push(key);$head.append($('<th>').text(label));});
    $head.append($('<th>').text('总计'));
    function appendRow(name,row){
      const $tr=$('<tr>').append($('<td>').text(name));
      $.each(keys,function(_,key){$tr.append(cell(row && row[field] ? row[field][key] : 0));});
      $tr.append(cell(row ? row[totalField] : 0));
      $list.append($tr);
    }
    appendRow('今日',today);
    $.each(rows,function(date,row){appendRow(date,row);});
  }

  function getData(force){
    const $title=$('#dashboard-title').text('正在加载数据…');
    $('#refresh-dashboard').prop('disabled',true);
    $.ajax({
      type:'GET',
      url:'ajax.php?act=getcount'+(force?'&getnew=1':''),
      dataType:'json',
      success:function(data){
        $title.text('后台管理首页');
        $('#count1').text(data.count1);
        $('#count2').text(data.count2);
        $('#usermoney').text(data.usermoney);
        $('#settlemoney').text(data.settlemoney);
        $('#success_rate').text(data.success_rate);
        renderTable('#paytype_head','#paytype_list',data.paytype,data.order,data.order_today,'paytype','all');
        renderTable('#channel_head','#channel_list',data.channel,data.order,data.order_today,'channel','all');
        renderTable('#profit_paytype_head','#profit_paytype_list',data.paytype,data.order,data.order_today,'profit_paytype','profit_all');
      },
      error:function(){
        $title.text('后台管理首页');
        if(window.epToast) epToast('error','数据加载失败，请稍后重试');
      },
      complete:function(){$('#refresh-dashboard').prop('disabled',false);}
    });
  }
  $('#refresh-dashboard').on('click',function(){getData(true);});
  getData(false);
})();
</script>
<?php ep_layout_foot(); ?>
