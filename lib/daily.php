<?php
class Daily
{
    private $id=null;
    private $tgtdate = null;
    private $tgtcategory = null;
    private $tgtitem = null;
    private $tgtmoney = null;
    private $tgtcalory = null;

    private $totalmoney=0;
    private $toalcalory=0;

    public function getTotalMoney()
    {
        return $this->totalmoney;
    }

    public function getTotalCalory()
    {
        return $this->toalcalory;
    }

    public function setTotalMoney($totalmoney)
    {
        $this->totalmoney = $totalmoney;
    }

    public function setTotalCalory($totalcalory)
    {
        $this->toalcalory = $totalcalory;
    }


    public function save()
    {
        $queryDaily = new QueryDaily();
        $queryDaily->setDaily($this);
        $queryDaily->save();
    }

    public function delete()
    {
        $queryDaily = new QueryDaily();
        $queryDaily->setDaily($this);
        $queryDaily->delete();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getDate()
    {
        return $this->tgtdate;
    }

    public function getCategory()
    {
        return $this->tgtcategory;
    }

    public function getItem()
    {
        return $this->tgtitem;
    }

    public function getMoney()
    {
        return $this->tgtmoney;
    }

    public function getCalory()
    {
        return $this->tgtcalory;
    }

    public function getCreatedAt()
    {
        return $this->created_at;
    }

    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setDate($tgtdate)
    {
        $this->tgtdate = $tgtdate;
    }

    public function setCategory($tgtcategory)
    {
        $this->tgtcategory = $tgtcategory;
    }

    public function setItem($tgtitem)
    {
        $this->tgtitem = $tgtitem;
    }

    public function setMoney($tgtmoney)
    {
        $this->tgtmoney = $tgtmoney;
    }

    public function setCalory($tgtcalory)
    {
        $this->tgtcalory = $tgtcalory;
    }

    public function setCreatedAt($created_at)
    {
        $this->created_at = $created_at;
    }

    public function setUpdatedAt($updated_at)
    {
        $this->updated_at = $updated_at;
    }
}
