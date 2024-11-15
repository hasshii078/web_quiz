<?php
include('db.php');
function home_page($who, $pass)
{
echo <<<EOT
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <title>スタート待機ページ</title>
</head>
<body>
<p>こんにちは $who さん</p>
<p>早速クイズをはじめましょう！</p>
<form method="POST" action="Final_Kadai.php">
    <input type="hidden" name="user" value="$who">
    <input type="hidden" name="pass" value="$pass">
    <button type="submit" name="quiz_start" value="quiz_start">スタート</button>
</form><br>

<form method="POST" action="Final_Kadai.php">
    <button type="submit" name="Logout" value="Logout">ログイン画面に戻る</button>
</form>
</body>
</html>
EOT;
}

function quiz_page($who, $pass)
{ 
    global $link; // データベース接続用変数

    // 現在のクイズ番号をセッションで管理（進行状況を保持）
    if (!isset($_SESSION['quiz_number'])) {
        $_SESSION['quiz_number'] = 1; // 最初の問題
        $_SESSION['score'] = 0; // 初期スコア
    }

    // 問題数を取得
    $total_questions = mysqli_query($link, "SELECT COUNT(*) AS total FROM question");
    $total_questions = mysqli_fetch_assoc($total_questions)['total'];

    // 最後の問題まで進んだら結果画面へ
    if ($_SESSION['quiz_number'] > $total_questions) {
        quiz_result_page($who, $pass); // 結果画面に遷移
        return;
    }

    // 現在の問題を取得
    $query = "SELECT id, question_text, option_a, option_b, option_c, option_d, correct FROM question LIMIT " . ($_SESSION['quiz_number'] - 1) . ", 1";
    $result = mysqli_query($link, $query);
    $quiz = mysqli_fetch_assoc($result);

    if ($quiz) {
        $question_text = htmlspecialchars($quiz['question_text'], ENT_QUOTES, 'UTF-8');
        $option_a = htmlspecialchars($quiz['option_a'], ENT_QUOTES, 'UTF-8');
        $option_b = htmlspecialchars($quiz['option_b'], ENT_QUOTES, 'UTF-8');
        $option_c = htmlspecialchars($quiz['option_c'], ENT_QUOTES, 'UTF-8');
        $option_d = htmlspecialchars($quiz['option_d'], ENT_QUOTES, 'UTF-8');
        $correct_answer = $quiz['correct'];
    } else {
        echo "クイズ全問終了。";
        quiz_result_page($who, $pass);
        return;
    }

echo <<<EOT
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <title>クイズページ</title>
    <style>
        h2 {
            text-align: center;
        }
        .quiz-container {
            width: 50%;
            margin-left: 0 auto;
            background-color: #ffffff;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            margin: 5px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
            text-align: left; /* ボタン内のテキストを左揃え */
        }
        button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
<h2>クイズ: $question_text</h2>
    <form method="POST" action="Final_Kadai.php">
        <input type="hidden" name="user" value="$who">
        <input type="hidden" name="pass" value="$pass">
        <input type="hidden" name="correct_answer" value="$correct_answer">
        <input type="hidden" name="quiz_number" value="{$_SESSION['quiz_number']}">
        
        <button type="submit" name="answer" value="A">A. $option_a</button><br>
        <button type="submit" name="answer" value="B">B. $option_b</button><br>
        <button type="submit" name="answer" value="C">C. $option_c</button><br>
        <button type="submit" name="answer" value="D">D. $option_d</button><br>
    </form>
</body>
</html>
EOT;
}
function quiz_result_page($who, $pass)
{ 
    global $link; // データベース接続用変数
    // セッションをリセットする前にスコアを取得
    $score = isset($_SESSION['score']) ? $_SESSION['score'] : 0;
    // 追加クエリ
    $query = "UPDATE player SET score = ? WHERE playername = ?";
    $stmt = mysqli_prepare($link, $query);
    $score = (int)$score; // scoreを整数にキャスト
    mysqli_stmt_bind_param($stmt, 'is', $score, $who);
    mysqli_stmt_execute($stmt);
    
    echo <<<EOT
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <title>クイズ結果</title>
       <style>
        h2{
            text-align: center;
        }
        h3{
            color: black;
            text-align: left; /* 左揃え */
        }
    </style>
</head>
<body>
<h2>クイズの結果</h2>
<p>あなたのスコアは $score 点です。</p>
<h3>各問題の結果:</h3>
<ul>
EOT;
 // セッション内の各問題の結果を表示
 if (isset($_SESSION['results'])) {
    foreach ($_SESSION['results'] as $result) {
        echo "<li>" . htmlspecialchars($result, ENT_QUOTES, 'UTF-8') . "</li>";
    }
}

echo <<<EOT
</ul>
<form method="POST" action="Final_Kadai.php">
    <input type="hidden" name="user" value="$who">
    <input type="hidden" name="pass" value="$pass">
</form>

<form method="POST" action="Final_Kadai.php">
    <button type="submit" name="Logout" value="Logout">ログイン画面に戻る</button>
</form>
</body>
</html>
EOT;
    // セッションをリセットして、他のユーザーが次回のクイズを開始できるようにする
    session_unset();  // セッション変数の全てを解除
    session_destroy();  // セッションを完全に破棄
}

?>
