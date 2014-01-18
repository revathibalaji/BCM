<?

	include_once("lib/compliance_package_lib.php");
	include_once("lib/tp_lib.php");
	include_once("lib/site_lib.php");

	$section = $_GET["section"];
	$subsection = $_GET["subsection"];
	$action = $_GET["action"];
	$tp_id = $_GET["tp_id"];
	
	$base_url_list  = build_base_url($section,"compliance_package_list");

?>


	<section id="content-wrapper">
		<h3>Upload a Complete Compliance Package</h3>
		<span class="description">You can upload a complete compliance package from your computer using a Comma Separated File (CSV). You need to be carefull on how you setup the columns of your CSV tough! <br><br><b>WARNINGGGGGG!!!!!!:</b> read instructions on how to use this <a href="http://www.eramba.org/howto/compliance-management/uploading-compliance-packages/">here</a></span>
		<div class="tab-wrapper"> 
			<ul class="tabs">
				<li class="first active">
					<a href="tab1">General</a>
					<span class="right"></span>
				</li>
			</ul>
			
			<div class="tab-content">
				<div class="tab" id="tab1">
<?
echo "					<form name=\"compliance_package_edit\" method=\"POST\" action=\"$base_url_list\" enctype=\"multipart/form-data\">";
?>
						<label for="name">To which Third Party you'll associate this Package?</label>
						<span class="description">Select one third party with whom you'll asociate this compliance package. If you dont have what you want, you can always create a new Third Party</span>
						<select name="tp_id" id="" class="chzn-select">
						<option value="-1">Select one third party</option>
<?
						list_drop_menu_tp(NULL,"tp_name");	
?>
						</select>
						
						<label for="description">Upload File</label>
						<span class="description">Right, you can select a CSV file and upload it. WARNING!: follow this instructions or use this sample CSV to understand how the formating works! </span>
<? 
	echo "<input type=\"file\" name=\"compliance_package_csv_file\"><br>";
?>
				</div>
				
			</div>
		</div>
		
		<div class="controls-wrapper">

				    <INPUT type="hidden" name="action" value="upload_compliance_package">
				    <INPUT type="hidden" name="section" value="compliance">
				    <INPUT type="hidden" name="subsection" value="compliance_package_list">

			<a>
			    <INPUT type="submit" value="Submit" class="add-btn"> 
			</a>
			
<?
echo "			<a href=\"$base_url_list\" class=\"cancel-btn\">";
?>
				Cancel
				<span class="select-icon"></span>
			</a>
					</form>
		</div>
		
		<br class="clear"/>
		
	</section>
</body>
</html>
