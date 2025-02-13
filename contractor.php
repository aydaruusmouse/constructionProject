<?php
include('db_connect.php');

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'save_contractor'){
    echo 'reached aido';
    extract($_POST);
    $data = "";
    foreach($_POST as $k => $v){
        if(!in_array($k, array('id', 'action')) && !is_numeric($k)){
            if($k == 'name')
                $v = htmlentities(str_replace("'","&#x2019;",$v));
            if(empty($data)){
                $data .= " $k='$v' ";
            } else {
                $data .= ", $k='$v' ";
            }
        }
    }
      echo $data;
      echo $_POST['id'];
    // Debugging statement
    error_log("Data to be inserted/updated: $data");
    
    if(empty($_POST['id'])){
        $sql = "INSERT INTO contractors SET $data";
        echo $sql;
    } else {
        $sql = "UPDATE contractors SET $data WHERE id = {$_POST['id']}";
        echo $sql;
    }
     echo 'not reached here';
    // Debugging statement
    error_log("SQL Query: $sql");

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    echo "Connected successfully";
    $conn->query($sql);    
}
?>
