<?php
/*
Plugin Name: Real Estate Statistics Core
Plugin URI: http://michellevancil.com/wordpress-plugins/real-estate-stats/
Description: Created a feature for realestate websites to present local area statitics that can  be managed by the content admininstrator. 
This is the core module for all other widgets.  
Version: 0.0.1
Author: Tadd Vancil
Author URI: http://twitter.com/tvancil

---------------------------------------------------------------------
This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
---------------------------------------------------------------------
*/
require_once('classes/real-estate-stats.class.php');
require_once('classes/real-estate-stats-management.class.php');
require_once('classes/real-estate-stats.common.php');


if( class_exists('RealEstateStats') && class_exists('RealEstateStatsManagement') ) :

$realEstateStats = new RealEstateStats();
$realEstateStatsManagement = new RealEstateStatsManagement();
$realEstateStatsCommon = new RealEstateStatsCommon();

if( isset($realEstateStats) && isset($realEstateStatsManagement))
{
	if($_POST['editAreaStats'] == 1)
	{
		$areaid = $_POST["mv_stat_frm_area"];
		$year = $_POST["mv_stat_frm_year"];
		$number_of_properties = 7;
		$number_of_stats = 5;

		//delete the existing data
		$realEstateStatsCommon->DeleteStatData($year,$areaid);

		for ($propertytypeid = 1; $propertytypeid <= $number_of_properties; $propertytypeid++) {
				
			for ($i = 1; $i <= $number_of_stats; $i++) {
				$formfield = "stat_" . $propertytypeid . "_" . $i;
				$realEstateStatsCommon->SaveStatData($areaid, $propertytypeid, $i, $year, $_POST[$formfield]);
			}
		}
	}

	function mvStatsInit()
	{
		global $realEstateStats;
		
		if( !function_exists('register_sidebar_widget') )
		{
			return;
		}
		
		register_sidebar_widget('Real Estate Stats', array(&$realEstateStats, 'displayMVWidget'));
	}
	function mvManagementInit()
	{
		global $realEstateStatsManagement;
		
		wp_enqueue_script( 'listman' );
		add_management_page('Real Estate Stats', 'Real Estate Stats', 5, basename(__FILE__), array(&$realEstateStatsManagement, 'displayRealEstateStatsManagementPage'));
 	}
 	

	function mvScriptEnqueuer()
   	{
		wp_register_script( "mv_management_stat_script", WP_PLUGIN_URL.'/real-estate-stats/js/mv_stat_management_ajax.js', array('jquery') );
		wp_localize_script( 'mv_management_stat_script', 'mvManagementStatAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));  

    	wp_enqueue_script('jquery');   
    	wp_enqueue_script( 'mv_management_stat_script' );
   	}

	add_action('activate_real-estate-stats/real-estate-stats.php', array(&$realEstateStats, 'createDatabaseTable'));
	add_action('deactivate_real-estate-stats/real-estate-stats.php', array(&$realEstateStats, 'deleteDatabaseTable'));
	add_action('init', 'mvScriptEnqueuer');
	add_action('admin_head', array(&$realEstateStats, 'mvHeaderContent'));
	add_action('wp_head', array(&$realEstateStats, 'mvHeaderContent'));
	add_action('admin_menu', 'mvManagementInit');
	add_action('plugins_loaded', 'mvStatsInit');
	add_action("wp_ajax_mvManagementGetEditibleStats", "mvManagementGetEditibleStats");
	add_action("wp_ajax_mvManagementGetGridStats", "mvManagementGetGridStats");
	add_action("wp_ajax_nopriv_mvManagementGetGridStats", "mvManagementGetGridStats");
	add_action("wp_ajax_mvManagementGetGraphStats", "mvManagementGetGraphStats");
	add_action("wp_ajax_nopriv_mvManagementGetGraphStats", "mvManagementGetGraphStats");


	function mvManagementGetEditibleStats()
 	{
 		if ( !wp_verify_nonce( $_REQUEST['nonce'], "mvManagementStat_nonce")) {
			exit("You are trying to hack the application, please stop. pretty please?");
		} 

		$year = $_REQUEST["year"];
		$areaId = $_REQUEST["areaId"];

		global $realEstateStatsCommon;
		header("Content-Type: application/json");

		$result = $realEstateStatsCommon->GetRealEstateStatsEntryForm($year, $areaId);
		$result = json_encode($result);
		echo $result;

		die();
	}
	function mvManagementGetGridStats()
 	{
 		//if ( !wp_verify_nonce( $_REQUEST['nonce'], "mvManagementStat_nonce")) {
		//	exit("You are trying to hack the application, please stop. pretty please?");
		//} 

		$year = $_REQUEST["year"];
		$areaId = $_REQUEST["areaId"];

		global $realEstateStatsCommon;
		header("Content-Type: application/json");

		$result = $realEstateStatsCommon->GetRealEstateStatsGrid($year, $areaId);
		$result = json_encode($result);
		echo $result;

		die();
	}
	function mvManagementGetGraphStats()
 	{
 		//if ( !wp_verify_nonce( $_REQUEST['nonce'], "mvManagementStat_nonce")) {
		//	exit("You are trying to hack the application, please stop. pretty please?");
		//} 
		$areaId = $_REQUEST["areaId"];
		$propertyTypeId = $_REQUEST["propertyTypeId"];
		$statTypeId = $_REQUEST["statTypeId"];

		global $realEstateStatsCommon;
		header("Content-Type: application/json");

		$result = $realEstateStatsCommon->GetChartData($areaId,$propertyTypeId,$statTypeId);
		$result = json_encode($result);
		echo $result;

		die();
	}
}

endif;

?>