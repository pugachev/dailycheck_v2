<?php
class QuerySearch extends connect
{
    private $search;

    public function __construct()
    {
        parent::__construct();
    }


    // SELECT * from records where tgtdate > '2022/05/01' and tgtdate < '2022/05/15' and tgtcategory='食費'
    // SELECT * from records where tgtdate > '2022/05/01' and tgtdate < '2022/05/15' and tgtcategory='食費' limit 0,10
    // SELECT * from records where tgtdate > '2022/05/01' and tgtdate < '2022/05/15' and tgtcategory='食費' limit 10,10
    // SELECT * from records where tgtdate > '2022/05/01' and tgtdate < '2022/05/15' and tgtcategory='食費' limit 20,10
    
    public function totalcount($from,$to,$cate)
    {
        // print_r("totalcount ".'   '.$from.'   '.$to.'   '.$cate);
        $sql="select count(id) as total from records where ";

        if(!empty($from) && !empty($to))
        {
            if(empty($cate)){
                $sql.="tgtdate > :from and tgtdate < :to order by tgtdate asc ";
                $stmt = $this->dbh->prepare($sql); 
                $tmpfrom = str_replace('-','/',$from);
                $tmpto=str_replace('-','/',$to);
                $stmt->bindParam(':from', $tmpfrom, PDO::PARAM_STR);
                $stmt->bindParam(':to', $tmpto, PDO::PARAM_STR);
                // print_r("debug0");
            }else{
                $sql.="tgtdate > :from and tgtdate < :to and tgtcategory=:cate order by tgtdate asc ";
                // print_r("debug");
                $stmt = $this->dbh->prepare($sql); 
                $tmpfrom = str_replace('-','/',$from);
                $tmpto=str_replace('-','/',$to);
                $stmt->bindParam(':from', $tmpfrom, PDO::PARAM_STR);
                $stmt->bindParam(':to', $tmpto, PDO::PARAM_STR);
                $stmt->bindParam(':cate', $cate, PDO::PARAM_STR);
                // print_r("debug1");
            }

        }
        else if(!empty($from))
        {
            // $stmt = $this->dbh->prepare("select * from records where tgtdate > :from order by tgtdate"); 
            if(empty($cate)){
                $sql.=" tgtdate > :from order by tgtdate asc ";
                $stmt = $this->dbh->prepare($sql); 
                $tmpfrom = str_replace('-','/',$from);
                $stmt->bindParam(':from', $tmpfrom, PDO::PARAM_STR);
                // print_r("debug2");
            }else{
                $sql.=" tgtdate > :from and tgtcategory=:cate order by tgtdate asc ";
                $stmt = $this->dbh->prepare($sql); 
                $tmpfrom = str_replace('-','/',$from);
                $stmt->bindParam(':from', $tmpfrom, PDO::PARAM_STR);
                $stmt->bindParam(':cate', $cate, PDO::PARAM_STR);
                // print_r("debug3");
            }

        }
        else if(!empty($to))
        {
            // $stmt = $this->dbh->prepare("select * from records where tgtdate < :to order by tgtdate"); 
            if(empty($cate)){
                $sql.=" tgtdate < :to order by tgtdate asc ";
                $stmt = $this->dbh->prepare($sql); 
                $tmpto=str_replace('-','/',$to);
                $stmt->bindParam(':to', $tmpto, PDO::PARAM_STR);
                // print_r("debug4");
            }else{
                $sql.=" tgtdate < :to  and tgtcategory=:cate order by tgtdate asc ";
                $stmt = $this->dbh->prepare($sql); 
                $tmpto=str_replace('-','/',$to);
                $stmt->bindParam(':to', $tmpto, PDO::PARAM_STR);
                $stmt->bindParam(':cate', $cate, PDO::PARAM_STR);
                // print_r("debug5");
            }

        }
        
        if(empty($from) && empty($to) && !empty($cate))
        {
            
            $sql.=" tgtcategory=:cate order by tgtdate asc ";
            $stmt = $this->dbh->prepare($sql); 
            $stmt->bindParam(':cate', $cate, PDO::PARAM_STR);
            // print_r("debug6");
        }

        $stmt->execute();
        
        $cnt =$this->getTotalCount($stmt->fetchAll(PDO::FETCH_ASSOC));
        // print_r('totalcount '.'     '.$cnt.'   '.$stmt->debugDumpParams());
        // print_r($stmt);
        // print_r('totalcount '.'     '.$cnt["total"].'   '.$stmt->debugDumpParams());
        // die();
        return $cnt;


    }
    /**
     * 日付パラメータが1個の場合
     * 日付パラメータが2個の場合
     * カテゴリーがある場合
     */
    public function search($from,$to,$cate)
    {
        // print_r('search '.$from.'   '.$to.'   '.$cate);
        // die();
        $sql="select * from records where ";

        if(!empty($from) && !empty($to))
        {
            if(empty($cate)){
                $sql.="tgtdate > :from and tgtdate < :to order by tgtdate asc ";
                $stmt = $this->dbh->prepare($sql); 
                $tmpfrom = str_replace('-','/',$from);
                $tmpto=str_replace('-','/',$to);
                $stmt->bindParam(':from', $tmpfrom, PDO::PARAM_STR);
                $stmt->bindParam(':to', $tmpto, PDO::PARAM_STR);
                // print_r("debug0");
            }else{
                $sql.="tgtdate > :from and tgtdate < :to and tgtcategory=:cate order by tgtdate asc ";
                // print_r("debug");
                $stmt = $this->dbh->prepare($sql); 
                $tmpfrom = str_replace('-','/',$from);
                $tmpto=str_replace('-','/',$to);
                $stmt->bindParam(':from', $tmpfrom, PDO::PARAM_STR);
                $stmt->bindParam(':to', $tmpto, PDO::PARAM_STR);
                $stmt->bindParam(':cate', $cate, PDO::PARAM_STR);
                // print_r("debug1");
            }

        }
        else if(!empty($from))
        {
            // $stmt = $this->dbh->prepare("select * from records where tgtdate > :from order by tgtdate"); 
            if(empty($cate)){
                $sql.=" tgtdate > :from order by tgtdate asc ";
                $stmt = $this->dbh->prepare($sql); 
                $tmpfrom = str_replace('-','/',$from);
                $stmt->bindParam(':from', $tmpfrom, PDO::PARAM_STR);
                // print_r("debug2");
            }else{
                $sql.=" tgtdate > :from and tgtcategory=:cate order by tgtdate asc ";
                $stmt = $this->dbh->prepare($sql); 
                $tmpfrom = str_replace('-','/',$from);
                $stmt->bindParam(':from', $tmpfrom, PDO::PARAM_STR);
                $stmt->bindParam(':cate', $cate, PDO::PARAM_STR);
                // print_r("debug3");
            }

        }
        else if(!empty($to))
        {
            // $stmt = $this->dbh->prepare("select * from records where tgtdate < :to order by tgtdate"); 
            if(empty($cate)){
                $sql.=" tgtdate < :to order by tgtdate asc ";
                $stmt = $this->dbh->prepare($sql); 
                $tmpto=str_replace('-','/',$to);
                $stmt->bindParam(':to', $tmpto, PDO::PARAM_STR);
                // print_r("debug4");
            }else{
                $sql.=" tgtdate < :to  and tgtcategory=:cate order by tgtdate asc ";
                $stmt = $this->dbh->prepare($sql); 
                $tmpto=str_replace('-','/',$to);
                $stmt->bindParam(':to', $tmpto, PDO::PARAM_STR);
                $stmt->bindParam(':cate', $cate, PDO::PARAM_STR);
                // print_r("debug5");
            }

        }
        
        if(empty($from) && empty($to) && !empty($cate))
        {
            
            $sql.=" tgtcategory=:cate order by tgtdate asc ";
            $stmt = $this->dbh->prepare($sql); 
            $stmt->bindParam(':cate', $cate, PDO::PARAM_STR);
            // print_r("debug6");
        }

        $stmt->execute();
        
        $setting = $this->getSearchData($stmt->fetchAll(PDO::FETCH_ASSOC));
        // print_r( $stmt->debugDumpParams());
        // die();
        return $setting;

    }

    public function save()
    {
        $id = $this->setting->getId();
        $tgtmaxcalory = $this->setting->getTgtmaxcalory();
        $tgtmaxmoney = $this->setting->getTgtmaxmoney();
        $tgtmailaddress = $this->setting->getTgtmailaddress();

        if ($id) 
        {
            // IDがあるときは上書き
            $id = intval($this->setting->getId());

            $stmt = $this->dbh->prepare("UPDATE setting SET tgtmaxcalory=:tgtmaxcalory, tgtmaxmoney=:tgtmaxmoney, tgtmailaddress=:tgtmailaddress,updated_at=NOW() WHERE id=:id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
         } 
         else 
         {
            // IDがなければ新規作成
            $stmt = $this->dbh->prepare("INSERT INTO setting (tgtmaxcalory, tgtmaxmoney, tgtmailaddress,created_at, updated_at)
                        VALUES (:tgtmaxcalory, :tgtmaxmoney, :tgtmailaddress, NOW(), NOW())");
        }
        $stmt->bindParam(':tgtmaxcalory', $tgtmaxcalory, PDO::PARAM_INT);
        $stmt->bindParam(':tgtmaxmoney', $tgtmaxmoney, PDO::PARAM_INT);
        $stmt->bindParam(':tgtmailaddress', $tgtmailaddress, PDO::PARAM_STR);
        $stmt->execute();
    }

    private function getTotalCount($results)
    {
        $dailies = array();
        foreach ($results as $result) {
            $dailies=array("total"=>$result["total"]);
        }

        // print_r("".'   '.$dailies);
        // die();
        return  $dailies;
    }


    private function getSearchData($results)
    {
        $dailies = array();
        foreach ($results as $result) {
            $dailies[]=array("total"=>$result["total"],"tgtdate"=>$result["tgtdate"],"tgtcategory"=>$result["tgtcategory"],"tgtitem"=>$result["tgtitem"],"tgtmoney"=>$result["tgtmoney"],"tgtcalory"=>$result["tgtcalory"]);
        }

        return  $dailies;
    }

    public function find()
    {
        $stmt = $this->dbh->prepare("SELECT  * FROM setting");
        $stmt->execute();
        $setting = $this->getSettingData($stmt->fetchAll(PDO::FETCH_ASSOC));
        return $setting;
    }

    private function getSettingData($results)
    {
        $setting = "";
        foreach ($results as $result) {
            $setting=array("id"=>$result["id"],"tgtmaxcalory"=>$result["tgtmaxcalory"],"tgtmaxmoney"=>$result["tgtmaxmoney"],"tgtmailaddress"=>$result["tgtmailaddress"]);
        }
        return  $setting;
    }

    public function delete()
    {
        if ($this->article->getId()) {
            $id = $this->article->getId();
            $stmt = $this->dbh->prepare("UPDATE articles SET is_delete=1 WHERE id=:id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
        }
    }

    public function getPager($page = 1, $limit = 10, $from,$to,$cate)
    {
        // print_r('getPager() ' .$from.'   '.$to.'   '.$cate.'   '.$page);
        // die();
        $start = ($page - 1) * $limit; // LIMIT x, y：1ページ目を表示するとき、xは0になる
        $pager = array('total' => null, 'results' => null);

        $sql="select * from records where ";

        if(!empty($from) && !empty($to))
        {
            if(empty($cate))
            {
                $sql.="tgtdate > :from and tgtdate < :to order by tgtdate asc LIMIT :start, :limit ";
                $stmt = $this->dbh->prepare($sql); 
                $tmpfrom = str_replace('-','/',$from);
                $tmpto=str_replace('-','/',$to);

                $stmt->bindParam(':from', $tmpfrom, PDO::PARAM_STR);
                $stmt->bindParam(':to', $tmpto, PDO::PARAM_STR);
                $stmt->bindParam(':start', $start, PDO::PARAM_INT);
                $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            }
            else
            {
                $sql.="tgtdate > :from and tgtdate < :to and tgtcategory=:cate order by tgtdate asc LIMIT :start, :limit ";
                // print_r("debug");
                $stmt = $this->dbh->prepare($sql); 
                $tmpfrom = str_replace('-','/',$from);
                $tmpto=str_replace('-','/',$to);
                $stmt->bindParam(':from', $tmpfrom, PDO::PARAM_STR);
                $stmt->bindParam(':to', $tmpto, PDO::PARAM_STR);
                $stmt->bindParam(':cate', $cate, PDO::PARAM_STR);
                $stmt->bindParam(':start', $start, PDO::PARAM_INT);
                $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            }

        }
        else if(!empty($from))
        {
            // $stmt = $this->dbh->prepare("select * from records where tgtdate > :from order by tgtdate"); 
            if(empty($cate))
            {
                $sql.=" tgtdate > :from order by tgtdate asc LIMIT :start, :limit ";
                $stmt = $this->dbh->prepare($sql); 
                $tmpfrom = str_replace('-','/',$from);
                $stmt->bindParam(':from', $tmpfrom, PDO::PARAM_STR);
                $stmt->bindParam(':start', $start, PDO::PARAM_INT);
                $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            }
            else
            {
                $sql.=" tgtdate > :from and tgtcategory=:cate order by tgtdate asc LIMIT :start, :limit ";
                $stmt = $this->dbh->prepare($sql); 
                $tmpfrom = str_replace('-','/',$from);
                $stmt->bindParam(':from', $tmpfrom, PDO::PARAM_STR);
                $stmt->bindParam(':cate', $cate, PDO::PARAM_STR);
                $stmt->bindParam(':start', $start, PDO::PARAM_INT);
                $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            }

        }
        else if(!empty($to))
        {
            // $stmt = $this->dbh->prepare("select * from records where tgtdate < :to order by tgtdate"); 
            if(empty($cate))
            {
                $sql.=" tgtdate < :to order by tgtdate asc LIMIT :start, :limit ";
                $stmt = $this->dbh->prepare($sql); 
                $tmpto=str_replace('-','/',$to);
                $stmt->bindParam(':to', $tmpto, PDO::PARAM_STR);
                $stmt->bindParam(':start', $start, PDO::PARAM_INT);
                $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            }
            else
            {
                $sql.=" tgtdate < :to  and tgtcategory=:cate order by tgtdate asc LIMIT :start, :limit ";
                $stmt = $this->dbh->prepare($sql); 
                $tmpto=str_replace('-','/',$to);
                $stmt->bindParam(':to', $tmpto, PDO::PARAM_STR);
                $stmt->bindParam(':cate', $cate, PDO::PARAM_STR);
                $stmt->bindParam(':start', $start, PDO::PARAM_INT);
                $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            }

        }
        
        if(empty($from) && empty($to) && !empty($cate))
        {
            
            $sql.=" tgtcategory=:cate order by tgtdate asc ";
            $stmt = $this->dbh->prepare($sql); 
            $stmt->bindParam(':cate', $cate, PDO::PARAM_STR);
            $stmt->bindParam(':start', $start, PDO::PARAM_INT);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        }




        $stmt->execute();
        // print_r($stmt->debugDumpParams());
        // $pager['total']="22";
        $pager['results'] = $this->getSearchData($stmt->fetchAll(PDO::FETCH_ASSOC));
        // print_r($pager['results']);
        // die();
        return $pager;

    }

}