<?php

//DB設定読み込み
require_once __DIR__ . '/conf/database_conf.php';

//h()関数読み込み
require_once __DIR__ . '/lib/h.php';

//validation() 関数読み込み
require_once __DIR__ . '/lib/validation.php';

try {
    //DB接続
    $db = new PDO("mysql:host=$dbServer;dbname=$dbName;charset=utf8","$dbUser","$dbPass");
    $db ->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $db ->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

    //追加ボタンが押されたら
    if (isset($_POST['seiban_insert'])) {
        //seiban_nameバリデーション
        validation($_POST['seiban_name'],'製番名','');
        //追加する製番名を取得
        $seiban_name = $_POST['seiban_name'];
        //seiban_idバリデーション
        validation($_POST['seiban_id'],'製番ID',7);
        //追加する製番IDを取得
        $seiban_id = $_POST['seiban_id'];

        //SQL準備(新規製番IDと製番名をseibanテーブルに追加)
        $sql = "INSERT INTO seiban (seiban_id,seiban_name) VALUES (:seiban_id,:seiban_name)";
        $prepare = $db -> prepare($sql);
        //seiban_idに挿入する変数と型を指定
        $prepare -> bindValue(':seiban_id',$seiban_id,PDO::PARAM_STR);
        //seiban_nameに挿入する変数と型を指定
        $prepare -> bindValue(':seiban_name',$seiban_name,PDO::PARAM_STR);
        //クエリ実行
        $prepare -> execute();

        echo '<p>追加完了</p>';
    }
} catch (PDOException $e) {
    echo 'データベースエラー発生:' . h($e->getMessage());
}
catch (Exception $e){
    echo 'その他エラー発生:' . h($e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="utf-8">
        <title>製番追加</title>
        <link rel="stylesheet" href="//fonts.googleapis.com/css?family=Roboto:300,300italic,700,700italic">
        <link rel="stylesheet" href="//cdn.rawgit.com/milligram/milligram/master/dist/milligram.min.css">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/3.0.3/normalize.css">
		<link rel="stylesheet" href="https://milligram.github.io/styles/main.css">
    </head>
    <body>
        <main class="wrapper">
            <section class="container">
                <h1 class="titles">製番追加</h1>
                <div class="example">
                    <!--製番入力フォーム-->
                    <form method="POST">
                        <fieldset>
                            <div class="colomn">
                                <label for="seibanName">製番名</label>
                                <input type="text" name="seiban_name" maxlength="30" id="seibanName" placeholder="製番名">
                                <label for="seibanID">製番ID</label>
                                <input type="text" name="seiban_id" maxlength="7" id="seibanId" placeholder="(例)201901A"><br>
                                <p>半角英数字7文字で入力してください</p>
                                <p>入力例：201901A→(2019年1回目の製番のA日程)</p>
                                <p>製番IDが被ると登録できません</p>
                                <p>前のページで他製番のIDを確認してから入力してください</p>
                                <input type="submit" name="seiban_insert" value="add">
                            </div>
                        </fieldset>
                    </form>
                </div>
                <p>
                    <a href="seiban_maintenance.php">
                        戻る
                    </a>
                </p>
            </section>

        </main>
    </body>
</html>