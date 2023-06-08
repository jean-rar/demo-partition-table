<?php namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;

class ApplicationControler extends ResourceController{
        function partitionTable(){
        $applicationModel = new ApplicationModel();
        $applicationModel->createTablePartitions('tableName');
    }


}