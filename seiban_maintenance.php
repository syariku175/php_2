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

    //削除ボタンが押されたら
    if (isset($_POST['seiban_delete'])) {
        //製番レコード削除
        //削除するseiban_idを取得
        $seiban_id = $_POST['seiban_id'];
        //SQL準備
        $sql = "DELETE FROM seiban WHERE seiban_id = :seiban_id";
        $prepare = $db -> prepare($sql);
        //seiban_idバインド
        $prepare -> bindValue(':seiban_id',$seiban_id,PDO::PARAM_STR);
        //クエリ実行
        $prepare -> execute();

        //その製番の製品レコード削除
        //SQL準備
        $sql = "DELETE FROM seihin WHERE seiban_id = :seiban_id";
        $prepare = $db -> prepare($sql);
        //seiban_idバインド
        $prepare -> bindValue(':seiban_id',$seiban_id,PDO::PARAM_STR);
        //クエリ実行
        $prepare -> execute();

        //その製番のformation情報削除
        //SQL準備
        $sql = "DELETE FROM formation
            WHERE seihin_id IN (SELECT seihin_id FROM seihin WHERE seiban_id = :seiban_id)";
        $prepare = $db -> prepare($sql);
        //seiban_idバインド
        $prepare -> bindValue(':seiban_id',$seiban_id,PDO::PARAM_STR);
        //クエリ実行
        $prepare -> execute();

        //結果表示
        echo "<p>消去成功</p>";

    }

?>

<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="utf-8">
        <title>製番メンテナンス</title>
        <link rel="stylesheet" href="//fonts.googleapis.com/css?family=Roboto:300,300italic,700,700italic">
        <link rel="stylesheet" href="//cdn.rawgit.com/milligram/milligram/master/dist/milligram.min.css">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/3.0.3/normalize.css">
		<link rel="stylesheet" href="https://milligram.github.io/styles/main.css">
    </head>
    <body>
        <main class="wrapper">
            <section class="container" id="tables">
                <h2 class="title">製番メンテナンス</h2>
                <h1 class="title">製番一覧</h1>
                <div class="example">
                    <table>
                        <thead><th>製番ID</th><th>製番</th><th></th></thead>
                        <tbody>

<?php

    //SQL準備
    $sql = 'SELECT * FROM seiban ORDER BY seiban_id';
    $prepare = $db->prepare($sql);
    //クエリ実行
    $prepare->execute();

    //製番名をひとつずつrowに設定
    foreach ($prepare as $row) {
?>

                            <tr>
                                <td>
                                    <!--製番ID表示-->
                                    <?= h($row['seiban_id']) ?>
                                </td>
                                <td>
                                    <!--製番名表示-->
                                    <?= h($row['seiban_name']) ?>
                                </td>
                                <td>
                                    <!--削除ボタン表示 POSTメソッドでseiban_idを削除部分に渡す-->
                                    <form method="POST">
                                        <input type="submit" name="seiban_delete" value="delete">
                                        <input type="hidden" name="seiban_id" value="<?= h($row['seiban_id']) ?>">
                                    </form>
                                </td>
                            </tr>

<?php
    }
}catch (PDOException $e) {
    echo 'データベースエラー発生：' . h($e->getMessage());
}catch (Exception $e){
    echo 'エラー発生' . h($e->getMessage());
}
?>

                        </tbody>
                    </table>
                </div>
                <p>
                    <a href="seiban_insert.php">
                        製番追加
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