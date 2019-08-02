<?php
if ( ! defined( 'ABSPATH' ) ) { 
    exit; // Exit if accessed directly
}

if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
 
class wooCustomers_List_Table extends WP_List_Table {
 
    var $woocustomer_data = array();
    function __construct(){
    global $status, $page;
 
        parent::__construct( array(
				'singular'  => __( 'woocustomer', 'woocustomers_list_table' ),     //singular name of the listed records
				'plural'    => __( 'woocustomers', 'woocustomers_list_table' ),   //plural name of the listed records
				'ajax'      => false        //does this table support ajax?
	 
		) );
 
    add_action( 'admin_head', array( &$this, 'admin_header' ) );            
 
    }
 
  function admin_header() {
    $page = ( isset($_GET['woocustomers'] ) ) ? esc_attr( $_GET['woocustomers'] ) : false;
    if( 'woocustomers' != $page )
    return;
    echo '<style type="text/css">
		.wp-list-table .column-customer { width: 20%; }
	    .wp-list-table .column-account_type { width: 20%; }
	    .wp-list-table .column-total_purchase { width: 20%; }
	    .wp-list-table .column-total_spent { width: 20%;}
	    .wp-list-table .column-last_date { width: 20%;}
	    </style>';
  }
 
  function no_items() {
    _e( 'No Customer Data found.' );
  }
 
  function column_default( $item, $column_name ) {
	$symbol = get_woocommerce_currency_symbol();
	$user = get_user_by( "email", $item["email"] );
	if(@$user->roles){
		$role = implode(",",$user->roles);	
	}
    switch( $column_name ) { 
		case 'customer':
			return '<div class="details-container clearfix">
            <a href="admin.php?page=woocustomers&cust_id='.$item["id"].'" class="gravatar">'.get_avatar($item["email"]).'</a>        
        <div class="customer-details clearfix">
          <div class="customer-name">
			  <a href="admin.php?page=woocustomers&cust_id='.$item["id"].'">'.$item["first_name"].' '.$item["last_name"].'</a>
			  </div>
          <div class="customer-email">'.$item["email"].'</div>
        </div>
      </div>';

		case 'last_date':
			return date("d M Y", strtotime($item[ $column_name ]));
		case 'total_spent':
			return $symbol."".$item[ "total_spent" ];
		case 'account_type':
			return (@$user->roles)? $role:"Guest";
        default:
            return $item[ $column_name ]; //Show the whole array for troubleshooting purposes
    }
  }
	 
	function get_sortable_columns() {
	  $sortable_columns = array(
'customer' => array('first_name',false),
		'account_type' => array('account_type',false),
		'total_purchases' => array('total_purchases',false),
		'total_spent' => array('total_spent',false),
		'last_date'   => array('last_date',false)
	  );
	  return $sortable_columns;
	}
	 
	function get_columns(){
			$columns = array(
				'cb'        => '',
				'customer'        => __( 'Customer', 'woocustomers_list_table' ),
				'account_type'    => __( 'Account Type', 'woocustomers_list_table' ),
				'total_purchases' => __( 'Total Purchases', 'woocustomers_list_table' ),
				'total_spent'    => __( 'Total Spent', 'woocustomers_list_table' ),
				'last_date'      => __( 'Last Order', 'woocustomers_list_table' )
			);
			 return $columns;
		}
	 
	function usort_reorder( $a, $b ) {
	  // If no sort, default to title
	  $orderby = ( ! empty( $_GET['orderby'] ) ) ? $_GET['orderby'] : 'woocustomer';
	  // If no order, default to asc
	  $order = ( ! empty($_GET['order'] ) ) ? $_GET['order'] : 'asc';
	  // Determine sort order
		if(is_numeric(@$a[$orderby])){
			$result = ($a[$orderby]-$b[$orderby]) ? ($a[$orderby]-$b[$orderby])/abs($a[$orderby]-$b[$orderby]) : 0;
		}else{
			$result = strcmp( @$a[$orderby], @$b[$orderby] );
		}
		// Send final sort direction to usort
		 return ( $order === 'asc' ) ? $result : -$result;
	}
	 
     
    
	function get_bulk_actions() {
	  $actions = array(
		'delete'    => 'Delete'
	  );
	  return $actions;
	}
	 
	function column_cb($item) {
			return "";    
		}
	 
	function prepare_items() {
		global $wpdb;
	  $columns  = $this->get_columns();
	  $hidden   = array();
	  $sortable = $this->get_sortable_columns();
	  $where = "";
	  
	  if(isset($_POST["search_customers"])){
			if($_POST["search"]){
				$search = addslashes($_POST["search"]);
				$where .= " AND first_name = '".$search."' || last_name='".$search."' || CONCAT( first_name,  ' ', last_name )='".$search."' || email = '".$search."'";
			}	
	  }
	  
	  //paging
      
      $postsperpage = $_POST['wp_screen_options']['value'];
      if($postsperpage == "" || $postsperpage == 0){
        $postsperpage = 25;
      }
	  $per_page = $postsperpage;
	  $current_page = $this->get_pagenum() - 1;
	  $limit = " Limit ".($current_page * $per_page).",".$per_page;
		"select * from ".WOOCUSTOMERS_TABLE." WHERE email IS NOT NULL".$where.$limit;	  
	  
	  $this->woocustomer_data = $wpdb->get_results("select * from ".WOOCUSTOMERS_TABLE." WHERE email IS NOT NULL".$where, ARRAY_A);
	  $this->_column_headers = array( $columns, $hidden, $sortable );
	  usort( $this->woocustomer_data, array( &$this, 'usort_reorder' ) );
	  $total_items = count($this->woocustomer_data);
		$this->set_pagination_args( array(
			'total_items' => $total_items,
			 'per_page'    => $per_page
		) );

	  // only ncessary because we have sample data
	$view_page = 0;
	if($current_page > 1 ){
		$view_page = ( $current_page-1 )* $per_page;
	}
	
//print_r($this->woocustomer_data);
	$res = array_slice ($this->woocustomer_data, $view_page, $per_page);
	$this->found_data = $res;
	  if(($_GET["orderby"] == "total_spent") || ($_GET["orderby"] == "total_purchases")){
		$this->items = array_reverse($res);
	  }else{
		$this->items = $res;
	  }
	}
	 
} //class
 
 
 
 
 
 
