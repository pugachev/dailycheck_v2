<?php
include 'lib/connect.php';
include 'lib/daily.php';
include 'lib/queryDaily.php';

$today="";
if ((!empty($_GET['tgtyyyymm']))) 
{
    //「前月へ」と「翌月へ」から取得したyyyyMMを使用する
    $today = $_GET['tgtyyyymm'];
}
else
{
    //画面の初回表示時点のyyyyMMをシステムから取得して使用する
    $today = date("Y/m");
}



$daily = new QueryDaily();
$results = $daily->findAll($today);


$json=json_encode($results,JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);

// print_r($json);
// die();
echo $json;

?>