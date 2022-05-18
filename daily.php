<?php
  include 'lib/connect.php';
  include 'lib/daily.php';
  include 'lib/queryDaily.php';

  header('Expires: Tue, 1 Jan 2019 00:00:00 GMT');
  header('Last-Modified:' . gmdate( 'D, d M Y H:i:s' ) . 'GMT');
  header('Cache-Control:no-cache,no-store,must-revalidate,max-age=0');
  header('Cache-Control:pre-check=0,post-check=0',false);
  header('Pragma:no-cache');

  //全画面から日付を取得する
  if(!empty($_GET['tgtdate'])){
      $tgtdate = $_GET['tgtdate'];
      //画像のパスだけを取得する
      $daily = new QueryDaily();
      //朝データを取得する
      $morning = $daily->getPicdata($tgtdate,1);
      //朝データを取得する
      $lunch = $daily->getPicdata($tgtdate,2);
      //朝データを取得する
      $dinner = $daily->getPicdata($tgtdate,3);

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
    <link rel="stylesheet" href="css/list.css" />
    <style>

            /* p {
                font-size: 16px;
                font-weight: bold;
                text-align: center;
                margin: 60px auto 40px;
            } */
            table {
                width: 80%;
                margin: 20px auto;
            }
            .tbl-r02 th {
                background: #e9727e;
                border: solid 1px #ccc;
                color: #fff;
                padding: 10px;
            }
            .tbl-r02 td {
                border: solid 1px #ccc;
                padding: 10px;
                text-align: center;
            }
            .images{
                    margin-right: calc(100% - 50vw);
                    margin-left: calc(100% - 50vw);
                }

                .images img {
                    display: block;
                    width: 100%;
                    height: auto;
                }
            @media screen and (max-width: 800px) {
                .last td:last-child {
                    border-bottom: solid 1px #ccc;
                    width: 100%;
                }
                .tbl-r02 {
                    width: 80%;
                }
                .tbl-r02 th,
                .tbl-r02 td {
                    border-bottom: none;
                    display: block;
                    width: 100%;
                }

                img {
                    width:100%;
                    max-width: 100%;
                    height: auto;
                }
            }
        </style>
    <title>一覧画面</title>
  </head>
  <body>
  <?php include 'header.php';?>
    <main>
        <?php 
            echo '<table class="tbl-r02">';
            echo  '<tr>';
            echo '<th width="15%">朝</th>';
            echo '<td width="10%">食パン</td>';
            echo '<td width="65%"><div class="images"><img src="'.$morning['tgtpicname'].'"></div></td>';
            echo '</tr>';
            echo '<tr>';
            echo '<th>昼</th>';
            echo '<td>スシロー</td>';
            echo '<td width="65%"><div class="images"><img src="'.$lunch['tgtpicname'].'"></div></td>';
            echo '</tr>';
            echo '<tr class="last">';
            echo '<th>夜</th>';
            echo '<td>正義のとんかつ</td>';
            echo '<td width="65%"><div class="images"><img src="'.$dinner['tgtpicname'].'"></div></td>';
            echo '</tr>';
            echo '</table>';

        ?>
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