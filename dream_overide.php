<?php
class OverideData
{
    private $_pdo;
    function __construct()
    {
        try {
            $this->_pdo = new PDO('mysql:host=' . config::get('mysql/host') . ';dbname=' . config::get('mysql/db'), config::get('mysql/username'), config::get('mysql/password'));
        } catch (PDOException $e) {
            $e->getMessage();
        }
    }
    public function unique($table, $field, $value)
    {
        if ($this->get($table, $field, $value)) {
            return true;
        } else {
            return false;
        }
    }

    public function getNo($table)
    {
        $query = $this->_pdo->query("SELECT * FROM $table");
        $num = $query->rowCount();
        return $num;
    }

    public function getCountReport($table, $field, $value, $where2, $id2, $where3, $id3)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $field >= '$value' AND $where2 <= '$id2' AND $where3 = '$id3'");
        $num = $query->rowCount();
        return $num;
    }

    public function getCountReport1($table, $field, $value, $where2, $id2, $where3, $id3, $where4, $id4)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $field >= '$value' AND $where2 <= '$id2' AND $where3 = '$id3' AND $where4 = '$id4'");
        $num = $query->rowCount();
        return $num;
    }

    public function getCountReport2($table, $field, $value, $where2, $id2, $where3, $id3, $where4, $id4, $where5, $id5)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $field >= '$value' AND $where2 <= '$id2' AND $where3 = '$id3' AND $where4 = '$id4' AND $where5 = '$id5'");
        $num = $query->rowCount();
        return $num;
    }

    public function getCount($table, $field, $value)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $field = '$value'");
        $num = $query->rowCount();
        return $num;
    }

    public function getCount1($table, $field, $value, $where2, $id2)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $field <= '$value' AND $where2 = '$id2'");
        $num = $query->rowCount();
        return $num;
    }

    public function getCount2($table, $field, $value, $where2, $id2)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $field <= '$value' AND $where2 = '$id2'");
        $num = $query->rowCount();
        return $num;
    }

    public function getCount3($table, $where2, $id2)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE notify_amount >= quantity AND $where2 = '$id2'");
        $num = $query->rowCount();
        return $num;
    }

    public function getCount4($table, $where2, $id2)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE notify_amount >= quantity AND $where2 = '$id2'");
        $num = $query->rowCount();
        return $num;
    }

    public function getCount5($table, $where2, $id2, $where3, $id3)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE notify_amount >= quantity AND $where2 = '$id2' AND $where3 = '$id3'");
        $num = $query->rowCount();
        return $num;
    }

    public function countData($table, $field, $value, $field1, $value1)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $field = '$value' AND $field1 = '$value1'");
        $num = $query->rowCount();
        return $num;
    }

    public function countData1($table, $field, $value, $field1, $value1, $field2, $value2)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $field = '$value' AND $field1 = '$value1' AND $field2 = '$value2'");
        $num = $query->rowCount();
        return $num;
    }

    public function countData2($table, $field, $value, $field1, $value1, $field2, $value2)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $field = '$value' AND $field1 = '$value1' AND $field2 = '$value2'");
        $num = $query->rowCount();
        return $num;
    }

    public function getData($table)
    {
        $query = $this->_pdo->query("SELECT * FROM $table");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getDataLimit($table, $page, $numRec)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE 1 limit $page,$numRec");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }


    public function firstRow($table, $param, $id, $where, $client_id)
    {
        $query = $this->_pdo->query("SELECT DISTINCT $param FROM $table WHERE $where = '$client_id' ORDER BY '$id' ASC");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function firstRow1($table, $param, $id, $where, $client_id, $where1, $id1)
    {
        $query = $this->_pdo->query("SELECT DISTINCT $param FROM $table WHERE $where = '$client_id' AND $where1 = '$id1' ORDER BY '$id' ASC");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function firstRow2($table, $param, $id, $where, $client_id, $where1, $id1, $where2, $id2)
    {
        $query = $this->_pdo->query("SELECT DISTINCT $param FROM $table WHERE $where = '$client_id' AND $where1 = '$id1'  AND $where2 = '$id2' ORDER BY '$id' ASC");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }


    public function getReport($table1, $table2, $id1, $id2)
    {
        $query = $this->_pdo->query("SELECT '$table2'.'$id2','$table1'.'$id1' FROM $table2 INNER JOIN '$table1' ON '$table2'.'$id2'='$table1'.'$id1'");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getNews($table, $where, $id, $where2, $id2)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where = '$id' AND $where2 = '$id2'");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    public function getNews2($table, $where, $id, $where2, $id2, $where3, $id3, $where4, $id4)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where = '$id' AND $where2 = '$id2' AND $where3 = '$id3' AND $where4 = '$id4'");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getNews3($table, $where, $id, $where2, $id2, $where3, $id3)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where = '$id' AND $where2 = '$id2' AND $where3 = '$id3'");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getNewsASC($table, $where, $id, $where2, $id2, $name)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where = '$id' AND $where2 = '$id2' ORDER BY $name ASC");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getNewsASC0($table, $where, $id, $where2, $id2, $name)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where = '$id' AND $where2 > '$id2' ORDER BY $name ASC");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getNewsASC0Count($table, $where, $id, $where2, $id2, $name)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where = '$id' AND $where2 > '$id2' ORDER BY $name ASC");
        $num = $query->rowCount();
        return $num;
    }

    public function getNewsASC1($table, $where, $id, $where2, $id2, $name)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where = '$id' AND $where2 <= '$id2' ORDER BY $name ASC");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getNewsAs2($table, $where, $id, $where2, $id2)
    {
        // Updated Query
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where = '$id' AND $where2 = '$id2' ORDER BY date ASC");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;

    }

    public function getNewsASC1Count($table, $where, $id, $where2, $id2, $name)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where = '$id' AND $where2 <= '$id2' ORDER BY $name ASC");
        $num = $query->rowCount();
        return $num;
    }


    public function getNewsASC0G($table, $where, $id, $where2, $id2, $where3, $id3, $name)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where = '$id' AND $where2 > '$id2' AND $where3 = '$id3'  ORDER BY $name ASC");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getNewsASC0CountG($table, $where, $id, $where2, $id2, $where3, $id3, $name)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where = '$id' AND $where2 > '$id2' AND $where3 = '$id3' ORDER BY $name ASC");
        $num = $query->rowCount();
        return $num;
    }

    public function getNewsASC1G($table, $where, $id, $where2, $id2, $where3, $id3, $name)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where = '$id' AND $where2 <= '$id2' AND $where3 = '$id3' ORDER BY $name ASC");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getNewsASC1CountG($table, $where, $id, $where2, $id2, $where3, $id3, $name)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where = '$id' AND $where2 <= '$id2' AND $where3 = '$id3' ORDER BY $name ASC");
        $num = $query->rowCount();
        return $num;
    }

    public function getNews1($table, $where, $id, $where2, $id2, $where3, $id3)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where = '$id' AND $where2 = '$id2' AND $where3 = '$id3'");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    public function get3($table, $where, $id, $where2, $id2, $where3, $id3)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where = '$id' AND $where2 = '$id2' AND $where3 = '$id3'");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function get3GretaerThan1($table, $where, $id, $where2, $id2, $where3, $id3)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where = '$id' AND $where2 = '$id2' AND $where3 >= '$id3'");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getFull($table, $where, $id, $where2, $id2)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where > '$id' AND $where2 = '$id2'");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function get4($table, $where, $id, $where2, $id2, $where3, $id3)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where > '$id' AND $where2 = '$id2' AND $where3 = '$id3'");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function get4b($table, $where, $id, $where2, $id2)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where > '$id' AND $where2 = '$id2'");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function get5($table, $where, $id, $where2, $id2, $where3, $id3)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where <= '$id' AND $where2 = '$id2' AND $where3 = '$id3'");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function get6($table, $where, $id, $where2, $id2, $where3, $id3)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where < '$id' AND $where2 = '$id2' AND $where3 = '$id3'");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function get7($table, $where, $id, $where2, $id2)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where <= '$id' AND $where2 = '$id2'");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getSumD($table, $variable)
    {
        $query = $this->_pdo->query("SELECT SUM($variable) FROM $table");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getSumD1($table, $variable, $field, $value)
    {
        $query = $this->_pdo->query("SELECT SUM($variable) FROM $table WHERE $field = '$value' ");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getSumD2($table, $variable, $field, $value, $field1, $value1)
    {
        $query = $this->_pdo->query("SELECT SUM($variable) FROM $table WHERE $field = '$value' AND $field1 = '$value1'");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getSumD3($table, $variable, $field, $value, $field1, $value1, $field2, $value2)
    {
        $query = $this->_pdo->query("SELECT SUM($variable) FROM $table WHERE $field = '$value' AND $field1 = '$value1' AND $field2 = '$value2'");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function get($table, $where, $id)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where = '$id'");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getData2($table, $field, $value)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $field = '$value'");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function get2($table, $value, $where, $id)
    {
        $query = $this->_pdo->query("SELECT $value FROM $table WHERE $where = '$id'");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    public function getDataAsc0($table, $name)
    {
        $query = $this->_pdo->query("SELECT * FROM $table ORDER BY $name ASC");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getDataAsc($table, $where, $id, $name)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where = '$id' ORDER BY $name ASC");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getDataDesc($table, $name)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE 1 ORDER BY $name DESC");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getDataDesc1($table, $where, $id, $name)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where = '$id' ORDER BY $name DESC");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getDataDesc2($table, $where, $id, $where1, $id1, $name)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where = '$id' AND $where1 = '$id1' ORDER BY $name DESC");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getDataDesc3($table, $where, $id, $where1, $id1, $where2, $id2, $name)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where = '$id' AND $where1 = '$id1' AND $where2 = '$id2' ORDER BY $name DESC");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function delete($table, $field, $value)
    {
        return $this->_pdo->query("DELETE FROM $table WHERE $field = $value");
    }

    public function delete1($table, $field, $value, $field1, $value1)
    {
        return $this->_pdo->query("DELETE FROM $table WHERE $field = '$value' AND $field1 = '$value1'");
    }

    public function lastRow($table, $value)
    {
        $query = $this->_pdo->query("SELECT * FROM $table ORDER BY $value DESC LIMIT 1");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    public function lastRow2($table, $field, $value, $orderBy)
    {
        $query = $this->_pdo->query("SELECT * FROM $table where $field='$value' ORDER BY $orderBy DESC LIMIT 1");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    public function selectData($table, $field, $value, $field1, $value1, $value2, $field2)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $field = '$value' AND $field1 = '$value1' AND $value2 = '$field2'");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    public function selectData1($table, $field, $value, $field1, $value1)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $field = '$value' AND $field1 = '$value1'");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getWithLimit4($table, $where, $id, $where2, $id2, $where3, $id3, $page, $numRec)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where = '$id' AND $where2 = '$id2' AND $where3 = '$id3' limit $page,$numRec");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getWithLimit3($table, $value, $where, $id, $page, $numRec)
    {
        $query = $this->_pdo->query("SELECT DISTINCT $value FROM $table WHERE $where = '$id' limit $page,$numRec");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getWithLimit2($table, $field, $value, $field1, $value1, $value2, $field2, $page, $numRec)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $field = '$value' AND $field1 = '$value1' AND $value2 = '$field2' limit $page,$numRec");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getWithLimit2Desc($table, $field, $value, $field1, $value1, $value2, $field2, $page, $numRec)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $field = '$value' AND $field1 = '$value1' AND $value2 = '$field2' ORDER BY 'id' DESC LIMIT $page, $numRec");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    public function getWithLimit0Desc($table, $page, $numRec)
    {
        $query = $this->_pdo->query("SELECT * FROM $table ORDER BY 'id' DESC LIMIT $page, $numRec");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getWithLimit0($table, $page, $numRec)
    {
        $query = $this->_pdo->query("SELECT * FROM $table limit $page,$numRec");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getWithLimit1($table, $where, $id, $where2, $id2, $page, $numRec)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where = '$id' AND $where2 = '$id2' limit $page,$numRec");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getWithLimit1Desc($table, $where, $id, $where2, $id2, $page, $numRec)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where = '$id' AND $where2 = '$id2' ORDER BY 'id' DESC LIMIT $page, $numRec");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getWithLimit($table, $where, $id, $page, $numRec)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where = '$id' limit $page,$numRec");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getWithLimitDesc($table, $where, $id, $page, $numRec)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where = '$id' ORDER BY id DESC LIMIT $page, $numRec");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getWithLimitDescendingOrder($table, $where, $id, $page, $numRec, $value)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where = '$id' limit $page,$numRec ORDER BY $value DESC");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getWithLimitAscendOrder($table, $where, $id, $page, $numRec, $value)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where = '$id' limit $page,$numRec ORDER BY $value DESC");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getDataWithLimit($table, $page, $numRec)
    {
        $query = $this->_pdo->query("SELECT * FROM $table limit $page,$numRec");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function searchBtnDate2($table, $var, $value, $var1, $value1)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $var >= '$value' AND $var1 <= '$value1'");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function searchBtnDate3($table, $var, $value, $var1, $value1, $var2, $value2)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $var >= '$value' AND $var1 <= '$value1' AND $var2 = '$value2'");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function searchBtnDate4($table, $var, $value, $var1, $value1, $var2, $value2, $var3, $value3)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $var >= '$value' AND $var1 <= '$value1' AND $var2 = '$value2' AND $var3 = '$value3'");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function searchBtnDate5($table, $var, $value, $var1, $value1, $var2, $value2, $var3, $value3, $var4, $value4)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $var >= '$value' AND $var1 <= '$value1' AND $var2 = '$value2' AND $var3 = '$value3' OR $var4 = '$value4'");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }


    public function searchBtnDateSufficient($table, $var, $value, $var1, $value1, $var2, $value2, $var3, $value3, $var4, $value4)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $var >= '$value' AND $var1 <= '$value1' AND $var2 < '$value2' AND $var3 = '$value3' AND $var4 = '$value4'");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function searchBtnDateLow($table, $var, $value, $var1, $value1, $var2, $value2, $var3, $value3, $var4, $value4)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $var >= '$value' AND $var1 <= '$value1' AND $var2 >= '$value2' AND $var3 = '$value3' AND $var4 = '$value4'");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function searchBtnDateOutStock($table, $var, $value, $var1, $value1, $var2, $value2, $var3, $value3, $var4, $value4)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $var >= '$value' AND $var1 <= '$value1' AND $var2 <= $value2 AND $var3 = '$value3' AND $var4 = '$value4'");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function searchBtnDateExpired($table, $var, $value, $var1, $value1, $var2, $value2, $var3, $value3, $var4, $value4)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $var >= '$value' AND $var1 <= '$value1' AND $var2 <= $value2 AND $var3 = '$value3' AND $var4 = '$value4'");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function searchBtnDateNotChecked($table, $var, $value, $var1, $value1, $var2, $value2, $var3, $value3, $var4, $value4)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $var >= '$value' AND $var1 <= '$value1' AND $var2 <= $value2 AND $var3 = '$value3' AND $var4 = '$value4'");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getLessThanDate($table, $where, $id, $where2, $id2)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where <= '$id' AND $where2 = '$id2'");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getWithLimitLessThanDate($table, $where, $id, $where2, $id2, $page, $numRec)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where <= '$id' AND $where2 = '$id2' limit $page,$numRec");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getLessThanDate30($table, $where, $id, $where2, $id2)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where <= '$id' AND $where2 = '$id2'");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getWithLimitLessThan30($table, $where, $id, $where2, $id2, $page, $numRec)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where <= '$id' AND $where2 = '$id2' limit $page,$numRec");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }


    public function getNewsASC030($table, $where, $id, $where2, $id2, $name)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where = '$id' AND $where2 > '$id2' ORDER BY $name ASC");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getNewsASC0Count30($table, $where, $id, $where2, $id2, $name)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where = '$id' AND $where2 > '$id2' ORDER BY $name ASC");
        $num = $query->rowCount();
        return $num;
    }

    public function getNewsASC1G30($table, $where, $id, $where2, $id2, $where3, $id3, $name)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where = '$id' AND $where2 <= '$id2' AND $where3 = '$id3' ORDER BY $name ASC");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getNewsASC1CountG30($table, $where, $id, $where2, $id2, $where3, $id3, $name)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where = '$id' AND $where2 <= '$id2' AND $where3 = '$id3' ORDER BY $name ASC");
        $num = $query->rowCount();
        return $num;
    }

    public function getlastRow($table, $where, $value, $id)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE  $where='$value' ORDER BY $id DESC LIMIT 1");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getDataPoints()
    {
        $query = $this->_pdo->query("SELECT * FROM descriptionlabels INNER JOIN datapoints ON descriptionlabels.id = datapoints.descriptionlabelid");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getDataRegister()
    {
        $query = $this->_pdo->query("SELECT MONTHNAME(date_registered) as monthname, SUM(status) as amount FROM clients GROUP BY monthname");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getDataRegister1()
    {
        $query = $this->_pdo->query("SELECT MONTHNAME(date_registered) , site_id, COUNT(*) as count FROM clients GROUP BY date_registered, site_id");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getDataRegister2()
    {
        $query = $this->_pdo->query("SELECT DATE_FORMAT(registration_date, '%Y-%m') AS month, gender, COUNT(*) AS count FROM registration_data GROUP BY month, gender");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getDataRegister3($where, $value)
    {
        $query = $this->_pdo->query("SELECT MONTHNAME(date_registered) AS monthname, site_id as site_id, COUNT(*) AS count FROM clients WHERE $where = '$value' GROUP BY monthname, site_id");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getDataRegister4($where, $value, $where1, $value1)
    {
        $query = $this->_pdo->query("SELECT MONTHNAME(date_registered) AS monthname, site_id as site_id, COUNT(*) AS count FROM clients WHERE $where = '$value' AND $where1 = '$value1' GROUP BY monthname, site_id");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }


    public function getDataStaff($table, $where, $id, $where2, $id2, $where3, $id3, $name)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where = '$id' AND $where2 = '$id2' AND $where3 < '$id3' ORDER BY $name ASC");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getDataStaffCount($table, $where, $id, $where2, $id2, $where3, $id3, $name)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where = '$id' AND $where2 = '$id2' AND $where3 < '$id3' ORDER BY $name ASC");
        $num = $query->rowCount();
        return $num;
    }

    public function getDataStaff1($table, $where, $id, $where2, $id2, $where3, $id3, $name)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where = '$id' AND $where2 = '$id2' AND $where3 >= '$id3' ORDER BY $name ASC");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getDataStaff1Count($table, $where, $id, $where2, $id2, $where3, $id3, $name)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where = '$id' AND $where2 = '$id2' AND $where3 >= '$id3' ORDER BY $name ASC");
        $num = $query->rowCount();
        return $num;
    }


    public function clearDataTable($table)
    {
        $query = $this->_pdo->query("TRUNCATE TABLE `$table`");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function deleteDataTable($table, $site)
    {
        $query = $this->_pdo->query("DELETE FROM `$table` WHERE 'site_id' = '$site'");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    // public function clearDataTable($table)
    // {
    //     $query = $this->_pdo->query("TRUNCATE TABLE `$table`");
    //     $result = $query->fetchAll(PDO::FETCH_ASSOC);
    //     return $result;
    // }

    public function AllTables()
    {
        $query = $this->_pdo->query("SHOW TABLES");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }


    public function AllTablesCont()
    {
        $query = $this->_pdo->query("SHOW TABLES");
        $num = $query->rowCount();
        return $num;
    }


    public function countRowsWithStatus($form_status_value)
    {
        $totalCount = 0;
        $allowedTables = ['screening', 'enrollment_form', 'respiratory', 'diagnosis', 'diagnosis_test'];

        // Get all table names
        $tablesQuery = $this->_pdo->query("SHOW TABLES");
        $tables = $tablesQuery->fetchAll(PDO::FETCH_COLUMN);

        // Iterate through allowed tables and count rows where form_statu = 1
        foreach ($tables as $table) {
            if (in_array($table, $allowedTables)) {
                $countQuery = $this->_pdo->query("SELECT COUNT(*) FROM `$table` WHERE form_status = '$form_status_value'");
                $count = $countQuery->fetchColumn();
                $totalCount += $count;
            }
        }

        return $totalCount;
    }


    public function AllColmuns($table)
    {
        $query = $this->_pdo->query("SHOW COLUMNS FROM $table");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function AllColmunsComments($table)
    {
        $query = $this->_pdo->query("SELECT COLUMN_NAME, COLUMN_COMMENT FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = '$table'");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }



    public function AllDatabasesCount()
    {
        $query = $this->_pdo->query("SHOW DATABASES");
        $num = $query->rowCount();
        return $num;
    }

    public function AllDatabases()
    {
        $query = $this->_pdo->query("SHOW DATABASES");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function setSiteId($table, $site_id, $value1, $value2)
    {
        $query = $this->_pdo->query("UPDATE $table SET $site_id='$value1' WHERE $value2");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function UnsetId($table, $where1, $value1, $where2, $value2, $where)
    {
        $query = $this->_pdo->query("UPDATE $table SET $where1 = '$value1' AND $where2 = '$value2' WHERE '$where'");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }



    public function getNews7Month()
    {
        $query = $this->_pdo->query("SELECT * FROM clients WHERE MONTH(created_on) >= MONTH(NOW() - INTERVAL 2 MONTH)
                            AND (YEAR(created_on) <= YEAR(NOW() - INTERVAL 0 MONTH))");

        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }


    public function getNews7Month2()
    {
        $query = $this->_pdo->query("SELECT * FROM clients WHERE MONTH(created_on) >= MONTH(NOW() - INTERVAL 2 MONTH)
                            AND (YEAR(created_on) <= YEAR(NOW() - INTERVAL 0 MONTH))");
        $num = $query->rowCount();
        return $num;
    }

    public function getMonthData()
    {
        $query = $this->_pdo->query("SELECT YEAR(created_on) AS year, MONTH(created_on) AS month, COUNT(*) AS records_count 
          FROM clients 
          GROUP BY YEAR(created_on), MONTH(created_on) 
          ORDER BY YEAR(created_on), MONTH(created_on)");

        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getMonthSum()
    {
        $query = $this->_pdo->query("SELECT MONTHNAME(created_on) as monthname, SUM(status) as amount FROM clients WHERE status = 1 GROUP BY monthname");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getMonthCount()
    {
        $query = $this->_pdo->query("SELECT MONTHNAME(created_on) as monthname, COUNT(status) as amount FROM clients WHERE status = 1 GROUP BY monthname");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getMonthCountSite($site_id)
    {
        $query = $this->_pdo->query("SELECT site_id, MONTHNAME(created_on) as monthname,COUNT(*) as count_data FROM clients WHERE site_id = '$site_id' AND status = 1 GROUP BY monthname, site_id ORDER BY monthname, site_id");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getMonthCountSiteTest($startDate, $endDate)
    {
        $query = $this->_pdo->query("SELECT site_id as site, MONTH(created_on) as month, COUNT(*) as count_data
        FROM clients
        WHERE created_on BETWEEN '$startDate' AND '$endDate'
        GROUP BY site, MONTH(created_on)
        ORDER BY site, MONTH(created_on)");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getcolumns($table, $id, $date, $firstname, $age)
    {
        $query = $this->_pdo->query("SELECT $id,$date, $firstname, $age FROM $table");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getWithLimit1SearchCount($table, $where, $id, $where1, $id1, $searchTerm, $where3, $where4, $where5, $where6)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE ($where3 LIKE '%$searchTerm%' OR $where4 LIKE '%$searchTerm%' OR $where5 LIKE '%$searchTerm%' OR $where6 LIKE '%$searchTerm%') AND ($where = '$id' AND $where1 = '$id1')");
        $num = $query->rowCount();
        return $num;
    }

    public function getWithLimit1Search($table, $where, $id, $where1, $id1, $page, $numRec, $searchTerm, $where3, $where4, $where5, $where6)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE ($where3 LIKE '%$searchTerm%' OR $where4 LIKE '%$searchTerm%' OR $where5 LIKE '%$searchTerm%' OR $where6 LIKE '%$searchTerm%') AND ($where = '$id' AND $where1 = '$id1') limit $page,$numRec");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getWithLimit3Search($table, $where, $id, $where1, $id1, $where2, $id2, $page, $numRec, $searchTerm, $where3, $where4, $where5, $where6)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE ($where3 LIKE '%$searchTerm%' OR $where4 LIKE '%$searchTerm%' OR $where5 LIKE '%$searchTerm%' OR $where6 LIKE '%$searchTerm%') AND ($where = '$id' AND $where1 = '$id1' AND $where2 = '$id2') limit $page,$numRec");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }


    public function getWithLimit3SearchCount($table, $where, $id, $where1, $id1, $where2, $id2, $searchTerm, $where3, $where4, $where5, $where6)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE ($where3 LIKE '%$searchTerm%' OR $where4 LIKE '%$searchTerm%' OR $where5 LIKE '%$searchTerm%' OR $where6 LIKE '%$searchTerm%') AND ($where = '$id' AND $where1 = '$id1' AND $where2 = '$id2')");
        $num = $query->rowCount();
        return $num;
    }

    public function getWithLimitSearch0($table, $page, $numRec, $searchTerm, $where3, $where4, $where5, $where6)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE ($where3 LIKE '%$searchTerm%' OR $where4 LIKE '%$searchTerm%' OR $where5 LIKE '%$searchTerm%' OR $where6 LIKE '%$searchTerm%') limit $page,$numRec");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getWithLimitSearch($table, $where, $id, $page, $numRec, $searchTerm, $where3, $where4, $where5, $where6)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE ($where3 LIKE '%$searchTerm%' OR $where4 LIKE '%$searchTerm%' OR $where5 LIKE '%$searchTerm%' OR $where6 LIKE '%$searchTerm%') AND ($where = '$id') limit $page,$numRec");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getWithLimit0SearchCount($table, $searchTerm, $where3, $where4, $where5, $where6)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE ($where3 LIKE '%$searchTerm%' OR $where4 LIKE '%$searchTerm%' OR $where5 LIKE '%$searchTerm%' OR $where6 LIKE '%$searchTerm%')");
        $num = $query->rowCount();
        return $num;
    }


    public function getWithLimit0Search($table, $page, $numRec, $searchTerm, $where3, $where4, $where5, $where6)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE ($where3 LIKE '%$searchTerm%' OR $where4 LIKE '%$searchTerm%' OR $where5 LIKE '%$searchTerm%' OR $where6 LIKE '%$searchTerm%')) limit $page,$numRec");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getWithLimitSearchCount($table, $where, $id, $searchTerm, $where3, $where4, $where5, $where6)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE ($where3 LIKE '%$searchTerm%' OR $where4 LIKE '%$searchTerm%' OR $where5 LIKE '%$searchTerm%' OR $where6 LIKE '%$searchTerm%') AND ($where = '$id')");
        $num = $query->rowCount();
        return $num;
    }


    public function getDataLimitSearch($table, $page, $numRec, $searchTerm, $where3, $where4, $where5, $where6)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE ($where3 LIKE '%$searchTerm%' OR $where4 LIKE '%$searchTerm%' OR $where5 LIKE '%$searchTerm%' OR $where6 LIKE '%$searchTerm%') limit $page,$numRec");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getWithLimitSearchNewsCount($table, $searchTerm, $where)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE ($where LIKE '%$searchTerm%')");
        $num = $query->rowCount();
        return $num;
    }

    public function getWithLimitSearchNewsCount1($table, $where, $id, $pid, $searchTerm)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where = '$id' AND ($pid LIKE '%$searchTerm%')");
        $num = $query->rowCount();
        return $num;
    }

    public function getWithLimitSearchNewsCount2($table, $searchTerm, $where, $where1, $id1, $where2, $id2)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where1 = '$id1' AND $where2 = '$id2' AND ($where LIKE '%$searchTerm%')");
        $num = $query->rowCount();
        return $num;
    }


    public function getWithLimitSearchNews($table, $searchTerm, $where, $where1, $id1)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where1 = '$id1' AND ($where LIKE '%$searchTerm%')");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getWithLimitSearchNews1($table, $where, $id, $pid, $searchTerm)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where = '$id' AND ($pid LIKE '%$searchTerm%')");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    // public function getWithLimitSearchNews1($table, $searchTerm, $where, $where1, $id1, $where2, $id2)
    // {
    //     $sql = "SELECT * FROM $table WHERE $where1 = :id1 AND $where2 = :id2 AND $where LIKE :searchTerm";
    //     $stmt = $this->_pdo->prepare($sql);

    //     // Bind parameters
    //     $stmt->bindValue(':id1', $id1, PDO::PARAM_INT);
    //     $stmt->bindValue(':id2', $id2, PDO::PARAM_INT);
    //     $stmt->bindValue(':searchTerm', "%$searchTerm%", PDO::PARAM_STR);

    //     $stmt->execute();
    //     return $stmt->fetchAll(PDO::FETCH_ASSOC);
    // }


    public function getWithLimitSearchNews2($table, $searchTerm, $where, $where1, $id1, $where2, $id2, $where3, $id3)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where1 = '$id1' AND $where2 = '$id2' AND $where3 = '$id3' AND ($where LIKE '%$searchTerm%')");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
}
