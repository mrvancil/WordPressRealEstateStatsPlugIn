<?php

if( !class_exists(' RealEstateStatsDb') ) :

class RealEstateStatsDb
{
	var $tableSalesStatsRegion;
	var $tableSalesStatsArea;
	var $tableSalesStatsPropertyType;
	var $tableSalesStatsStatType;
	var $tableSalesStatSalesStatsEntry;

	function RealEstateStatsDb()
	{
		global $wpdb;
		
		$this->tableSalesStatsRegion = $wpdb->prefix . 'SalesStatsRegion';
		$this->tableSalesStatsArea = $wpdb->prefix . 'SalesStatsArea';
		$this->tableSalesStatsPropertyType = $wpdb->prefix . 'SalesStatsPropertyType';
		$this->tableSalesStatsStatType = $wpdb->prefix . 'SalesStatsStatType';
		$this->tableSalesStatsEntry = $wpdb->prefix . 'SalesStatsEntry';
	}

	function GetDistinctYears()
	{
		global $wpdb;
		
		$sql = "SELECT DISTINCT EntryYear from " . $this->tableSalesStatsEntry . ' ORDER BY EntryYear Desc' ;
		$results = $wpdb->get_results($sql);
		return $results;
	}

	function GetAreaData()
	{
		global $wpdb;
		
		$sql = "SELECT * FROM " . $this->tableSalesStatsArea . ' ORDER BY Area' ;
		$results = $wpdb->get_results($sql);
		return $results;
	}
	function GetArea($areaId)
	{
		global $wpdb;
		
		$sql = "SELECT Area FROM " . $this->tableSalesStatsArea . " where ID=" . $areaId ;
		$areaName = $wpdb->get_var($sql); 
		return $areaName;
	}
	function GetPropertyData()
	{
		global $wpdb;
		
		$sql = "SELECT * FROM " . $this->tableSalesStatsPropertyType ;
		$results = $wpdb->get_results($sql);
		return $results;
	}
	function GetStatsData()
	{
		global $wpdb;
		
		$sql = "SELECT * FROM " . $this->tableSalesStatsStatType;
		$results = $wpdb->get_results($sql);
		return $results;
	}
	
	function GetStatsDataByYearAndArea($Year, $AreaId)
	{
		global $wpdb;
		
		$sql = 'SELECT * from ' . $this->tableSalesStatsEntry . ' where EntryYear=' . $Year . ' AND AreaID=' . $AreaId ;
		$results = $wpdb->get_results($sql);
		return $results;
	}
	
	function DeleteStatData($Year, $AreaId)
	{
		global $wpdb;
		$sql = 'DELETE FROM ' . $this->tableSalesStatsEntry . ' where EntryYear=' . $Year . ' AND AreaID=' . $AreaId ;
		$results = $wpdb->get_results($sql);
	}

	function SaveStatData($AreaId, $PropertyTypeId, $StatTypeId, $EntryYear, $EntryValue)
	{
		global $wpdb;
		$sql = 'INSERT INTO ' . $this->tableSalesStatsEntry . '(AreaID,PropertyTypeID,StatTypeID,EntryYear,EntryValue) values ('. $AreaId .',' . $PropertyTypeId .',' . $StatTypeId .',' . $EntryYear . ','. $EntryValue .')' ;
		$results = $wpdb->get_results($sql);
	}

	function GetChartData($areaid, $propertytypeid, $stattypeid)
	{
		global $wpdb;
		$sql = 'SELECT EntryYear, EntryValue FROM ' . $this->tableSalesStatsEntry . ' where AreaId=' . $areaid . ' AND PropertyTypeId=' . $propertytypeid . ' AND StatTypeID=' . $stattypeid . ' ORDER BY EntryYear Asc' ;
		$results = $wpdb->get_results($sql);
		return $results;
	}
}
endif;
?>