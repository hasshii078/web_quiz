<?php
include('db.php');

function quiz_edit($who, $pass) {
    global $link; // $linkを関数内で使用するためにglobal宣言
    // ユーザー一覧の取得クエリ
    $query = "SELECT id, question_text, option_a, option_b, option_c, option_d, correct FROM question";
    $result = mysqli_query($link, $query);
    
    echo <<<EOT
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8" />
        <title>クイズ編集ページ</title>
        <style>
            h3 {
                color: black;
            }
            input[type="text"], textarea {
                width: 100%;
                padding: 10px;
                margin-bottom: 12px;
                border: 1px solid #ccc;
                border-radius: 5px;
                font-size: 16px;
            }
        </style>
    </head>
    <body>
    <h2>クイズ編集ページ</h2>
    <form method="POST" action="Final_Kadai.php">
        <input type="hidden" name="user" value="$who">
        <input type="hidden" name="pass" value="$pass">
        <button type="submit" name="admin" value="admin">管理者画面に戻る</button><br>

    </form>
    <h3>新しいクイズを追加する</h3>
    <form method="POST" action="Final_Kadai.php">
        <input type="hidden" name="user" value="$who">
        <input type="hidden" name="pass" value="$pass">
        <label for="new_id">クイズid:</label>
        <input type="text" name="new_id" required><br>
        <label for="new_quiz">クイズ本文:</label><br>
        <textarea name="new_quiz" rows="5" cols="20" required></textarea><br>
        <label for="new_option_a">選択肢A:</label>
        <input type="text" name="new_option_a" required><br>
        <label for="new_option_b">選択肢B:</label>
        <input type="text" name="new_option_b" required><br>
        <label for="new_option_c">選択肢C:</label>
        <input type="text" name="new_option_c" required><br>
        <label for="new_option_d">選択肢D:</label>
        <input type="text" name="new_option_d" required><br>
        <label for="new_correct">正解の選択肢:</label>
        <input type="text" name="new_correct" required><br>
        <button type="submit" name="add_quiz" value="add_quiz">追加</button>
    </form>

    <h3>クイズを削除する</h3>
    <form method="POST" action="Final_Kadai.php">
        <input type="hidden" name="user" value="$who">
        <input type="hidden" name="pass" value="$pass">
        <label for="id">削除するクイズID:</label>
        <input type="text" name="id" required><br>
        <button type="submit" name="delete_quiz" value="delete_quiz">削除</button>
    </form>

    <h3>現在のクイズ一覧</h3>
    <table border="1">
        <tr>
            <th>クイズID</th>
            <th>クイズ内容</th>
            <th>選択肢A</th>
            <th>選択肢B</th>
            <th>選択肢C</th>
            <th>選択肢D</th>
            <th>正解</th>
        </tr>
EOT;

    // 取得したクイズ一覧を表形式で出力
    while ($row = mysqli_fetch_assoc($result)) {
        $id = htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8');
        $question_text = htmlspecialchars($row['question_text'], ENT_QUOTES, 'UTF-8');
        $option_a = htmlspecialchars($row['option_a'], ENT_QUOTES, 'UTF-8');
        $option_b = htmlspecialchars($row['option_b'], ENT_QUOTES, 'UTF-8');
        $option_c = htmlspecialchars($row['option_c'], ENT_QUOTES, 'UTF-8');
        $option_d = htmlspecialchars($row['option_d'], ENT_QUOTES, 'UTF-8');
        $correct = htmlspecialchars($row['correct'], ENT_QUOTES, 'UTF-8');
        
        echo "<tr>
                <td>$id</td>
                <td>$question_text</td>
                <td>$option_a</td>
                <td>$option_b</td>
                <td>$option_c</td>
                <td>$option_d</td>
                <td>$correct</td>
              </tr>";
    }
    echo <<<EOT
    </table>
    </body>
    </html>
    EOT;
}
?>
