<?php
  include 'lib/connect.php';
  include 'lib/daily.php';
  include 'lib/queryDaily.php';
  include 'lib/querySearch.php';

  header('Expires: Tue, 1 Jan 2019 00:00:00 GMT');
  header('Last-Modified:' . gmdate( 'D, d M Y H:i:s' ) . 'GMT');
  header('Cache-Control:no-cache,no-store,must-revalidate,max-age=0');
  header('Cache-Control:pre-check=0,post-check=0',false);
  header('Pragma:no-cache');




  //画面から取得した検索項目を受け取る
  if ((!empty($_POST['tgtFromdate']) || !empty($_POST['tgtTodate']) || !empty($_POST['tgtcategory'])))
  {
    // print_r($_POST['tgtFromdate'].'   '.$_POST['tgtTodate'].'   '.$_POST['tgtcategory']);
    // die();

    $limit=10;
    $page=1;
    $tgtFromdate = $_POST['tgtFromdate'];
    $tgtTodate = $_POST['tgtTodate'];
    $tgtCategory = $_POST['tgtcategory'];

    // print_r($tgtFromdate.'   '.$tgtTodate.'   '. $tgtCategory);
    // die();
    $search = new QuerySearch();
    // $results = $search->search($tgtFromdate,$tgtTodate,$tgtCategory);
    $pager = $search->getPager($page,$limit,$tgtFromdate,$tgtTodate,$tgtCategory);
    
    // print_r('debug0 '.$pager);
    // die();

    $fromdate = $_POST['tgtFromdate'];
    $todate = $_POST['tgtTodate'];
    $cate = $_POST['tgtcategory'];
    $total =  $search->totalcount($tgtFromdate,$tgtTodate,$tgtCategory);
    // print_r("POST".'   '.$total["total"]);
  }

  if ((!empty($_GET['tgtFromdate']) || !empty($_GET['tgtTodate']) || !empty($_GET['tgtcategory']) || !empty($_GET['page'])))
  {
    $limit=10;
    $tgtFromdate =str_replace("-","/", $_GET['tgtFromdate']);
    $tgtTodate = str_replace("-","/", $_GET['tgtTodate']);
    $tgtCategory = $_GET['tgtcategory'];

    $search = new QuerySearch();
    // $results = $search->search($_GET['tgtFromdate'],$_GET['tgtTodate'],$_GET['tgtcategory']);
    $pager = $search->getPager($_GET['page'],$limit,$_GET['tgtFromdate'],$_GET['tgtTodate'],$_GET['tgtcategory']);

    // print_r('debug1 '.$pager);

    $fromdate = str_replace("-","/", $_GET['tgtFromdate']);
    $todate = str_replace("-","/", $_GET['tgtTodate']);
    $cate = $_GET['tgtcategory'];

    $total =  $search->totalcount($tgtFromdate,$tgtTodate,$tgtCategory);
    // print_r("GET".'   '.$total["total"]);
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
    <link rel="stylesheet" href="css/search.css" />
    <link rel="stylesheet" href="css/list.css" />
    <link rel="stylesheet" href="css/pagenation.css" />
    <title>入力画面</title>
  </head>
  <body>
    <?php include 'header.php';?>
    <main>
    <!-- 検索要テーブル -->
    <form action="search.php" method="post">
      <table class="search-table">
        <tbody>
          <tr>
            <th>日付(From)</th>
            <td><label><input type="date" name="tgtFromdate"></label></td>
            <th>日付(To)</th>
            <td><label><input type="date" name="tgtTodate"></label></td>
            <th>カテゴリー</th>
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
                </select>
              </div>
            </td>
            <td>
            <button class="button">検索</button>
            </td>
          </tr>
        </tbody>
      </table>
    </form>
    <?php 
      if(!empty($pager['results']))
      {
         echo '<table  class="result-table">';
         echo '<tbody>';
        // foreach($results as $result)
        foreach($pager['results'] as $result)
        {
          echo "<tr>";
          echo "<th>日付</th>";
          echo "<td>".$result["tgtdate"]."</td>";
          echo "<th>出費</th>";
          echo "<td>".$result["tgtmoney"]."</td>";
          echo "<th>分類</th>";
          echo "<td>".$result["tgtcategory"]."</td>";
          echo "<th>品目</th>";
          echo "<td>".$result["tgtitem"]."</td>";
          echo "<th>熱量</th>";
          echo "<td>".$result["tgtcalory"]."</td>";
          echo "</tr>";
        }
        echo '</tbody>';
        echo '</table>';
        if(!empty($pager['results'])){
          echo '<div class="pager">';
          echo '<ul class="pagination">';
          for ($i = 1; $i <= ceil(intval($total["total"]) / $limit); $i++)
          {
            echo '<li><a href=search.php?page='.$i.'&tgtFromdate='.$fromdate.'&tgtTodate='.$todate.'&tgtcategory='.$cate.'><span>'.$i.'</span></a></li>';
          }
          echo '</ul>';
          echo '</div>';
        }

      }
      else
      {
          echo '<div id="alert">データは存在しません！</div>';
      }
   ?>

    </main>
    <footer>
      <div class="copy">
        <p>Copyright(c) 2005-2022 ikefukuro_40 . All Rights Reserved.</p>
      </div>
    </footer>
    <script src="jquery-3.5.1.min.js"></script>
    <script src="humberger.js"></script>
    <script src="datedropper-javascript.js"></script>
    <script>
      new dateDropper({
        selector: 'input[type="date"]'
      });
    </script>
  </body>
</html>
