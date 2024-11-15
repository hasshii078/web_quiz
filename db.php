<?php
#自分のSQLのものを入力
 $hostname = ' ';
 $username = ' ';
 $password = ' ';
 $dbname = ' ';

// データベースに接続
$link = mysqli_connect($hostname, $username, $password, $dbname);
// 接続確認
if (!$link) {
    die("データベースへの接続に失敗しました: " . mysqli_connect_error());
}
?>
