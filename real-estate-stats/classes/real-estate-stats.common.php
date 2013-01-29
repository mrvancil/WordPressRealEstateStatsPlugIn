<?php

require_once('real-estate-stats.db.php');

if( !class_exists(' RealEstateStatsCommon') ) :

class ResidentialUnitSale 
{
   	public $year; 
	public $total; 
}

class RealEstateStatsCommon
{

	function RealEstateStatsCommon()
	{
		global $wpdb;
	}

	function WriteYearSelection($defaultSelection)
	{
		$realEstateStatsDb = new RealEstateStatsDb();
		$results = $realEstateStatsDb->GetDistinctYears();
		
		echo "<select class='mv-stat-select' name='mv_stat_frm_year' id='mv_stat_frm_year'>";
		
		//echo "<div>";
		foreach($results as $result)
		{
			$this->createYearOption($result->EntryYear,$defaultSelection);
		}	
		//echo "</div>";
		echo "</select>";
	}

	function createYearOption($year,$makeSelected)
	{
		//echo "<a class=\"mv-stat-year-tab\">" . $year . "</a>&nbsp;";
		$selected = "";
		if($makeSelected == $year) {
			$selected = "selected=\"checked\"";
		}
		echo "<option value='" . $year . "' " . $selected . ">" . $year . "</option>";
	}

	function WriteYearSelect($selectYear)
	{
		echo "<select class='mv-stat-select' name='mv_stat_frm_year' id='mv_stat_frm_year'>";
		$currentYear = date("Y"); 
		$i=$currentYear + 1;
		while ($i > 1997) 
		{
			$selected = "";
			if($currentYear == $i) {
				$selected = "selected=\"checked\"";
			}

			if($selectYear==$i){
				$selected = "selected=\"checked\"";
			}

			echo "<option value='" . $i . "' " . $selected . ">" . $i . "</option>";
			
			$i--;
		}
		echo "</select>";
	}
	function WriteAreaSelect($selectArea)
	{
		$realEstateStatsDb = new RealEstateStatsDb();
		$results = $realEstateStatsDb->GetAreaData();

		echo "<select class='mv-stat-select' name='mv_stat_frm_area' id='mv_stat_frm_area'>";
		foreach($results as $result)
		{
			$selected = "";
			if($selectArea==$result->ID){
				$selected = "selected=\"checked\"";
			}

			echo "<option value='" . $result->ID . "' " . $selected . ">" . $result->Area . "</option>"; 
		}
		echo "</select>";
	}
	function WritePropertySelect()
	{
		$realEstateStatsDb = new RealEstateStatsDb();
		$results = $realEstateStatsDb->GetPropertyData();

		echo "<select class='mv-stat-select' name='mv_stat_frm_property' id='mv_stat_frm_property'>";
		foreach($results as $result)
		{
			echo "<option value='" . $result->ID . "'>" . $result->PropertyType . "</option>"; 
		}
		echo "</select>";
	}
	function WriteStatSelect()
	{
		$realEstateStatsDb = new RealEstateStatsDb();
		$results = $realEstateStatsDb->GetStatsData();

		echo "<select class='mv-stat-select' name='mv_stat_frm_stat' id='mv_stat_frm_stat'>";
		foreach($results as $result)
		{
			echo "<option value='" . $result->ID . "'>" . $result->StatType . "</option>"; 
		}
		echo "</select>";
	}

	function DeleteStatData($year,$areaid)
	{
		$realEstateStatsDb = new RealEstateStatsDb();
		$realEstateStatsDb->DeleteStatData($year,$areaid);
	}

	function SaveStatData($areaid, $propertytypeid, $i, $year, $formfield)
	{
		$realEstateStatsDb = new RealEstateStatsDb();
		$results = $realEstateStatsDb->SaveStatData($areaid, $propertytypeid, $i, $year, $formfield);
	}


	function GetRealEstateStatsEntryForm($year, $areaId)
	{
		global $wpdb;
		global $realEstateStatsDb;
		$realEstateStatsDb = new RealEstateStatsDb();
		
		$areaName = $realEstateStatsDb->GetArea($areaId);

		$d = "<h2>Edit Stats for " . $areaName . ' /  ' . $year . "</h2>";
		$d = $d . "<table class=\"stats-edit-table\">";
		$d = $d . "<tr><td>&nbsp;</td><td>Number Sold</td><td>Sales Volume</td><td>Average Price</td><td>Median Price</td><td>Percent Diff</td></tr>";
		
		
		$results = $realEstateStatsDb->GetPropertyData();
		$stats = $realEstateStatsDb->GetStatsDataByYearAndArea($year,$areaId);
	
		foreach($results as $result){
			$row = $this->createInputRow($result->ID,$result->PropertyType,$stats);
			$d = $d . $row;
		}
 
		$d = $d . "</table>";
		$d = $d . "<h2>" . $areaName . ' / ' . $year . "</h2>";
		return $d;
	}

	function createInputRow($propertytypeid, $propertyname,$stats)
	{
		$ir = '';

		$number_of_stats = 5;
		$ir = $ir . "<tr><td>" . $propertyname . "</td>";
		for ($i = 1; $i <= $number_of_stats; $i++) {
			$ir = $ir . $this->createInput($propertytypeid,$i,$stats);
		}
		$ir = $ir . "</tr>";

		return $ir;
	}

	function createInput($propertytypeid, $stattypeid, $stats)
	{
		$name_of_input = "stat_" . $propertytypeid . "_" . $stattypeid;
		$value_of_input = "0";
		
		mysql_data_seek ($stats, 0);
		foreach($stats as $stat){
			if(($stat->PropertyTypeID==$propertytypeid) && ($stat->StatTypeID==$stattypeid))
			{
				//if($stattypeid==5)
				//{
				//	$value_of_input = "*calculated";
				//}
				//else
				//{
					$value_of_input = $stat->EntryValue;
				//}
			}
		}
		
		$val = "<td><input type='text' value='" . $value_of_input . "' name='" . $name_of_input . "'/></td>";
		return $val;
	}
	function GetRealEstateStatsGrid($year,$areaId)
	{
		$realEstateStatsDb = new RealEstateStatsDb();
		$areaName = $realEstateStatsDb->GetArea($areaId);

		$d = "<br><h4>" . $areaName . ' /  ' . $year . "</h4>";
		$d = $d. "<table border='1'>";
		$d = $d . "<tr><td>" . $year . "</td><td>Number Sold</td><td>Sales Volume</td><td>Average Price</td><td>Median Price</td><td>Percent Diff</td></tr>";
		
		$results = $realEstateStatsDb->GetPropertyData();
		$stats = $realEstateStatsDb->GetStatsDataByYearAndArea($year,$areaId);

		foreach($results as $result){
			$row = $this->createDataRow($result->ID,$result->PropertyType,$stats);
			$d = $d . $row;
		}
 
		$d = $d . "</table>";
		return $d;
	}

	function createDataRow($propertytypeid, $propertyname, $stats)
	{
		$number_of_stats = 5;
		$dr = "<tr><td>" . $propertyname . "</td>";
		for ($i = 1; $i <= $number_of_stats; $i++) {
			$dr = $dr . $this->createValueCell($propertytypeid, $i,$stats);
		}
		$dr = $dr. "</tr>";
		return $dr;
	}
	function createValueCell($propertytypeid, $stattypeid, $stats)
	{
 		mysql_data_seek ($stats, 0);
		foreach($stats as $stat){
			if(($stat->PropertyTypeID==$propertytypeid) && ($stat->StatTypeID==$stattypeid))
			{
				$formatedValue = $this->formatForUi($stattypeid, $stat->EntryValue);
			}
		}

		$val = "<td class=\"mv-data-stat\">" . $formatedValue . "</td>";
		return $val;
	}

	function formatForUi($stattypeid, $value)
	{
		$returnValue = '';
		switch ($stattypeid) {
		    case 5:
        		$returnValue = $value . "%";
        		break;
    		case 4:
    		case 3:
    		case 2:
    			setlocale(LC_MONETARY, 'en_US');
		        //$returnValue = money_format('%i',$value);
		        $returnValue = "$" . number_format($value);
		        break;
		    default:
		    	$returnValue = number_format($value);
		}

		return $returnValue;
	}

	function GetChartData($areaid, $propertytypeid, $stattypeid)
	{
		$sales = array();
		$realEstateStatsDb = new RealEstateStatsDb();
		$results = $realEstateStatsDb->GetChartData($areaid,$propertytypeid,$stattypeid);

		foreach($results as $result){
			$temp=new ResidentialUnitSale();
			$temp->year = $result->EntryYear; //mysql_result($result,$i,"EntryYear");
			$temp->total = $result->EntryValue; //mysql_result($result,$i,"EntryValue");
			$sales[] = $temp;
		}
		
		return $sales;
	}
}

endif;

?>