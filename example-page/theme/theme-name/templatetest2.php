<?php
/*
Template Name: templatetest2
*/
// Exit if accessed directly
if ( !defined('ABSPATH')) exit;

require_once('wp-content/plugins/real-estate-stats/classes/real-estate-stats.common.php');
?>
<?php get_header(); ?>

<div id="content" class="grid col-620">
<h1>Interactive Sales Tool</h1>
<div>
<?php

	$areaId = 1;
	$realEstateStatsCommon = new RealEstateStatsCommon();
	$realEstateStatsCommon->WriteAreaSelect($areaId);
	$realEstateStatsCommon->WritePropertySelect();
	$realEstateStatsCommon->WriteStatSelect();
	echo '<input type="button" class="button mv-stats-graph-view-results" value="View Graph">';
	echo "<div id=\"mv_stats_spinner_container\"><img src=\"" . WP_PLUGIN_URL. "/real-estate-stats/img/spinner.gif\"></div>";
	echo "<div style=\"width: 100%; height: 400px;\" id=\"mv_stats_graph\" class=\"c1\"></div>";
?>
<script>
	loadChartStats();
</script>
</div>  
</div><!-- end of #content -->
       
<?php get_footer(); ?>
