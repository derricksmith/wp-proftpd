<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://derrick-smith.com
 * @since      1.0.0
 *
 * @package    WP ProFTPd
 * @subpackage WP ProFTPd/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    WP ProFTPd
 * @subpackage WP ProFTPd/admin
 * @author     Derrick Smith <derricksmith01@msn.com>
 */
class Wp_Proftpd_Admin_Logs {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}
	
	public function datatables_load_callback() {
		global $wpdb;
		
		$request= $_GET;
		$nonce = $request['proftpd_nonce'];
		if (!wp_verify_nonce( $nonce, "proftpd_logs" ))
			die( __( 'Security check', 'wp-proftpd' ) ); 
		
		
		header("Content-Type: application/json");

		$columns = array(
			0 => 'logdatetime',
			1 => 'ip',
			2 => 'username',
			3 => 'operation'
		);

		/* Array of database columns which should be read and sent back to DataTables. Use a space where
		 * you want to insert a non-database field (for example a counter or static image)
		 */
		$aColumns = array( 'logdatetime', 'ip', 'username', 'operation' );
		
		/* Indexed column (used for fast and accurate table cardinality) */
		$sIndexColumn = "logdatetime";
		
		/* DB table to use */
		$sTable = "wp_proftpd_logs";
		
		/* 
		 * Paging
		 */
		$sLimit = "";
		if ( isset( $request['start'] ) && $request['length'] != '-1' )
		{
			$sLimit = "LIMIT ".sanitize_text_field( $request['start'] ).", ".
				sanitize_text_field( $request['length'] );
		}
		
		
		/*
		 * Ordering
		 */
		$sOrder = "";
		if ( isset( $request['order'] ) )
		{
			$sOrder = "ORDER BY  ";
			for ( $i=0 ; $i<count( $request['order'] ) ; $i++ )
			{
				$sOrder .= $aColumns[$request['order'][$i]['column']]."
					".sanitize_text_field( $request['order'][$i]['dir'] ) .", ";
				
			}
			
			$sOrder = substr_replace( $sOrder, "", -2 );
			if ( $sOrder == "ORDER BY" )
			{
				$sOrder = "";
			}
		}
		
		
		/* 
		 * Filtering
		 * NOTE this does not match the built-in DataTables filtering which does it
		 * word by word on any field. It's possible to do here, but concerned about efficiency
		 * on very large tables, and MySQL's regex functionality is very limited
		 */
		$sWhere = "";
		if ( isset($request['search']['value']) && $request['search']['value'] != "" )
		{
			$sWhere = "WHERE (";
			for ( $i=0 ; $i<count($aColumns) ; $i++ )
			{
				$sWhere .= $aColumns[$i]." LIKE '%".sanitize_text_field( $request['search']['value'] )."%' OR ";
			}
			$sWhere = substr_replace( $sWhere, "", -3 );
			$sWhere .= ')';
		}
		
		/* Individual column filtering */
		//for ( $i=0 ; $i<count($aColumns) ; $i++ )
		//{
		//	if ( isset($request['bSearchable_'.$i]) && $request['bSearchable_'.$i] == "true" && isset($request['sSearch_'.$i]) && $request['sSearch_'.$i] != '' )
		//	{
		//		if ( $sWhere == "" )
		//		{
		//			$sWhere = "WHERE ";
		//		}
		//		else
		//		{
		//			$sWhere .= " AND ";
		//		}
		//		$sWhere .= $aColumns[$i]." LIKE '%".sanitize_text_field($request['sSearch_'.$i])."%' ";
		//	}
		//}
		
		
		/*
		 * SQL queries
		 * Get data to display
		 */
		$sQuery = "
			SELECT SQL_CALC_FOUND_ROWS ".str_replace(" , ", " ", implode(", ", $aColumns))."
			FROM   $sTable
			$sWhere
			$sOrder
			$sLimit
		";
		$rResult = $wpdb->get_results($sQuery);
		
		/* Data set length after filtering */
		$sQuery = "
			SELECT FOUND_ROWS()
		";
		
		$iFilteredTotal = $wpdb->get_var($sQuery);
		
		/* Total data set length */
		$sQuery = "
			SELECT COUNT(".$sIndexColumn.")
			FROM   $sTable
		";
		
		$iTotal = $wpdb->get_var($sQuery);
		
		
		/*
		 * Output
		 */
		$output = array(
			"draw" => intval($request['draw']),
			"recordsTotal" => intval($iTotal),
			"recordsFiltered" => intval($iFilteredTotal),
			"data" => array()
		);
		
		foreach ($rResult as $aRow){
			$row = array();
			$row[] = $aRow->logdatetime;
			$row[] = $aRow->ip;
			$row[] = $aRow->username;
			$row[] = $aRow->operation;
			$output['data'][] = $row;
		}
		
		echo json_encode( $output );
		wp_die();

	}
	
	public function datatables_clear_callback() {
		global $wpdb;
		$request= $_GET;
		$nonce = $request['proftpd_nonce'];
		if (!wp_verify_nonce( $nonce, "proftpd_logs_clear" ))
			die( __( 'Security check', 'wp-proftpd' ) ); 
		
		$table  = $wpdb->prefix . 'proftpd_logs';
		$delete = $wpdb->query("TRUNCATE TABLE $table");
		wp_send_json_success(true);
	}
}