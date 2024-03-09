<?php

//メッセージを保存するファイルのパス設定
define('FILENAME','./message.txt');
define('IMAGEPLACE', './images_after');

//タイムゾーン設定
date_default_timezone_set('Asia/Tokyo');

//変数の初期化
$current_date = null;
$data = null;
$file_handle = null;
$split_data = null;
$message = array();
$message_array = array();
$success_message = null;
$error_message = array();

session_start();

if(!empty($_FILES)){
    #$_FILESからファイル名取得
    $filename = $_FILES['upload_image']['name'];
    #$_FILESからから保存先の取得と、images_afterというローカルフォルダに移す
    $uploaded_path = 'images_after/'.$filename;

    $result = move_uploaded_file($_FILES['upload_image']['tmp_name'],$uploaded_path);

    if($result){
        $MSG = 'アップロード成功！ファイル名：'.$filename;
        $img_path = $uploaded_path;
      }else{
        $MSG = 'アップロード失敗！エラーコード：'.$_FILES['upload_image']['error'];
      }
}
    
if ( !empty($_POST['btn_submit']) ){

    if(empty($_POST['view_name'])){
        $error_message[] = '表示名を入力してください。';
    }

    //メッセージの入力チェック
    if(empty($_POST['message'])){
        $error_message[] = '一言メッセージを入力してください';
    }

    if(empty($error_message)){
        if($file_handle = fopen(FILENAME,"a")){
        
            //書き込み日時の取得
            $current_date = date("Y-m-d H:i:s");

            //書き込むデータの作成
            $data = "'".$_POST['view_name']."','".$_POST['message']."','".$current_date."','".$uploaded_path."'\n";

            //書き込み
            fwrite($file_handle, $data);

            //ファイルを閉じる
            fclose($file_handle);

       
        }

        $_SESSION['success_message'] = 'メッセージを書き込みました。';
        header('Location: ./');
		exit();
        }

}

if( $file_handle = fopen(FILENAME,'r') ) {
    while( $data = fgets($file_handle) ){
        $split_data = preg_split( '/\'/', $data);

        $message = array(
            'view_name' => $split_data[1],
            'message' => $split_data[3],
            'post_date' => $split_data[5],
            'img_data' => $split_data[7]
        );
        array_unshift( $message_array, $message);
    }

    // ファイルを閉じる
    fclose( $file_handle);
}

?>

<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8">
<title>BBS</title>
<style>
<<<<<<< HEAD
    #box{overflow-y:scroll;height:500px;background:#FFF;width:95%;right:0;left:0;margin:auto;top:10px;position:relative;border:solid 4px #000;border-radius:40px;}
    #moveBtn{
        font-size:40px;color:#FFF;background-color:#00CCCC;
        right:0;left:35%;bottom:65px;margin:auto;padding:5px;position:relative;border-radius:100%;text-align:center;height:52px;width:52px;z-index:2;}
    .showImg{width:35%;}
    .time{font-size:20px;background:#ffcc00;}
    .lineHeight{line-height:0.5px;}
=======
    #innerBody{margin:auto;position:relative;top:360px;/*background:red;*/}
    #form-box{height:300px;background:#FFF;width:95%;right:0;left:0;margin:auto;bottom:30px;position:relative;border:solid 4px #000;border-radius:40px;}
    #box{overflow-y:scroll;height:700px;background:#FFF;width:95%;right:0;left:0;margin:auto;top:10px;position:relative;border:solid 4px #000;border-radius:40px;}
    #moveBtn{
        font-size:75px;color:#FFF;background-color:#00CCCC;
        right:0;left:35%;bottom:65px;margin:auto;padding:5px;position:relative;border-radius:100%;text-align:center;height:92px;width:92px;z-index:2;}
    .showImg{width:95%;}
    .time{font-size:20px;background:#ffcc00;}
    .lineHeight{line-height:0.5px;}
    table{border-bottom:solid 2px #000; /*background:red;*/}
    td{flex-wrap: wrap;}
    .box0{width:95%; position:relative; right:0; left:0; margin:auto;/*background:skyblue;*/}
    .td0{width:15%; font-size:24px; /*background:red;*/} /*time*/
    .td1{width:65%; /*background:yellow;*/} /*msg*/ .msg{font-size:30px;}
    .td2{width:20%; text-align:center; /*background:green;*/} /*photo*/
    .usrName{line-height:1px;}
>>>>>>> 16a401b (feature:掲示板見た目調整)
</style>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.7.1/css/lightbox.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.7.1/js/lightbox.min.js" type="text/javascript"></script>
</head>
<body>
<<<<<<< HEAD
<h1>ひと言掲示板</h1>
<?php if( !empty($success_message) ): ?>
    <p class="success_message"><?php echo $success_message; ?></p>
<?php endif; ?>
<?php if( !empty($error_message) ): ?>
	<ul class="error_message">
		<?php foreach( $error_message as $value ): ?>
			<li><?php echo $value; ?></li>
		<?php endforeach; ?>
	</ul>
<?php endif; ?>
<form method="post", enctype = "multipart/form-data">
	<div>
		<label for="view_name">表示名</label>
		<input id="view_name" type="text" name="view_name" value="">
	</div>
	<div>
		<label for="message">ひと言メッセージ</label>
		<textarea id="message" name="message"></textarea>
	</div>
    <div>
    <input type = "file", name = "upload_image">
    </div>
	<input type="submit" name="btn_submit" value="書き込む">
</form>
<!-- ここにメッセージの入力フォームを設置 -->
<hr>
<div id="box">
    <!-- ここに投稿されたメッセージを表示 -->
    <?php if( !empty($message_array) ): ?>
    <?php foreach( $message_array as $value ): ?>
    <table>
        <h2><?php echo $value['view_name']; ?></h2>
        <td>
            <!--<time class="time">--><!--時間-->
                <p><?php echo date('m月d日', strtotime($value['post_date'])); ?></p>
                <p class="lineHeight"><?php echo date('H:i', strtotime($value['post_date'])); ?></p>
            <!--</time>-->
        </td>
        <td><?php echo $value['message']; ?></td><!--メッセージを表示しているところ-->
        <!--画像を表示している箇所-->
        <td>
            <!--<?php if(!empty($MSG)) echo $MSG;?>--><!--画像のファイル名, 後で削除-->

            <?php if(!empty($img_path)){;?>
                <img class="showImg" src = "<?php echo $value['img_data'];?>" alt="">
            <?php }; ?>
        </td>
        
    </table>
    <?php endforeach; ?>
    <?php endif; ?>
</div>
=======
<div id="innerBody">
    <?php if( !empty($error_message) ): ?>
        <ul class="error_message">
            <?php foreach( $error_message as $value ): ?>
                <li><?php echo $value; ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
    <form method="post", enctype = "multipart/form-data">
        <table id="form-box">
            <tr>
                <td>
                    <label for="view_name">name</label>
                    <input id="view_name" type="text" name="view_name" value="">
                </td>
            </tr>
            <tr>
                <td>
                    <label for="message">message</label><br>
                    <textarea id="message" name="message"></textarea>
                </td>
            </tr>
            <tr>
                <td>
                    <input type = "file", name = "upload_image">
                    <input type="submit" name="btn_submit" value="書き込む">
                    <?php if( !empty($success_message) ): ?>
                        <p class="success_message"><?php echo $success_message; ?></p>
                    <?php endif; ?>
                </td>
            </tr>
        </table>
    </form>
    <!-- ここにメッセージの入力フォームを設置 -->
    <div id="box">
        <!-- ここに投稿されたメッセージを表示 -->
        <div class="box0">
            <?php if( !empty($message_array) ): ?>
            <?php foreach( $message_array as $value ): ?>
                <table>
                    <td class="td0"><!--投稿時間-->
                        <p><?php echo date('m月d日', strtotime($value['post_date'])); ?></p>
                        <p class="lineHeight"><?php echo date('H:i', strtotime($value['post_date'])); ?></p>
                    </td>

                    <td class="td1"><!--メッセージを表示しているところ-->
                        <h1 class="usrName"><?php echo $value['view_name']; ?></h1>
                        <p class="msg"><?php echo $value['message']; ?></p>
                    </td>

                    <td class="td2"><!--画像を表示している箇所-->
                        <!--<?php if(!empty($MSG)) echo $MSG;?>--><!--画像のファイル名, 後で削除-->
                        <?php if(!empty($img_path)){;?>
                            <a href="<?php echo $value['img_data'];?>" data-lightbox="group"><img class="showImg" src = "<?php echo $value['img_data'];?>" alt="">
                        <?php }; ?>
                    </td>
                </table>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
>>>>>>> 16a401b (feature:掲示板見た目調整)

    <p id="moveBtn" onclick="moveTop()">↑</p><!--スクロール用ボタン-->
</div>
<script>
    let box=document.getElementById("box");
    let topBtn=class{
    constructor(y,blue,defaultc){
        this.y=y;
        this.blue=blue;
        this.defaultc=defaultc;
    }
    }
    let topInstance=new topBtn(0,"background:blue;","background:#00BFFF;");
    let moveBtn=document.getElementById("moveBtn");
    let moveTop=function(n=true){
    moveBtn.style=topInstance.blue;
    if(n==true && box.scrollTop>topInstance.y){
        let cnt=0;
        function cntUp(){
        cnt++;
        let time=setTimeout(cntUp,100);
        console.log(cnt);
        if(cnt==2){
            clearTimeout(time);
            console.log("quit");
            moveBtn.style=topInstance.defaultc;
        }
        }
        cntUp();
    }
    else{moveBtn.style=topInstance.defaultc;}
    box.scrollTop=topInstance.y; //0
    }
</script>
</body>
</html>