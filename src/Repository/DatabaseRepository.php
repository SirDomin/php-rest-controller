<?php

namespace App\Repository;

use App\Converter\Converter;

class DatabaseRepository {

    private string $databaseHost = 'localhost:3306';

    private string $databaseUserName = 'root';

    private string $databasePassword = '';

    private string $databaseName = 'php-rest';

    private $connection = '';

    function __construct() {
        $this->connection = mysqli_connect($this->databaseHost, $this->databaseUserName, $this->databasePassword, $this->databaseName) or die("Database failed to respond.");

        mysqli_set_charset($this->connection, "utf8");
    }

    public function customQuery(array $query) {

        $errors = [];
        foreach($query as $q) {
            mysqli_query($this->connection, $q) or $errors[] = mysqli_error_list($this->connection);
        }
    }

    function getRecordData($tableName, $fldVal, $fldName = "id", $debug = false): ?object {
        $query = "SELECT * FROM " . $tableName . " WHERE " . $fldName . " = '" . $fldVal . "'";
        $retval = [];

        $mysqliQuery = mysqli_query($this->connection, $query);

        if (!$mysqliQuery) {
            return null;
        }

        $result = mysqli_fetch_assoc($mysqliQuery);

        if(!$result) {
            return null;
        }

        $objectClass = Converter::convertTableToObject($tableName);

        $object = new $objectClass();

        foreach (get_object_vars($object) as $key => $val) {
            if(is_array($object->$key)) {
                $object->$key = $this->getListDataMultiCondition(substr($key, 0, -1), [$tableName . 'Id' => (string) $object->id]);
            } else if(is_object($object->$key) && get_class($object->$key) === 'DateTime') {
                $object->$key = new \DateTime($result[$key]);
            }else {
                $object->$key = $result[$key];
            }
        }

        return $object;
    }

    function getRecordDataMultiCondition($tableName, array $array, $debug = false): ?object {
        $query = "SELECT * FROM " . $tableName;

        $index = 0;
        foreach ($array as $key => $val) {
            $query .= ($index === 0 ? " WHERE " : " AND ") . $key . " = '" . $val . "'";
            $index++;
        }

        $mysqliQuery = mysqli_query($this->connection, $query);

        if ($mysqliQuery === false) {
            return null;
        }

        $result = mysqli_fetch_assoc($mysqliQuery);
        if(!$result) {
            return null;
        }
        $objectClass = Converter::convertTableToObject($tableName);

        $object = new $objectClass();

        foreach (get_object_vars($object) as $key => $val) {
            if(is_array($object->$key)) {

            } else {
                $object->$key = $result[$key];
            }
        }

        return $object;
    }

    function getListDataMultiCondition($tableName, $arr = [], $start = 0, $limit = 0, $sortBy = 'id', $sortOrder = 'ASC', $debug = false) {
        $strToQuery = "";
        foreach ($arr as $key => $val)
        {
            if (strlen($key)) {
                $strToQuery .= " AND ".$key."='" . $val . "'";
            }
        }

        if (is_array($sortBy) && count($sortBy))
        {
            $sortString = " ORDER BY ";
            foreach($sortBy as $key=>$val) {
                $sortString .= " ".$key." ".$val.",";
            }
            $sortString = substr($sortString, 0, -1);
        }
        else
            $sortString = (strlen($sortBy) ? " ORDER BY ".$sortBy.(strlen($sortOrder) ? " ".$sortOrder : " ASC") : "");

        $query = "SELECT * ";
        $query .= "FROM ".$tableName." ";
        if (strlen($strToQuery)) $query .= "WHERE ".str_replace('\'', '"', substr($strToQuery, 5));
        $query .= $sortString;
        $query .= ($limit > 0 ? " LIMIT ".$start.",".$limit : "");
        if ($debug) echo $query;
        $result = mysqli_query($this->connection, $query);

        $i = 0;
        $returnArr = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $objectClass = Converter::convertTableToObject($tableName);

            $object = new $objectClass();

            foreach (get_object_vars($object) as $key => $val) {

                if(is_array($object->$key)) {

                } else {
                    $object->$key = $row[$key];
                }
            }

            $returnArr[] = $object;
        }

        return $returnArr;
    }

    function insertRecordData($tableName, $arr, $retId = false, $debug = false): int {
        $_query1 = "";
        $_query2 = "";

        if (is_array($arr)) {
            foreach ($arr as $key=>$val) {
                if (isset($key) && !is_array($val))
                {
                    $_query1 .= $key.",";
                    if ($val=="NULL")
                            $_query2 .= "NULL,";
                    else {
                        if (gettype($val) === 'object' && get_class($val) === 'DateTime') {
                            $_query2 .= "'".str_replace('\'', ' ',$val->format('Y-m-d H:i:s'))."',";
                        }else {
                            $_query2 .= "'".str_replace('\'', ' ',$val)."',";
                        }
                    }
                }
            }
        }
        $_query1 = substr($_query1, 0, -1);
        $_query2 = substr($_query2, 0, -1);


        $query = "INSERT INTO ".$tableName." ";
        $query .= "(".$_query1.") ";
        $query .= "VALUES (".$_query2.")";

        mysqli_query($this->connection, $query);

        return mysqli_insert_id($this->connection) ?? 0;
    }

    function updateData($tableName, $arr, $fldId = "id", $debug = false) {
      $_query1 = "";
      $_query2 = "";
      foreach ($arr as $key => $val)
        {
        if ($val=="NULL")
          $_query1 .= $key."=NULL,";
        elseif ($key == $fldId)
          $_query2 .= $key."='".$val."'";
        elseif (isset($key))
          $_query1 .= $key."='".$val."',";
        }
        $_query1 = substr($_query1, 0, -1);
      if (strlen($_query1) && strlen($_query2))
        {
        $query = "UPDATE ".$tableName." ";
        $query .= "SET ".$_query1." ";
        $query .= "WHERE ".$_query2 .";";
        mysqli_query($this->connection, $query);
        $feedback = mysqli_affected_rows($this->connection);
        }
      else
        $feedback = false;

        return $feedback;
      }

    function removeRecordData($tableName, $fldVal, $fldName = "id", $debug = false)
    {
        $query = "DELETE FROM ".$tableName." ";
        $query .= "WHERE ".$fldName."='".$fldVal."'";
        if ($debug) echo $query;
        mysqli_query($this->connection, $query);
        $feedback = mysqli_affected_rows($this->connection);

        return $feedback;
    }

    function _stringtodb($strParamString)
    {
        return addslashes($strParamString);
    }

}
