<?php
/**
 * 聚合收款码牌
 */
include("../includes/common.php");
if($islogin==1){}else exit("<script language='javascript'>window.location.href='./login.php';</script>");
$createSql="CREATE TABLE IF NOT EXISTS pre_onecode (
  `code` varchar(32) NOT NULL,
  `uid` int(11) DEFAULT NULL,
  `addtime` datetime DEFAULT NULL,
  `bindtime` datetime DEFAULT NULL,
  PRIMARY KEY (`code`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
if($DB->exec($createSql)===false)sysmsg('码牌数据表初始化失败：'.$DB->error());
$title='聚合收款码牌';
include './head.php';
?>
<div class="container" style="padding-top:70px;">
  <div class="col-md-12 center-block" style="float:none;">
    <div class="panel panel-primary">
      <div class="panel-heading"><h3 class="panel-title">聚合收款码牌</h3></div>
      <div class="panel-body">
        <p class="text-muted">码牌是预先印制的固定二维码。顾客扫码访问 <code>/paypage/?ucode=码牌编号</code>，系统根据这里绑定的商户发起聚合收款。</p>
        <form class="form-inline" onsubmit="return savePlate(this)">
          <div class="form-group">
            <input type="text" class="form-control" name="code" maxlength="32" placeholder="码牌编号（字母或数字）" required>
          </div>
          <div class="form-group">
            <input type="number" class="form-control" name="uid" min="1" placeholder="绑定商户ID" required>
          </div>
          <button type="submit" class="btn btn-success">新增码牌</button>
        </form>
      </div>
    </div>

    <div class="panel panel-default">
      <div class="panel-heading">码牌列表</div>
      <div class="table-responsive">
        <table class="table table-striped table-hover" style="margin-bottom:0;">
          <thead><tr><th>码牌编号</th><th>商户ID</th><th>商户名称</th><th>收款链接</th><th>操作</th></tr></thead>
          <tbody id="plateList"><tr><td colspan="5" class="text-center">正在加载...</td></tr></tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<script src="<?php echo $cdnpublic?>layer/3.1.1/layer.js"></script>
<script>
function escapeHtml(value) {
  return $('<div>').text(value == null ? '' : value).html();
}
function loadPlates() {
  $.getJSON('ajax_onecode.php?act=list', function(data) {
    if (data.code !== 0) {
      layer.alert(data.msg || '加载失败');
      return;
    }
    var html = '';
    $.each(data.data, function(_, row) {
      var link = <?php echo json_encode($siteurl.'paypage/?ucode=')?> + encodeURIComponent(row.code);
      html += '<tr>' +
        '<td><code>' + escapeHtml(row.code) + '</code></td>' +
        '<td>' + escapeHtml(row.uid || '未绑定') + '</td>' +
        '<td>' + escapeHtml(row.username || '-') + '</td>' +
        '<td><input class="form-control input-sm" value="' + escapeHtml(link) + '" readonly></td>' +
        '<td style="white-space:nowrap">' +
          '<button class="btn btn-xs btn-primary rebind-plate" data-code="' + escapeHtml(row.code) + '" data-uid="' + Number(row.uid || 0) + '">绑定</button> ' +
          '<button class="btn btn-xs btn-warning unbind-plate" data-code="' + escapeHtml(row.code) + '">解绑</button> ' +
          '<button class="btn btn-xs btn-danger delete-plate" data-code="' + escapeHtml(row.code) + '">删除</button>' +
        '</td></tr>';
    });
    $('#plateList').html(html || '<tr><td colspan="5" class="text-center">暂无码牌</td></tr>');
  });
}
function savePlate(form) {
  $.post('ajax_onecode.php?act=save', $(form).serialize(), handleResult, 'json');
  return false;
}
function rebindPlate(code, uid) {
  layer.prompt({title:'输入要绑定的商户ID', value:uid > 0 ? uid : ''}, function(value, index) {
    layer.close(index);
    $.post('ajax_onecode.php?act=save', {code:code, uid:value}, handleResult, 'json');
  });
}
function unbindPlate(code) {
  layer.confirm('确定解绑码牌 '+code+' 吗？', function(index) {
    layer.close(index);
    $.post('ajax_onecode.php?act=unbind', {code:code}, handleResult, 'json');
  });
}
function deletePlate(code) {
  layer.confirm('删除后该二维码将失效，确定删除 '+code+' 吗？', function(index) {
    layer.close(index);
    $.post('ajax_onecode.php?act=delete', {code:code}, handleResult, 'json');
  });
}
function handleResult(data) {
  if (data.code === 0) {
    layer.msg(data.msg || '操作成功', {icon:1});
    loadPlates();
  } else {
    layer.alert(data.msg || '操作失败', {icon:2});
  }
}
$('#plateList').on('click', '.rebind-plate', function(){ rebindPlate($(this).data('code'), Number($(this).data('uid'))); });
$('#plateList').on('click', '.unbind-plate', function(){ unbindPlate($(this).data('code')); });
$('#plateList').on('click', '.delete-plate', function(){ deletePlate($(this).data('code')); });
$(loadPlates);
</script>
