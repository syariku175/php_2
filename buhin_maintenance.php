<?php

// //DB設定読み込み
require_once __DIR__ . '/conf/database_conf.php';

// //h()関数読み込み
require_once __DIR__ . '/lib/h.php';

try {

    //DB接続
    $db = new PDO("mysql:host=$dbServer;dbname=$dbName;charset=utf8","$dbUser","$dbPass");
    $db ->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $db ->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

    //削除ボタン押下
    if (isset($_POST['buhin_delete'])) {
        //削除するbuhin_idを取得
        $buhin_id = $_POST['buhin_id'];
        //SQL準備(buhin_idのレコードを削除)
        $sql = "DELETE FROM buhin WHERE buhin_id = :buhin_id";
        $prepare = $db -> prepare($sql);
        //buhin_idに挿入する変数と型を指定
        $prepare -> bindValue(':buhin_id',$buhin_id,PDO::PARAM_STR);
        //クエリ実行
        $prepare -> execute();

        //結果表示
        echo "<p>消去が成功しました</p>";
    }

?>

<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="utf-8">
        <title>部品メンテナンス</title>
        <link rel="stylesheet" href="//fonts.googleapis.com/css?family=Roboto:300,300italic,700,700italic">
        <link rel="stylesheet" href="//cdn.rawgit.com/milligram/milligram/master/dist/milligram.min.css">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/3.0.3/normalize.css">
		<link rel="stylesheet" href="https://milligram.github.io/styles/main.css">
    </head>
    <body>
        <main class="wrapper">
            <section class="container">
                <h2>部品メンテナンス</h2>

                <h1>部品一覧</h1>
                <div class="example">
                    <table>
                        <thead><th>部品ID</th><th>部品名</th><th></th></thead>
                        <tbody>

<?php

    //SQL準備
    $sql = 'SELECT * FROM buhin ORDER BY buhin_id';
    $prepare = $db->prepare($sql);
    //クエリ実行
    $prepare->execute();

    //部品ひとつずつrowに設定
    foreach ($prepare as $row) {
?>

                            <tr>
                                <td>
                                    <!--部品ーID表示-->
                                    <?= h($row['buhin_id']) ?>
                                </td>
                                <td>
                                    <!--部品名表示-->
                                    <?= h($row['buhin_name']) ?>
                                </td>
                                <td>
                                    <!--追加ボタン表示 POSTメソッドでbuhin_idを削除部分に渡す-->
                                    <form method="POST">
                                        <input type="submit" name="buhin_delete" value="delete">
                                        <input type="hidden" name="buhin_id" value="<?= h($row['buhin_id']) ?>">
                                    </form>
                                </td>
                            </tr>

<?php
    }
} catch (PDOException $e) {
    echo 'データベースエラー発生：' . h($e->getMessage());
}catch (Exception $e){
    echo 'エラー発生' . h($e->getMessage());
}
?>

                        </tbody>
                    </table>
                </div>
                <p>
                    <a href="buhin_insert.php">
                        部品追加
                    </a>
                </p>
                <p>
                    <a href="index.php">
                        戻る
                    </a>
                </p>
            </section>
        </main>
    </body>
</html>