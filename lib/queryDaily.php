<?php
include 'lib/setting.php';
include 'lib/querySetting.php';

class QueryDaily extends connect
{
    private $daily;

    public function __construct()
    {
        parent::__construct();
    }

    public function setDaily(Daily $daily)
    {
        $this->daily = $daily;
    }

    public function save()
    {
        $id = $this->daily->getId();
        $tgtdate = $this->daily->getDate();
        $tgtcategory = $this->daily->getCategory();
        $tgtitem = $this->daily->getItem();
        $tgtmoney = $this->daily->getMoney();
        $tgtcalory = $this->daily->getCalory();

        if ($this->daily->getId()) {
            // IDがあるときは上書き
            $id = $this->daily->getId();

            $stmt = $this->dbh->prepare("UPDATE records
            SET tgtdate=:tgtdate, tgtcategory=:tgtcategory, tgtitem=:tgtitem, tgtmoney=:tgtmoney, tgtcalory=:tgtcalory,updated_at=NOW() WHERE id=:id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
         } else {
            // IDがなければ新規作成
            $stmt = $this->dbh->prepare("INSERT INTO records (tgtdate, tgtcategory, tgtitem, tgtmoney, tgtcalory,created_at, updated_at)
                        VALUES (:tgtdate, :tgtcategory, :tgtitem, :tgtmoney,:tgtcalory, NOW(), NOW())");
        }
        $stmt->bindParam(':tgtdate', $tgtdate, PDO::PARAM_STR);
        $stmt->bindParam(':tgtcategory', $tgtcategory, PDO::PARAM_STR);
        $stmt->bindParam(':tgtitem', $tgtitem, PDO::PARAM_STR);
        $stmt->bindParam(':tgtmoney', $tgtmoney, PDO::PARAM_INT);
        $stmt->bindParam(':tgtcalory', $tgtcalory, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function searchArticle($word)
    {
        $stmt = $this->dbh->prepare("SELECT * FROM articles WHERE body like :word AND is_delete=0");
        $params = [
            ':word' => '%' . $word . '%',
        ];
        $stmt->execute($params);
        $pager['articles'] = $this->getArticles($stmt->fetchAll(PDO::FETCH_ASSOC));
        return $pager;
    }

    // public function find($id)
    // {
    //     $stmt = $this->dbh->prepare("SELECT * FROM articles WHERE id=:id AND is_delete=0");
    //     $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    //     $stmt->execute();
    //     $articles = $this->getArticles($stmt->fetchAll(PDO::FETCH_ASSOC));
    //     return $articles[0];
    // }

    public function find($tgtyearmonthdate)
    {

        $stmt = $this->dbh->prepare("SELECT  * FROM records WHERE tgtdate LIKE :tgtyearmonthdate");
        $stmt->bindParam(':tgtyearmonthdate', $tgtyearmonthdate, PDO::PARAM_STR);
        $stmt->execute();
        $articles = $this->getDailyData($stmt->fetchAll(PDO::FETCH_ASSOC));
        // print_r("debug find".'   '.$tgtyearmonthdate);
        return $articles;
    }

    public function findTotalByDaily($tgtyearmonthdate)
    {
        //SELECT tgtdate,sum(tgtmoney) as totalmoeny,sum(tgtcalory) as totalcalory FROM `records` where tgtdate = '2022/04/28'group by tgtdate
        $stmt = $this->dbh->prepare("SELECT tgtdate,sum(tgtmoney) as totalmoeny,sum(tgtcalory) as totalcalory FROM `records` where tgtdate=:tgtyearmonthdate group by tgtdate");
        $stmt->bindParam(':tgtyearmonthdate', $tgtyearmonthdate, PDO::PARAM_STR);
        $stmt->execute();
        $articles = $this->getToalDataByDay($stmt->fetchAll(PDO::FETCH_ASSOC));
        return $articles;
    }

    //対象月の全データを取得する
    public function findAll($tgtdate)
    {
        $tmp = $tgtdate;
        $tgtdate .= '%';
        $stmt = $this->dbh->prepare("SELECT  tgtdate,sum(tgtmoney) as 'totalmoney',sum(tgtcalory) as 'totalcalory' FROM records WHERE tgtdate LIKE :tgtdate group by tgtdate ORDER BY tgtdate DESC");
        $stmt->bindParam(':tgtdate', $tgtdate, PDO::PARAM_STR);
        $stmt->execute();
        $dailies = $this->getMonthlyData($tmp,$stmt->fetchAll(PDO::FETCH_ASSOC));

        return $dailies;
    }

    public function getPager($page = 1, $limit = 20, $month = null)
    {
        $start = ($page - 1) * $limit; // LIMIT x, y：1ページ目を表示するとき、xは0になる
        $pager = array('total' => null, 'articles' => null);
        // 月指定があれば「2021-01%」のように検索できるよう末尾に追加 アーカイブ欄から渡される模様
        if ($month) {
            $month .= '%';
        }
        // 総記事数
        if ($month) {
            $stmt = $this->dbh->prepare("SELECT COUNT(*) FROM articles WHERE is_delete=0 AND title LIKE :month");
            $stmt->bindParam(':month', $month, PDO::PARAM_STR);
        } else {
            $stmt = $this->dbh->prepare("SELECT COUNT(*) FROM articles WHERE is_delete=0");
        }
        $stmt->execute();
        $pager['total'] = $stmt->fetchColumn();

        // 表示するデータ
        if ($month) {
             $stmt = $this->dbh->prepare("SELECT * FROM articles WHERE is_delete=0 AND title LIKE :month ORDER BY title DESC LIMIT :start, :limit");
            $stmt->bindParam(':month', $month, PDO::PARAM_STR);
        } else {
            $stmt = $this->dbh->prepare("SELECT * FROM articles WHERE is_delete=0 ORDER BY title DESC LIMIT :start, :limit");
        }
        $stmt->bindParam(':start', $start, PDO::PARAM_INT);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        $pager['articles'] = $this->getArticles($stmt->fetchAll(PDO::FETCH_ASSOC));
        return $pager;
    }

    public function getMonthlyArchiveMenu()
    {
        $stmt = $this->dbh->prepare("
          SELECT DATE_FORMAT(title, '%Y-%m') AS month_menu, COUNT(*) AS count
          FROM articles
          WHERE is_delete = 0
          GROUP BY DATE_FORMAT(title, '%Y-%m')
          ORDER BY month_menu DESC");
        $stmt->execute();
        $return = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $return[] = array('month' => $row['month_menu'], 'count' => $row['count']);
        }
        return $return;
    }

    public function getHoliday($tgtmonth)
    {
        $tgtmonth .= "%";
        $stmt = $this->dbh->prepare("SELECT * FROM holiday WHERE tgtdate like :tgtmonth ORDER BY tgtdate asc");
        $stmt->bindParam(':tgtmonth', $tgtmonth, PDO::PARAM_STR);
        $stmt->execute();
        $returns = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // $returns[] = array('holiday' => $row['tgtdate']);
            // $returns[] = $row['tgtdate'];
            $returns[]=ltrim(substr($row['tgtdate'],8),0);
        }
        // foreach($returns as $return){
        // }
        // die();
        return $returns;
    }

    private function getDailyData($results)
    {
        $dailies = array();
        foreach ($results as $result) {
            $dailies[]=array("id"=>$result["id"],"tgtcategory"=>$result["tgtcategory"],"tgtitem"=>$result["tgtitem"],"tgtmoney"=>$result["tgtmoney"],"tgtcalory"=>$result["tgtcalory"]);
        }
        // print_r($dailies);
        // die();
        return  $dailies;
    }


    private function getToalDataByDay($results)
    {
        // print_r("debug");
        $dailies = "";
        foreach ($results as $result) {
            $dailies=array("tgtdate"=>$result["tgtdate"],"totalmoeny"=>$result["totalmoeny"],"totalcalory"=>$result["totalcalory"]);
        }

        //settingテーブルから出費とカロリーの最大値を取得する
        $setting = new QuerySetting();
        $settings = $setting->find();
       
        //dailyのデータがない場合 or 設定値がない場合
        if(empty($settings['tgtmaxcalory'])){
            $settings['tgtmaxcalory'] = intval(0);
        }else if(empty($settings['tgtmaxmoney'])){
            $settings['tgtmaxmoney'] = intval(0);
        }else{

        }

        if(empty($dailies['totalcalory'])){
            $tmpdiffcalory = intval($settings['tgtmaxcalory']) - intval(0);
            $tmpdiffmoney = intval($settings['tgtmaxmoney']) - intval(0);
        }else if(empty($dailies['totalmoeny'])){
            $tmpdiffmoney = intval($settings['tgtmaxmoney']) - intval(0);
        }else{
            $tmpdiffcalory = intval($settings['tgtmaxcalory']) - intval($dailies['totalcalory']);
            $tmpdiffmoney = intval($settings['tgtmaxmoney']) - intval($dailies['totalmoeny']);
        }

        //print_r("settings ".$settings);
        //die();

        //上で取得した現在のtotal値とMaxの差分を配列に格納する
        if($dailies==""){
            $dailies=[];
        }
        $dailies['diffcalory'] = $tmpdiffcalory;
        $dailies['diffmoney'] = $tmpdiffmoney;

        return  $dailies;
    }


    private function getMonthlyData($tgtdate,$results)
    {
        $dailies = array();

        // $today = date("Y/m");
        $today = $tgtdate;
        $tmparray = explode("/",$today);
        $dailies['year']=$tmparray[0];
        $dailies['month']=$tmparray[1];
        
        //既存の値を取得して画面にだす
        $setting = new QuerySetting();
        $settings = $setting->find();

        $tmp=array();
        foreach ($results as $result) {
            $day = explode("/",$result['tgtdate'])[2];
             if((intval($result['totalcalory']) > intval($settings["tgtmaxcalory"])) || (intval($result['totalmoney']) > intval($settings["tgtmaxmoney"])) )
            {
                $tmp[] =array('day'=>abs($day),"title"=>'出費計: '.$result['totalmoney'].' 円   カロリー計: '.$result['totalcalory'].' kcal ',"type"=>"red");
            }else{
                $tmp[] =array('day'=>abs($day),"title"=>'出費計: '.$result['totalmoney'].' 円   カロリー計: '.$result['totalcalory'].' kcal ',"type"=>"blue");
            }
            
        }
        $dailies['event'] = $tmp;


        // $dailies['holiday']= array("9","12","25");
        $tgtholiday = $dailies['year'].'/'.$dailies['month'];
        $dailies['holiday'] = $this->getHoliday($tgtholiday);

        // print_r(substr($dailies['holiday'][0],0,8));
        // print_r($dailies['holiday']);
        // die();
        
        return $dailies;
    }

    public function update($id,$tgtmoney,$tgtcalory)
    {
        $stmt = $this->dbh->prepare("UPDATE records SET tgtmoney=:tgtmoney, tgtcalory=:tgtcalory,updated_at=NOW() WHERE id=:id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':tgtmoney', $tgtmoney, PDO::PARAM_INT);
        $stmt->bindParam(':tgtcalory', $tgtcalory, PDO::PARAM_INT);
        // print_r($id.'  '.$tgtmoney.'  '.$tgtcalory);
        $stmt->execute();
    }

    public function delete($id)
    {
        $stmt = $this->dbh->prepare("delete from records WHERE id=:id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }
}
