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

    public function getCount($table, $field, $value)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $field = '$value'");
        $num = $query->rowCount();
        return $num;
    }

    public function getCount1($table, $field, $value, $field1, $value1)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $field = '$value' AND $field1 = '$value1'");
        $num = $query->rowCount();
        return $num;
    }

    public function getCount2($table, $field, $value, $field1, $value1, $field2, $value2)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $field = '$value' AND $field1 = '$value1' OR $field2 = '$value2'");
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
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $field = '$value' AND $field1 = '$value1' AND $field2 = '$value2' OR $field3 = '$value3'");
        $num = $query->rowCount();
        return $num;
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
    public function getNews($table, $where, $id, $where2, $id2)
    {
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where = '$id' AND $where2 = '$id2'");
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

            $result         =   $this->_pdo->query('SELECT * FROM ' . $tables);
            $fields_amount  =   $result->columnCount();
            $rows_num       =   $result->rowCount();
            $res            =   $this->_pdo->query('SHOW CREATE TABLE ' . $tables);
            $TableMLine     =   $res->fetchAll(PDO::FETCH_ASSOC);

            $content        = (!isset($content) ?  '' : $content) . "\n\n" . $TableMLine[1] . ";\n\n";

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
        $queryTables    = $mysqli->query("SHOW TABLES");
        while ($row = $queryTables->fetch_row()) {
            $target_tables[] = $row['0']; //put each table name into array
        }
        if ($tables) {
            $target_tables = array_intersect($target_tables, $tables);
        }
        foreach ($target_tables as $table) {
            $result         =   $mysqli->query('SELECT * FROM ' . $table);
            $fields_amount  =   $result->field_count;
            $rows_num       =   $mysqli->affected_rows;
            $res            =   $mysqli->query('SHOW CREATE TABLE ' . $table);
            $TableMLine     =   $res->fetch_row();
            $content        = (!isset($content) ?  '' : $content) . "\n\n" . $TableMLine[1] . ";\n\n";

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
}
