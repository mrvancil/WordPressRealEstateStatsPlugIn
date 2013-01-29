<?php
/*
Template Name: templatetest
*/
// Exit if accessed directly
if ( !defined('ABSPATH')) exit;

require_once('wp-content/plugins/real-estate-stats/classes/real-estate-stats.common.php');
?>
<?php get_header(); ?>

<div id="content" class="grid col-620">
<h1>Sales Statistics</h1>
<div>
<?php

	$currentYear = date("Y"); 
	$year=$currentYear - 1;
	$areaId = 1;

	$realEstateStatsCommon = new RealEstateStatsCommon();
	$realEstateStatsCommon->WriteAreaSelect($areaId);
	$realEstateStatsCommon->WriteYearSelection($year);
	echo '<input type="button" class="button mv-stats-grid-view-results" value="View Results">';
	echo "<div id=\"mv_stats_spinner_container\"><img src=\"" . WP_PLUGIN_URL. "/real-estate-stats/img/spinner.gif\"></div>";
	echo "<div id=\"mv_stats_grid\"></div>";

?>
<script>
	loadGridStats();
</script>
</div>  
</div><!-- end of #content -->
       
<?php get_footer(); ?>
