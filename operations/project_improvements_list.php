<?
	include_once("lib/site_lib.php");
	include_once("lib/configuration.inc");
	include_once("lib/system_records_lib.php");
	include_once("lib/system_users_lib.php");
	include_once("lib/project_improvements_lib.php");
	include_once("lib/project_improvements_expenses_lib.php");
	include_once("lib/project_improvements_achievements_lib.php");
	include_once("lib/project_improvements_status_lib.php");

	# get global vars
	global $services_conf;	

	# general variables - YOU SHOULDNT NEED TO CHANGE THIS
	$show_id = isset($_GET["show_id"]) ? $_GET["show_id"] : null;
	$sort = isset($_GET["sort"])? $_GET["sort"] : null;
	$section = isset($_GET["section"]) ? $_GET["section"] : null;
	$subsection = isset($_GET["subsection"])? $_GET["subsection"]:null;
	$action = isset($_GET["action"])? $_GET["action"]:null;

	$project_improvements_id = isset($_GET["project_improvements_id"])? $_GET["project_improvements_id"]:null;
	$project_improvements_title = isset($_GET["project_improvements_title"])? $_GET["project_improvements_title"]:null;
	$project_improvements_goal = isset($_GET["project_improvements_goal"])? $_GET["project_improvements_goal"]:null;
	$project_improvements_rca = isset($_GET["project_improvements_rca"])? $_GET["project_improvements_rca"]:null;
	$project_improvements_proactive = isset($_GET["project_improvements_proactive"])? $_GET["project_improvements_proactive"]:null ;
	$project_improvements_reactive = isset($_GET["project_improvements_reactive"])? $_GET["project_improvements_reactive"]:null;
	$project_improvements_start = isset($_GET["project_improvements_start"])? $_GET["project_improvements_start"]:null;
	$project_improvements_deadline = isset($_GET["project_improvements_deadline"])? $_GET["project_improvements_deadline"]:null;
	$project_improvements_status_id = isset($_GET["project_improvements_status_id"])? $_GET["project_improvements_status_id"]:null;
	$project_improvements_plan_budget = isset($_GET["project_improvements_plan_budget"])? $_GET["project_improvements_plan_budget"]:null;
	$project_improvements_owner_id = isset($_GET["project_improvements_owner_id"])? $_GET["project_improvements_owner_id"]:null;
	$project_improvements_completion = isset($_GET["project_improvements_completion"])? $_GET["project_improvements_completion"]:null;
	$project_improvements_expenses_date = isset($_GET["project_improvements_expenses_date"])? $_GET["project_improvements_expenses_date"]:null;

	# expenses stuff
	$project_improvements_expenses_id = isset($_GET["project_improvements_expenses_id"])? $_GET["project_improvements_expenses_id"]:null;
	$project_improvements_expenses_description = isset($_GET["project_improvements_expenses_description"])? $_GET["project_improvements_expenses_description"]:null;
	$project_improvements_expenses_amount = isset($_GET["project_improvements_expenses_amount"])? $_GET["project_improvements_expenses_amount"]:null;
	$project_improvements_expenses_disabled = isset($_GET["project_improvements_expenses_disabled"])? $_GET["project_improvements_expenses_disabled"]:null;

	# achievements stuff
	$project_improvements_achievements_id = isset($_GET["project_improvements_achievements_id"])? $_GET["project_improvements_achievements_id"]:null;
	$project_improvements_achievements_text = isset($_GET["project_improvements_achievements_text"])? $_GET["project_improvements_achievements_text"]:null;
	$project_improvements_achievements_date = isset($_GET["project_improvements_achievements_date"])? $_GET["project_improvements_achievements_date"]:null;
	$project_improvements_achievements_owner = isset($_GET["project_improvements_achievements_owner"])? $_GET["project_improvements_achievements_owner"]:null;
	$project_improvements_achievements_disabled = isset($_GET["project_improvements_achievements_disabled"])? $_GET["project_improvements_achievements_disabled"]:null;
		
	# this got to have a value
	if ($project_improvements_status_id == "-1") {
		$project_improvements_status_id = 1;
	}
	
	$base_url_list = build_base_url($section,"project_improvements_list");
	$base_url_edit = build_base_url($section,"project_improvements_edit");
	$base_url_edit_expenses = build_base_url($section,"project_improvements_expenses_edit");
	$base_url_list_expenses = build_base_url($section,"project_improvements_expenses_list");
	$base_url_achievements_edit = build_base_url($section,"project_improvements_achievements_edit");
	$base_url_achievements_list = build_base_url($section,"project_improvements_achievements_list");
	
	# local variables - YOU MUST ADJUST THIS! 
	$project_improvements_id = isset($_GET["project_improvements_id"])? $_GET["project_improvements_id"]:null;

	$project_improvements_mitigation_strategy_id = isset($_GET["project_improvements_mitigation_strategy_id"])? $_GET["project_improvements_mitigation_strategy_id"]:null;
	$security_services_id = isset($_GET["security_services_id"])? $_GET["security_services_audit_id"]:null;
	$project_improvements_exception_id = isset($_GET["project_improvements_exception_id"])? $_GET["project_improvements_exception_id"]:null;

	$project_improvements_periodicity_review = isset($_GET["project_improvements_periodicity_review"])? $_GET["project_improvements_periodicity_review"]:null;
	if (!is_numeric($project_improvements_periodicity_review) OR $project_improvements_periodicity_review>12 OR $project_improvements_periodicity_review<0) {
		$project_improvements_periodicity_review = 12;
	}
	$project_improvements_residual_score = isset($_GET["project_improvements_residual_score"])? $_GET["project_improvements_residual_score"]:null;
	if (!is_numeric($project_improvements_residual_score)) {
		$project_improvements_residual_score = isset($_GET["project_improvements_classification_score"])? $_GET["project_improvements_classification_score"]:null;
	}

	$security_services_id = isset($_GET["security_services_id"])? $_GET["security_services_id"]:null;
	$project_improvements_exception_id = isset($_GET["project_improvements_exception_id"])? $_GET["project_improvements_exception_id"]:null;
	
	#actions .. edit, update or disable - YOU MUST ADJUST THIS!
	if ($action == "edit" && is_numeric($project_improvements_id)) {
		$project_improvements_update = array(
			'project_improvements_title' => $project_improvements_title,
			'project_improvements_goal' => $project_improvements_goal,
			'project_improvements_rca' => $project_improvements_rca,
			'project_improvements_proactive' => $project_improvements_proactive,
			'project_improvements_reactive' => $project_improvements_reactive,
			'project_improvements_start' => $project_improvements_start,
			'project_improvements_deadline' => $project_improvements_deadline,
			'project_improvements_status_id' => $project_improvements_status_id,
			'project_improvements_plan_budget' => $project_improvements_plan_budget,
			'project_improvements_owner_id' => $project_improvements_owner_id,
			'project_improvements_completion' => $project_improvements_completion
		);	
		update_project_improvements($project_improvements_update,$project_improvements_id);
		add_system_records("operations","project_improvements_edit","$project_improvements_id",$_SESSION['logged_user_id'],"Update","");

		# now i need to update the current_budget ..
		update_current_budget($project_improvements_id);

	} elseif ($action == "edit" && !is_numeric($project_improvements_id)) {

		$project_improvements_update = array(
			'project_improvements_id' => $project_improvements_id,
			'project_improvements_title' => $project_improvements_title,
			'project_improvements_goal' => $project_improvements_goal,
			'project_improvements_rca' => $project_improvements_rca,
			'project_improvements_proactive' => $project_improvements_proactive,
			'project_improvements_reactive' => $project_improvements_reactive,
			'project_improvements_start' => $project_improvements_start,
			'project_improvements_deadline' => $project_improvements_deadline,
			'project_improvements_status_id' => $project_improvements_status_id,
			'project_improvements_plan_budget' => $project_improvements_plan_budget,
			'project_improvements_owner_id' => $project_improvements_owner_id,
			'project_improvements_completion' => $project_improvements_completion
		);	
		
		$id = add_project_improvements($project_improvements_update);
		add_system_records("operations","project_improvements_edit","$id",$_SESSION['logged_user_id'],"Insert","");
		
		
	 }
	
	if ($action == "edit_achievements" && is_numeric($project_improvements_achievements_id) && is_numeric($project_improvements_id)) {
		
		#it seems i need to add an expense to a project	
		$project_improvements_update = array(
			'project_improvements_achievements_text' => $project_improvements_achievements_text,
			'project_improvements_achievements_owner' => $project_improvements_achievements_owner,
			'project_improvements_achievements_date' => $project_improvements_achievements_date
		);	

		$id = update_project_improvements_achievements($project_improvements_update, $project_improvements_achievements_id);
		add_system_records("operations","project_improvements_achievements_edit","$id",$_SESSION['logged_user_id'],"Update","");
		
		# now i need to update the current_budget ..
		update_current_completion($project_improvements_id,$project_improvements_completion);
	}

	if ($action == "edit_achievements" && !is_numeric($project_improvements_achievements_id) && is_numeric($project_improvements_id)) {

		#it seems i need to add an expense to a project	
		$project_improvements_update = array(
			'project_improvements_achievements_proj_id' => $project_improvements_id,
			'project_improvements_achievements_text' => $project_improvements_achievements_text,
			'project_improvements_achievements_owner' => $project_improvements_achievements_owner,
			'project_improvements_achievements_date' => $project_improvements_achievements_date
		);	

		$id = add_project_improvements_achievements($project_improvements_update);
		add_system_records("operations","project_improvements_achievements_edit","$id",$_SESSION['logged_user_id'],"Insert","");
		
		# now i need to update the current_budget ..
		update_current_completion($project_improvements_id,$project_improvements_completion);

	}
	
	if ($action == "edit_expenses" && !is_numeric($project_improvements_expenses_id) && is_numeric($project_improvements_id)) {

		#it seems i need to add an expense to a project	
		$project_improvements_update = array(
			'project_improvements_expenses_project_id' => $project_improvements_id,
			'project_improvements_expenses_description' => $project_improvements_expenses_description,
			'project_improvements_expenses_amount' => $project_improvements_expenses_amount,
			'project_improvements_expenses_date' => $project_improvements_expenses_date
		);	

		$id = add_project_improvements_expenses($project_improvements_update);
		add_system_records("operations","project_improvements_expenses_edit","$id",$_SESSION['logged_user_id'],"Insert","");
		
		# now i need to update the current_budget ..
		update_current_budget($project_improvements_id);

	}
	
	if ($action == "edit_expenses" && is_numeric($project_improvements_expenses_id) && is_numeric($project_improvements_id)) {

		#it seems i need to update a expense to a project	
		$project_improvements_update = array(
			'project_improvements_expenses_description' => $project_improvements_expenses_description,
			'project_improvements_expenses_amount' => $project_improvements_expenses_amount,
			'project_improvements_expenses_date' => $project_improvements_expenses_date
		);	
		
		update_project_improvements_expenses($project_improvements_update, $project_improvements_expenses_id);
		add_system_records("operations","project_improvements_expenses_edit","$id",$_SESSION['logged_user_id'],"Update","");
		
		# now i need to update the current_budget ..
		update_current_budget($project_improvements_id);
	}
        
	if ($action == "disable_achievements" && is_numeric($project_improvements_achievements_id)) {
		disable_project_improvements_achievements($project_improvements_achievements_id);
		add_system_records("operations","project_improvements_achievements_edit","$project_improvements_achievements_id",$_SESSION['logged_user_id'],"Disable","");
	}

        if ($action == "disable_expenses" && is_numeric($project_improvements_expenses_id)) {
		disable_project_improvements_expenses($project_improvements_expenses_id);
		add_system_records("operations","project_improvements_expenses_edit","$project_improvements_id",$_SESSION['logged_user_id'],"Disable","");

		# now i need to update the current_budget ..
		$project_id = lookup_project_improvements_expenses("project_improvements_expenses_id",$project_improvements_expenses_id);
		update_current_budget($project_id[project_improvements_expenses_project_id]);
	}

	if ($action == "disable" && is_numeric($project_improvements_id)) {
		disable_project_improvements($project_improvements_id);

		# i have to disable all project expenses too
		disable_all_project_improvements_expenses($project_improvements_id);	

		add_system_records("operations","project_improvements_edit","$project_improvements_id",$_SESSION['logged_user_id'],"Disable","");
	}

	if ($action == "csv") {
		export_project_improvements_csv();
		add_system_records("operations","project_improvements","$project_improvements_id",$_SESSION['logged_user_id'],"Export","");
	}

	# ---- END TEMPLATE ------

?>


	<section id="content-wrapper">
		<h3>Project Management</h3>
		<span class=description>Manage your projects priorities, assignations, etc.</span>
		<br>
		<br>
		<div class="controls-wrapper">
<?
echo "			<a href=\"$base_url_edit&action=edit\" class=\"add-btn\">";
?>
				<span class="add-icon"></span>
				Add a new Project 
			</a>
			
			<div class="actions-wraper">
				<a href="#" class="actions-btn">
					Actions
					<span class="select-icon"></span>
				</a>
				<ul class="action-submenu">
<?
# -------- TEMPLATE! YOU MUST ADJUST THIS ------------
if ($action == "csv") {
	echo '<li><a href="' . $base_url_list . '&download_export=project_improvements_export">Download</a></li>';
} else { 
echo "					<li><a href=\"$base_url_list&action=list&sort=1\">Just an Idea Projects</a></li>";
echo "					<li><a href=\"$base_url_list&action=list&sort=2\">On-Going Projects</a></li>";
echo "					<li><a href=\"$base_url_list&action=list&sort=3\">Completed Projects</a></li>";
echo "					<li><a href=\"$base_url_list&action=csv\">Export All</a></li>";
}
?>
				</ul>
			</div>

		</div>
			
		
		<ul id="accordion">

	<br>
	<span class=description>On-going Projects</span>
	<br>
			
<?
	if ($show_id) {
		$project_improvements_list = list_project_improvements(" WHERE project_improvements_disabled = 0 AND project_improvements_id = $show_id");
	} else {
		if ($sort) {
			$project_improvements_list = list_project_improvements(" WHERE project_improvements_disabled=\"0\" AND project_improvements_status_id = \"$sort\"");
		} else {
			$project_improvements_list = list_project_improvements(" WHERE project_improvements_disabled=\"0\"");
		}
	}

	foreach($project_improvements_list as $project_improvements_item) {

echo "			<li>";
echo "				<div class=\"header\">";
echo "					$project_improvements_item[project_improvements_title]";
echo "					<span class=\"actions\">";
echo "						<a class=\"edit\" href=\"$base_url_edit&action=edit&project_improvements_id=$project_improvements_item[project_improvements_id]\">edit</a>";
echo "						&nbsp;|&nbsp;";
echo "						<a class=\"delete\" href=\"?section=system&subsection=system_records_list&system_records_lookup_section=operations&system_records_lookup_subsection=project_improvements_edit&system_records_lookup_item_id=$project_improvements_item[project_improvements_id]\">records</a>";
echo "						&nbsp;|&nbsp;";
echo "						<a class=\"edit\" href=\"$base_url_list&action=disable&project_improvements_id=$project_improvements_item[project_improvements_id]\">delete</a>";
echo "					</span>";
echo "					<span class=\"icon\"></span>";
echo "				</div>";
echo "				<div class=\"content table\">";
echo "					<table>";
echo "						<tr>";
echo "							<th class=\"center\">Status</th>";
echo "							<th class=\"center\">Start</th>";
echo "							<th class=\"center\">Planned End</th>";
echo "							<th class=\"center\">Owner</th>";
echo "							<th class=\"center\">Completion</th>";
echo "							<th class=\"center\">Plan Budget</th>";
echo "							<th class=\"center\">Current Budget</th>";
echo "						</tr>";

echo "						<tr>";
				$status_item = lookup_project_improvements_status("project_improvements_status_id",$project_improvements_item['project_improvements_status_id']);
echo "							<td><center>$status_item[project_improvements_status_name]</td>";
echo "							<td><center>$project_improvements_item[project_improvements_start]</td>";
echo "							<td><center>$project_improvements_item[project_improvements_deadline]</td>";
echo "							<td><center>$project_improvements_item[project_improvements_owner_id]</td>";

echo "							<td class=\"action-cell\">
								<div class=\"cell-label\">
					$project_improvements_item[project_improvements_completion] % 
								</div>
								<div class=\"cell-actions\">
<a href=\"$base_url_achievements_edit&project_improvements_id=$project_improvements_item[project_improvements_id]\" class=\"edit-action\">add an update</a> 
<a href=\"$base_url_achievements_list&project_improvements_id=$project_improvements_item[project_improvements_id]\" class=\"delete-action\">view all updates</a>
						</td>";

echo "							<td><center>$project_improvements_item[project_improvements_plan_budget] $services_conf[system_currency]</td>";

echo "							<td class=\"action-cell\">
								<div class=\"cell-label\">
					$project_improvements_item[project_improvements_current_budget] $services_conf[system_currency]
								</div>
								<div class=\"cell-actions\">
<a href=\"$base_url_edit_expenses&project_improvements_id=$project_improvements_item[project_improvements_id]\" class=\"edit-action\">add an expense</a> 
<a href=\"$base_url_list_expenses&project_improvements_id=$project_improvements_item[project_improvements_id]\" class=\"delete-action\">view all expenses</a>
						</td>";

echo "						</tr>";
	#}
echo "					</table>";
echo "<br>";
echo "					<div class=\"rounded\">";
echo "						<table class=\"sub-table\">";
echo "							<tr>";
echo "							</tr>";
echo " 					<th>Project Goal</th>";
echo "							<tr>";
echo " 					<td>$project_improvements_item[project_improvements_goal]</td>";
echo "							</tr>";
echo "						</table>";
echo "					</div>";
echo "<br>";
echo "<br>";
echo "					<div class=\"rounded\">";
echo "						<table class=\"sub-table\">";
echo "							<tr>";
echo "							</tr>";
echo " 					<th>Root Cause Analysis</th>";
echo "							<tr>";
echo " 					<td>$project_improvements_item[project_improvements_rca]</td>";
echo "							</tr>";
echo "						</table>";
echo "					</div>";
echo "<br>";
echo "<br>";
echo "					<div class=\"rounded\">";
echo "						<table class=\"sub-table\">";
echo "							<tr>";
echo "							</tr>";
echo " 					<th>Proactive Plans</th>";
echo "							<tr>";
echo " 					<td>$project_improvements_item[project_improvements_proactive]</td>";
echo "							</tr>";
echo "						</table>";
echo "					</div>";
echo "<br>";
echo "<br>";
echo "					<div class=\"rounded\">";
echo "						<table class=\"sub-table\">";
echo "							<tr>";
echo "							</tr>";
echo " 					<th>Reactive Plans</th>";
echo "							<tr>";
echo " 					<td>$project_improvements_item[project_improvements_reactive]</td>";
echo "							</tr>";
echo "						</table>";
echo "					</div>";
echo "<br>";
### INJERTO ENDS
echo "				</div>";
echo "			</li>";
	}
?>
		</ul>
		
		<br class="clear"/>
		
	</section>
