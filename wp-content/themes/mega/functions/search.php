<?
//*****************************************************************************
//
//				Search
//
//*****************************************************************************
function SS_search_form( $form ) {
$form = '
<form role="search" method="get" class="navbar-form " action="' . home_url( '/' ) . '" >
	<div class="input-group">
		<input type="text" value="' . get_search_query() . '" name="s" id="s" class="form-control" />
		<span class="input-group-btn">
			<button class="btn btn-default btn-search" type="submit">Search</button>
		</span>
	</div>
</form>';

return $form;
}

add_filter( 'get_search_form', 'SS_search_form' );


//*****************************************************************************
//					DEPLOYMENT
//
//					get_search_form('SS_search_form'); 				
//
//*****************************************************************************




?>