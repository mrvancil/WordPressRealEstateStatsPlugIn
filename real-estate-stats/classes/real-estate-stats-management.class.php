<?php
require_once('real-estate-stats.common.php');

if( !class_exists('RealEstateStatsManagement') ) :

class RealEstateStatsManagement
{
	function RealEstateStatsManagement()
	{
		global $wpdb;
	}

	function displayRealEstateStatsManagementPage()
	{
		global $wpdb;
		echo '<h2>Real Estate Statistic Management</h2>';
		echo '<p>Please utilize this form to update the statistics per area and year for the Widget.</p>';
		echo '<form name="EditRealEstateStatsForm" method="post" action="?page=real-estate-stats.php">';
		
		$areaId = $_POST["mv_stat_frm_area"];
		$year = $_POST["mv_stat_frm_year"];
		
		echo "\t\t<div>Select A Year And Area To Manage : " ;

		$realEstateStatsCommon = new RealEstateStatsCommon();
		$realEstateStatsCommon->writeYearSelect($year);
		$realEstateStatsCommon->writeAreaSelect($areaId);
		
		$nonce = wp_create_nonce("mvManagementStat_nonce");
		$link = admin_url('admin-ajax.php?action=mvManagementGetEditibleStats&nonce=' .$nonce);

		echo '<input class="mv_stat_button" data-nonce="'. $nonce . '" type="button" name="load" id="load" value="Load For Editing">'; 
		echo '<div id="mv_stats_entry_area"></div>';
		echo '<p class="submit">';
		echo '<input type="hidden" name="editAreaStats" value="1" />';
		echo '<input class="stats-save-button hide-me" id="save-stats-button" type="submit" name="submit" value="Save Statistics" />';
		echo '</p>';
		echo '</form>';
		echo '</div>';
	}
}
endif;
?>