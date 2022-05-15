<?php
include 'lib/connect.php';
include 'lib/daily.php';
include 'lib/queryDaily.php';

header('Expires: Tue, 1 Jan 2019 00:00:00 GMT');
header('Last-Modified:' . gmdate( 'D, d M Y H:i:s' ) . 'GMT');
header('Cache-Control:no-cache,no-store,must-revalidate,max-age=0');
header('Cache-Control:pre-check=0,post-check=0',false);
header('Pragma:no-cache');

// include 'lib/setting.php';
// include 'lib/querySetting.php';

use PHPMailer\PHPMailerPHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

// 設置した場所のパスを指定する
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/SMTP.php';

if ((!empty($_POST['tgtdate']) || !empty($_POST['tgtcategory'])) || (!empty($_GET['tgtitem']))) {

  // インスタンスを生成（true指定で例外を有効化）
  $mail = new PHPMailer(true);

  // 文字エンコードを指定
  $mail->CharSet = 'utf-8';

  $title = ""; // タイトル
  $body = ""; // 本文
  $title_alert = ""; // タイトルのエラー文言
  $body_alert = ""; // 本文のエラー文言


  $tgtdate = $_POST['tgtdate'];
  $tgtcategory = $_POST['tgtcategory'];
  $tgtitem = $_POST['tgtitem'];
  $tgtmoney = $_POST['tgtmoney'];
  $tgtcalory = $_POST['tgtcalory'];


    //既存の値を取得して画面にだす
    $setting = new QuerySetting();
    $result = $setting->find();

  // if(intval($tgtcalory) > intval($result["tgtmaxcalory"]) || intval($tgtmoney) > intval($result["tgtmaxmoney"]))
  if((intval($tgtcalory) > intval($result["tgtmaxcalory"])) || (intval($tgtmoney) > intval($result["tgtmaxmoney"])) )
  {
    // $secretKey =  '6LfFg0ofAAAAACb-DWNN0-a3fxIqUmSxX3aygE-b';
    // $captchaResponse = $_POST['g-recaptcha-response'];
    
    // // APIリクエスト
    // $verifyResponse = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=6LfFg0ofAAAAACb-DWNN0-a3fxIqUmSxX3aygE-b&response={$captchaResponse}");
    
    // //var_dump($verifyResponse);
    // // APIレスポンス確認
    // $responseData = json_decode($verifyResponse);

    // if($responseData->success)
		// {
	            // デバッグ設定
	            // $mail->SMTPDebug = 2; // デバッグ出力を有効化（レベルを指定）
	            // $mail->Debugoutput = function($str, $level) {echo "debug level $level; message: $str<br>";};

	            // SMTPサーバの設定
	            $mail->isSMTP(); // SMTPの使用宣言
	            $mail->Host = 'sv13004.xserver.jp'; // SMTPサーバーを指定
	            $mail->SMTPAuth = true; // SMTP authenticationを有効化
	            $mail->Username = 'mailtest@ikefukuro40.tech'; // SMTPサーバーのユーザ名
	            $mail->Password = 'Manabu2020'; // SMTPサーバーのパスワード
	            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // 暗号化を有効（tls or ssl）無効の場合はfalse
	            $mail->Port = 465; // TCPポートを指定（tlsの場合は465や587）

	            //$tgt=['apimaster2018@gmail.com','mtaketani37@gmail.com'];

	            // 送受信先設定（第二引数は省略可）
	            $mail->setFrom('mailtest@ikefukuro40.tech', '差出人名'); // 送信者
	            // $mail->addAddress($result['tgtmailaddress'], '受信者名'); // 宛先
              $mail->addAddress("mtaketani37@gmail.com", '受信者名'); // 宛先
	            // $mail->addAddress('mtaketani37@gmail.com', '受信者名'); // 宛先
	            //$mail->addReplyTo('mailtest@ikefukuro40.tech', 'お問い合わせ'); // 返信先
	            // $mail->addCC('finfizz2000@yahoo.co.jp', '受信者名'); // CC宛先
	            $mail->Sender = 'mailtest@ikefukuro40.tech'; // Return-path

	            // 送信内容設定
	            $mail->Subject = '警告';
	            $mail->Body = '金額またカロリーの合計が設定値を超えました。';

	            // 送信
	            $mail->send();

		// } 
		// else 
		// {
		// 	echo '<h1 class="text-center">Sorry unexpected error occurred, please try again later.</h1>'; // 失敗
		// }
  }

  $daily = new Daily();
  $daily->setDate(str_replace("-","/",$tgtdate));
  $daily->setCategory($tgtcategory);
  $daily->setItem($tgtitem);
  $daily->setMoney($tgtmoney);
  $daily->setCalory($tgtcalory);

  $daily->save();

  header('Location: index.php');

}
?>
<html lang="ja">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="sanitize.css" />
    <link rel="preconnect" href="https://fonts.gstatic.com" />
    <link
      href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="css/style.css" />
    <link rel="stylesheet" href="css/input.css" />
    <title>入力画面</title>
  </head>
  <body>
  <?php include 'header.php';?>
    <main>


	<!-- メイン開始 -->
  <form action="input.php" method="post">
    <table class="form-table">
      <tbody>
        <tr>
          <th>日付</th>
          <td><label><input type="date" name="tgtdate" size="30" value=""></label>
          </td>
        </tr>
        <tr>
          <th>分類</th>
          <td>
            <div class="category cate">
              <select name="tgtcategory">
                <option value="" hidden>選ぶ</option>
                <option value="食費">食費</option>
                <option value="日用品">日用品</option>
                <option value="Amazon">Amazon</option>
                <option value="医療費">医療費</option>
                <option value="公共料金">公共料金</option>
                <option value="税金">税金</option>
                <option value="外食">外食</option>
                <option value="その他">その他</option>
                <option value="カロリー摂取のみ">カロリー摂取のみ</option>
              </select>
            </div>
          </td>
        </tr>
        <tr>
          <th>品目</th>
          <td><input type="text" name="tgtitem" size="60" value="">
          </td>
        </tr>
        <tr>
          <th>出費</th>
          <td><input type="text" name="tgtmoney" size="60" value="">
          </td>
        </tr>
        <tr>
          <th>熱量</th>
          <td><input type="text" name="tgtcalory" size="60" value="">
          </td>
        </tr>
      </tbody>
    </table>
    <div class="button_wrapper">
      <button class="button">登録</button>
      <button class="button">キャンセル</button>
    </div>
  </form>
  </main>
    <footer>
      <div class="copy">
        <p>Copyright(c) 2005-2022 ikefukuro_40 . All Rights Reserved.</p>
      </div>
    </footer>
    <script src="jquery-3.5.1.min.js"></script>
    <script src="humberger.js"></script>
    <!-- <script src="https://www.google.com/recaptcha/api.js?render=6LfFg0ofAAAAAPuE7t2E39YxBdN0Bu0hxbNZHP8Z"></script> 
<script>
grecaptcha.ready(function() {
    grecaptcha.execute('6LfFg0ofAAAAAPuE7t2E39YxBdN0Bu0hxbNZHP8Z', {action: 'homepage'}).then(function(token) {
        var recaptchaResponse = document.getElementById('g-recaptcha-response');
        recaptchaResponse.value = token;
    });
});
</script> -->
  </body>
</html>
