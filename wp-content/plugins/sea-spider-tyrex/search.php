<?
//*****************************************************************************
//
//				Search
//
//*****************************************************************************
function seaspider_search_form( $form ) {
$form = '
<form id="searchform" role="search" method="get" class="navbar-form navbar-right" action="' . home_url( '/' ) . '" >
	<div class="input-group">
		<input type="text" value="' . get_search_query() . '" name="s" id="s" class="form-control" />
		<span class="input-group-btn">
			<button class="btn btn-default btn-search" type="submit">Search</button>
		</span>
	</div>
</form>';

return $form;
}

add_filter( 'get_search_form', 'seaspider_search_form' );



//*****************************************************************************
//					DEPLOYMENT
//
//					get_search_form('SS_search_form'); 				
//
//*****************************************************************************





//*****************************************************************************
//
//		http://www.evagoras.com/2012/11/01/how-to-handle-and-customize-an-empty-search-query-in-wordpress/
//
//*****************************************************************************
function seaSearchFilter($query) {
    // If 's' request variable is set but empty
    if (isset($_GET['s']) && empty($_GET['s']) && $query->is_main_query()){
        $query->is_search = true;
        $query->is_home = false;
    }
    return $query;
}
add_filter('pre_get_posts','seaSearchFilter');

?>