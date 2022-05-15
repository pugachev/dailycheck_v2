<?php
  include 'lib/connect.php';
  include 'lib/daily.php';
  include 'lib/queryDaily.php';

  header('Expires: Tue, 1 Jan 2019 00:00:00 GMT');
  header('Last-Modified:' . gmdate( 'D, d M Y H:i:s' ) . 'GMT');
  header('Cache-Control:no-cache,no-store,must-revalidate,max-age=0');
  header('Cache-Control:pre-check=0,post-check=0',false);
  header('Pragma:no-cache');

  //「修正」or「削除」ボタン押下時に処理
  if ((!empty($_POST['id']) || !empty($_POST['tgtmoney']) || !empty($_POST['tgtcalory'])))
   {

    //「修正」の場合
    if(!empty($_POST['modify']))
    {
      $id = $_POST['id'];
      $tgtmoney = $_POST['tgtmoney'];
      $tgtcalory  = $_POST['tgtcalory'];

      $daily = new QueryDaily();
      $daily->update($id,$tgtmoney,$tgtcalory);

    }
     //「削除」の場合
    else if(!empty($_POST['delete']))
    {
      $id = $_POST['id'];
      $daily = new QueryDaily();
      $daily->delete($id);
    }

    // header('Location: index.php');

  }

  //カレンダー画面から取得した「月」と「日」
  if(!empty($_GET['yearmonth']) && !empty($_GET['day'])){
    $yearmonth = $_GET['yearmonth'];
    $day = $_GET['day'];
    $tgtyearmonthdate=$yearmonth.'/'.$day;
  }else if(!empty($_POST['yearmonth'])){
    $tgtyearmonthdate = $_POST['yearmonth'];
    // print_r($tgtyearmonthdate);
    // die();
  }

  $daily = new QueryDaily();
  $results = $daily->find($tgtyearmonthdate);
  $head_reqults = $daily->findTotalByDaily($tgtyearmonthdate);
  
  // print_r($head_reqults);
  // die();
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
    <link rel="stylesheet" href="css/list.css" />
    <title>一覧画面</title>
  </head>
  <body>
  <?php include 'header.php';?>
    <main>
	        <table>
            <div class="headinfo">
              <div class="tgthead"><?php echo  $tgtyearmonthdate; ?></div>
            </div>
            <div class="headinfo">
              <div class="tgthead2">出費計:<?php echo $head_reqults['totalmoeny']; ?>円(差分:<?php echo $head_reqults['diffmoney']; ?> 円)</div>
              <div class="tgthead2">熱量計:<?php echo $head_reqults['totalcalory']; ?>kcal(差分:<?php echo $head_reqults['diffcalory']; ?> kcal)</div>
            </div>
            <tbody>
              <?php 
              if(!empty($results)){
                foreach($results as $result){
                  echo "<tr>";
                  echo "<form action='list.php' method='post'>";
                  echo "<th>ID</th>";
                  echo '<td><input type="text" value='.$result["id"].' name="id" class="hoge"></td>';
                  echo "<th>出費</th>";
                  echo '<td><input type="text" value='.$result["tgtmoney"].' name="tgtmoney"  class="hoge"></td>';
                  echo "<th>分類</th>";
                  echo "<td>".$result["tgtcategory"]."</td>";
                  echo "<th>品目</th>";
                  echo "<td>".$result["tgtitem"]."</td>";
                  echo "<th>熱量</th>";
                  echo '<td><input type="text" value='.$result["tgtcalory"].' name="tgtcalory"  class="hoge"></td>';
                  echo "<td><button class='button' name='modify' value='1' >修正</button></td>";
                  echo "<td><button class='button' name='delete' value='2' >削除</button></td>";
                  // echo '<td><input type="hidden" name="yearmonth" value="2022/05/13"></td>';
                  echo '<td><input type="hidden" name="yearmonth" value='. $tgtyearmonthdate.' ></td>';
                  echo "</form>";
                  echo "</tr>";
                }
              }
              else
              {
                echo '<div id="alert">データは存在しません！</div>'; 
              }
              ?>
            </tbody>
         </table>
        </form>
    </main>
    <footer>
      <div class="copy">
        <p>Copyright(c) 2005-2022 ikefukuro_40 . All Rights Reserved.</p>
      </div>
    </footer>
    <script src="jquery-3.5.1.min.js"></script>
    <script src="humberger.js"></script>
    <script> 
         $(function(){
          // location.reload(true)
        })

    </script>
  </body>
</html>