# 易支付系统

自用改版易支付，当前分支为单管理员模式：后台目录为 `adminlogin/`，商户自助注册和用户中心功能已禁用。

## 默认后台账号

首次安装后默认后台信息如下：

| 项目 | 默认值 | 用途 |
| --- | --- | --- |
| 后台地址 | `/adminlogin/` | 管理员登录入口 |
| 管理员账号 | `admin` | 后台登录用户名 |
| 管理员登录密码 | `123456` | 登录后台使用 |
| 支付密码 | `123456` | 后台转账、API 退款等敏感操作使用 |

> 首次登录后必须立即修改管理员登录密码和支付密码，不要在生产环境继续使用默认密码。

## 首次安装配置

1. 将源码完整上传到网站根目录，网站运行目录直接指向项目根目录。
2. 创建 MySQL/MariaDB 数据库和数据库用户。
3. 确认 PHP 已启用常用扩展：`pdo_mysql`、`curl`、`openssl`、`mbstring`、`json`、`session`。
4. 访问 `/install/`，按安装向导填写数据库地址、端口、用户名、密码和数据库名。
5. 安装完成后确认存在 `install/install.lock`。如果安装程序提示无法写入，请手动在 `install/` 目录创建 `install.lock` 文件。
6. 单管理员版本需要 `pre_onecode` 表；如果安装后后台码牌功能报表不存在，请手动导入 `install/single_admin.sql`。
7. 进入 `/adminlogin/`，使用默认账号登录。
8. 登录后进入后台账号配置页面，立即修改：
   - 管理员账号/登录密码；
   - 支付密码。
9. 配置系统网址、HTTPS、伪静态和易支付接口参数。
10. 完成支付宝、微信各一笔小额支付测试，并测试退款后再正式上线。

## 伪静态配置

支付通知、支付返回和部分拉起支付页面依赖 `/pay/` 路由。生产环境必须配置伪静态，否则 `/pay/notify/订单号/`、`/pay/return/订单号/` 等地址会 404，异步通知无法正常处理，订单也不会自动变为已支付。

### Nginx / 宝塔 Nginx

宝塔路径：

```text
网站 → 选择站点 → 伪静态
```

填入：

```nginx
location /pay/ {
    rewrite ^/pay/(.*)$ /pay.php?s=$1 last;
}
```

保存后重载 Nginx。

### Apache

如果使用 Apache，在网站根目录 `.htaccess` 中加入：

```apache
<IfModule mod_rewrite.c>
RewriteEngine On
RewriteRule ^pay/(.*)$ pay.php?s=$1 [L,QSA]
</IfModule>
```

### 需要验证的地址

伪静态配置后，以下格式的地址应由 `pay.php` 接收处理：

```text
/pay/notify/订单号/
/pay/return/订单号/
/pay/alipay/订单号/
/pay/wxpay/订单号/
```

如果这些地址返回 Web 服务器 404，说明伪静态没有生效。

## 重要注意事项

- `config.php` 保存数据库连接配置，生产环境升级或覆盖代码时不要用本地文件覆盖生产 `config.php`。
- `install/install.lock` 是安装锁，生产环境必须保留；不要删除后暴露安装入口。
- 支付通知和返回地址依赖伪静态规则，例如 `/pay/notify/订单号/`、`/pay/return/订单号/`。
- 本项目没有 npm 构建流程，部署时直接上传 PHP、CSS、JS 和插件文件即可。
