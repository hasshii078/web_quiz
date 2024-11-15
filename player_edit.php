<?php
include('db.php');

// プレイヤー編集ページ
function player_edit($who, $pass) {
    global $link; // $linkを関数内で使用するためにglobal宣言
    // ユーザー一覧の取得クエリ
    $query = "SELECT playername, playerpass, score, overview FROM player";
    $result = mysqli_query($link, $query);
    echo <<<EOT
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8" />
        <title>ユーザー編集ページ</title>
        <style>
            h3 {
                color: black;
            }
        </style>
    </head>
    <body>
    <h2>ユーザー編集ページ</h2>
    <form method="POST" action="Final_Kadai.php">
        <input type="hidden" name="user" value="$who">
        <input type="hidden" name="pass" value="$pass">
        <button type="submit" name="admin" value="admin">管理者画面に戻る</button>
    </form>

    <h3>新しいユーザーを追加する</h3>
    <form method="POST" action="Final_Kadai.php">
        <input type="hidden" name="user" value="$who">
        <input type="hidden" name="pass" value="$pass">
        <label for="new_playername">ユーザー名:</label>
        <input type="text" name="new_playername" required><br>
        <label for="new_playerpass">パスワード:</label>
        <input type="password" name="new_playerpass" required><br>
        <label for="new_overview">概要:</label>
        <input type="text" name="new_overview" required><br>
        <button type="submit" name="add_user" value="add_user">追加</button>
    </form>

    <h3>ユーザーを削除する</h3>
    <form method="POST" action="Final_Kadai.php">
        <input type="hidden" name="user" value="$who">
        <input type="hidden" name="pass" value="$pass">
        <label for="playername">削除するユーザー名:</label>
        <input type="text" name="playername" required><br>
        <button type="submit" name="delete_user" value="delete_user">削除</button>
    </form>

    <h3>現在のユーザー一覧</h3>
    <table border="1">
        <tr>
            <th>ユーザー名</th>
            <th>パスワード</th>
            <th>最終スコア</th>
            <th>概要</th>
        </tr>
EOT;

// 取得したユーザー一覧を表形式で出力
while ($row = mysqli_fetch_assoc($result)) {
    $playername = htmlspecialchars($row['playername'], ENT_QUOTES, 'UTF-8');
    $playerpass = htmlspecialchars($row['playerpass'], ENT_QUOTES, 'UTF-8');
    $score = htmlspecialchars($row['score']);
    $overview = htmlspecialchars($row['overview'], ENT_QUOTES, 'UTF-8');
    echo "<tr><td>$playername</td><td>$playerpass</td><td>$score</td><td>$overview</td></tr>";
}
    echo <<<EOT
    </table>
    </body>
    </html>
    EOT;
}

?>
