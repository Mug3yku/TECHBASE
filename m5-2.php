<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="utf-8">
        <title>m5-2 掲示板</title>
    </head>
    <body>
        <?php
        //データベース接続
        $dsn = 'mysql:dbname=データベース名;host=localhost';    
        $user = 'ユーザー名';
        $password = 'パスワード';
        $pdo = new PDO($dsn, $user, $password, 
        array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
        //データベース接続部分終了

        $sql="CREATE TABLE IF NOT EXISTS board3"     //board3という表を作成
        ."("
        ."id INT AUTO_INCREMENT PRIMARY KEY,"
        ."name char(32),"
        ."comment TEXT,"
        ."date TEXT"
        .");";
        $stmt=$pdo->query($sql);

        //投稿機能開始
        $pass="123";

        if(!empty($_POST["name"]) && !empty($_POST["comment"]) 
        && !empty($_POST["pass"])){     //名前とコメントとパスワードが入力されたとき
            if($_POST["pass"] == $pass){    //パスワードが正しいとき
                $name=$_POST["name"];
                $comment=$_POST["comment"];
                $date=date("Y/m/d/H:i:s");

                if(empty($_POST["editNO"])){     //editNOが空白のときに新規投稿をする
                    $sql=$pdo->prepare("INSERT INTO board3
                    (name, comment, date) VALUES(:name, :comment, :date)");    //入力準備
                    $sql->bindParam(":name", $name, PDO::PARAM_STR);            //書き込み
                    $sql->bindParam(":comment", $comment, PDO::PARAM_STR);
                    $sql->bindParam(":date", $date, PDO::PARAM_STR);    
                    $sql->execute();    //実行
                }else{      //editNOに番号が入っているとき編集用の投稿をする
                    $editNO=$_POST["editNO"];
                    $id=$editNO;
                    $sql="UPDATE board3 SET name=:name,comment=:comment,
                    date=:date WHERE id=:id";      //内容の更新
                    $stmt=$pdo->prepare($sql);
                    $stmt->bindParam(":id", $id, PDO::PARAM_INT);
                    $stmt->bindParam(":name", $name, PDO::PARAM_STR); 
                    $stmt->bindParam(":comment", $comment, PDO::PARAM_STR);
                    $stmt->bindParam(":date", $date, PDO::PARAM_STR);
                    $stmt->execute();
                }
            }
        }


        //削除機能開始
        if(!empty($_POST["deletenum"]) && !empty($_POST["deletepass"])){    //削除番号とパスワードが入力されたとき
            if($_POST["deletepass"] == $pass){  //パスワードが正しいとき
                $deletenum=$_POST["deletenum"]; 
                
                $sql="SELECT * FROM board3";    //テーブルのすべてを選択
                $stmt = $pdo->prepare($sql);                  
                $stmt->bindParam(':id', $id, PDO::PARAM_INT); 
                $stmt->execute(); 
                $results=$stmt->fetchAll();
                foreach($results as $row){     //rowの中にはテーブルのカラムが入る
                    
                    if($deletenum == $row["id"]){    //削除番号と投稿番号が一致したとき 
                        $id=$deletenum;
                        $sql="DELETE FROM board3 WHERE id=:id";     //特定の番号を削除
	                $stmt=$pdo->prepare($sql);
	                $stmt->bindParam(":id", $id, PDO::PARAM_INT);
                        $stmt->execute();
                    }
                }   
            }
        }

        //編集機能開始
        if(!empty($_POST["edit"]) && !empty($_POST["editpass"])){       //編集番号とパスワードが入力されたとき
            if($_POST["editpass"] == $pass){        //パスワードが正しいとき
                $edit=$_POST["edit"];
                $sql="SELECT * FROM board3";    //テーブルのすべてを選択
                $stmt = $pdo->prepare($sql);                  
                $stmt->bindParam(":id", $id, PDO::PARAM_INT); 
                $stmt->execute(); 
                $results=$stmt->fetchAll();
                foreach($results as $row){
                    if($edit == $row["id"]){        //編集番号と投稿番号が一致したとき
                    $editnum=$row["id"];
                    $editname=$row["name"];     //新たな名前を取得
                    $editcomment=$row["comment"];  //新たなコメントを取得
                    }
                }
            }
        }

        ?> 
        
        
        <h1>掲示板</h1><br>
        <h3>パスワードは123です。</h3>
        <h3>名前、コメント、パスワードを入力してください</h3><br>

        <form action="" method="post">  <!--投稿フォーム -->
            <input type="text" name="name" placeholder="名前"
            value="<?php if(isset($editname)){
                echo $editname;} ?>"><br>
            <input type="text" name="comment" placeholder="コメント"
            value="<?php if(isset($editcomment)){
                echo $editcomment;} ?>">
            <input type="text" name="pass" placeholder="パスワード">
            <input type="submit" value="送信"><br>
            <input type="hidden" name="editNO" 
            value="<?php if(isset($editnum)){
                echo $editnum;} ?>">
        </form>
        
        <h3>削除したい番号とパスワードを入力してください</h3><br>
        <form action="" method="post">      <!--削除番号-->
            <input type="number" name="deletenum" placeholder="削除番号">
            <input type="text" name="deletepass" placeholder="パスワード">
            <input type="submit" value="削除"><br>
        </form>
        
        <h3>編集したい番号とパスワードを入力してください</h3><br>
        <form action="" method="post">      <!--編集番号-->
            <input type="number" name="edit" placeholder="編集番号">
            <input type="text" name="editpass" placeholder="パスワード">
            <input type="submit" value="編集"><br>
        </form>     <!--投稿フォーム終了-->


        <?php
        //ブラウザへの表示
        $sql = "SELECT * FROM board3";
        $stmt = $pdo->prepare($sql);                  
        $stmt->bindParam(":id", $id, PDO::PARAM_INT); 
        $stmt->execute();           
        $results = $stmt->fetchAll();
        foreach ($results as $row){
           echo $row["id"]." ";
           echo $row["name"]." ";
           echo $row["comment"]." ";
           echo $row["date"]."<br>";
        }
        ?>
    
    </body>
</html>
