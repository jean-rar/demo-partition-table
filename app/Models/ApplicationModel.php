<?php namespace App\Models;

use CodeIgniter\Model;

class ApplicationModel extends Model{
    protected $DBGroup = "default";
        function createTablePartitions($tableName){
        $db = \Config\Database::connect();
        $strPartition = '';
        for($month = -1; $month <= 43; $month++){//create partition start from current month to next 43 month

            $monthIncrement = $month +1;
            $passMonth = date('Y-m', strtotime($month.' month'));
            $currentDataTime = date('Y-m-d 00:00:00', strtotime($monthIncrement.' month'));
            $monthToNameFormat = str_replace("-", "", $passMonth);
            $partitionName = "p".$monthToNameFormat; //the $partitionName should be like: p202304 "p" means partition "2023" means the year and "04" the month
            $strPartition = $strPartition."PARTITION $partitionName VALUES LESS THAN ( UNIX_TIMESTAMP('$currentDataTime')),"; // concat the partition syntax that should be like PARTITION p202304 VALUES LESS THAN( UNIX_TIMESTAMP('2023-05-01 00:00:00')),p202305 VALUES LESS THAN( UNIX_TIMESTAMP('2023-06-01 00:00:00')),...

        }
        $strPartitionFormat = rtrim($strPartition, ',');
        $db->transBegin();
        $db->query("ALTER TABLE $tableName PARTITION BY RANGE (unix_timestamp(insert_timestamp))(".$strPartitionFormat.");");//query to create partition: ALTER TABLE tableName PARTITION BY RANGE (unix_timestamp(insert_timestamp))(PARTITION p202304 VALUES LESS THAN( UNIX_TIMESTAMP('2023-05-01 00:00:00')),p202305 VALUES LESS THAN( UNIX_TIMESTAMP('2023-06-01 00:00:00')),...);

        if ($this->db->transStatus() === FALSE) {

            $this->db->transRollback();
        } else {
            $this->db->transCommit();
        }

    }

}