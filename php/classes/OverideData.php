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

    public function get2($table, $where, $id, $where2, $id2)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where = '$id' AND $where2 = '$id2'");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }


    public function getUniqueYears($table1, $date_col1, $table2, $date_col2)
    {
        $query = $this->_pdo->query("
        SELECT DISTINCT YEAR($date_col1) as year FROM $table1
        UNION
        SELECT DISTINCT YEAR($date_col2) as year FROM $table2
    ");

        return $query->fetchAll(PDO::FETCH_COLUMN);
    }

    public function countDataByYear($table, $date_col, $year, $field, $value, $site_field, $site_id)
    {
        $query = $this->_pdo->prepare("
        SELECT COUNT(*) as count FROM $table
        WHERE $field = ? AND YEAR($date_col) = ? AND $site_field = ?
    ");
        $query->execute([$value, $year, $site_id]);
        return $query->fetch(PDO::FETCH_ASSOC)['count'];
    }

    public function countDataByMonthYear($table, $date_col, $year, $field, $value, $site_field, $site_id)
    {
        $query = $this->_pdo->prepare("
        SELECT COUNT(*) as count FROM $table
        WHERE $field = ? AND YEAR($date_col) = ? AND $site_field = ?
    ");
        $query->execute([$value, $year, $site_id]);
        return $query->fetch(PDO::FETCH_ASSOC)['count'];
    }




    public function get6($table, $where, $id, $where2, $id2, $where3, $id3, $where4, $id4)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where = '$id' AND $where2 = '$id2' AND $where3 = '$id3' AND $where4 = '$id4'");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    // public function get3($table, $where, $id, $where2, $id2, $where3, $id3)
    // {
    //     $query = $this->_pdo->query("SELECT * FROM $table WHERE $where = '$id' AND $where2 = '$id2' AND $where3 = '$id3'");
    //     $result = $query->fetchAll(PDO::FETCH_ASSOC);
    //     return $result;
    // }

    // public function get4($table, $where, $id, $where2)
    // {
    //     $query = $this->_pdo->query("SELECT * FROM $table WHERE $where = '$id' AND $where2 >= 20");
    //     $result = $query->fetchAll(PDO::FETCH_ASSOC);
    //     return $result;
    // }

    public function get5($table, $where, $id, $id2, $where2)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where = '$id' AND $id2 >= '$where2'");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getNo0($table, $field, $value, $field1, $value1, $field2, $value2)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $field = '$value' AND ($field1 = '$value1' OR $field2 = '$value2')");
        $num = $query->rowCount();
        return $num;
    }



    public function getNo1All($table, $field, $value)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $field < '$value'");
        $num = $query->rowCount();
        return $num;
    }

    public function get0($table, $where, $id, $where1, $id1)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where = '$id' AND $where1 >= '$id1'");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getAsc($table, $where, $id)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where = '$id' ORDER BY 'medication_id' ASC");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getNo1($table, $field, $value, $field1, $value1)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $field < '$value' AND $field1 = '$value1'");
        $num = $query->rowCount();
        return $num;
    }

    public function getNo2($table, $field, $value, $field1, $value1, $field2, $value2)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $field < '$value' AND $field1 = '$value1' AND $field2 = '$value2'");
        $num = $query->rowCount();
        return $num;
    }

    public function getNoType0($table, $field, $value, $field1, $value1, $field2, $value2, $field3, $value3, $field4, $value4, $field5, $value5)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $field = '$value' AND ($field1 = '$value1' OR $field2 = '$value2' OR $field3 = '$value3' OR $field4 = '$value4' OR $field5 = '$value5')");
        $num = $query->rowCount();
        return $num;
    }

    public function getNoType($table, $field, $value, $field1, $value1, $field1_1, $value1_1, $field2, $value2, $field3, $value3, $field4, $value4, $field5, $value5, $field6, $value6, $field96, $value96)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $field = '$value' AND $field1 = '$value1' AND ($field1_1 = '$value1_1' OR $field2 = '$value2' OR $field3 = '$value3' OR $field4 = '$value4' OR $field5 = '$value5' OR $field6 = '$value6' OR $field96 = '$value96')");
        $num = $query->rowCount();
        return $num;
    }

    public function getCount6($table, $field, $value, $field1, $value1, $field2, $value2)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $field = '$value' AND $field1 = '$value1' AND $field2 <= '$value2'");
        $num = $query->rowCount();
        return $num;
    }

    public function getCount5($table, $field, $value, $field1, $value1, $field2, $value2)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $field = '$value' AND $field1 = '$value1' AND $field2 <= '$value2'");
        $num = $query->rowCount();
        return $num;
    }

    public function getCount4($table, $field, $value, $field1, $value1)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $field = '$value' AND $field1 <= '$value1'");
        $num = $query->rowCount();
        return $num;
    }



    public function getCountNULL($table, $field, $value, $field1)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $field = '$value' AND $field1 IS NULL");
        $num = $query->rowCount();
        return $num;
    }

    public function getCountNULL1($table, $field, $value, $field2, $value2, $field1)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $field = '$value' AND $field2 = '$value2' AND $field1 IS NULL");
        $num = $query->rowCount();
        return $num;
    }

    public function getCount($table, $field, $value)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $field = '$value'");
        $num = $query->rowCount();
        return $num;
    }

    public function getCountAugust($table, $field, $value, $field1, $value1)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $field = '$value' AND $field1 <= '$value1'");
        $num = $query->rowCount();
        return $num;
    }

    public function getCount1($table, $field, $value, $field1, $value1)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $field = '$value' AND $field1 = '$value1'");
        $num = $query->rowCount();
        return $num;
    }

    public function getCount1August($table, $field, $value, $field1, $value1, $field2, $value2)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $field = '$value' AND $field1 = '$value1' AND $field2 <= '$value2'");
        $num = $query->rowCount();
        return $num;
    }


    public function getCount2($table, $field, $value, $field1, $value1, $field2, $value2)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $field = '$value' AND $field1 = '$value1' OR $field2 = '$value2'");
        $num = $query->rowCount();
        return $num;
    }

    public function getCount2August($table, $field, $value, $field1, $value1, $field2, $value2, $field3, $value3)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $field = '$value' AND $field1 = '$value1' AND $field2 = '$value2' AND $field3 <= '$value3'");
        $num = $query->rowCount();
        return $num;
    }

    public function getCount3August($table, $field, $value, $field1, $value1, $field2, $value2, $field3, $value3, $field4, $value4)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $field = '$value' AND $field1 = '$value1' AND $field2 = '$value2' AND $field3 = '$value3' AND $field4 <= '$value4'");
        $num = $query->rowCount();
        return $num;
    }

    public function getCountStatus($table, $field, $value, $field1, $value1, $field2, $value2)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $field = '$value' AND $field1 >= '$value1' AND  $field2 = '$value2'");
        $num = $query->rowCount();
        return $num;
    }

    public function getCountStatus1($table, $field, $value, $field1, $value1, $field2, $value2, $field3, $value3)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $field = '$value' AND $field1 >= '$value1' AND  $field2 = '$value2' AND  $field3 <= '$value3'");
        $num = $query->rowCount();
        return $num;
    }

    public function setSiteId($table, $site_id, $value1, $value2)
    {
        $query = $this->_pdo->query("UPDATE $table SET $site_id='$value1' WHERE $value2");
        $num = $query->rowCount();
        return $num;
    }

    public function setStudyId($table, $study_id, $value1, $value2)
    {
        $query = $this->_pdo->query("UPDATE $table SET $study_id='$value1' WHERE $value2");
        $num = $query->rowCount();
        return $num;
    }

    public function UnsetStudyId($table, $study_id, $value1, $value2)
    {
        $query = $this->_pdo->query("UPDATE $table SET $study_id='$value1' WHERE $value2");
        $num = $query->rowCount();
        return $num;
    }


    public function DoctorConfirm($table, $site_id, $value1, $value2)
    {
        $query = $this->_pdo->query("UPDATE $table SET $site_id='$value1' WHERE $value2");
        $num = $query->rowCount();
        return $num;
    }

    public function countData2Active($table, $field, $value, $field1, $value1, $field2, $value2)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $field = '$value' AND $field1 = '$value1' AND $field2 < '$value2'");
        $num = $query->rowCount();
        return $num;
    }

    public function countData2Locked($table, $field, $value, $field1, $value1, $field2, $value2)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $field = '$value' AND $field1 = '$value1' AND $field2 >= '$value2'");
        $num = $query->rowCount();
        return $num;
    }

    public function countData($table, $field, $value, $field1, $value1)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $field = '$value' AND $field1 = '$value1'");
        $num = $query->rowCount();
        return $num;
    }

    public function countData1($table, $where, $id, $where2, $id2, $where3, $id3)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where = '$id' AND $where2 = '$id2' AND $where3 = '$id3'");
        $num = $query->rowCount();
        return $num;
    }

    public function countData2($table, $field, $value, $field1, $value1, $field2, $value2)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $field = '$value' AND $field1 = '$value1' AND $field2 = '$value2'");
        $num = $query->rowCount();
        return $num;
    }

    public function countData3($table, $field, $value, $field1, $value1, $field2, $value2, $field3, $value3)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $field = '$value' AND $field1 = '$value1' AND ($field2 = '$value2' OR $field3 = '$value3')");
        $num = $query->rowCount();
        return $num;
    }

    public function countData4($table, $field, $value, $field1, $value1, $field2, $value2, $field3, $value3)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $field = '$value' AND $field1 = '$value1' AND $field2 = '$value2' AND $field3 = '$value3'");
        $num = $query->rowCount();
        return $num;
    }

    public function getDataAsc($table, $where, $id, $name)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where = '$id' ORDER BY $name ASC");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getDataAsc1($table, $where, $id, $where1, $id1, $name)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where = '$id' AND $where1 = '$id1' ORDER BY $name ASC");
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

    public function getData($table)
    {
        $query = $this->_pdo->query("SELECT * FROM $table");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    public function getData2($table, $field, $value)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $field = '$value'");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getNewsNULL($table, $where, $id, $where2)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where = '$id' AND $where2 IS NULL");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getNewsNULL1($table, $where, $id, $where2, $where3, $where1)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where = '$id' AND $where2 = '$where3' AND $where1 IS NULL");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getNews($table, $where, $id, $where2, $id2)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where = '$id' AND $where2 = '$id2'");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getNews0($table, $where, $id)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where < '$id'");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getNews1($table, $where, $id, $where2, $id2)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where < '$id' AND $where2 = '$id2'");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getNews2($table, $where, $id, $where2, $id2, $where3, $id3)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where < '$id' AND $where2 = '$id2' AND $where3 = '$id3'");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getNews4($table, $where, $id, $where2, $id2, $where3, $id3, $where4, $id4)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where < '$id' AND $where2 = '$id2' AND $where3 = '$id3' AND $where4 = '$id4'");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getNews3($table, $where, $id, $where2, $id2)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where = '$id' AND $where2 = '$id2'");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getNewsAsc($table, $where, $id, $where2, $id2)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where = '$id' AND $where2 = '$id2' ORDER id ASC ");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getNewsAsc0($table, $where, $id, $where2, $id2)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where = '$id' AND $where2 = '$id2' ORDER BY $where2 ASC");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    // public function getNewsAsc($table, $where, $id, $where2, $id2, $id3)
    // {
    //     $query = $this->_pdo->query("SELECT * FROM $table WHERE $where = '$id' AND $where2 = '$id2' ORDER BY $id3 ASC");
    //     $result = $query->fetchAll(PDO::FETCH_ASSOC);
    //     return $result;
    // }

    public function getNewsAsc1($table, $where, $id, $where2, $id2, $where3, $id3, $id4)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where = '$id' AND $where2 = '$id2' AND $where3 = '$id3' ORDER BY $id4");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function get3($table, $where, $id, $where2, $id2, $where3, $id3)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where = '$id' AND $where2 = '$id2' AND $where3 = '$id3'");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function get4($table, $where, $id, $where2, $id2, $where3, $id3, $where4, $id4)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where = '$id' AND $where2 = '$id2' AND $where3 = '$id3'  AND $where4 = '$id4'");
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


    // public function getSumD1($table, $variable, $field, $value)
    // {
    //     $query = $this->_pdo->query("SELECT SUM($variable) FROM $table WHERE $field = '$value' ");
    //     $result = $query->fetchAll(PDO::FETCH_ASSOC);
    //     return $result;
    // }

    public function getSumD2($table, $variable, $field, $value, $field1, $value1)
    {
        $query = $this->_pdo->query("SELECT SUM($variable) FROM $table WHERE $field = '$value' AND $field1 = '$value1'");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function get($table, $where, $id)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where = '$id'");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function get1($table, $where, $id, $where1, $id1)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where = '$id' AND $where1 = '$id1'");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getNews5($table, $where, $id, $where1, $id1, $where2, $id2)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where = '$id' AND $where1 = '$id1' AND $where2 = '$id2'");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getRQ1($table)
    {
        $query = $this->_pdo->query("SELECT * FROM $table");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function get_new($table, $where, $id, $where1, $type)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where = '$id' AND $where1 = '$type'");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function delete($table, $field, $value)
    {
        return $this->_pdo->query("DELETE FROM $table WHERE $field = $value");
    }

    public function lastRow($table, $value)
    {
        $query = $this->_pdo->query("SELECT * FROM $table ORDER BY $value DESC LIMIT 1");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getlastRow($table, $where, $value, $id)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE  $where='$value' ORDER BY $id DESC LIMIT 1");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getlastRow1($table, $where, $value, $where1, $value1, $id)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE  $where='$value' AND $where1='$value1' ORDER BY $id DESC LIMIT 1");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getWithLimit3Active($table, $where, $id, $where2, $id2, $where3, $id3, $page, $numRec)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where = '$id' AND $where2 = '$id2' AND $where3 < '$id3' limit $page,$numRec");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getWithLimit3Locked($table, $where, $id, $where2, $id2, $where3, $id3, $page, $numRec)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where = '$id' AND $where2 = '$id2' AND $where3 >= '$id3' limit $page,$numRec");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getWithLimit2($table, $field, $value, $field1, $value1, $value2, $field2, $page, $numRec)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $field = '$value' AND $field1 = '$value1' AND $value2 = '$field2' limit $page,$numRec");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    public function getWithLimit1($table, $where, $id, $where2, $id2, $page, $numRec)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where = '$id' AND $where2 = '$id2' limit $page,$numRec");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getWithLimit11($table, $where, $id, $where2, $id2, $where3, $id3, $page, $numRec)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where = '$id' AND $where2 = '$id2' OR $where3 = '$id3'  limit $page,$numRec");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getWithLimit3($table, $where, $id, $where2, $id2, $where3, $id3, $page, $numRec)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where = '$id' AND $where2 = '$id2' AND $where3 = '$id3' limit $page,$numRec");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getWithLimit4($table, $where, $id, $where2, $id2, $where3, $id3, $where4, $id4, $page, $numRec)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where = '$id' AND $where2 = '$id2' AND $where3 = '$id3' OR $where4 = '$id4' limit $page,$numRec");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getWithLimit($table, $where, $id, $page, $numRec)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where = '$id' limit $page,$numRec");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getDataLimit($table, $page, $numRec)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE 1 limit $page,$numRec");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    function tableHeader($table)
    {
        $query = $this->_pdo->query("DESCRIBE $table");
        $result = $query->fetchAll(PDO::FETCH_COLUMN);
        return $result;
    }

    public function firstRow($table, $param, $id, $where, $client_id)
    {
        $query = $this->_pdo->query("SELECT DISTINCT $param FROM $table WHERE $where = '$client_id' ORDER BY '$id' ASC");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    // public function firstRow($table, $param, $id, $where, $client_id)
    // {
    //     $query = $this->_pdo->query("SELECT DISTINCT $param FROM $table WHERE $where = '$client_id' ORDER BY '$id' ASC");
    //     $result = $query->fetchAll(PDO::FETCH_ASSOC);
    //     return $result;
    // }

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

    public function searchClient($firstname, $middlename, $lastname, $client_id)
    {
        $query = $this->_pdo->query("SELECT * FROM clients WHERE firstname LIKE '%$firstname%' OR middlename LIKE '%$middlename%' OR lastname LIKE '%$lastname%' OR id LIKE '%$client_id%'");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }


    public function getReport($table1, $table2, $id1, $id2)
    {
        $query = $this->_pdo->query("SELECT '$table2'.'$id2','$table1'.'$id1' FROM $table2 INNER JOIN '$table1' ON '$table2'.'$id2'='$table1'.'$id1'");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getReport2($table1)
    {
        $query = $this->_pdo->query("SELECT screening.client_id,clients.id FROM screening INNER JOIN clients ON screening.client_id=clients.id");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function searchBtnDate3($table, $var, $value, $var1, $value1, $var2, $value2)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $var >= '$value' AND $var1 <= '$value1' AND $var2 = '$value2'");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getCountReport($table, $field, $value, $where2, $id2, $where3, $id3)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $field >= '$value' AND $where2 <= '$id2' AND $where3 = '$id3'");
        $num = $query->rowCount();
        return $num;
    }

    public function clearDataTable($table)
    {
        $query = $this->_pdo->query("TRUNCATE TABLE `$table`");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

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


    public function DBbackups()
    {
        $get_all_table_query = $this->_pdo->query("SHOW DATABASES");
        $result = $get_all_table_query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function DBCreateTables($table)
    {
        $show_table_query = $this->_pdo->query("SHOW CREATE TABLE " . $table);
        // $result = $show_table_query->fetchAll(PDO::FETCH_ASSOC);
        return $show_table_query;
    }


    public function MissingData()
    {
        $query = $this->_pdo->query("select a.*,
        (select status from crf1 c1 where c1.study_id = a.study_id and c1.vcode = a.visit_code) as crf1,
        (select status from crf2 c2 where c2.study_id = a.study_id and c2.vcode = a.visit_code) as crf2,
        (select status from crf3 c3 where c3.study_id = a.study_id and c3.vcode = a.visit_code) as crf3,
        (select status from crf4 c4 where c4.study_id = a.study_id and c4.vcode = a.visit_code) as crf4,
        (select status from crf5 c5 where c5.study_id = a.study_id and c5.vcode = a.visit_code) as crf5,
        (select status from crf6 c6 where c6.study_id = a.study_id and c6.vcode = a.visit_code) as crf6,
        (select distinct status from crf7 c7 where c7.study_id = a.study_id and c7.vcode = a.visit_code) as crf7
        from
        (select distinct a.visit_code, g.study_id, a.expected_date, a.visit_status, a.visit_date
        from visit a left join
        (select distinct (a.study_id) from visit a where a.study_id not in ('') ) g on a.study_id = g.study_id
        where
        g.study_id is not null) a where (case when a.visit_code = 'D0' then a.expected_date in ('') else a.expected_date < CURDATE() end) and a.visit_status is null order by a.study_id");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function MissingDataNo()
    {
        $query = $this->_pdo->query("select a.*,
        (select status from crf1 c1 where c1.study_id = a.study_id and c1.vcode = a.visit_code) as crf1,
        (select status from crf2 c2 where c2.study_id = a.study_id and c2.vcode = a.visit_code) as crf2,
        (select status from crf3 c3 where c3.study_id = a.study_id and c3.vcode = a.visit_code) as crf3,
        (select status from crf4 c4 where c4.study_id = a.study_id and c4.vcode = a.visit_code) as crf4,
        (select status from crf5 c5 where c5.study_id = a.study_id and c5.vcode = a.visit_code) as crf5,
        (select status from crf6 c6 where c6.study_id = a.study_id and c6.vcode = a.visit_code) as crf6,
        (select distinct status from crf7 c7 where c7.study_id = a.study_id and c7.vcode = a.visit_code) as crf7
        from
        (select distinct a.visit_code, g.study_id, a.expected_date, a.visit_status, a.visit_date
        from visit a left join
        (select distinct (a.study_id) from visit a where a.study_id not in ('') ) g on a.study_id = g.study_id
        where
        g.study_id is not null) a where (case when a.visit_code = 'D0' then a.expected_date in ('') else a.expected_date < CURDATE() end) and a.visit_status is null order by a.study_id");
        $num = $query->rowCount();
        return $num;
    }

    public function MissingData1()
    {
        $query = $this->_pdo->query("select * from visit where expected_date < CURDATE() and visit_status is null");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function MissingDataNo1()
    {
        $query = $this->_pdo->query("select * from visit where expected_date < CURDATE() and visit_status is null");
        $num = $query->rowCount();
        return $num;
    }

    public function MissingDataNoSite($site_id)
    {
        $query = $this->_pdo->query("select * from visit where expected_date < CURDATE()  and site_id = '$site_id' and visit_status is null");
        $num = $query->rowCount();
        return $num;
    }

    public function MissingDataNoAll()
    {
        $query = $this->_pdo->query("select * from visit where expected_date < CURDATE() and visit_status is null");
        $num = $query->rowCount();
        return $num;
    }


    public function MissingData2()
    {
        $query = $this->_pdo->query("select distinct a.visit_code, g.study_id,
                (select status from crf1 c1 where c1.study_id = a.study_id and c1.vcode = a.visit_code) as crf1,
                (select status from crf2 c2 where c2.study_id = a.study_id and c2.vcode = a.visit_code) as crf2,
                (select status from crf3 c3 where c3.study_id = a.study_id and c3.vcode = a.visit_code) as crf3,
                (select status from crf4 c4 where c4.study_id = a.study_id and c4.vcode = a.visit_code) as crf4,
                (select status from crf5 c5 where c5.study_id = a.study_id and c5.vcode = a.visit_code) as crf5,
                (select status from crf6 c6 where c6.study_id = a.study_id and c6.vcode = a.visit_code) as crf6,
                (select distinct status from crf7 c7 where c7.study_id = a.study_id and c7.vcode = a.visit_code) as crf7
                from visit a left join
                (select distinct (a.study_id) from visit a where a.study_id not in ('') ) g on a.study_id = g.study_id
                where g.study_id is not null order by study_id");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function MissingDataNo2()
    {
        $query = $this->_pdo->query("select distinct a.visit_code, g.study_id,
                (select status from crf1 c1 where c1.study_id = a.study_id and c1.vcode = a.visit_code) as crf1,
                (select status from crf2 c2 where c2.study_id = a.study_id and c2.vcode = a.visit_code) as crf2,
                (select status from crf3 c3 where c3.study_id = a.study_id and c3.vcode = a.visit_code) as crf3,
                (select status from crf4 c4 where c4.study_id = a.study_id and c4.vcode = a.visit_code) as crf4,
                (select status from crf5 c5 where c5.study_id = a.study_id and c5.vcode = a.visit_code) as crf5,
                (select status from crf6 c6 where c6.study_id = a.study_id and c6.vcode = a.visit_code) as crf6,
                (select distinct status from crf7 c7 where c7.study_id = a.study_id and c7.vcode = a.visit_code) as crf7
                from visit a left join
                (select distinct (a.study_id) from visit a where a.study_id not in ('') ) g on a.study_id = g.study_id
                where g.study_id is not null order by study_id");
        $num = $query->rowCount();
        return $num;
    }


    public function AvailableDataNoAll()
    {
        $query = $this->_pdo->query("select a.*,
        (select status from crf1 c1 where c1.study_id = a.study_id and c1.vcode = a.visit_code) as crf1,
        (select status from crf2 c2 where c2.study_id = a.study_id and c2.vcode = a.visit_code) as crf2,
        (select status from crf3 c3 where c3.study_id = a.study_id and c3.vcode = a.visit_code) as crf3,
        (select status from crf4 c4 where c4.study_id = a.study_id and c4.vcode = a.visit_code) as crf4,
        (select status from crf5 c5 where c5.study_id = a.study_id and c5.vcode = a.visit_code) as crf5,
        (select status from crf6 c6 where c6.study_id = a.study_id and c6.vcode = a.visit_code) as crf6,
        (select distinct status from crf7 c7 where c7.study_id = a.study_id and c7.vcode = a.visit_code) as crf7
        from
        (select distinct a.visit_code, g.study_id, a.expected_date, a.visit_status, a.visit_date
        from visit a left join
        (select distinct (a.study_id) from visit a where a.study_id not in ('') ) g on a.study_id = g.study_id
        where
        g.study_id is not null) a where (case when a.visit_code = 'D0' then a.expected_date in ('') else a.expected_date < CURDATE() end) and a.visit_status is null order by a.study_id");
        $num = $query->rowCount();
        return $num;
    }


    public function AvailableDataAll()
    {
        $query = $this->_pdo->query("select a.*,
        (select status from crf1 c1 where c1.study_id = a.study_id and c1.vcode = a.visit_code) as crf1,
        (select status from crf2 c2 where c2.study_id = a.study_id and c2.vcode = a.visit_code) as crf2,
        (select status from crf3 c3 where c3.study_id = a.study_id and c3.vcode = a.visit_code) as crf3,
        (select status from crf4 c4 where c4.study_id = a.study_id and c4.vcode = a.visit_code) as crf4,
        (select status from crf5 c5 where c5.study_id = a.study_id and c5.vcode = a.visit_code) as crf5,
        (select status from crf6 c6 where c6.study_id = a.study_id and c6.vcode = a.visit_code) as crf6,
        (select distinct status from crf7 c7 where c7.study_id = a.study_id and c7.vcode = a.visit_code) as crf7
        from
        (select distinct a.visit_code, g.study_id, a.expected_date, a.visit_status, a.visit_date
        from visit a left join
        (select distinct (a.study_id) from visit a where a.study_id not in ('') ) g on a.study_id = g.study_id
        where
        g.study_id is not null) a where (case when a.visit_code = 'D0' then a.expected_date in ('') else a.expected_date < CURDATE() end) and a.visit_status is null order by a.study_id");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }



    // $backup_config = array(
    //     'DB_HOST' => '127.0.0.1',////Database hostname
    //     'DB_NAME' => 'test_db',//Database name to backup
    //     'DB_USERNAME' => 'root',//Database account username
    //     'DB_PASSWORD' => '',//Database account password
    //     'INCLUDE_DROP_TABLE' => false,//Include DROP TABLE IF EXISTS
    //     'SAVE_DIR' => '',//Folder to save file in
    //     'SAVE_AS' => 'test_db-',//Prepend filename
    //     'APPEND_DATE_FORMAT' => 'Y-m-d-H-i',//Append date to file name
    //     'TIMEZONE' => 'UTC',//Timezone for date format
    //     'COMPRESS' => true,//Compress into gz otherwise keep as .sql
    // );
    // echo backupDB($backup_config);

    // function backupDB(array $config): string
    // {
    // $db = new PDO("mysql:host={$config['DB_HOST']};dbname={$config['DB_NAME']}; charset=utf8", $config['DB_USERNAME'], $config['DB_PASSWORD']);
    // $db->setAttribute(PDO::ATTR_ORACLE_NULLS, PDO::NULL_NATURAL);
    // date_default_timezone_set($config['TIMEZONE']);
    // $do_compress = $config['COMPRESS'];
    // if ($do_compress) {
    //     $save_string = $config['SAVE_AS'] . $config['SAVE_DIR'] . date($config['APPEND_DATE_FORMAT']) . '.sql.gz';
    //     $zp = gzopen($save_string, "a9");
    // } else {
    //     $save_string = $config['SAVE_AS'] . $config['SAVE_DIR'] . date($config['APPEND_DATE_FORMAT']) . '.sql';
    //     $handle = fopen($save_string, 'a+');
    // }

    //array of all database field types which just take numbers
    // $numtypes = array('tinyint', 'smallint', 'mediumint', 'int', 'bigint', 'float', 'double', 'decimal', 'real');
    // $return = "";
    // $return .= "CREATE DATABASE `{$config['DB_NAME']}`;\n";
    // $return .= "USE `{$config['DB_NAME']}`;\n";
    // //get all tables
    // $pstm1 = $db->query('SHOW TABLES');
    // while ($row = $pstm1->fetch(PDO::FETCH_NUM)) {
    //     $tables[] = $row[0];
    // }
    // //cycle through the table(s)
    // foreach ($tables as $table) {
    //     $result = $db->query("SELECT * FROM $table");
    //     $num_fields = $result->columnCount();
    //     $num_rows = $result->rowCount();
    //     if ($config['INCLUDE_DROP_TABLE']) {
    //         $return .= 'DROP TABLE IF EXISTS `' . $table . '`;';
    //     }
    //     //table structure
    //     $pstm2 = $db->query("SHOW CREATE TABLE $table");
    //     $row2 = $pstm2->fetch(PDO::FETCH_NUM);
    //     $ifnotexists = str_replace('CREATE TABLE', 'CREATE TABLE IF NOT EXISTS', $row2[1]);
    //     $return .= "\n\n" . $ifnotexists . ";\n\n";
    //     if ($do_compress) {
    //         gzwrite($zp, $return);
    //     } else {
    //         fwrite($handle, $return);
    //     }
    //     $return = "";
    //     //insert values
    //     if ($num_rows) {
    //         $return = 'INSERT INTO `' . $table . '` (';
    //         $pstm3 = $db->query("SHOW COLUMNS FROM $table");
    //         $count = 0;
    //         $type = array();
    //         while ($rows = $pstm3->fetch(PDO::FETCH_NUM)) {
    //             if (stripos($rows[1], '(')) {
    //                 $type[$table][] = stristr($rows[1], '(', true);
    //             } else {
    //                 $type[$table][] = $rows[1];
    //             }
    //             $return .= "`" . $rows[0] . "`";
    //             $count++;
    //             if ($count < ($pstm3->rowCount())) {
    //                 $return .= ", ";
    //             }
    //         }
    //         $return .= ")" . ' VALUES';
    //         if ($do_compress) {
    //             gzwrite($zp, $return);
    //         } else {
    //             fwrite($handle, $return);
    //         }
    //         $return = "";
    //     }
    //     $counter = 0;
    //     while ($row = $result->fetch(PDO::FETCH_NUM)) {
    //         $return = "\n\t(";
    //         for ($j = 0; $j < $num_fields; $j++) {
    //             if (isset($row[$j])) {
    //                 //if number, take away "". else leave as string
    //                 if ((in_array($type[$table][$j], $numtypes)) && (!empty($row[$j]))) {
    //                     $return .= $row[$j];
    //                 } else {
    //                     $return .= $db->quote($row[$j]);
    //                 }
    //             } else {
    //                 $return .= 'NULL';
    //             }
    //             if ($j < ($num_fields - 1)) {
    //                 $return .= ',';
    //             }
    //         }
    //         $counter++;
    //         if ($counter < ($result->rowCount())) {
    //             $return .= "),";
    //         } else {
    //             $return .= ");";
    //         }
    //         if ($do_compress) {
    //             gzwrite($zp, $return);
    //         } else {
    //             fwrite($handle, $return);
    //         }
    //         $return = "";
    //     }
    //     $return = "\n\n-- ------------------------------------------------ \n\n";
    //     if ($do_compress) {
    //         gzwrite($zp, $return);
    //     } else {
    //         fwrite($handle, $return);
    //     }
    //     $return = "";
    // }
    // $error1 = $pstm2->errorInfo();
    // $error2 = $pstm3->errorInfo();
    // $error3 = $result->errorInfo();
    // echo $error1[2];
    // echo $error2[2];
    // echo $error3[2];
    // if ($do_compress) {
    //     gzclose($zp);
    // } else {
    //     fclose($handle);
    // }
    // return "{$config['DB_NAME']} saved as $save_string";
    // }

    function Export_Database($tables = false, $backup_name = false)
    {
        $queryTables = $this->_pdo->query("SHOW TABLES");
        $result = $queryTables->fetchAll(PDO::FETCH_ASSOC);

        foreach ($result as $table) {

            $result = $this->_pdo->query('SELECT * FROM ' . $tables);
            $fields_amount = $result->columnCount();
            $rows_num = $result->rowCount();
            $res = $this->_pdo->query('SHOW CREATE TABLE ' . $tables);
            $TableMLine = $res->fetchAll(PDO::FETCH_ASSOC);

            $content = (!isset($content) ? '' : $content) . "\n\n" . $TableMLine[1] . ";\n\n";

            for ($i = 0, $st_counter = 0; $i < $fields_amount; $i++, $st_counter = 0) {
                while ($row = $result->fetchAll(PDO::FETCH_ASSOC)) {
                    //when started (and every after 100 command cycle):


                    if ($st_counter % 100 == 0 || $st_counter == 0) {
                        $content .= "\nINSERT INTO " . $tables . " VALUES";
                    }

                    // $user->createRecord($tables, array(
                    //     'study_id' => $_GET['sid'],));


                    $content .= "\n(";
                    for ($j = 0; $j < $fields_amount; $j++) {
                        $row[$j] = str_replace("\n", "\\n", addslashes($row[$j]));
                        if (isset($row[$j])) {
                            $content .= '"' . $row[$j] . '"';
                        } else {
                            $content .= '""';
                        }
                        if ($j < ($fields_amount - 1)) {
                            $content .= ',';
                        }
                    }
                    $content .= ")";
                    //every after 100 command cycle [or at last line] ....p.s. but should be inserted 1 cycle eariler
                    if ((($st_counter + 1) % 100 == 0 && $st_counter != 0) || $st_counter + 1 == $rows_num) {
                        $content .= ";";
                    } else {
                        $content .= ",";
                    }
                    $st_counter = $st_counter + 1;
                }
            }
            $content .= "\n\n\n";
            // return $content;
        }

        $events = $this->_pdo->query("SHOW EVENTS");
        while ($events && ($row = $events->fetchAll(PDO::FETCH_ASSOC))) {
            $res = $this->_pdo->query("SHOW CREATE EVENT " . $row[0] . '.' . $row[1]);
            $TableMLine = $res->fetchAll(PDO::FETCH_ASSOC);
            $content .= "\n\n" . $TableMLine[3] . ";\n\n";
        }

        $backup_name = $backup_name ? $backup_name : $backup_name . ".sql";
        header('Content-Type: application/octet-stream');
        header("Content-Transfer-Encoding: Binary");
        header("Content-disposition: attachment; filename=\"" . $backup_name . "\"");
        echo $content;
        exit;
    }


    function Export_Database_og($host, $user, $pass, $name, $tables = false, $backup_name = false)
    {
        $mysqli = new mysqli($host, $user, $pass, $name);
        $mysqli->select_db($name);
        $mysqli->query("SET NAMES 'utf8'");
        $queryTables = $mysqli->query("SHOW TABLES");
        while ($row = $queryTables->fetch_row()) {
            $target_tables[] = $row['0']; //put each table name into array
        }
        if ($tables) {
            $target_tables = array_intersect($target_tables, $tables);
        }
        foreach ($target_tables as $table) {
            $result = $mysqli->query('SELECT * FROM ' . $table);
            $fields_amount = $result->field_count;
            $rows_num = $mysqli->affected_rows;
            $res = $mysqli->query('SHOW CREATE TABLE ' . $table);
            $TableMLine = $res->fetch_row();
            $content = (!isset($content) ? '' : $content) . "\n\n" . $TableMLine[1] . ";\n\n";

            for ($i = 0, $st_counter = 0; $i < $fields_amount; $i++, $st_counter = 0) {
                while ($row = $result->fetch_row()) {
                    //when started (and every after 100 command cycle):
                    if ($st_counter % 100 == 0 || $st_counter == 0) {
                        $content .= "\nINSERT INTO " . $table . " VALUES";
                    }
                    $content .= "\n(";
                    for ($j = 0; $j < $fields_amount; $j++) {
                        $row[$j] = str_replace("\n", "\\n", addslashes($row[$j]));
                        if (isset($row[$j])) {
                            $content .= '"' . $row[$j] . '"';
                        } else {
                            $content .= '""';
                        }
                        if ($j < ($fields_amount - 1)) {
                            $content .= ',';
                        }
                    }
                    $content .= ")";
                    //every after 100 command cycle [or at last line] ....p.s. but should be inserted 1 cycle eariler
                    if ((($st_counter + 1) % 100 == 0 && $st_counter != 0) || $st_counter + 1 == $rows_num) {
                        $content .= ";";
                    } else {
                        $content .= ",";
                    }
                    $st_counter = $st_counter + 1;
                }
            }
            $content .= "\n\n\n";
        }

        $events = $mysqli->query("SHOW EVENTS");
        while ($events && ($row = $events->fetch_row())) {
            $res = $mysqli->query("SHOW CREATE EVENT " . $row[0] . '.' . $row[1]);
            $TableMLine = $res->fetch_row();
            $content .= "\n\n" . $TableMLine[3] . ";\n\n";
        }

        $backup_name = $backup_name ? $backup_name : $name . ".sql";
        header('Content-Type: application/octet-stream');
        header("Content-Transfer-Encoding: Binary");
        header("Content-disposition: attachment; filename=\"" . $backup_name . "\"");
        echo $content;
        exit;
    }



    function UpdateSiteStaus($table, $status, $value)
    {
        $query = $this->_pdo->query("UPDATE $table SET $status = '$value' WHERE 1");
        $result = $query->fetchAll(PDO::FETCH_COLUMN);
        return $result;
    }

    function FollowUpList0()
    {
        $query = $this->_pdo->query("SELECT t1.id ,t1.enrollment_date,t1.enrollment_id , t1.firstname , t1.lastname, t1.phone_number, t2.client_id, t2.expected_date, t2.visit_date, t2.visit_name,t1.site_id FROM clients AS t1 INNER JOIN visit AS t2 ON t1.id = t2.client_id WHERE t1.status = '1' AND t2.expected_date <= '2023-10-05' AND t2.visit_code = 'M6'");
        $result = $query->fetchAll(PDO::FETCH_COLUMN);
        return $result;
    }

    function FollowUpList()
    {
        $query = $this->_pdo->query("SELECT t1.id AS 'NO.',t1.enrollment_date AS 'ENROLLMENT DATE',t1.enrollment_id AS 'PATIENT ID', t1.firstname AS 'FIRST NAME' , t1.lastname AS 'LAST NAME', t1.phone_number AS 'PHONE NUMBER', t2.client_id 'PATIENT ID', t2.expected_date AS 'EXPECTED DATE', t2.visit_date AS 'VISIT DATE', t2.visit_name AS 'VISIT NAME',t1.site_id AS 'SITE NAME' FROM clients AS t1 INNER JOIN visit AS t2 ON t1.id = t2.client_id WHERE t1.status = '1' AND t2.expected_date <= '2023-10-05' AND t2.visit_code = 'M6'");
        $result = $query->fetchAll(PDO::FETCH_COLUMN);
        return $result;
    }

    function FollowUpList1($date)
    {
        $query = $this->_pdo->query("SELECT t1.id AS 'NO.',t1.enrollment_date AS 'ENROLLMENT DATE',t1.enrollment_id AS 'PATIENT ID', t1.firstname AS 'FIRST NAME' , t1.lastname AS 'LAST NAME', t1.phone_number AS 'PHONE NUMBER', t2.client_id 'PATIENT ID', t2.expected_date AS 'EXPECTED DATE', t2.visit_date AS 'VISIT DATE', t2.visit_name AS 'VISIT NAME',t1.site_id AS 'SITE NAME' FROM clients AS t1 INNER JOIN visit AS t2 ON t1.id = t2.client_id WHERE t1.status = '1' AND t2.expected_date <= '$date' AND t2.visit_code = 'M6'");
        $result = $query->fetchAll(PDO::FETCH_COLUMN);
        return $result;
    }

    function FollowUpList2($site, $date)
    {
        $query = $this->_pdo->query("SELECT t1.id AS 'NO.',t1.enrollment_date AS 'ENROLLMENT DATE',t1.enrollment_id AS 'PATIENT ID', t1.firstname AS 'FIRST NAME' , t1.lastname AS 'LAST NAME', t1.phone_number AS 'PHONE NUMBER', t2.client_id 'PATIENT ID', t2.expected_date AS 'EXPECTED DATE', t2.visit_date AS 'VISIT DATE', t2.visit_name AS 'VISIT NAME',t1.site_id AS 'SITE NAME' FROM clients AS t1 INNER JOIN visit AS t2 ON t1.id = t2.client_id WHERE t1.status = '1' AND t1.sitte_id <= '$site' AND t2.expected_date <= '$date' AND t2.visit_code = 'M6'");
        $result = $query->fetchAll(PDO::FETCH_COLUMN);
        return $result;
    }

    public function FollowUpList3()
    {
        $query = $this->_pdo->query("SELECT t1.id AS NO,
       t1.enrollment_date AS ENROLLMENT_DATE,
       t1.enrollment_id AS PATIENT_ID,
       t1.firstname AS FIRST_NAME ,
       t1.lastname AS LAST_NAME,
       t1.phone_number AS PHONE_NUMBER,
       t2.client_id AS CLIENT_ID,
       t2.expected_date AS EXPECTED_DATE,
       t2.visit_date AS VISIT_DATE,
       t2.visit_name AS VISIT_NAME,
       t1.site_id AS SITE_NAME
FROM clients AS t1 INNER JOIN visit AS t2 ON t1.id = t2.client_id
WHERE t1.status = '1' AND t2.expected_date <= '2023-10-05' AND t2.visit_code = 'M6'");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function FollowUpList4($date)
    {
        $query = $this->_pdo->query("SELECT t1.id AS NO,
       t1.enrollment_date AS ENROLLMENT_DATE,
       t1.enrollment_id AS PATIENT_ID,
       t1.firstname AS FIRST_NAME ,
       t1.lastname AS LAST_NAME,
       t1.phone_number AS PHONE_NUMBER,
       t2.client_id AS CLIENT_ID,
       t2.expected_date AS EXPECTED_DATE,
       t2.visit_date AS VISIT_DATE,
       t2.visit_name AS VISIT_NAME,
       t1.site_id AS SITE_NAME
FROM clients AS t1 INNER JOIN visit AS t2 ON t1.id = t2.client_id
WHERE t1.status = '1' AND t2.expected_date <= '$date' AND t2.visit_code = 'M6' ORDER BY t1.site_id,t2.expected_date");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }


    public function FollowUpList4Count($date)
    {
        $query = $this->_pdo->query("SELECT t1.id AS NO,
       t1.enrollment_date AS ENROLLMENT_DATE,
       t1.enrollment_id AS PATIENT_ID,
       t1.firstname AS FIRST_NAME ,
       t1.lastname AS LAST_NAME,
       t1.phone_number AS PHONE_NUMBER,
       t2.client_id AS CLIENT_ID,
       t2.expected_date AS EXPECTED_DATE,
       t2.visit_date AS VISIT_DATE,
       t2.visit_name AS VISIT_NAME,
       t1.site_id AS SITE_NAME
FROM clients AS t1 INNER JOIN visit AS t2 ON t1.id = t2.client_id
WHERE t1.status = '1' AND t2.expected_date <= '$date' AND t2.visit_code = 'M6' ORDER BY t1.site_id,t2.expected_date");
        $num = $query->rowCount();
        return $num;
    }


    public function FollowUpList5($site, $date)
    {
        $query = $this->_pdo->query("SELECT t1.id AS NO,
       t1.enrollment_date AS ENROLLMENT_DATE,
       t1.enrollment_id AS PATIENT_ID,
       t1.firstname AS FIRST_NAME ,
       t1.lastname AS LAST_NAME,
       t1.phone_number AS PHONE_NUMBER,
       t2.client_id AS CLIENT_ID,
       t2.expected_date AS EXPECTED_DATE,
       t2.visit_date AS VISIT_DATE,
       t2.visit_name AS VISIT_NAME,
       t1.site_id AS SITE_NAME
FROM clients AS t1 INNER JOIN visit AS t2 ON t1.id = t2.client_id
WHERE t1.status = '1' AND t1.site_id = '$site'  AND t2.expected_date <= '$date' AND t2.visit_code = 'M6'");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function FollowUpList5Count($site, $date)
    {
        $query = $this->_pdo->query("SELECT t1.id AS NO,
       t1.enrollment_date AS ENROLLMENT_DATE,
       t1.enrollment_id AS PATIENT_ID,
       t1.firstname AS FIRST_NAME,
       t1.middlename AS MIDDLE_NAME,
       t1.lastname AS LAST_NAME,
       t1.phone_number AS PHONE_NUMBER,
       t2.client_id AS CLIENT_ID,
       t2.expected_date AS EXPECTED_DATE,
       t2.visit_date AS VISIT_DATE,
       t2.visit_name AS VISIT_NAME,
       t1.site_id AS SITE_NAME
FROM clients AS t1 INNER JOIN visit AS t2 ON t1.id = t2.client_id
WHERE t1.status = '1' AND t1.site_id = '$site'  AND t2.expected_date <= '$date' AND t2.visit_code = 'M6'");
        $num = $query->rowCount();
        return $num;
    }

    public function FollowUpList6($date, $date2)
    {
        $query = $this->_pdo->query("SELECT t1.id AS NO,
       t1.enrollment_date AS ENROLLMENT_DATE,
       t1.enrollment_id AS PATIENT_ID,
       t1.firstname AS FIRST_NAME,
       t1.middlename AS MIDDLE_NAME ,
       t1.lastname AS LAST_NAME,
       t1.phone_number AS PHONE_NUMBER,
       t2.client_id AS CLIENT_ID,
       t2.expected_date AS EXPECTED_DATE,
       t2.visit_date AS VISIT_DATE,
       t2.status AS VISIT_STATUS,
       t2.visit_name AS VISIT_NAME,
       t1.site_id AS SITE_NAME
        FROM clients AS t1 INNER JOIN visit AS t2 ON t1.id = t2.client_id
        WHERE t1.status = '1' AND t2.expected_date >= '$date' AND t2.expected_date <= '$date2' AND t2.visit_code = 'M6' ORDER BY t1.site_id,t1.enrollment_id");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function FollowUpList6Count($date, $date2)
    {
        $query = $this->_pdo->query("SELECT t1.id AS NO,
       t1.enrollment_date AS ENROLLMENT_DATE,
       t1.enrollment_id AS PATIENT_ID,
       t1.firstname AS FIRST_NAME,
       t1.middlename AS MIDDLE_NAME ,
       t1.lastname AS LAST_NAME,
       t1.phone_number AS PHONE_NUMBER,
       t2.client_id AS CLIENT_ID,
       t2.expected_date AS EXPECTED_DATE,
       t2.visit_date AS VISIT_DATE,
       t2.status AS VISIT_STATUS,
       t2.visit_name AS VISIT_NAME,
       t1.site_id AS SITE_NAME
        FROM clients AS t1 INNER JOIN visit AS t2 ON t1.id = t2.client_id
        WHERE t1.status = '1' AND t2.expected_date >= '$date' AND t2.expected_date <= '$date2' AND t2.visit_code = 'M6'");
        $num = $query->rowCount();
        return $num;
    }

    public function FollowUpList6Count1($date, $date2, $value)
    {
        $query = $this->_pdo->query("SELECT t1.id AS NO,
       t1.enrollment_date AS ENROLLMENT_DATE,
       t1.enrollment_id AS PATIENT_ID,
       t1.ctc_number AS CTC_ID,
       t1.firstname AS FIRST_NAME ,
       t1.middlename AS MIDDLE_NAME ,
       t1.lastname AS LAST_NAME,
       t1.phone_number AS PHONE_NUMBER,
       t2.client_id AS CLIENT_ID,
       t2.expected_date AS EXPECTED_DATE,
       t2.visit_date AS VISIT_DATE,
       t2.status AS VISIT_STATUS,
       t2.visit_name AS VISIT_NAME,
       t1.site_id AS SITE_NAME
        FROM clients AS t1 INNER JOIN visit AS t2 ON t1.id = t2.client_id
        WHERE t1.status = '1' AND t2.expected_date >= '$date' AND t2.expected_date <= '$date2' AND t2.visit_code = 'M6' AND t2.status = '$value'");
        $num = $query->rowCount();
        return $num;
    }

    public function FollowUpList7($date, $date2, $site)
    {
        $query = $this->_pdo->query("SELECT t1.id AS NO,
       t1.enrollment_date AS ENROLLMENT_DATE,
       t1.enrollment_id AS PATIENT_ID,
       t1.ctc_number AS CTC_ID,
       t1.firstname AS FIRST_NAME,
       t1.middlename AS MIDDLE_NAME ,
       t1.lastname AS LAST_NAME,
       t1.phone_number AS PHONE_NUMBER,
       t2.client_id AS CLIENT_ID,
       t2.expected_date AS EXPECTED_DATE,
       t2.visit_date AS VISIT_DATE,
       t2.status AS VISIT_STATUS,
       t2.visit_name AS VISIT_NAME,
       t1.site_id AS SITE_NAME
        FROM clients AS t1 INNER JOIN visit AS t2 ON t1.id = t2.client_id
        WHERE t1.status = '1' AND t1.site_id = '$site' AND t2.expected_date >= '$date' AND t2.expected_date <= '$date2' AND t2.visit_code = 'M6' ORDER BY t1.id");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function FollowUpList7Count($date, $date2, $site)
    {
        $query = $this->_pdo->query("SELECT t1.id AS NO,
       t1.enrollment_date AS ENROLLMENT_DATE,
       t1.enrollment_id AS PATIENT_ID,
       t1.ctc_number AS CTC_ID,
       t1.firstname AS FIRST_NAME,
       t1.middlename AS MIDDLE_NAME ,
       t1.lastname AS LAST_NAME,
       t1.phone_number AS PHONE_NUMBER,
       t2.client_id AS CLIENT_ID,
       t2.expected_date AS EXPECTED_DATE,
       t2.visit_date AS VISIT_DATE,
       t2.status AS VISIT_STATUS,
       t2.visit_name AS VISIT_NAME,
       t1.site_id AS SITE_NAME
        FROM clients AS t1 INNER JOIN visit AS t2 ON t1.id = t2.client_id
        WHERE t1.status = '1' AND t1.site_id = '$site' AND t2.expected_date >= '$date' AND t2.expected_date <= '$date2' AND t2.visit_code = 'M6'");
        $num = $query->rowCount();
        return $num;
    }

    public function FollowUpList7Count1($date, $date2, $site, $value)
    {
        $query = $this->_pdo->query("SELECT t1.id AS NO,
       t1.enrollment_date AS ENROLLMENT_DATE,
       t1.enrollment_id AS PATIENT_ID,
       t1.ctc_number AS CTC_ID,
       t1.firstname AS FIRST_NAME,
       t1.middlename AS MIDDLE_NAME ,
       t1.lastname AS LAST_NAME,
       t1.phone_number AS PHONE_NUMBER,
       t2.client_id AS CLIENT_ID,
       t2.expected_date AS EXPECTED_DATE,
       t2.visit_date AS VISIT_DATE,
       t2.status AS VISIT_STATUS,
       t2.visit_name AS VISIT_NAME,
       t1.site_id AS SITE_NAME
        FROM clients AS t1 INNER JOIN visit AS t2 ON t1.id = t2.client_id
        WHERE t1.status = '1' AND t1.site_id = '$site' AND t2.expected_date >= '$date' AND t2.expected_date <= '$date2' AND t2.visit_code = 'M6'  AND t2.status = '$value'");
        $num = $query->rowCount();
        return $num;
    }

    public function AllFollowUpRequired($date)
    {
        $query = $this->_pdo->query("SELECT t1.id AS ID,
       t1.firstname AS FIRST_NAME,
       t2.client_id AS CLIENT_ID,
       t2.expected_date AS EXPECTED_DATE,
       t2.status AS VISIT_STATUS,
       t1.site_id AS SITE_NAME
        FROM clients AS t1 INNER JOIN visit AS t2 ON t1.id = t2.client_id
        WHERE t1.status = '1' AND t2.expected_date < '$date'");
        $num = $query->rowCount();
        return $num;
    }


    public function AllFollowUpAvailable($date)
    {
        $query = $this->_pdo->query("SELECT t1.id AS ID,
       t1.firstname AS FIRST_NAME,
       t2.client_id AS CLIENT_ID,
       t2.expected_date AS EXPECTED_DATE,
       t2.status AS VISIT_STATUS,
       t1.site_id AS SITE_NAME
        FROM clients AS t1 INNER JOIN visit AS t2 ON t1.id = t2.client_id
        WHERE t1.status = '1' AND t2.status = '1' AND t2.expected_date < '$date'");
        $num = $query->rowCount();
        return $num;
    }

    public function AllFollowUpMissing($date)
    {
        $query = $this->_pdo->query("SELECT t1.id AS ID,
       t1.firstname AS FIRST_NAME,
       t2.client_id AS CLIENT_ID,
       t2.expected_date AS EXPECTED_DATE,
       t2.status AS VISIT_STATUS,
       t1.site_id AS SITE_NAME
        FROM clients AS t1 INNER JOIN visit AS t2 ON t1.id = t2.client_id
        WHERE t1.status = '1' AND t2.status = '0' AND t2.expected_date < '$date'");
        $num = $query->rowCount();
        return $num;
    }


    public function AllFollowUpRequiredDay($date, $Day)
    {
        $query = $this->_pdo->query("SELECT t1.id AS ID,
       t1.firstname AS FIRST_NAME,
       t2.client_id AS CLIENT_ID,
       t2.expected_date AS EXPECTED_DATE,
       t2.status AS VISIT_STATUS,
       t1.site_id AS SITE_NAME
        FROM clients AS t1 INNER JOIN visit AS t2 ON t1.id = t2.client_id
        WHERE t1.status = '1' AND t2.visit_code = '$Day' AND t2.expected_date < '$date'");
        $num = $query->rowCount();
        return $num;
    }


    public function AllFollowUpAvailableDay($date, $Day)
    {
        $query = $this->_pdo->query("SELECT t1.id AS ID,
       t1.firstname AS FIRST_NAME,
       t2.client_id AS CLIENT_ID,
       t2.expected_date AS EXPECTED_DATE,
       t2.status AS VISIT_STATUS,
       t1.site_id AS SITE_NAME
        FROM clients AS t1 INNER JOIN visit AS t2 ON t1.id = t2.client_id
        WHERE t1.status = '1' AND t2.status = '1' AND t2.visit_code = '$Day' AND t2.expected_date < '$date'");
        $num = $query->rowCount();
        return $num;
    }

    public function AllFollowUpMissingDay($date, $Day)
    {
        $query = $this->_pdo->query("SELECT t1.id AS ID,
       t1.firstname AS FIRST_NAME,
       t2.client_id AS CLIENT_ID,
       t2.expected_date AS EXPECTED_DATE,
       t2.status AS VISIT_STATUS,
       t1.site_id AS SITE_NAME
        FROM clients AS t1 INNER JOIN visit AS t2 ON t1.id = t2.client_id
        WHERE t1.status = '1' AND t2.status = '0'  AND t2.visit_code = '$Day' AND t2.expected_date < '$date'");
        $num = $query->rowCount();
        return $num;
    }


    public function SiteFollowUpRequired($date, $site)
    {
        $query = $this->_pdo->query("SELECT t1.id AS ID,
       t1.firstname AS FIRST_NAME,
       t2.client_id AS CLIENT_ID,
       t2.expected_date AS EXPECTED_DATE,
       t2.status AS VISIT_STATUS,
       t1.site_id AS SITE_NAME
        FROM clients AS t1 INNER JOIN visit AS t2 ON t1.id = t2.client_id
        WHERE t1.status = '1' AND t1.site_id = '$site' AND t2.expected_date < '$date'");
        $num = $query->rowCount();
        return $num;
    }

    public function SiteFollowUpAvailable($date, $site)
    {
        $query = $this->_pdo->query("SELECT t1.id AS ID,
       t1.firstname AS FIRST_NAME,
       t2.client_id AS CLIENT_ID,
       t2.expected_date AS EXPECTED_DATE,
       t2.status AS VISIT_STATUS,
       t1.site_id AS SITE_NAME
        FROM clients AS t1 INNER JOIN visit AS t2 ON t1.id = t2.client_id
        WHERE t1.status = '1' AND t2.status = '1' AND t1.site_id = '$site' AND t2.expected_date < '$date'");
        $num = $query->rowCount();
        return $num;
    }


    public function SiteFollowUpMissing($date, $site)
    {
        $query = $this->_pdo->query("SELECT t1.id AS ID,
       t1.firstname AS FIRST_NAME,
       t2.client_id AS CLIENT_ID,
       t2.expected_date AS EXPECTED_DATE,
       t2.status AS VISIT_STATUS,
       t1.site_id AS SITE_NAME
        FROM clients AS t1 INNER JOIN visit AS t2 ON t1.id = t2.client_id
        WHERE t1.status = '1' AND t2.status = '0' AND t1.site_id = '$site' AND t2.expected_date < '$date'");
        $num = $query->rowCount();
        return $num;
    }


    public function SiteFollowUpRequiredDay($date, $site, $day)
    {
        $query = $this->_pdo->query("SELECT t1.id AS ID,
       t1.firstname AS FIRST_NAME,
       t2.client_id AS CLIENT_ID,
       t2.expected_date AS EXPECTED_DATE,
       t2.status AS VISIT_STATUS,
       t1.site_id AS SITE_NAME
        FROM clients AS t1 INNER JOIN visit AS t2 ON t1.id = t2.client_id
        WHERE t1.status = '1' AND t1.site_id = '$site' AND t2.visit_code = '$day' AND t2.expected_date < '$date'");
        $num = $query->rowCount();
        return $num;
    }

    public function SiteFollowUpAvailableDay($date, $site, $day)
    {
        $query = $this->_pdo->query("SELECT t1.id AS ID,
       t1.firstname AS FIRST_NAME,
       t2.client_id AS CLIENT_ID,
       t2.expected_date AS EXPECTED_DATE,
       t2.status AS VISIT_STATUS,
       t1.site_id AS SITE_NAME
        FROM clients AS t1 INNER JOIN visit AS t2 ON t1.id = t2.client_id
        WHERE t1.status = '1' AND t2.status = '1' AND t1.site_id = '$site'  AND t2.visit_code = '$day' AND t2.expected_date < '$date'");
        $num = $query->rowCount();
        return $num;
    }


    public function SiteFollowUpMissingDay($date, $site, $day)
    {
        $query = $this->_pdo->query("SELECT t1.id AS ID,
       t1.firstname AS FIRST_NAME,
       t2.client_id AS CLIENT_ID,
       t2.expected_date AS EXPECTED_DATE,
       t2.status AS VISIT_STATUS,
       t1.site_id AS SITE_NAME
        FROM clients AS t1 INNER JOIN visit AS t2 ON t1.id = t2.client_id  
        WHERE t1.status = '1' AND t2.status = '0' AND t1.site_id = '$site' AND t2.visit_code = '$day' AND t2.expected_date < '$date'");
        $num = $query->rowCount();
        return $num;
    }

    public function getCount0($table, $field, $value, $field1, $value1)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $field = '$value' AND $field1 >= '$value1'");
        $num = $query->rowCount();
        return $num;
    }

    public function getCountNot($table, $field, $value, $field1, $value1, $value2)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $field = '$value' AND $field1  NOT IN ('$value1','$value2')");
        $num = $query->rowCount();
        return $num;
    }

    public function getDataStatus()
    {
        $query = $this->_pdo->query("select a.*,
    (select status from crf1 c1 where c1.study_id = a.study_id and c1.vcode = a.visit_code limit 1) as crf1,
    (select status from crf2 c2 where c2.study_id = a.study_id and c2.vcode = a.visit_code limit 1) as crf2,
    (select status from crf3 c3 where c3.study_id = a.study_id and c3.vcode = a.visit_code limit 1) as crf3,
    (select status from crf4 c4 where c4.study_id = a.study_id and c4.vcode = a.visit_code limit 1) as crf4,
    (select status from crf5 c5 where c5.study_id = a.study_id and c5.vcode = a.visit_code limit 1) as crf5,
    (select status from crf6 c6 where c6.study_id = a.study_id and c6.vcode = a.visit_code limit 1) as crf6,
    (select distinct status from crf7 c7 where c7.study_id = a.study_id and c7.vcode = a.visit_code limit 1) as crf7
    from
    (select distinct a.visit_code, g.study_id, a.expected_date, a.visit_status, a.visit_date, a.site_id, a.client_id
                    from visit a left join
    (select distinct (a.study_id) from visit a where a.study_id not in ('') ) g on a.study_id = g.study_id
    where g.study_id is not null) a order by a.study_id;");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getDataStatusCount()
    {
        $query = $this->_pdo->query("select a.*,
    (select status from crf1 c1 where c1.study_id = a.study_id and c1.vcode = a.visit_code limit 1) as crf1,
    (select status from crf2 c2 where c2.study_id = a.study_id and c2.vcode = a.visit_code limit 1) as crf2,
    (select status from crf3 c3 where c3.study_id = a.study_id and c3.vcode = a.visit_code limit 1) as crf3,
    (select status from crf4 c4 where c4.study_id = a.study_id and c4.vcode = a.visit_code limit 1) as crf4,
    (select status from crf5 c5 where c5.study_id = a.study_id and c5.vcode = a.visit_code limit 1) as crf5,
    (select status from crf6 c6 where c6.study_id = a.study_id and c6.vcode = a.visit_code limit 1) as crf6,
    (select distinct status from crf7 c7 where c7.study_id = a.study_id and c7.vcode = a.visit_code limit 1) as crf7
    from
    (select distinct a.visit_code, g.study_id, a.expected_date, a.visit_status, a.visit_date, a.site_id, a.client_id
                    from visit a left join
    (select distinct (a.study_id) from visit a where a.study_id not in ('') ) g on a.study_id = g.study_id
    where g.study_id is not null) a order by a.study_id;");
        $num = $query->rowCount();
        return $num;
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

    public function getWithLimitSearch($table, $where, $id, $page, $numRec, $searchTerm, $where3, $where4, $where5, $where6)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE ($where3 LIKE '%$searchTerm%' OR $where4 LIKE '%$searchTerm%' OR $where5 LIKE '%$searchTerm%' OR $where6 LIKE '%$searchTerm%') AND ($where = '$id') limit $page,$numRec");
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

    // public function getCountByMonthYear($table, $col1, $val1, $month, $year, $col2 = null, $val2 = null)
    // {
    //     $query = "SELECT COUNT(*) AS count FROM $table WHERE $col1 = ? AND MONTH(created_on) = ? AND YEAR(created_on) = ?";
    //     $params = [$val1, $month, $year];

    //     if ($col2 !== null && $val2 !== null) {
    //         $query .= " AND $col2 = ?";
    //         $params[] = $val2;
    //     }

    //     $result = $this->db->query($query, $params);
    //     return $result ? $result[0]['count'] : 0;
    // }

    // public function getDistinctYears($table, $dateColumn, $where, $value) {
    //     $query = $this->_pdo->query("SELECT DISTINCT YEAR($dateColumn) AS year FROM $table WHERE $where = '$value' ORDER BY year DESC");
    //     $result = $query->fetchAll(PDO::FETCH_ASSOC);
    //     return $result;
    //  }
    
    //  public function getMonthsByYear($table, $dateColumn, $where, $value) {
    //     $query = $this->_pdo->query("SELECT DISTINCT MONTH($dateColumn) AS month FROM $table WHERE YEAR($dateColumn) = ? AND $where = '$value' ORDER BY month ASC");
    //     $result = $query->fetchAll(PDO::FETCH_ASSOC);
    //     return $result;
    //  }

    
    // public function countRowsByMonth($table, $dateColumn, $year, $month) {
    //     $query = "SELECT COUNT(*) AS count FROM $table WHERE YEAR($dateColumn) = ? AND MONTH($dateColumn) = ? AND status = 1";
    //     return $this->query($query, [$year, $month])->first()->count;
    // }

    // public function countRowsByMonth($table, $dateColumn, $year, $month) {
    //     $query = $this->_pdo->prepare("SELECT COUNT(*) as count FROM $table WHERE YEAR($dateColumn) = :year AND MONTH($dateColumn) = :month AND status = :status");
    //     $query->execute(['year' => $year, 'month' => $month, 'status' => 1]);
    //     $result = $query->fetch(PDO::FETCH_ASSOC);
    //     return $result['count'];
    // }


    public function eligible_counts($value)
    {
        $query = $this->_pdo->query("SELECT `study_id`,`id_number`,`firstname`,`middlename`,`lastname`,`screened`,`eligibility2`,`eligible`,`enrolled`,`site_id`,`status` FROM `clients` WHERE `status`=1  AND `screened`=1 AND `eligible`=1 AND `site_id`='$value' ORDER BY `site_id`
");
        $num = $query->rowCount();
        return $num;      

    }

    public function eligible($value)
    {
        $query = $this->_pdo->query("SELECT `study_id`,`id_number`,`firstname`,`middlename`,`lastname`,`screened`,`eligibility2`,`eligible`,`enrolled`,`site_id`,`status` FROM `clients` WHERE `status`=1  AND `screened`=1 AND `eligible`=1 AND `site_id`='$value' ORDER BY `site_id`
");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;       

    }

   


    public function eligible_enrolled_counts($value)
    {
        $query = $this->_pdo->query("SELECT `study_id`,`id_number`,`firstname`,`middlename`,`lastname`,`screened`,`eligibility2`,`eligible`,`enrolled`,`site_id`,`status` FROM `clients` WHERE `status`=1  AND `screened`=1 AND `eligible`=1 AND `enrolled`=1 AND `site_id`='$value' ORDER BY `site_id`");
        $num = $query->rowCount();
        return $num;       

    }

    public function eligible_enrolled($value)
    {
        $query = $this->_pdo->query("SELECT `study_id`,`id_number`,`firstname`,`middlename`,`lastname`,`screened`,`eligibility2`,`eligible`,`enrolled`,`site_id`,`status` FROM `clients` WHERE `status`=1  AND `screened`=1 AND `eligible`=1 AND `enrolled`=1 AND `site_id`='$value' ORDER BY `site_id`");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;     

    }

    public function eligible_not_enrolled_counts($value)
    {
        $query = $this->_pdo->query("SELECT `study_id`,`id_number`,`firstname`,`middlename`,`lastname`,`screened`,`eligibility2`,`eligible`,`enrolled`,`site_id`,`status` FROM `clients` WHERE `status`=1  AND `screened`=1 AND `eligible`=1 AND (`enrolled`=0 OR `enrolled`=2) AND `site_id`='$value' ORDER BY `site_id`");
        $num = $query->rowCount();
        return $num;      

    }

    public function eligible_not_enrolled($value)
    {
        $query = $this->_pdo->query("SELECT `study_id`,`id_number`,`firstname`,`middlename`,`lastname`,`screened`,`eligibility2`,`eligible`,`enrolled`,`site_id`,`status` FROM `clients` WHERE `status`=1  AND `screened`=1 AND `eligible`=1 AND (`enrolled`=0 OR `enrolled`=2) AND `site_id`='$value' ORDER BY `site_id`");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;     
    }


    public function screened_counts($value)
    {
        $query = $this->_pdo->query("SELECT `study_id`,`id_number`,`firstname`,`middlename`,`lastname`,`screened`,`eligibility2`,`eligible`,`enrolled`,`site_id`,`status` FROM `clients` WHERE `status`=1 AND `screened`=1 AND `site_id`='$value' ORDER BY `site_id`");
        $num = $query->rowCount();
        return $num;      

    }

    public function screened($value)
    {
        $query = $this->_pdo->query("SELECT `study_id`,`id_number`,`firstname`,`middlename`,`lastname`,`screened`,`eligibility2`,`eligible`,`enrolled`,`site_id`,`status` FROM `clients` WHERE `status`=1 AND `screened`=1 AND `site_id`='$value' ORDER BY `site_id`");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;     
    }

    public function not_eligible_counts($value)
    {
        $query = $this->_pdo->query("SELECT `study_id`,`id_number`,`firstname`,`middlename`,`lastname`,`screened`,`eligibility2`,`eligible`,`enrolled`,`site_id`,`status` FROM `clients` WHERE `status`=1 AND `screened`=1 AND (`eligible`=0 OR `eligible`=2) AND `site_id`='$value' ORDER BY `site_id`");
        $num = $query->rowCount();
        return $num;      

    }

    public function not_eligible($value)
    {
        $query = $this->_pdo->query("SELECT `study_id`,`id_number`,`firstname`,`middlename`,`lastname`,`screened`,`eligibility2`,`eligible`,`enrolled`,`site_id`,`status` FROM `clients` WHERE `status`=1 AND `screened`=1 AND (`eligible`=0 OR `eligible`=2) AND `site_id`='$value' ORDER BY `site_id`");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;     
    }

}
