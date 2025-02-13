<?php
include 'db_connect.php';

$stat = array("Pending","Started","On-Progress","On-Hold","Over Due","Done");
$qry = $conn->query("SELECT * FROM project_list where id = ".$_GET['id'])->fetch_array();
foreach($qry as $k => $v){
    $$k = $v;
}



// Calculate total material costs
$material_cost_result = $conn->query("SELECT SUM(cost) as total_material_cost FROM task_list WHERE project_id = {$id}");
$material_cost = $material_cost_result->num_rows > 0 ? $material_cost_result->fetch_array()['total_material_cost'] : 0;
$workplan_cost= $conn->query("SELECT SUM(cost) as workplan_cost FROM user_productivity WHERE project_id = {$id}");
$workplan_cost = $workplan_cost->num_rows > 0 ? $workplan_cost->fetch_array()['workplan_cost'] : 0;



// Calculate total task list costs
// $task_list_cost_result = $conn->query("SELECT SUM(cost) as total_task_list_cost FROM task_list WHERE project_id = {$id}");
// $task_list_cost = $task_list_cost_result->num_rows > 0 ? $task_list_cost_result->fetch_array()['total_task_list_cost'] : 0;

// Calculate total subcontractor costs
$subcontractor_cost_result = $conn->query("SELECT SUM(total_rate) as total_subcontractor_cost FROM contractors WHERE project_id = {$id}");
$subcontractor_cost = $subcontractor_cost_result->num_rows > 0 ? $subcontractor_cost_result->fetch_array()['total_subcontractor_cost'] : 0;

// Calculate remaining project budget
$total_cost = $material_cost  + $subcontractor_cost;
$remaining_budget = $project_cost - $total_cost - $workplan_cost;


$tprog = $conn->query("SELECT * FROM task_list where project_id = {$id}")->num_rows;
$cprog = $conn->query("SELECT * FROM task_list where project_id = {$id} and status = 3")->num_rows;
// $usertype = $conn->query("SELECT * FROM users type= 1")->num_rows;
// echo $usertype;
$prog = $tprog > 0 ? ($cprog/$tprog) * 100 : 0;
$prog = $prog > 0 ?  number_format($prog, 2) : $prog;
$prod = $conn->query("SELECT * FROM user_productivity where project_id = {$id}")->num_rows;

if($status == 0 && strtotime(date('Y-m-d')) >= strtotime($start_date)):
    if($prod > 0 || $cprog > 0)
        $status = 2;
    else
        $status = 1;
elseif($status == 0 && strtotime(date('Y-m-d')) > strtotime($end_date)):
    $status = 4;
endif;

$manager = $conn->query("SELECT *, concat(firstname, ' ', lastname) as name FROM users where id = $manager_id");
$manager = $manager->num_rows > 0 ? $manager->fetch_array() : array();
?>
<div class="col-lg-12">
	<div class="row">
		<div class="col-md-12">
			<div class="callout callout-info">
				<div class="col-md-12">
					<div class="row">
						<div class="col-sm-6">
						<dl>
                                <dt><b class="border-bottom border-primary">Project Name</b></dt>
                                <dd><?php echo ucwords($name) ?></dd>
                                <dt><b class="border-bottom border-primary">Description</b></dt>
                                <dd><?php echo html_entity_decode($description) ?></dd>
                                <dt><b class="border-bottom border-primary">Project Cost</b></dt>
                                <dd>$<?php  
								
								$usertype=  ($_SESSION['login_type']);
						if ($usertype== 1) {
							echo $project_cost;
							# code...
						}else{
							
						}
								
								
								
								
								?></dd>
                                <dt><b class="border-bottom border-primary">Material Cost</b></dt>
                                <dd>$<?php echo ($material_cost) ?></dd>
                               
								<dt><b class="border-bottom border-primary">Work Plan Cost</b></dt>
                                <dd>$<?php echo ($workplan_cost) ?></dd>
                                <dt><b class="border-bottom border-primary">Subcontractor Cost</b></dt>
                                <dd>$<?php echo ($subcontractor_cost) ?></dd>
                                <dt><b class="border-bottom border-primary">Total Cost</b></dt>
                                <dd>$<?php echo ($total_cost) ?></dd>
                                <dt><b class="border-bottom border-primary">Remaining Budget</b></dt>
                                <dd>$<?php 
								
						$usertype=  ($_SESSION['login_type']);
						if ($usertype== 1) {
							echo $remaining_budget;
							# code...
						}else{
							
						}
								?></dd>
                            </dl>
						</div>
						<div class="col-md-6">
							<dl>
								<dt><b class="border-bottom border-primary">Start Date</b></dt>
								<dd><?php echo date("F d, Y",strtotime($start_date)) ?></dd>
							</dl>
							<dl>
								<dt><b class="border-bottom border-primary">End Date</b></dt>
								<dd><?php echo date("F d, Y",strtotime($end_date)) ?></dd>
							</dl>
							<dl>
								<dt><b class="border-bottom border-primary">Status</b></dt>
								<dd>
									<?php
									  if($stat[$status] =='Pending'){
									  	echo "<span class='badge badge-secondary'>{$stat[$status]}</span>";
									  }elseif($stat[$status] =='Started'){
									  	echo "<span class='badge badge-primary'>{$stat[$status]}</span>";
									  }elseif($stat[$status] =='On-Progress'){
									  	echo "<span class='badge badge-info'>{$stat[$status]}</span>";
									  }elseif($stat[$status] =='On-Hold'){
									  	echo "<span class='badge badge-warning'>{$stat[$status]}</span>";
									  }elseif($stat[$status] =='Over Due'){
									  	echo "<span class='badge badge-danger'>{$stat[$status]}</span>";
									  }elseif($stat[$status] =='Done'){
									  	echo "<span class='badge badge-success'>{$stat[$status]}</span>";
									  }
									?>
								</dd>
							</dl>
							<dl>
								<dt><b class="border-bottom border-primary">Project Manager</b></dt>
								<dd>
									<?php if(isset($manager['id'])) : ?>
									<div class="d-flex align-items-center mt-1">
										<img class="img-circle img-thumbnail p-0 shadow-sm border-info img-sm mr-3" src="assets/uploads/<?php echo $manager['avatar'] ?>" alt="Avatar">
										<b><?php echo ucwords($manager['name']) ?></b>
									</div>
									<?php else: ?>
										<small><i>Eng Mohamed Dayr</i></small>
									<?php endif; ?>
								</dd>
							</dl>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-4">
			<div class="card card-outline card-primary">
				<div class="card-header">
					<span><b>Contracters:</b></span>
					<div class="card-tools">
					<button class="btn btn-primary bg-gradient-primary btn-sm" type="button" id="new_contractor"><i class="fa fa-plus"></i> Add Contractor</button>
					</div>
				</div>
				<div class="card-body">
				<div class="card-body p-0">
					<div class="table-responsive">
					<table class="table table-condensed m-0 table-hover">
						<colgroup>
							<col width="5%">
							<col width="25%">
							<col width="30%">
							<col width="15%">
							<col width="15%">
						</colgroup>
						<thead>
						<th>Contractor ID</th>
                <th>Name</th>
                <th>Total Rate</th>
               
                <th>Action</th>
						</thead>
						<tbody>
							<?php 
							$i = 1;
							$contractors = $conn->query("SELECT *, name FROM contractors where project_id = {$id} ORDER BY name ASC");
                            

							
							while($row = $contractors->fetch_assoc()):
								
								$trans = get_html_translation_table(HTML_ENTITIES,ENT_QUOTES);
								unset($trans["\""], $trans["<"], $trans[">"], $trans["<h2"]);
								$desc = strtr(html_entity_decode($row['total_rate']),$trans);
								$desc=str_replace(array("<li>","</li>"), array("",", "), $desc);
							?>
								<tr>
								<td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['name']; ?></td>
                    <td><?php echo $row['total_rate']; ?></td>

                    <td>
                        <a href="contractor_jobs.php?contractor_id=<?php echo $row['id']; ?>" class="btn btn-info">View</a>
                    </td>
			                        
		                    	</tr>
							<?php 
							endwhile;
							?>
						</tbody>
					</table>
					</div>
				</div>
				</div>
			</div>
		</div>
		<div class="col-md-8">
			<div class="card card-outline card-primary">
				<div class="card-header">
					<span><b>Material List:</b></span>
					<?php if($_SESSION['login_type'] != 3): ?>
					<div class="card-tools">
						<button class="btn btn-primary bg-gradient-primary btn-sm" type="button" id="new_task"><i class="fa fa-plus"></i> New Materials</button>
					</div>
				<?php endif; ?>
				</div>
				<div class="card-body p-0">
					<div class="table-responsive">
					<table class="table table-condensed m-0 table-hover">
						<colgroup>
							<col width="5%">
							<col width="25%">
							<col width="30%">
							<col width="15%">
							<col width="15%">
						</colgroup>
						<thead>
							<th>#</th>
							<th>Material Name</th>
							 <th>Quantity</th>
							 <th>Cost</th>
							 <th>Description</th>
							<th>Action</th>
						</thead>
						<tbody>
							<?php 
							$i = 1;
							$tasks = $conn->query("SELECT * FROM task_list where project_id = {$id} order by materialname asc");
							while($row=$tasks->fetch_assoc()):
								$trans = get_html_translation_table(HTML_ENTITIES,ENT_QUOTES);
								unset($trans["\""], $trans["<"], $trans[">"], $trans["<h2"]);
								$desc = strtr(html_entity_decode($row['quantity']),$trans);
								$desc=str_replace(array("<li>","</li>"), array("",", "), $desc);
							?>
								<tr>
			                        <td class="text-center"><?php echo $i++ ?></td>
			                        <td class=""><b><?php echo ucwords($row['materialname']) ?></b></td>
			                        <td class=""><p class="truncate"><?php echo strip_tags($desc) ?></p></td>
									<td class=""><b><?php echo ucwords($row['cost']) ?></b></td>
			                        <td><?php echo ucwords($row['description']) ?></td>
			                        <td class="text-center">
										<button type="button" class="btn btn-default btn-sm btn-flat border-info wave-effect text-info dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
					                      Action
					                    </button>
					                    <div class="dropdown-menu" style="">
					                      <a class="dropdown-item view_task" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>"  data-task="<?php echo $row['materialname'] ?>">View</a>
					                      <div class="dropdown-divider"></div>
					                      <?php if($_SESSION['login_type'] != 3): ?>
					                      <a class="dropdown-item edit_task" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>"  data-task="<?php echo $row['materialname'] ?>">Edit</a>
					                      <div class="dropdown-divider"></div>
					                      <a class="dropdown-item delete_task" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>">Delete</a>
					                  <?php endif; ?>
					                    </div>
									</td>
		                    	</tr>
							<?php 
							endwhile;
							?>
						</tbody>
					</table>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
	<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <b>Workplan Activity</b>
            <div class="card-tools">
                <button class="btn btn-primary bg-gradient-primary btn-sm" type="button" id="new_productivity"><i class="fa fa-plus"></i> New workplan</button>
            </div>
        </div>
        <div class="card-body">
            <?php 
            $progress = $conn->query("SELECT * FROM user_productivity WHERE project_id = {$id}");
            if($progress->num_rows > 0):
            ?>
            <table class="table table-condensed m-0 table-hover">
                <colgroup>
                    <col width="5%">
                    <col width="20%">
                    <col width="30%">
                    <col width="15%">
                    <col width="15%">
                    <col width="15%">
                </colgroup>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Workplans</th>
                        <th>Description</th>
                        <th>Cost</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $i = 1;
                    while($row = $progress->fetch_assoc()):
                        $trans = get_html_translation_table(HTML_ENTITIES, ENT_QUOTES);
                        unset($trans["\""], $trans["<"], $trans[">"], $trans["<h2"]);
                        $desc = strtr(html_entity_decode($row['comment']), $trans);
                        $desc = str_replace(array("<li>", "</li>"), array("", ", "), $desc);
                    ?>
                    <tr>
                        <td class="text-center"><?php echo $i++; ?></td>
                        <td><b><?php echo ucwords($row['workplan']); ?></b></td>
                        <td><p class="truncate"><?php echo strip_tags($desc); ?></p></td>
                        <td><p class="truncate"><?php echo strip_tags($row['cost']); ?></p></td>
                        <td>
                            <?php if($_SESSION['login_id'] == $row['user_id']): ?>
                            <div class="btn-group dropleft">
                                <button type="button" class="btn btn-secondary btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Actions
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item manage_progress" href="javascript:void(0)" data-id="<?php echo $row['id']; ?>" data-task="<?php echo $row['id']; ?>">Edit</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item delete_progress" href="javascript:void(0)" data-id="<?php echo $row['id']; ?>">Delete</a>
                                </div>
                            </div>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <?php else: ?>
            <p>No Workplan Activity.</p>
            <?php endif; ?>
        </div>
    </div>
</div>
			</row>
<style>
	.users-list>li img {
	    border-radius: 50%;
	    height: 67px;
	    width: 67px;
	    object-fit: cover;
	}
	.users-list>li {
		width: 33.33% !important
	}
	.truncate {
		-webkit-line-clamp:1 !important;
	}
</style>
<script>
   $('#new_contractor').click(function(){
		uni_modal("New Contractors For <?php echo ucwords($name) ?>","manage_contractors.php?pid=<?php echo $id ?>","mid-large")
	})
	$('.edit_contractor').click(function(){
		uni_modal("Edit Contractors: "+$(this).attr('data-task'),"manage_contractors.php?pid=<?php echo $id ?>&id="+$(this).attr('data-id'),"mid-large")
	})
	$('.view_contractor').click(function(){
		uni_modal("Contractor Details","edit_contractors.php?id="+$(this).attr('data-id'),"mid-large")
		// uni_modal("Edit Contractor: "+$(this).attr('data-task'),"edit_contractor.php?pid=<?php echo $id ?>&id="+$(this).attr('data-id'),"mid-large")
		// uni_modal("New Task For <?php echo ucwords($name) ?>","edit_contractors.php?pid=<?php echo $id ?>","mid-large")
	})

	$('#new_task').click(function(){
		uni_modal("New Task For <?php echo ucwords($name) ?>","manage_task.php?pid=<?php echo $id ?>","mid-large")
	})
	$('.delete_task').click(function(){
	_conf("Are you sure to delete this task?","delete_task",[$(this).attr('data-id')])
	})
	$('.delete_contractor').click(function(){
		console.log('function trigered');
	_conf("Are you sure to delete this contractor?","delete_contractor",[$(this).attr('data-id')])
	})

	$('.edit_task').click(function(){
		uni_modal("Edit Task: "+$(this).attr('data-task'),"manage_task.php?pid=<?php echo $id ?>&id="+$(this).attr('data-id'),"mid-large")
	})
	$('.view_task').click(function(){
		uni_modal("Task Details","view_task.php?id="+$(this).attr('data-id'),"mid-large")
	})
	$('#new_productivity').click(function(){
		uni_modal("<i class='fa fa-plus'></i> New Progress","manage_progress.php?pid=<?php echo $id ?>",'large')
	})
	$('.manage_progress').click(function(){
		uni_modal("<i class='fa fa-edit'></i> Edit Progress","manage_progress.php?pid=<?php echo $id ?>&id="+$(this).attr('data-id'),'large')
	})
	$('.delete_progress').click(function(){
	_conf("Are you sure to delete this progress?","delete_progress",[$(this).attr('data-id')])
	})
	function delete_progress($id){
		start_load()
		$.ajax({
			url:'ajax.php?action=delete_progress',
			method:'POST',
			data:{id:$id},
			success:function(resp){
				if(resp==1){
					alert_toast("Data successfully deleted",'success')
					setTimeout(function(){
						location.reload()
					},1500)

				}
			}
		})
	}
	function delete_task($id){
		start_load()
		$.ajax({
			url:'ajax.php?action=delete_task',
			method:'POST',
			data:{id:$id},
			success:function(resp){
				if(resp==1){
					alert_toast("Data successfully deleted",'success')
					setTimeout(function(){
						location.reload()
					},1500)

				}
			}
		})
	}
	function delete_contractor($id){
		start_load()
		$.ajax({
			url:'ajax.php?action=delete_contractor',
			method:'POST',
			data:{id:$id},
			success:function(resp){
				if(resp==1){
					alert_toast("Data successfully deleted",'success')
					setTimeout(function(){
						location.reload()
					},1500)
					
				}else{
					alert_toast("Data successfully deleted",'success')
					stop_load();
				}
			}
		})
	}
</script>