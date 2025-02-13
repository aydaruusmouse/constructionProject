<?php 
include 'db_connect.php';
if(isset($_GET['id'])){
    $qry = $conn->query("SELECT * FROM contractors where id = ".$_GET['id']);
    if($qry->num_rows > 0){
        $row = $qry->fetch_array();
        foreach($row as $k => $v){
            $$k = $v;
        }
    }
}else {
    echo 'id iss empty';
}
?>
<div class="container-fluid">
    <dl>
        <dt><b class="border-bottom border-primary">Name</b></dt>
        <dd><?php echo isset($name) ? ucwords($name) : 'N/A' ?></dd>
    </dl>
    
    <dl>
        <dt><b class="border-bottom border-primary">Rate</b></dt>
        <dd><?php echo isset($rate) ? html_entity_decode($rate) : 'N/A' ?></dd>
    </dl>
    <dl>
        <dt><b class="border-bottom border-primary">Date</b></dt>
        <dd><?php echo isset($date) ? html_entity_decode($date) : 'N/A' ?></dd>
    </dl>
    <dl>
        <dt><b class="border-bottom border-primary">Total Rate</b></dt>
        <dd><?php echo isset($total_rate) ? html_entity_decode($total_rate) : 'N/A' ?></dd>
    </dl>
    
    <!-- <dl>
        <dt><b class="border-bottom border-primary">Cost</b></dt>
        <dd><?php echo isset($cost) ? html_entity_decode($cost) : 'N/A' ?></dd>
    </dl> -->
</div>
