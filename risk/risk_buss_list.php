<?
	include_once("lib/tp_lib.php");
	include_once("lib/bu_lib.php");
	include_once("lib/risk_lib.php");
	include_once("lib/legal_lib.php");
	include_once("lib/risk_classification_lib.php");
	include_once("lib/risk_exception_lib.php");
	include_once("lib/site_lib.php");
	include_once("lib/asset_lib.php");
	include_once("lib/risk_security_services_join_lib.php");
	include_once("lib/risk_buss_process_join_lib.php");
	include_once("lib/risk_tp_asset_join_lib.php");
	include_once("lib/security_services_lib.php");
	include_once("lib/security_services_status_lib.php");
	include_once("lib/risk_risk_exception_join_lib.php");
	include_once("lib/risk_mitigation_strategy_lib.php");
	include_once("lib/system_records_lib.php");
	include_once("lib/bcm_plans_lib.php");
	include_once("lib/bcm_plans_audit_lib.php");

	# general variables - YOU SHOULDNT NEED TO CHANGE THIS
	$sort = filter_input(INPUT_GET,"sort",FILTER_SANITIZE_STRING);
	$section = filter_input(INPUT_GET,"section",FILTER_SANITIZE_STRING);
	$subsection = filter_input(INPUT_GET,"subsection",FILTER_SANITIZE_STRING);
	$action = filter_input(INPUT_GET,"action",FILTER_SANITIZE_STRING);
	
	$base_url_list = build_base_url($section,"risk_buss_list");
	$base_url_edit = build_base_url($section,"risk_buss_edit");
	
	# local variables - YOU MUST ADJUST THIS! 
	$bu_id = isset ($_GET["bu_id"]) ? $_GET["bu_id"]:null;
	
	if ($action == "update") {
		if (is_array($bu_id)) {
			# echo "puta: bu_id is an array<br>";
			foreach($bu_id as $bu_item) {
				#check this is a valid tp
				$bu_information=lookup_bu("bu_id",$bu_item);
				if ($bu_information[bu_disabled]!="0") {
					#echo "puta: i'm not doing anything if i dont get a valid bu<br>";
					$action = NULL;
				}
			}
		} else {
			#echo "puta: no bu_id no game<br>";
			$action = NULL;
		}
	}

	$risk_id = isset ($_GET["risk_id"]) ? $_GET["risk_id"]:null;
	
	# if i get a risk_id, i want to make sure it's a really valid one...otherwise void
	if ($risk_id) {
		$risk_information = lookup_risk("risk_id",$risk_id);
		if ($risk_information[risk_disabled]!="0") {
			$action = NULL;
		}
	} 

	$risk_title = filter_input(INPUT_GET,"risk_title",FILTER_SANITIZE_STRING);
	$risk_buss_what_if = filter_input(INPUT_GET,"risk_buss_what_if",FILTER_SANITIZE_STRING);
	$risk_threat = filter_input(INPUT_GET,"risk_threat",FILTER_SANITIZE_STRING);
	$risk_vulnerabilities = filter_input(INPUT_GET,"risk_vulnerabilities",FILTER_SANITIZE_STRING);
	$risk= filter_input(INPUT_GET,"risk",FILTER_SANITIZE_NUMBER_INT);
	# $tp_asset_id= filter_input(INPUT_GET,"tp_asset_id",FILTER_SANITIZE_NUMBER_INT);
	$tp_asset_id = isset ($_GET["tp_asset_id"]) ? $_GET["tp_asset_id"]:null;
	$risk_mitigation_bcm_id = isset ($_GET["risk_mitigation_bcm_id"]) ? $_GET["risk_mitigation_bcm_id"]:null;
	$risk_classification = isset ($_GET["risk_classification"]) ? $_GET["risk_classification"]:null;
	# $risk_classification= filter_input(INPUT_GET,"risk_classification",FILTER_SANITIZE_NUMBER_INT);
	$risk_classification_score = isset ($_GET["risk_classification_score"]) ? $_GET["risk_classification_score"]:null;
	# $risk_classification_score= filter_input(INPUT_GET,"risk_classification_score",FILTER_SANITIZE_NUMBER_INT);
	if (!is_numeric($risk_classification_score)) {
		$risk_classification_score = 0;
	}

	if (!$risk_title) {
		$risk_title = "Un-named Risk";
	}

	$risk_mitigation_strategy_id = isset ($_GET["risk_mitigation_strategy_id"]) ? $_GET["risk_mitigation_strategy_id"]:null;
	# $risk_mitigation_strategy_id= filter_input(INPUT_GET,"risk_mitigation_strategy_id",FILTER_SANITIZE_NUMBER_INT);
	$security_services_id = isset ($_GET["security_services_id"]) ? $_GET["security_services_id"]:null;
	# $security_services_id= filter_input(INPUT_GET,"security_services_id",FILTER_SANITIZE_NUMBER_INT);
	$risk_exception_id = isset ($_GET["risk_exception_id"]) ? $_GET["risk_exception_id"]:null;
	# $risk_exception_id= filter_input(INPUT_GET,"risk_exception_id",FILTER_SANITIZE_NUMBER_INT);

	$risk_periodicity_review = isset ($_GET["risk_periodicity_review"]) ? $_GET["risk_periodicity_review"]:null;
	$risk_residual_score = isset ($_GET["risk_residual_score"]) ? $_GET["risk_residual_score"]:null;
	if (!is_numeric($risk_residual_score)) {
		$risk_residual_score = $risk_classification_score;
	}

	$security_services_id = isset ($_GET["security_services_id"]) ? $_GET["security_services_id"]:null;
	$risk_exception_id = isset ($_GET["risk_exception_id"]) ? $_GET["risk_exception_id"]:null;
	
	#actions .. edit, update or disable - YOU MUST ADJUST THIS!
	if ($action == "update" && is_numeric($risk_id) && !empty($bu_id)) {
		#echo "puta, deberia estar updateando risk tp";
		$risk_update = array(
			'risk_title' => $risk_title,
			'risk_buss_what_if' => $risk_buss_what_if,
			'risk_threat' => $risk_threat,
			'risk_vulnerabilities' => $risk_vulnerabilities,
			'risk_classification_score' => $risk_classification_score,
			'risk_mitigation_strategy_id' => $risk_mitigation_strategy_id,
			'risk_mitigation_bcm_id' => $risk_mitigation_bcm_id,
			'risk_periodicity_review' => $risk_periodicity_review,
			'risk_residual_score' => $risk_residual_score
		);	
		update_risk($risk_update,$risk_id);
		add_system_records("risk","risk_buss_list","$risk_id",$_SESSION['logged_user_id'],"Update","");
		
		# delete all bus for this risk
		if ($risk_id) {
		delete_risk_buss_process_join($risk_id);
		}

		# add all selected tps for this risk
		if (is_array($bu_id)) {
			foreach($bu_id as $bu_item) {
				# now i insert this stuff
				add_risk_buss_process_join($risk_id, $bu_item);
			}
		}
		
		
		# delete all assets for this risk
		delete_risk_tp_asset_join($risk_id);
		
		# delete all security services for this risk
		delete_risk_security_services_join($risk_id);
		# add all selected security services for this risk
		if (is_array($security_services_id)) {
			$count_security_services_id_item = count($security_services_id);
			for($count = 0 ; $count < $count_security_services_id_item ; $count++) {
				# now i insert this stuff
				add_risk_security_services_join($risk_id, $security_services_id[$count]);
			}
		}
		
		# delete all risk_exceptions for this risk
		delete_risk_risk_exception_join($risk_id);
		# add all selected security services for this risk
		if (is_array($risk_exception_id)) {
			$count_risk_exception_id_item = count($risk_exception_id);
			for($count = 0 ; $count < $count_risk_exception_id_item ; $count++) {
				# now i insert this stuff
				add_risk_risk_exception_join($risk_id, $risk_exception_id[$count]);
			}
		}

		# 1) delete all classifications for this risk
		delete_risk_classification_join($risk_id);
		# 2) insert all classification for this risk
		if (is_array($risk_classification)) {
			$count_risk_classification_item = count($risk_classification);
			for($count = 0 ; $count < $count_risk_classification_item ; $count++) {
				# now i insert this stuff
				add_risk_classification_join($risk_id, $risk_classification[$count]);
			}
		}

	} elseif ($action == "update" && !is_numeric($risk_id) && !empty($bu_id)) {

		$risk_update = array(
			'risk_title' => $risk_title,
			'risk_buss_what_if' => $risk_buss_what_if,
			'risk_threat' => $risk_threat,
			'risk_vulnerabilities' => $risk_vulnerabilities,
			'risk_classification_score' => $risk_classification_score,
			'risk_mitigation_strategy_id' => $risk_mitigation_strategy_id,
			'risk_mitigation_bcm_id' => $risk_mitigation_bcm_id,
			'risk_periodicity_review' => $risk_periodicity_review,
			'risk_residual_score' => $risk_residual_score
		);	
		$risk_id = add_risk($risk_update);

		add_system_records("risk","risk_buss_list","$risk_id",$_SESSION['logged_user_id'],"Insert","");
		
		# delete all tps for this risk
		if ($risk_id) {
		delete_risk_buss_process_join($risk_id);
		}

		# add all selected tps for this risk
		if (is_array($bu_id)) {
			foreach($bu_id as $tp_item) {
				# now i insert this stuff
				add_risk_buss_process_join($risk_id, $tp_item);
			}
		}
		
		# delete all security services for this risk
		delete_risk_security_services_join($risk_id);
		# add all selected security services for this risk
		if (is_array($security_services_id)) {
			$count_security_services_id_item = count($security_services_id);
			for($count = 0 ; $count < $count_security_services_id_item ; $count++) {
				# now i insert this stuff
				add_risk_security_services_join($risk_id, $security_services_id[$count]);
			}
		}
		
		# delete all risk_exceptions for this risk
		delete_risk_risk_exception_join($risk_id);
		# add all selected security services for this risk
		if (is_array($risk_exception_id)) {
			$count_risk_exception_id_item = count($risk_exception_id);
			for($count = 0 ; $count < $count_risk_exception_id_item ; $count++) {
				# now i insert this stuff
				add_risk_risk_exception_join($risk_id, $risk_exception_id[$count]);
			}
		}

		# 1) delete all classifications for this risk
		delete_risk_classification_join($risk_id);
		# 2) insert all classification for this risk
		if (is_array($risk_classification)) {
			$count_risk_classification_item = count($risk_classification);
			for($count = 0 ; $count < $count_risk_classification_item ; $count++) {
				# now i insert this stuff
				add_risk_classification_join($risk_id, $risk_classification[$count]);
			}
		}
		
	 }

	if ($action == "disable" & is_numeric($risk_id)) {
		disable_risk($risk_id);
		delete_risk_buss_process_join($risk_id);
		add_system_records("risk","risk_buss_list","$risk_id",$_SESSION['logged_user_id'],"Disable","");
		#i should also disable all risk asociated items
	}

	if ($action == "csv") {
		export_risk_buss_csv();
		add_system_records("risk","risk_buss_list","$risk_id",$_SESSION['logged_user_id'],"Export","");
	}

	# ---- END TEMPLATE ------

?>


	<section id="content-wrapper">
		<h3>Business Based - Risk Analysis</h3>
		<span class=description>Identifying and analysing Business Risks can be usefull if executed in a simple and practical way. For each Business line, identify and analyse risks. You will later have the option to mitigate them by the use of controls or Continuity Plans.</span>
		<br>
		<br>
		<div class="controls-wrapper">
<?
echo "			<a href=\"$base_url_edit&action=edit_risk\" class=\"add-btn\">";
?>
				<span class="add-icon"></span>
				Add a new Risk 
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
echo '<li><a href="' . $base_url_list . '&download_export=risk_buss_export">Download</a></li>';
} else { 
echo "					<li><a href=\"$base_url_list&action=csv\">Export All</a></li>";
}
?>
				</ul>
			</div>

		</div>
			
		
		<ul id="accordion">
			
<?
	$bu_list = list_bu(" WHERE bu_disabled=\"0\"");
	foreach($bu_list as $bu_item) {

	echo "<br><h4>Business Unit Name: $bu_item[bu_name]</h4>";

	$risk_list = list_risk_buss_process_join(" WHERE risk_buss_process_join_bu_id = \"$bu_item[bu_id]\"");

	if (count($risk_list)==NULL) {	
		continue;
	} else {

	foreach ($risk_list as $risk_item) {

	$risk_data = lookup_risk("risk_id",$risk_item[risk_buss_process_join_risk_id]);
	$risk_mitigation = lookup_risk_mitigation_strategy("risk_mitigation_strategy_id",$risk_data[risk_mitigation_strategy_id]); 
	
	# i'm now checking the expcetions asociated for expiration date
	$risk_exception_for_this_risk_list = list_risk_risk_exception_join(" WHERE risk_risk_exception_join_risk_id = \"$risk_data[risk_id]\""); 
	foreach($risk_exception_for_this_risk_list as $risk_exception_for_this_risk_item) {
		if (check_risk_exception($risk_exception_for_this_risk_item[risk_risk_exception_join_risk_exception_id])) {
			$warning_expired_exception= " - (Warning: Expired Risk Exception)";
		} else {
			$warning_expired_exception= "";
		}
	}

	unset($risk_exception_expiration);

	# i'm checking if the risk expiration review is ok or not 
	if (check_expired_risk_review($risk_data[risk_id])) {
		$warning_expired_risk = " - (Warning: Expired Risk Review)";
	} else {
		$warning_expired_risk = "";
	}

	# i'm now checking if the controls asociated with this risk (if any) are ok or not
	if ( check_bcm_plans_last_audit_result($risk_data[risk_mitigation_bcm_id])) {
		$warning_control = "";
	} else {
		$warning_control = " - (Warning: Mitigation Plans with Issues)";
	}

	if (empty($risk_data[risk_mitigation_bcm_id]) or $risk_data[risk_mitigation_bcm_id] < "0") {
		$warning_no_control = " - (Warning: Missing Mitigation Plans)";
	}

echo "			<li>";
echo "				<div class=\"header\">";
echo "					Risk Title: $risk_data[risk_title] $warning_expired_risk $warning_control $warning_expired_exception $warning_no_control";
	
	unset($warning_expired_risk);
	unset($warning_control);
	unset($warning_expired_exception);
	unset($warning_no_control);
	
echo "					<span class=\"actions\">";
echo "						<a class=\"edit\" href=\"$base_url_edit&action=edit&risk_id=$risk_data[risk_id]\">edit</a>";
echo "						&nbsp;|&nbsp;";
echo "						<a class=\"delete\" href=\"?section=system&subsection=system_records_list&system_records_lookup_section=risk&system_records_lookup_subsection=risk_buss_list&system_records_lookup_item_id=$risk_data[risk_id]\">records</a>";
echo "						&nbsp;|&nbsp;";
echo "						<a class=\"delete\" href=\"?action=edit&section=operations&subsection=project_improvements_edit&ciso_pmo_lookup_section=risk&ciso_pmo_lookup_subsection=risk_buss_list&ciso_pmo_lookup_item_id=$risk_data[risk_id]\">improve</a>";
echo "						&nbsp;|&nbsp;";
echo "						<a class=\"edit\" href=\"$base_url_list&action=disable&risk_id=$risk_data[risk_id]\">delete</a>";
echo "					</span>";
echo "					<span class=\"icon\"></span>";
echo "				</div>";
echo "				<div class=\"content table\">";
echo "					<table>";
echo "						<tr>";
echo "							<th>Threats</th>";
echo "							<th>Vulnerabilities</th>";
echo "							<th class=\"center\">Risk Score</th>";
echo "							<th class=\"center\">Mitigation Strategy</th>";
echo "							<th class=\"center\">Review Periodicity</th>";
echo "							<th class=\"center\">Residual Risk</th>";
echo "						</tr>";

echo "						<tr>";
echo "							<td class=\"action-cell\">";
echo "								 	$risk_data[risk_threat]";
echo "							</td>";
echo "							<td>$risk_data[risk_vulnerabilities]</td>";
echo "							<td><center>$risk_data[risk_classification_score]</td>";
echo "							<td><center>$risk_mitigation[risk_mitigation_strategy_name]</td>";
echo "							<td><center>$risk_data[risk_periodicity_review]</td>";
echo "							<td><center>$risk_data[risk_residual_score]</td>";
echo "						</tr>";
	#}

echo "					</table>";
echo "<br>";


echo "<div class=\"rounded\">";
echo "<table class=\"sub-table\">";
echo "<tr>";
echo "<th class=\"left\">What is the business impact if this threats materializes?</th>";
echo "</tr>";
echo "<tr>";
echo "<td class=\"left\">".substr($risk_data[risk_buss_what_if],0,200)."...</td>";
echo "</tr>";
echo "</table>";
echo "</div>";



echo "<br>";
### INJERTO STARTS
echo "					<div class=\"rounded\">";
echo "						<table class=\"sub-table\">";
echo "							<tr>";
	# first i create columns for each category .. no more than 5!
	$risk_classification_list = list_risk_classification_distinct();
	foreach($risk_classification_list as $risk_classification_item) {
		echo "<th><center>$risk_classification_item[risk_classification_type]</th>";
	}
echo "							</tr>";
echo "							<tr>";
	# now i put the values 
	$risk_classification_list = list_risk_classification_distinct();
	foreach($risk_classification_list as $risk_classification_item) {
		# echo "Trola: $risk_classification_item[risk_classification_type] risk: $risk_data[risk_id]";
		$value = pre_selected_risk_classification_values($risk_classification_item[risk_classification_type], $risk_data[risk_id]);	
		# echo "classification: $value";
		$name = lookup_risk_classification("risk_classification_id", $value);
		echo "<td><center>$name[risk_classification_name]</td>";
	}

echo "							</tr>";
echo "						</table>";
echo "					</div>";
echo "<br>";

echo "					<div class=\"rounded\">";
echo "			<table class=\"sub-table\">";
echo "				<tr>";
echo "					<th class=\"center\">Continuity Plan</th>";
echo "					<th class=\"center\">Objective</th>";
echo "					<th class=\"center\">Status</th>";
echo "					<th class=\"center\">Audit Results</th>";
echo "				</tr>";

echo "				<tr>";

	$bcm_data = lookup_bcm_plans("bcm_plans_id",$risk_data[risk_mitigation_bcm_id]);	
	$bcm_status_name = lookup_security_services_status("security_services_status_id",$bcm_data[bcm_plans_status]);

	echo "<td class=\"left\">$bcm_data[bcm_plans_title]</td>";
	echo "<td class=\"left\">".substr($bcm_data[bcm_plans_objective],0,100)."...</td>";
	echo "<td class=\"center\">$bcm_status_name[security_services_status_name]</td>";

	if ( check_bcm_plans_last_audit_result($risk_data[risk_mitigation_bcm_id]) ) {
		$audit_result = "Ok";
	} else {
		$audit_result = "Not Ok";
	}


	echo "<td class=\"center\">$audit_result</td>";


echo "				</tr>";
echo "			</table>";
echo "					</div>";
echo "<br>";

echo "<br>";
echo "					<div class=\"rounded\">";
echo "			<table class=\"sub-table\">";
echo "				<tr>";
echo "					<th class=\"center\">Risk Exceptions</th>";
echo "					<th class=\"center\">Description</th>";
echo "					<th class=\"center\">Author</th>";
echo "					<th class=\"center\">Expiration</th>";
echo "				</tr>";
$risk_exception_for_this_risk_list = list_risk_risk_exception_join(" WHERE risk_risk_exception_join_risk_id = \"$risk_data[risk_id]\""); 
foreach($risk_exception_for_this_risk_list as $risk_exception_for_this_risk_item) {
	$risk_exception_data = lookup_risk_exception("risk_exception_id",$risk_exception_for_this_risk_item[risk_risk_exception_join_risk_exception_id]);	
echo "				<tr>";
	echo "<td class=\"left\">$risk_exception_data[risk_exception_title]</td>";
	echo "<td class=\"left\">$risk_exception_data[risk_exception_description]</td>";
	echo "<td class=\"center\">$risk_exception_data[risk_exception_author]</td>";
	echo "<td class=\"center\">$risk_exception_data[risk_exception_expiration]</td>";
echo "				</tr>";
}
echo "			</table>";
echo "					</div>";
echo "<br>";

### INJERTO ENDS
echo "				</div>";
echo "			</li>";
	}
	}
	}
?>
		</ul>
		
		<br class="clear"/>
		
	</section>
