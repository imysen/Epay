<?php
if(!defined('IN_CRONLITE'))exit();
?>
<div class="ep-card">
  <div class="ep-card-head">
    <h2>微信参数配置查询</h2>
    <button type="button" class="ep-btn ep-btn-secondary ep-btn-sm" onclick="window.location.reload()"><?=ep_icon('refresh',14)?>刷新</button>
  </div>
  <div class="ep-card-body">
    <div class="ep-detail-grid" style="padding:0">
      <span class="k">已关联的公众号 AppID</span>
      <span class="v" style="text-align:left"><?php foreach($data['appid_config_list'] as $row){ echo htmlspecialchars($row['sub_appid']).'<br>'; }?></span>
      <span class="k">JSAPI 支付授权目录</span>
      <span class="v" style="text-align:left"><?php foreach($data['jsapi_path_list'] as $row){ echo htmlspecialchars($row).'<br>'; }?></span>
    </div>
  </div>
</div>

<div class="ep-card">
  <div class="ep-card-head"><h2>微信参数配置</h2></div>
  <div class="ep-card-body">
    <form action="./plugin_page.php?channel=<?php echo (int)$channel['id']?>&func=wxconfig" method="POST" class="ep-plugin-form">
      <div class="ep-plugin-field">
        <label for="sub_appid">公众号 AppID</label>
        <input id="sub_appid" type="text" name="sub_appid" value="<?php echo htmlspecialchars($data['appid'])?>" placeholder="只能填写已认证的服务号，且必须与当前主体或渠道商主体一致" class="ep-input" required>
      </div>
      <div class="ep-plugin-field">
        <label for="jsapi_path">JSAPI 支付授权目录</label>
        <input id="jsapi_path" type="text" name="jsapi_path" value="<?php echo htmlspecialchars($siteurl)?>" placeholder="必须以 http:// 或 https:// 开头，以 / 结尾" class="ep-input" required>
      </div>
      <div class="ep-plugin-actions"><button type="submit" name="submit" value="提交" class="ep-btn ep-btn-primary">提交保存</button></div>
    </form>
  </div>
</div>
<style>
.ep-plugin-form{max-width:680px}.ep-plugin-field{margin-bottom:18px}.ep-plugin-field label{display:block;font-size:13px;color:var(--ep-gray-600);margin-bottom:6px}.ep-plugin-field .ep-input{width:100%}.ep-plugin-actions{display:flex;justify-content:flex-end}
</style>
