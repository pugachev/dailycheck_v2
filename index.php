<?php
  header('Expires: Tue, 1 Jan 2019 00:00:00 GMT');
  header('Last-Modified:' . gmdate( 'D, d M Y H:i:s' ) . 'GMT');
  header('Cache-Control:no-cache,no-store,must-revalidate,max-age=0');
  header('Cache-Control:pre-check=0,post-check=0',false);
  header('Pragma:no-cache');

?>
<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="sanitize.css" />
    <link rel="preconnect" href="https://fonts.gstatic.com" />
    <link rel=”shortcut icon” href=”https://ikefukuro40.tech/favicon.ico” />
    <link
      href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="css/style.css" />
    <link rel="stylesheet" href="css/calendar.css" />

    <title>カレンダー画面</title>
  </head>
  <body>
  <?php include 'header.php';?>
    <main>
    <div id="wrap">
	<div id="mini-calendar"></div>
</div>
    </main>
    <?php include 'footer.php';?>
  </body>
</html>