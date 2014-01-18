<?php
include_once("header.php");
include_once("lib/dashboard_general_lib.php");
$compliance_chart_data = list_dashboard_chart("compliance_dashboard_tbl");
?>

	<section id="content-wrapper">
		<div id="widgets-area-wrap">
			<div id="main-area">
				<div class="widget">
					<div class="widget-header">Number of compliance items, compliance items without mitigation controls and items with failed mitigation controls</div>
					<div class="widget-content">

						<script type="text/javascript">
						var window_width = $(window).width() - 100;
						  function drawVisualization() {
						    // Create and populate the data table.
						    var data = google.visualization.arrayToDataTable([
						      ['x',         'Comp Items', 'Items without Controls', 'Items with failed controls'],
						      <?php
						     	
						      	foreach ( $compliance_chart_data as $data ) : ?>
						      		<?php $date = date( "m-Y", strtotime($data["dashboard_date"]) ); ?>
						      		['<?php echo $date ?>', <?php echo $data['compliance_dashboard_comp_items']; ?>, <?php echo $data['compliance_dashboard_without_mitigation']; ?>, <?php echo $data['compliance_dashboard_control_failed']; ?>],
						      	<?php endforeach; ?>
						    ]);
						  
						    // Create and draw the visualization.
						    new google.visualization.LineChart(document.getElementById('visualization')).
						        draw(data, {curveType: "function",
						                    width: window_width, height: 200,
						                    vAxis: {maxValue: 10},
								fontSize: 11
								}
						            );
						  }


						  google.setOnLoadCallback(drawVisualization);

						</script>

							<div id="visualization" style="height: 200px;"></div>

					</div>
				</div>
				<div class="widget">
					<div class="widget-header">Number of compliance items by status</div>
					<div class="widget-content">

						<script type="text/javascript">
						var window_width = $(window).width() - 100;
						  function drawVisualization() {
						    // Create and populate the data table.
						    var data = google.visualization.arrayToDataTable([
						      ['x',         'Status Ongoing', 'Status Compliant', 'Status Noncomp', 'Status'],
						      <?php
						     	
						      	foreach ( $compliance_chart_data as $data ) : ?>
						      		<?php $date = date( "m-Y", strtotime($data["dashboard_date"]) ); ?>
						      		['<?php echo $date ?>', <?php echo $data['compliance_dashboard_status_ongoing']; ?>, <?php echo $data['compliance_dashboard_status_compliant']; ?>, <?php echo $data['compliance_dashboard_status_noncomp']; ?>, <?php echo $data['compliance_dashboard_status_na']; ?>],
						      	<?php endforeach; ?>
						    ]);
						  
						    // Create and draw the visualization.
						    new google.visualization.LineChart(document.getElementById('visualization2')).
						        draw(data, {curveType: "function",
						                    width: window_width, height: 200,
						                    vAxis: {maxValue: 10},
									fontSize: 11 }
						            );
						  }

						  google.setOnLoadCallback(drawVisualization);

						</script>

							<div id="visualization2" style="height: 200px;"></div>

					</div>
				</div>
				<div class="widget">
					<div class="widget-header">Number of compliance items by response strategy</div>
					<div class="widget-content">

						<script type="text/javascript">
						var window_width = $(window).width() - 100;
						  function drawVisualization() {
						    // Create and populate the data table.
						    var data = google.visualization.arrayToDataTable([
						      ['x',         'Strategy Mitigate', 'Strategy'],
						      <?php
						     	
						      	foreach ( $compliance_chart_data as $data ) : ?>
						      		<?php $date = date( "m-Y", strtotime($data["dashboard_date"]) ); ?>
						      		['<?php echo $date ?>', <?php echo $data['compliance_dashboard_strategy_mitigate']; ?>, <?php echo $data['compliance_dashboard_strategy_na']; ?>],
						      	<?php endforeach; ?>
						    ]);
						  
						    // Create and draw the visualization.
						    new google.visualization.LineChart(document.getElementById('visualization3')).
						        draw(data, {curveType: "function",
						                    width: window_width, height: 200,
						                    vAxis: {maxValue: 10},
									fontSize: 11 }
						            );
						  }

						  google.setOnLoadCallback(drawVisualization);

						</script>

							<div id="visualization3" style="height: 200px;"></div>

					</div>
				</div>
			</div>
		</div>
	</section>

<?
include_once("footer.php");
?>
