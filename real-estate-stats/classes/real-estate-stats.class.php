<?php
if( !class_exists('RealEstateStats') ) :

class RealEstateStats
{
	var $widgetSettingsKey;
	var $currentVersion;
	var $pluginPath;
	var $tableSalesStatsRegion;
	var $tableSalesStatsArea;
	var $tableSalesStatsPropertyType;
	var $tableSalesStatsStatType;
	var $tableSalesStatSalesStatsEntry;

	//adding styles here to reduce calls for files.
	function mvHeaderContent()
	{
		echo "<style type='text/css'>	
			.hide-me{display: none;}
			.show-me { display:block;}
			.stats-edit-table{ margin: 5px;	border: 1px;}
			.mv-stat-select{ width:150px;} 
			#mv_stats_spinner_container {display:table-cell; vertical-align:middle; text-align:center;height:400px;width:600px;}
			</style>";
	}

	function RealEstateStats()
	{
		global $wpdb;
		
		if( !function_exists('get_option') )
		{
			require_once('../../../wp-config.php');
		}

		$this->widgetSettingsKey = "widgetRealEstateStats";		
		$this->currentVersion = '0.0.1';
		$this->pluginPath = get_option('siteurl') . '/wp-content/plugins/real-estate-stats/';
		$this->tableSalesStatsRegion = $wpdb->prefix . 'SalesStatsRegion';
		$this->tableSalesStatsArea = $wpdb->prefix . 'SalesStatsArea';
		$this->tableSalesStatsPropertyType = $wpdb->prefix . 'SalesStatsPropertyType';
		$this->tableSalesStatsStatType = $wpdb->prefix . 'SalesStatsStatType';
		$this->tableSalesStatsEntry = $wpdb->prefix . 'SalesStatsEntry';

		$options = get_option($this->widgetSettingsKey);
		$options['version'] = $this->currentVersion;
		update_option($this->widgetSettingsKey, $options);
	}

	function createDatabaseTable()
	{
		global $wpdb;
		require_once(ABSPATH . 'wp-admin/upgrade-functions.php');

		$sql = "CREATE TABLE $this->tableSalesStatsRegion (
  			ID int NOT NULL PRIMARY KEY,
  			Region  char(100) NOT NULL
		);";
		dbDelta($sql);

		$rows_affected = $wpdb->insert( $this->tableSalesStatsRegion, array( 'ID' => '1', 'Region' => 'Region One' ) );
		$rows_affected = $wpdb->insert( $this->tableSalesStatsRegion, array( 'ID' => '2', 'Region' => 'Region Two' ) );

		$sql = "CREATE TABLE $this->tableSalesStatsArea (
  			ID int NOT NULL PRIMARY KEY,
  			Area  char(100) NOT NULL,
  			RegionID int not null,
  			Foreign Key (RegionID) References $this->tableSalesStatsRegion(ID)
		);";
		dbDelta($sql);

		$rows_affected = $wpdb->insert( $this->tableSalesStatsArea, array( 'ID' => '1', 'Area' => 'Boulder', 'RegionID' => '1' ) );
		$rows_affected = $wpdb->insert( $this->tableSalesStatsArea, array( 'ID' => '2', 'Area' => 'Layfayette', 'RegionID' => '1' ) );
		$rows_affected = $wpdb->insert( $this->tableSalesStatsArea, array( 'ID' => '3', 'Area' => 'Louisville', 'RegionID' => '1' ) );
		$rows_affected = $wpdb->insert( $this->tableSalesStatsArea, array( 'ID' => '4', 'Area' => 'Superior', 'RegionID' => '1' ) );
		$rows_affected = $wpdb->insert( $this->tableSalesStatsArea, array( 'ID' => '5', 'Area' => 'Longmont', 'RegionID' => '1' ) );
		$rows_affected = $wpdb->insert( $this->tableSalesStatsArea, array( 'ID' => '6', 'Area' => 'Broomfield', 'RegionID' => '1' ) );
		$rows_affected = $wpdb->insert( $this->tableSalesStatsArea, array( 'ID' => '7', 'Area' => 'Erie', 'RegionID' => '1' ) );
		$rows_affected = $wpdb->insert( $this->tableSalesStatsArea, array( 'ID' => '8', 'Area' => 'Denver Metro', 'RegionID' => '1' ) );

		//PropertyType Table and seeded values	
		$sql = "CREATE TABLE $this->tableSalesStatsPropertyType (
  			ID int NOT NULL PRIMARY KEY,
  			PropertyType char(100) NOT NULL
		);";
		dbDelta($sql);		

		$rows_affected = $wpdb->insert( $this->tableSalesStatsPropertyType, array( 'ID' => '1', 'PropertyType' => 'Residential' ) );
		$rows_affected = $wpdb->insert( $this->tableSalesStatsPropertyType, array( 'ID' => '2', 'PropertyType' => 'Attached' ) );
		$rows_affected = $wpdb->insert( $this->tableSalesStatsPropertyType, array( 'ID' => '3', 'PropertyType' => 'Income' ) );
		$rows_affected = $wpdb->insert( $this->tableSalesStatsPropertyType, array( 'ID' => '4', 'PropertyType' => 'Lease' ) );
		$rows_affected = $wpdb->insert( $this->tableSalesStatsPropertyType, array( 'ID' => '5', 'PropertyType' => 'Commercial' ) );
		$rows_affected = $wpdb->insert( $this->tableSalesStatsPropertyType, array( 'ID' => '6', 'PropertyType' => 'Vacant Land' ) );
		$rows_affected = $wpdb->insert( $this->tableSalesStatsPropertyType, array( 'ID' => '7', 'PropertyType' => 'Farms' ) );

		//Stat Type Table and seeded values
		$sql = "CREATE TABLE $this->tableSalesStatsStatType (
  			ID int NOT NULL PRIMARY KEY,
  			StatType char(100) NOT NULL,
  			StatTypeHeader char(100) NOT NULL
  		);";
		dbDelta($sql);

		$rows_affected = $wpdb->insert( $this->tableSalesStatsStatType, array( 'ID' => '1', 'StatType' => 'NumberSold', 'StatTypeHeader' => 'Sold' ) );
		$rows_affected = $wpdb->insert( $this->tableSalesStatsStatType, array( 'ID' => '2', 'StatType' => 'SalesVolume', 'StatTypeHeader' => 'Sales Volume' ) );
		$rows_affected = $wpdb->insert( $this->tableSalesStatsStatType, array( 'ID' => '3', 'StatType' => 'AveragePrice', 'StatTypeHeader' => 'Average Price' ) );
		$rows_affected = $wpdb->insert( $this->tableSalesStatsStatType, array( 'ID' => '4', 'StatType' => 'MedianPrice', 'StatTypeHeader' => 'Median Price' ) );
		$rows_affected = $wpdb->insert( $this->tableSalesStatsStatType, array( 'ID' => '5', 'StatType' => 'PercentDifferenceMedian', 'StatTypeHeader' => 'Difference Over Previous Year (Median Price)' ) );
	
		//Stat EntryTable and seeded values
		$sql = "CREATE TABLE $this->tableSalesStatsEntry (
  			ID int not null AUTO_INCREMENT PRIMARY KEY,
			AreaID int not null,
			PropertyTypeID int not null,
			StatTypeID int not null,
			EntryYear int not null,
			EntryValue double not null,
			FOREIGN KEY (AreaID) REFERENCES $this->tableSalesStatsArea(ID),
			FOREIGN KEY (StatTypeID) REFERENCES $this->tableSalesStatsStatType(ID),
			FOREIGN KEY (PropertyTypeID) REFERENCES $this->tableSalesStatsPropertyType(ID)
  		);";
		dbDelta($sql);

	}

	function deleteDatabaseTable()
	{
		if( !function_exists('get_option') )
		{
			require_once('../../../wp-config.php');
		}
		delete_option($this->widgetSettingsKey);
		
		global $wpdb;
		
		$sql = "DROP TABLE IF EXISTS " . $this->tableSalesStatsEntry . ";";	
		$wpdb->query($sql);

		$sql = "DROP TABLE IF EXISTS " . $this->tableSalesStatsStatType . ";";	
		$wpdb->query($sql);

		$sql = "DROP TABLE IF EXISTS " . $this->tableSalesStatsPropertyType . ";";	
		$wpdb->query($sql);

		$sql = "DROP TABLE IF EXISTS " . $this->tableSalesStatsArea . ";";	
		$wpdb->query($sql);

		$sql = "DROP TABLE IF EXISTS " . $this->tableSalesStatsRegion . ";";	
		$wpdb->query($sql);
	}
	function displayMVWidget($args)
	{	
		extract( $args );	
		global $wpdb;
		$sql = "select * from " . $this->tableSalesStatsEntry . " where entryyear = 2011 and propertytypeid = 1 and stattypeid = 1";
		$results = $wpdb->get_results($sql);
		$r = $results[0];

		echo $before_widget . $before_title . "Real Estate Stats" . $after_title;

		echo "<div id=\"mv_statsarea\"><div id=\"mv_statcontainer\">\n";
		echo "Total Units sold in Boulder in 2011 : " . $r->EntryValue . "\n";
		echo "</div></div>\n";
				
		echo $after_widget;
	}
}

endif;
?>