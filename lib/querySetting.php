<?php
class QuerySetting extends connect
{
    private $setting;

    public function __construct()
    {
        parent::__construct();
    }

    public function setSetting(Setting $setting)
    {
        $this->setting = $setting;
    }

    public function save()
    {
        $id = $this->setting->getId();
        $tgtmaxcalory = $this->setting->getTgtmaxcalory();
        $tgtmaxmoney = $this->setting->getTgtmaxmoney();
        $tgtmailaddress = $this->setting->getTgtmailaddress();
        $tgtfile = $this->setting->getFile();

        // print_r($tgtfile['tgtholilday']['tmp_name']);
        // die();

        /* CSV処理 */
        $detect_order = 'ASCII,JIS,UTF-8,CP51932,SJIS-win';
        setlocale(LC_ALL, 'ja_JP.UTF-8');

        /* 文字コードを変換してファイルを置換 */
        $buffer = file_get_contents($tgtfile);
        if (!$encoding = mb_detect_encoding($buffer, $detect_order, true)) {
            // 文字コードの自動判定に失敗
            unset($buffer);
            throw new RuntimeException('Character set detection failed');
        }
        file_put_contents($tgtfile, mb_convert_encoding($buffer, 'UTF-8', $encoding));
        unset($buffer);

        /* トランザクション処理 */
        $this->dbh->beginTransaction();
        try 
        {
            $fp = fopen($tgtfile, 'rb');
            while ($row = fgetcsv($fp)) {
                if ($row === array(null)) {
                    // 空行はスキップ
                    continue;
                }
                $stmt = $this->dbh->prepare("INSERT INTO holiday (tgtdate,created_at, updated_at) VALUES (:tgtdate, NOW(), NOW())");
                $stmt->bindParam(':tgtdate', $row[0], PDO::PARAM_STR);
                $stmt->execute();
            }
            if (!feof($fp)) {
                // ファイルポインタが終端に達していなければエラー
                throw new RuntimeException('CSV parsing error');
            }
            fclose($fp);
            $this->dbh->commit();
        } catch (Exception $e) {
            fclose($fp);
            $this->dbh->rollBack();
            throw $e;
        }

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

    public function find()
    {
        $setting=[];
        $stmt = $this->dbh->prepare("SELECT  * FROM setting");
        $stmt->execute();
        $setting = $this->getSettingData($stmt->fetchAll(PDO::FETCH_ASSOC));
        // print_r($setting);
        // die();
        return $setting;
    }

    private function getSettingData($results)
    {
        $setting = [];
        foreach ($results as $result) {
            $setting=array("id"=>$result["id"],"tgtmaxcalory"=>$result["tgtmaxcalory"],"tgtmaxmoney"=>$result["tgtmaxmoney"],"tgtmailaddress"=>$result["tgtmailaddress"]);
        }

        // print_r($setting);
        // die();
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
}
