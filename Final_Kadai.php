<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <style>
            body {
                font-family: Arial, sans-serif;
                background-color: #f0f8ff; /* 背景色を水色に設定 */
                color: #333;
                text-align: center;
                padding: 50px;
            }
            h2 {
                color: #4CAF50;
            }
            h3 {
                color: #ff6347;
            }
            form {
                display: inline-block;
                text-align: left;
                background-color: white;
                padding: 20px;
                border-radius: 10px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            }
            input[type="text"], input[type="password"] {
                padding: 10px;
                margin: 10px 0;
                width: 200px;
                border-radius: 5px;
                border: 1px solid #ccc;
            }
            button {
                padding: 10px 20px;
                background-color: #4CAF50;
                color: white;
                border: none;
                border-radius: 5px;
                cursor: pointer;
                font-size: 16px;
            }
            button:hover {
                background-color: #45a049;
            }
            table {
                width: 100%;
                margin-top: 20px;
                border-collapse: collapse;
            }
            th, td {
                padding: 10px;
                text-align: center;
                border: 1px solid #ccc;
            }
            th {
                background-color: #f2f2f2;
            }
            tr:nth-child(even) {
                background-color: #f9f9f9;
            }
            ul {
                text-align: left; /* リスト全体を左揃え */
                padding-left: 20px; /* リストの左の余白を追加 */
                }
        </style>
</head>
<body>

<?php
session_start(); // セッションを開始する
include('db.php'); // MySQL接続用のファイルをインクルード
include('player_edit.php'); // ユーザー編集ページ
include('quiz_edit.php'); // クイズ編集ページ
include('admin.php'); // 管理者ページ
include('quiz.php'); // クイズページ

// ログアウト処理
if (isset($_POST['Logout'])) {
    login_page("ログイン画面に戻りました");
    exit;
}

// 各ページへの遷移
if (isset($_POST['player_edit'])) {
    player_edit($_POST['user'], $_POST['pass']);
    exit;
}

if (isset($_POST['quiz_edit'])) {
    quiz_edit($_POST['user'], $_POST['pass']);
    exit;
}

if (isset($_POST['admin'])) {
    admin_page($_POST['user'], $_POST['pass']);
    exit;
}

if (isset($_POST['quiz_start'])) {
    quiz_page($_POST['user'], $_POST['pass']);
    exit;
}

if (isset($_POST['next_question'])) {
    quiz_page($_POST['user'], $_POST['pass']);
}

// 認証がない場合はログイン画面へ
if (!array_key_exists('user', $_POST)) {
    login_page("ログイン");
    exit;
}

$user = $_POST['user'];
$pass = $_POST['pass'];

// ユーザーの追加
if (isset($_POST['add_user'])) {
    $new_playername = $_POST['new_playername']; // 新しいユーザー名
    $new_playerpass = $_POST['new_playerpass']; // 新しいパスワード
    $new_overview = $_POST['new_overview']; // 新しいパスワード

    require_once('db.php'); // データベース接続
    // ユーザー名の重複チェック
    $check_query = "SELECT COUNT(*) AS count FROM player WHERE playername = ?";
    $check_stmt = mysqli_prepare($link, $check_query);
    mysqli_stmt_bind_param($check_stmt, 's', $new_playername);
    mysqli_stmt_execute($check_stmt);
    mysqli_stmt_bind_result($check_stmt, $count);
    mysqli_stmt_fetch($check_stmt);
    mysqli_stmt_close($check_stmt);

    // ユーザーがすでに存在する場合
    if ($count > 0) {
        echo "プレイヤー名が重複しています。別のプレイヤー名を使用してください。";
        player_edit($user, $pass);
        exit;
    }

    // 追加クエリ
    $query = "INSERT INTO player (playername, playerpass, overview) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($link, $query);
    mysqli_stmt_bind_param($stmt, 'sss', $new_playername, $new_playerpass,$new_overview);

    // クエリ実行
    if (mysqli_stmt_execute($stmt)) {
        echo "新しいユーザーが追加されました。";
    } else {
        echo "ユーザーの追加に失敗しました: " . mysqli_error($link);
    }

    // ユーザー編集ページに戻る
    player_edit($user, $pass);
    exit;
}

// ユーザー情報の削除
if (isset($_POST['delete_user'])) {
    $playername = $_POST['playername']; // 削除するユーザー名

    // 管理者アカウントが削除されないようにする
    if ($playername === 'Administrator') {
        echo "管理者アカウントは削除できません。";
        player_edit($user, $pass); // プレイヤー編集ページに戻る
        exit;
    }
    require_once('db.php'); // データベース接続

    // 削除クエリ
    $query = "DELETE FROM player WHERE playername = ?";
    $stmt = mysqli_prepare($link, $query);
    mysqli_stmt_bind_param($stmt, 's', $playername);

    // クエリ実行
    if (mysqli_stmt_execute($stmt)) {
        echo "ユーザーが削除されました。";
    } else {
        echo "削除に失敗しました: " . mysqli_error($link);
    }
    // ユーザー編集ページに戻る
    player_edit($user, $pass);
    exit;
}

// クイズの追加
if (isset($_POST['add_quiz'])) {
    $new_id = $_POST['new_id'];
    $new_quiz = $_POST['new_quiz']; 
    $new_option_a = $_POST['new_option_a'];
    $new_option_b = $_POST['new_option_b'];
    $new_option_c = $_POST['new_option_c'];
    $new_option_d = $_POST['new_option_d'];
    $new_correct = $_POST['new_correct'];

    if (!in_array($new_correct, ['A', 'B', 'C', 'D'])) {
        echo "正解の選択肢は A, B, C, D のいずれかでなければなりません。";
        quiz_edit($user, $pass);
        exit;
    }

    require_once('db.php'); // データベース接続

    // idの重複チェック
    $check_query = "SELECT COUNT(*) AS count FROM question WHERE id = ?";
    $check_stmt = mysqli_prepare($link, $check_query);
    mysqli_stmt_bind_param($check_stmt, 's', $new_id);
    mysqli_stmt_execute($check_stmt);
    mysqli_stmt_bind_result($check_stmt, $count);
    mysqli_stmt_fetch($check_stmt);
    mysqli_stmt_close($check_stmt);

    // idがすでに存在する場合
    if ($count > 0) {
        echo "IDが重複しています。別のIDを使用してください。";
        quiz_edit($user, $pass);
        exit;
    }
    
    // 追加クエリ
    $query = "INSERT INTO question (id, question_text,option_a,option_b,option_c,option_d,correct) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($link, $query);
    mysqli_stmt_bind_param($stmt, 'sssssss', $new_id, $new_quiz,$new_option_a,$new_option_b,$new_option_c,$new_option_d,$new_correct);

    // クエリ実行
    if (mysqli_stmt_execute($stmt)) {
        echo "新しいクイズが追加されました。";
    } else {
        echo "ユーザーの追加に失敗しました: " . mysqli_error($link);
    }

    // クイズ編集ページに戻る
    quiz_edit($user, $pass);
    exit;
}

// クイズの削除
if (isset($_POST['delete_quiz'])) {
    $id = $_POST['id']; // 削除するユーザー名

    require_once('db.php'); // データベース接続

    // 削除クエリ
    $query = "DELETE FROM question WHERE id = ?";
    $stmt = mysqli_prepare($link, $query);
    mysqli_stmt_bind_param($stmt, 's', $id);

    // クエリ実行
    if (mysqli_stmt_execute($stmt)) {
        echo "クイズが削除されました。";
    } else {
        echo "削除に失敗しました: " . mysqli_error($link);
    }
    // クイズ編集ページに戻る
    quiz_edit($user, $pass);
    exit;
}

// 管理者認証
if ($user == 'Administrator') {
    admin_page($user, $pass);
    exit;
}

// 回答後の正誤判定
if (isset($_POST['answer'])) {
    $who = $_POST['user'];
    $pass = $_POST['pass'];

    $selected_answer = $_POST['answer'];
    $correct_answer = $_POST['correct_answer'];
    
    // 現在の問題を取得
    $query = "SELECT id, question_text, option_a, option_b, option_c, option_d, correct FROM question LIMIT " . ($_SESSION['quiz_number'] - 1) . ", 1";
    $result = mysqli_query($link, $query);
    $quiz = mysqli_fetch_assoc($result);
        
    $option_a = htmlspecialchars($quiz['option_a'], ENT_QUOTES, 'UTF-8');
    $option_b = htmlspecialchars($quiz['option_b'], ENT_QUOTES, 'UTF-8');
    $option_c = htmlspecialchars($quiz['option_c'], ENT_QUOTES, 'UTF-8');
    $option_d = htmlspecialchars($quiz['option_d'], ENT_QUOTES, 'UTF-8');


    // 選択肢に対応する文を配列で定義
    $choices = [
    'A' => $option_a,
    'B' => $option_b,
    'C' => $option_c,
    'D' => $option_d
];
    if ($selected_answer === $correct_answer) {
        $_SESSION['score'] += 1; // 正解ならスコアを加算
        $_SESSION['results'][] = "問題 {$_SESSION['quiz_number']}: 正解";
        $result_message = "正解です! 正しい選択肢は: $correct_answer の{$choices[$correct_answer]}";
    } else {
        $_SESSION['results'][] = "問題 {$_SESSION['quiz_number']}: 不正解 (正解: $correct_answer)";
        $result_message = "不正解です! 正しい選択肢は: $correct_answer の{$choices[$correct_answer]}";
    }

    // クイズ番号を進める
    $_SESSION['quiz_number']++;

    // 結果を表示
    echo "<p>$result_message</p>";
    
    // 次のクイズを表示するために再度quiz_pageを呼び出す
    echo '<form method="POST" action="Final_Kadai.php">
            <input type="hidden" name="user" value="' . $_POST['user'] . '">
            <input type="hidden" name="pass" value="' . $_POST['pass'] . '">
            <button type="submit" name="next_question" value="next">次の問題へ</button>
          </form>';
    exit;
}

// プレイヤー認証
$query = "SELECT playerpass FROM player WHERE playername = ?";
$stmt = mysqli_prepare($link, $query);
mysqli_stmt_bind_param($stmt, 's', $user);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $db_pass);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

// データベースにない、もしくはパスワードが一致しない場合
if (!$db_pass || $db_pass != $pass) {
    login_page("ユーザー名またはパスワードが違います");
    exit;
}

// プレイヤーログイン成功時のページ遷移
// デフォルトではhome_pageを表示
if (!isset($_POST['quiz_start']) && !isset($_POST['answer']) && !isset($_POST['next_question'])) {
    home_page($_POST['user'] ?? 'ゲスト', $_POST['pass'] ?? '');
}

////////////////////////////////////////////////////////////////////////
function login_page($msg)
{
echo <<<EOT
<!DOCTYPE html>
<html>
<head>
    <title>ログインページ</title>            
</head>
<body>
<h2>ようこそ、クイズサイトへ！サイトに入るにはログインしてね！</h2>
<h3>$msg</h3>
<form method="POST" action="Final_Kadai.php">
    プレイヤー名: <input type="text" name="user" value=""><br>
      パスワード:   <input type="password" name="pass" value=""><br>
    <button type="submit" name="login" value="login">ログイン</button>
</form>
</body>
</html>
EOT;
}
?>
