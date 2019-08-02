<?php
/* Adds a small amount of sample data to the UPCP database for demonstration purposes */
function UPCP_Output_Welcome_Screen() {
	include UPCP_CD_PLUGIN_PATH . 'html/WelcomeScreen.php';
}

function UPCP_Initial_Install_Screen() {
	add_dashboard_page(
			esc_html__( 'Ultimate Product Catalog - Welcome!', 'ultimate-product-catalogue' ),
			esc_html__( 'Ultimate Product Catalog - Welcome!', 'ultimate-product-catalogue' ),
			'manage_options',
			'upcp-getting-started',
			'UPCP_Output_Welcome_Screen'
		);
}

function UPCP_Remove_Install_Screen_Admin_Menu() {
	remove_submenu_page( 'index.php', 'upcp-getting-started' );
}

function UPCP_Welcome_Screen_Redirect() {
	global $wpdb;
	global $catalogues_table_name;
	global $categories_table_name;

	if ( ! get_transient( 'upcp-getting-started' ) ) {
		return;
	}
	
	delete_transient( 'upcp-getting-started' );

	if ( is_network_admin() || isset( $_GET['activate-multi'] ) ) {
		return;
	}

	$Catalogues = $wpdb->get_results("SELECT Catalogue_ID FROM $catalogues_table_name");
	$Catalogue_Count = $wpdb->num_rows;
	$Categories = $wpdb->get_results("SELECT Category_ID FROM $categories_table_name");
	$Category_Count = $wpdb->num_rows;
	if ($Catalogue_Count or $Category_Count) {
		set_transient('upcp-admin-install-notice', true, 5);
		return;
	}

	wp_safe_redirect( admin_url( 'index.php?page=upcp-getting-started' ) );
	exit;
}

add_action( 'admin_menu', 'UPCP_Initial_Install_Screen' );
add_action( 'admin_head', 'UPCP_Remove_Install_Screen_Admin_Menu' );
add_action( 'admin_init', 'UPCP_Welcome_Screen_Redirect', 9999 );
?>