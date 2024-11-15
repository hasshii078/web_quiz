<?php
include('db.php');
function admin_page($who, $pass) {
    echo <<<EOT
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8" />
        <title>管理者ページ</title>
    </head>
    <body>
    <h2>管理者専用ページ</h2>
    <p>プレイヤーの編集はこちらから↓</p>
    <form method="POST" action="Final_Kadai.php">
        <input type="hidden" name="user" value="$who">
        <input type="hidden" name="pass" value="$pass">
        <button type="submit" name="player_edit" value="player_edit">プレイヤー編集</button>
    </form>

    <p>クイズの編集はこちらから↓</p>
    <form method="POST" action="Final_Kadai.php">
        <input type="hidden" name="user" value="$who">
        <input type="hidden" name="pass" value="$pass">
        <button type="submit" name="quiz_edit" value="quiz_edit">クイズ編集</button>
    </form>

    <p>ログイン画面に戻る↓</p>
    <form method="POST" action="Final_Kadai.php">
        <button type="submit" name="Logout" value="Logout">ログイン画面</button>
    </form>
    </body>
    </html>
    EOT;
}


?>
