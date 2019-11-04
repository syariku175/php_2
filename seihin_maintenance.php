<?php

// //DB設定読み込み
require_once __DIR__ . '/conf/database_conf.php';

// //h()関数読み込み
require_once __DIR__ . '/lib/h.php';

//例外処理
try {
    //DB接続
    $db = new PDO("mysql:host=$dbServer;dbname=$dbName;charset=utf8","$dbUser","$dbPass");
    $db ->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $db ->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

    //製品削除ボタンが押されたら
    if (isset($_POST['seihin_delete'])) {
        //削除するseihin_idを取得
        $seihin_id = $_POST['seihin_id'];
        //製品テーブルのseihin_idのレコードを削除
        //SQL準備
        $sql = "DELETE FROM seihin WHERE seihin_id = :seihin_id";
        $prepare = $db -> prepare($sql);
        //seihin_idバインド
        $prepare -> bindValue(':seihin_id',$seihin_id,PDO::PARAM_STR);
        //クエリ実行
        $prepare -> execute();

        //フォーメーションテーブルのseihin_idのレコードを削除
        //SQL準備
        $sql = "DELETE FROM formation WHERE seihin_id = :seihin_id";
        $prepare = $db -> prepare($sql);
        //seiban_idに挿入する変数と型を指定
        $prepare -> bindValue(':seihin_id',$seihin_id,PDO::PARAM_STR);
        //クエリ実行
        $prepare -> execute();

        //結果表示
        echo "<p>製品消去成功</p>";
    }

    //部品削除ボタンが押されたら
    if(isset($_POST['buhin_delete'])){
        //削除するseihin_idを取得
        $seihin_id = $_POST['seihin_id'];
        //formationテーブルのseihin_idのレコードを削除
        //SQL準備
        $sql = "DELETE FROM formation WHERE seihin_id = :seihin_id";
        $prepare = $db -> prepare($sql);
        //seihin_idバインド
        $prepare -> bindValue(':seihin_id',$seihin_id,PDO::PARAM_STR);
        //クエリ実行
        $prepare -> execute();

        //結果表示
        echo "<p>部品消去成功</p>";
    }

?>

<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="utf-8">
        <title>製品メンテナンス</title>
        <link rel="stylesheet" href="//fonts.googleapis.com/css?family=Roboto:300,300italic,700,700italic">
        <link rel="stylesheet" href="//cdn.rawgit.com/milligram/milligram/master/dist/milligram.min.css">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/3.0.3/normalize.css">
		<link rel="stylesheet" href="https://milligram.github.io/styles/main.css">
    </head>
    <body>
        <main class="wrapper">
            <section class="container">
<?php
    //もしseiban_idがGET送信されてこのページに来たら
    if (isset($_GET['seiban_id'])) {
        //seiban_idを取得
        $seiban_id = $_GET['seiban_id'];
        //SQL準備
        $sql = 'SELECT * FROM seiban WHERE seiban_id = :seiban_id';
        $prepare = $db->prepare($sql);
        //バインド
        $prepare -> bindValue(':seiban_id',$seiban_id,PDO::PARAM_STR);
        //クエリ実行
        $prepare->execute();
        //出力
        foreach ($prepare as $row) {
            echo "<h2>" . h($row['seiban_name']) . "</h2>";
        }
    }
?>
                <h1>出荷一覧</h1>
                <div class="example">
                    <table>
                        <thead>
                            <th>出荷順</th>
                            <th>製番</th>
                            <th>製品名</th>
                            <th>部品</th>
                            <th>リードタイム</th>
                            <th>製番削除</th>
                            <th>製品情報更新</th>
                            <th>部品登録</th>
                            <th>部品削除</th>
                        </thead>
                        <tbody>

<?php

    //もし製番がPOST送信されてこのページに来たら
    if (isset($_GET['seiban_id'])) {
        //SQL準備
        $sql = 'SELECT * from seihin
            WHERE seiban_id=:seiban_id
            ORDER BY performance_num';
        $prepare = $db->prepare($sql);
        //バインド
        $prepare -> bindValue(':seiban_id',$seiban_id,PDO::PARAM_STR);
        //クエリ実行
        $prepare->execute();

        //製番をひとつずつrowに設定
        foreach ($prepare as $row) {
            //部品SELECT用に製品IDを変数に設定
            $seihin_id = $row['seihin_id'];
?>

                            <tr>
                                <td>
                                    <!--出荷順番表示-->
                                    <?= h($row['performance_num']) ?>
                                </td>
                                <td>
                                    <!--製品名表示-->
                                    <?= h($row['seihin_id']) ?>
                                </td>
                                <td>
                                    <!--部品名表示-->
                                    <?= h($row['seihin_name']) ?>
                                </td>
                                <td>

<!--部品表示-->
<?php
            //SQL準備
            $sql = "SELECT
            buhin.buhin_name
            FROM buhin
                INNER JOIN formation
                    ON buhin.buhin_id=formation.buhin_id
                INNER JOIN seihin
                    ON formation.seihin_id=seihin.seihin_id
            WHERE seihin.seiban_id=:seiban_id AND seihin.seihin_id=:seihin_id";
            $prepare = $db->prepare($sql);
            //製番でバインド
            $prepare -> bindValue(':seiban_id',$seiban_id,PDO::PARAM_STR);
            //製品でバインド
            $prepare -> bindValue(':seihin_id',$seihin_id,PDO::PARAM_STR);
            //クエリ実行
            $prepare->execute();
            //部品名をひとつずつrowに設定
            foreach ($prepare as $row_n) {
                echo h($row_n['buhin_name']). "/";
            }
?>
                                </td>
                                <td>
                                    <!--スケジュール表示-->
                                    <?= h($row['performance_time']) ?>
                                </td>
                                <td>
                                    <!--削除ボタン表示 POSTメソッドでseihin_idを削除部分に渡す-->
                                    <form method="POST">
                                        <input type="hidden" name="seihin_id" value="<?= $row['seihin_id'] ?>">
                                        <input type="submit" name="seihin_delete" value="delete">
                                    </form>
                                </td>
                                <td>
                                    <!--製品情報更新ボタン表示 POSTメソッドでseihin_idを変更画面に渡す-->
                                    <form method="POST" action="seihin_update.php">
                                        <input type="hidden" name="seihin_id" value="<?= $row['seihin_id'] ?>">
                                        <input type="hidden" name="seiban_id" value="<?= $seiban_id ?>">
                                        <input type="submit" value="update">
                                    </form>
                                </td>
                                <td>
                                    <!--部品登録ボタン表示 POSTメソッドでseihin_idを追加画面に渡す-->
                                    <form method="POST" action="formation.php">
                                        <input type="hidden" name="seihin_id" value="<?= $row['seihin_id'] ?>">
                                        <input type="hidden" name="seiban_id" value="<?= $seiban_id ?>">
                                        <input type="submit" value="entry">
                                    </form>
                                </td>
                                <td>
                                    <!-- 製品部品ー削除（一括） -->
                                    <form method="POST">
                                        <input type="hidden" name="seihin_id" value="<?= $row['seihin_id'] ?>">
                                        <input type="submit" name="buhin_delete" value="buhin">
                                    </form>
                                </td>
                            </tr>

<?php
        }
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

                <p>製品追加</p>
                <p>
                    <!-- 製品追加画面へ(seiban_idを渡す)-->
                    <form method="POST" action="seihin_insert.php">
                        <input type="hidden" name="seiban_id" value="<?= $seiban_id ?>">
                        <input type="submit" value="add">
                    </form>
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