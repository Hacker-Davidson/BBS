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
    body{margin:auto;background:#dcbc55;}
    .display-flex{display:flex;width:92%;margin:auto;/*background:red;*/}
    header{background:#376169;width:100%;margin:auto;}
    #header{font-size:50px;text-align:center;padding-top:80px;color:#FFF;}
    #innerBody{margin:auto;position:relative;top:375px;/*background:red;*/}
    #form-box{height:488px;background:#FFF;width:95%;right:0;left:0;margin:auto;bottom:50px;position:relative;border:solid 3px #000;border-radius:40px;}
    .form-box0{width:92%;margin:auto;}
    .title{font-size:30px;font-weight:bolder;right:0;left:0;}
    input, textarea{border:2px solid #000;box-sizing:border-box;}
    .centre{text-align:center;}
    #message{height:20em;font-size:10px;}
    #box{overflow-y:scroll;height:700px;background:#FFF;width:95%;right:0;left:0;margin:auto;top:10px;position:relative;border:solid 4px #000;border-radius:40px;}
    #moveBtn{
        font-size:75px;color:#FFF;background-color:#00CCCC;
        right:0;left:35%;bottom:65px;margin:auto;padding:5px;position:relative;border-radius:100%;text-align:center;height:92px;width:92px;z-index:2;}
    #inputBtn0{color:#0099FF;font-size:26px;background:#DDD;border-radius:20px;}
    #inputBtn1{color:#FFF;font-weight:bold;font-size:26px;background:#FF3300;border-radius:20px;padding:6px;}
    .showImg{width:95%;}
    .time{font-size:20px;background:#ffcc00;}
    .lineHeight{line-height:0.5px;}
    table{border-bottom:solid 2px #000;width:100%;}
    td{flex-wrap: wrap;}
    .box0{width:95%; position:relative; right:0; left:0; margin:auto;/*background:skyblue;*/}
    .td0{width:15%; font-size:24px;} /*time*/
    .td1{width:65%;} /*msg*/ .msg{font-size:30px;}
    .td2{width:20%; text-align:center;} /*photo*/
    .usrName{line-height:1px;}
</style>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.7.1/css/lightbox.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.7.1/js/lightbox.min.js" type="text/javascript"></script>
</head>
<body>
<header>
    <div id="header">
    𝗛𝗮𝗶𝗤𝘂𝗿𝗶
    </div>
</header>
<div id="innerBody">
    <form method="post", enctype = "multipart/form-data">
        <div id="form-box">
            <p class="form-box0"><br>
                <label class="title" for="view_name">name</label>
                <input id="view_name" type="text" name="view_name" value="" style="width:50%;">
            </p>
            <p class="form-box0">
                <label class="title" for="message">message</label><br>
                <textarea id="message" name="message" style="width:100%;"></textarea>
            </p>
            <p class="form-box0">
                <div class="display-flex">
                    <p class="centre">
                        <span id="inputBtn0" style="padding:6px 25px;">
                            <label><input type="file" name="upload_image" style="display:none;">画像をアップロード</label>
                        </span>
                    </p>
                    <p class="centre" style="margin-left:auto;">
                        <span id="inputBtn1" style="padding:6px 45px;">
                            <label><input type="submit" name="btn_submit" style="display:none;">POST</label>
                        </span>
                    </p>
                </div>
                <?php if( !empty($error_message) ): ?>
                    <ul class="error_message">
                        <?php foreach( $error_message as $value ): ?><li><?php echo $value; ?></li><?php endforeach; ?>
                    </ul>
                <?php endif; ?>
                <?php if( empty($_POST['btn_submit']) && !empty($_SESSION['success_message']) ): ?>
                    <span class="success_message">
                        <?php echo htmlspecialchars( $_SESSION['success_message'], ENT_QUOTES, 'UTF-8'); ?></span>
                    <?php unset($_SESSION['success_message']); ?>
                <?php endif; ?>
                <!--<?php if( !empty($success_message) && !empty($_SESSION['success_message']) ): ?>
                    <span class="success_message">
                        <?php echo htmlspecialchars( $_SESSION['success_message'], ENT_QUOTES, 'UTF-8'); ?>
                    </span>
                <?php endif; ?>-->
            </p>
        </div>
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
                        <h1 class="usrName" style="line-break:anywhere;"><?php echo $value['view_name']; ?></h1>
                        <p class="msg" style="line-break:anywhere;"><?php echo $value['message']; ?></p>
                    </td>

                    <td class="td2"><!--画像を表示している箇所-->
                        <?php if(!empty ($value['img_data'])){;?>
                            <a href="<?php echo $value['img_data'];?>" data-lightbox="group"><img class="showImg" src = "<?php echo $value['img_data'];?>" alt="">
                        <?php }; ?>
                    </td>
                </table>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

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