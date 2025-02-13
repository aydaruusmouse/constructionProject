<?php 
include 'db_connect.php';
if(isset($_GET['id'])){
	$qry = $conn->query("SELECT * FROM task_list where id = ".$_GET['id'])->fetch_array();
	foreach($qry as $k => $v){
		$$k = $v;
	}
}
?>
<div class="container-fluid">
	
	
	<dl>
		<dt><b class="border-bottom border-primary">Material Name</b></dt>
		<dd><?php echo html_entity_decode($materialname) ?></dd>
	</dl>
	<dl>
		<dt><b class="border-bottom border-primary">Quantity</b></dt>
		<dd><?php echo html_entity_decode($quantity) ?></dd>
	</dl>
	
	<dl>
		<dt><b class="border-bottom border-primary">Cost</b></dt>
		<dd><?php echo html_entity_decode($cost) ?></dd>
	</dl>
</div>