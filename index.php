<?php
if (version_compare(PHP_VERSION, '7.4.0', '<')) {
    die('require PHP >= 7.4 !');
}

$mod = isset($_GET['mod'])?$_GET['mod']:'index';
$is_defend = true;
$allow_search = true;
include("./includes/common.php");

if(!isset($_GET['doc']) && $mod=='index'){
    $root_redirect = isset($conf['root_redirect_url']) ? trim($conf['root_redirect_url']) : '';
    if($root_redirect !== '' && (substr($root_redirect,0,7)=='http://' || substr($root_redirect,0,8)=='https://')){
        header('Location: '.$root_redirect, true, 302);
        exit;
    }
    http_response_code(404);
    exit;
}

if(isset($_GET['doc'])){
    $doc = trim($_GET['doc']);
    if(!$conf['apiurl'])$conf['apiurl'] = $siteurl;
    $loadfile = \lib\Template::loadDoc($doc);
    include $loadfile;
    exit;
}

$loadfile = \lib\Template::load($mod);
include $loadfile;
