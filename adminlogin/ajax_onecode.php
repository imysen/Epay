<?php
include("../includes/common.php");
if($islogin==1){}else exit(json_encode(['code'=>-3, 'msg'=>'No Login']));
if(!checkRefererHost())exit(json_encode(['code'=>403, 'msg'=>'Forbidden']));

@header('Content-Type: application/json; charset=UTF-8');
$act=isset($_GET['act'])?trim($_GET['act']):'';

$createSql="CREATE TABLE IF NOT EXISTS pre_onecode (
  `code` varchar(32) NOT NULL,
  `uid` int(11) DEFAULT NULL,
  `addtime` datetime DEFAULT NULL,
  `bindtime` datetime DEFAULT NULL,
  PRIMARY KEY (`code`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
if($DB->exec($createSql)===false)exit(json_encode(['code'=>-1, 'msg'=>'码牌数据表初始化失败：'.$DB->error()]));

switch($act){
case 'list':
    $rows=$DB->getAll("SELECT A.code,A.uid,B.username FROM pre_onecode A LEFT JOIN pre_user B ON A.uid=B.uid ORDER BY A.code ASC");
    if($rows===false)exit(json_encode(['code'=>-1, 'msg'=>'码牌列表加载失败：'.$DB->error()]));
    exit(json_encode(['code'=>0, 'data'=>$rows]));
case 'save':
    if($_SERVER['REQUEST_METHOD']!=='POST')exit(json_encode(['code'=>405, 'msg'=>'Method Not Allowed']));
    $code=strtoupper(trim($_POST['code']));
    $uid=intval($_POST['uid']);
    if(!preg_match('/^[A-Z0-9]{1,32}$/', $code))exit(json_encode(['code'=>-1, 'msg'=>'码牌编号只能包含1到32位字母或数字']));
    $user=$DB->find('user', 'uid', ['uid'=>$uid]);
    if(!$user)exit(json_encode(['code'=>-1, 'msg'=>'绑定商户不存在']));
    $exists=$DB->find('onecode', 'code,uid', ['code'=>$code]);
    if($exists && !empty($exists['uid']) && intval($exists['uid'])!==$uid){
        exit(json_encode(['code'=>-1, 'msg'=>'该码牌已绑定商户ID '.$exists['uid'].'，请先解绑后再换绑']));
    }
    if($exists){
        $result=$DB->update('onecode', ['uid'=>$uid, 'bindtime'=>'NOW()'], ['code'=>$code]);
    }else{
        $result=$DB->insert('onecode', ['code'=>$code, 'uid'=>$uid, 'addtime'=>'NOW()', 'bindtime'=>'NOW()']);
    }
    if($result!==false){
        $DB->insert('log', ['uid'=>0, 'type'=>'绑定码牌', 'date'=>'NOW()', 'ip'=>$clientip, 'data'=>json_encode(['code'=>$code, 'uid'=>$uid])]);
        exit(json_encode(['code'=>0, 'msg'=>'码牌已绑定']));
    }
    exit(json_encode(['code'=>-1, 'msg'=>'保存失败：'.$DB->error()]));
case 'unbind':
    if($_SERVER['REQUEST_METHOD']!=='POST')exit(json_encode(['code'=>405, 'msg'=>'Method Not Allowed']));
    $code=strtoupper(trim($_POST['code']));
    if(!preg_match('/^[A-Z0-9]{1,32}$/', $code))exit(json_encode(['code'=>-1, 'msg'=>'码牌编号错误']));
    $result=$DB->update('onecode', ['uid'=>null, 'bindtime'=>null], ['code'=>$code]);
    if($result!==false){
        $DB->insert('log', ['uid'=>0, 'type'=>'解绑码牌', 'date'=>'NOW()', 'ip'=>$clientip, 'data'=>json_encode(['code'=>$code])]);
        exit(json_encode(['code'=>0, 'msg'=>'码牌已解绑']));
    }
    exit(json_encode(['code'=>-1, 'msg'=>'解绑失败：'.$DB->error()]));
case 'delete':
    if($_SERVER['REQUEST_METHOD']!=='POST')exit(json_encode(['code'=>405, 'msg'=>'Method Not Allowed']));
    $code=strtoupper(trim($_POST['code']));
    if(!preg_match('/^[A-Z0-9]{1,32}$/', $code))exit(json_encode(['code'=>-1, 'msg'=>'码牌编号错误']));
    $result=$DB->delete('onecode', ['code'=>$code]);
    if($result!==false){
        $DB->insert('log', ['uid'=>0, 'type'=>'删除码牌', 'date'=>'NOW()', 'ip'=>$clientip, 'data'=>json_encode(['code'=>$code])]);
        exit(json_encode(['code'=>0, 'msg'=>'码牌已删除']));
    }
    exit(json_encode(['code'=>-1, 'msg'=>'删除失败：'.$DB->error()]));
default:
    exit(json_encode(['code'=>-1, 'msg'=>'No Act']));
}
