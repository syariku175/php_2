<?php

// //DB設定読み込み
require_once __DIR__ . '/conf/database_conf.php';

// //h()関数読み込み
require_once __DIR__ . '/lib/h.php';

//validation() 関数読み込み
require_once __DIR__ . '/lib/validation.php';

try {
    //DB接続
    $db = new PDO("mysql:host=$dbServer;dbname=$dbName;charset=utf8","$dbUser","$dbPass");
    $db ->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $db ->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);


    //部品登録する部品のbuhin_idを受け取っていれば
    if(isset($_POST['buhin_id'])){
        //seihin_idバリデーション
        validation($_POST['seihin_id'],'製品ID',4);
        //部品を追加する製品のseihin_idを取得
        $seihin_id = $_POST['seihin_id'];
        //buhin_idバリデーション
        validation($_POST['buhin_id'],'部品ID',7);
        //追加する部品のbuhin_idを取得
        $buhin_id = $_POST['buhin_id'];
        //SQL準備(formationテーブルに加入するseihin_idと加わるbuhin_idを追加)
        $sql = "INSERT INTO formation (seihin_id,buhin_id) VALUES (:seihin_id,:buhin_id)";
        $prepare = $db -> prepare($sql);
        //seihin_idに挿入する変数と型を指定
        $prepare -> bindValue(':seihin_id',$seihin_id,PDO::PARAM_STR);
        //buhin_idに挿入する変数と型を指定
        $prepare -> bindValue(':buhin_id',$buhin_id,PDO::PARAM_STR);
        //クエリ実行
        $prepare -> execute();

        echo '<p>追加完了</p>';
    }
}catch (PDOException $e) {
    echo 'データベースエラー発生：' . h($e->getMessage());
}catch (Exception $e){
    echo 'エラー発生' . h($e->getMessage());
}finally{
    //出荷一覧からseihin_idを受け取っていれば
    if(isset($_POST['seihin_id'])){
        //部品を追加する製品のseihin_idを取得
        $seihin_id = $_POST['seihin_id'];
    }
    //出荷一覧からseiban_idを受け取っていたら
    if(isset($_POST['seiban_id'])){
        //seiban_idを取得
        $seiban_id = $_POST['seiban_id'];
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="utf-8">
        <title>部品登録</title>
        <link rel="stylesheet" href="//fonts.googleapis.com/css?family=Roboto:300,300italic,700,700italic">
        <link rel="stylesheet" href="//cdn.rawgit.com/milligram/milligram/master/dist/milligram.min.css">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/3.0.3/normalize.css">
		<link rel="stylesheet" href="https://milligram.github.io/styles/main.css">
    </head>
    <body>
        <main class="wrapper">
            <section class="container">
<?php
try{
    //もしseiban_idがPOST送信されてこのページに来たら
    if (isset($_POST['seiban_id'])) {
        //seiban_idバリデーション
        validation($_POST['seiban_id'],'製品ID',7);
        //seiban_idを取得
        $seiban_id = $_POST['seiban_id'];
        //seihin_idバリデーション
        validation($_POST['seihin_id'],'製品ID',4);
        //seihin_idを取得
        $seihin_id = $_POST['seihin_id'];
        //SQL準備
        $sql = 'SELECT seihin_name,seiban_name FROM seiban
            INNER JOIN seihin ON seiban.seiban_id=seihin.seiban_id
        WHERE seiban.seiban_id = :seiban_id AND seihin.seihin_id=:seihin_id';
        $prepare = $db->prepare($sql);
        //seiban_idバインド
        $prepare -> bindValue(':seiban_id',$seiban_id,PDO::PARAM_STR);
        //seihin_idバインド
        $prepare -> bindValue(':seihin_id',$seihin_id,PDO::PARAM_STR);
        //クエリ実行
        $prepare->execute();
        //出力
        foreach ($prepare as $row) {
            echo "<h3>" . h($row['seiban_name']) . "</h3>";
            echo "<h2>" . h($row['seihin_name']) . "</h2>";
        }
    }
}catch (PDOException $e) {
    echo 'データベースエラー発生：' . h($e->getMessage());
}catch (Exception $e){
    echo 'エラー発生' . h($e->getMessage());
}
?>
                <h1>部品登録</h1>
                <!--部品入力フォーム-->
                <form method="POST">
                    <fieldset>
                        <div class="colomn">
                            <label for="buhinId">部品ID</label>
                            <input type="text" name="buhin_id" maxlength="7" placeholder="(例)2019001">
                            <p>半角数字で入力してください</p>
                            <input type="hidden" name="seiban_id" value="<?= $seiban_id ?>">
                            <input type="hidden" name="seihin_id" value="<?= $seihin_id ?>">
                            <input type="submit" value="add">
                        </div>
                    </fieldset>
                </form>
                <form method="GET" action="seihin_maintenance.php">
                    <input type="hidden" name="seiban_id" value="<?= $seiban_id ?>">
                    <input type="submit" value="戻る">
                </form>
            </section>
        </main>
    </body>
</html>