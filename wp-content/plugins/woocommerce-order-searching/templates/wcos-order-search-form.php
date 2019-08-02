<form role="search" method="get" id="order_search_form" action="" >
	<?php 
	if(isset($_GET)){
		foreach($_GET as $key=>$value){
			?><input type="hidden" name="<?php echo $key; ?>" value="<?php echo $value ?>" /><?php
		}
	}
	?>
    <input type="hidden" name="wcos_search_type" value="shop_order" />
    <div>
        <label class="screen-reader-text" for="search_value"><?php echo __('Search for:') ?></label>
        <input type="text"  size="50"value="<?php echo isset($_REQUEST['search_value'] ) ? $_REQUEST['search_value'] : '' ?>" name="search_value" id="search_value" />
        <select name="search_key" >
            <?php if(is_array($order_search_keys)){
            foreach($order_search_keys as $key=>$value){ 
                $selected = selected($key,$_REQUEST['search_key'],false);
                printf('<option %3$s value="%1$s">%2$s</option>',$key,$value,$selected);
            }
            }?>
        </select>
        <input type="submit" id="order_search_submit" value="<?php echo esc_attr__('Search') ?>" />
    </div>
</form>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type="text/javascript">
/*jQuery(document).ready(function(){
  jQuery('.order-search-list').hide();
  jQuery('#order_search_submit').click(function(){
    jQuery('.order-search-list').show();
    return false;
    if(jQuery('#search_value').val() == ''){
    alert('Input can not be left blank');
      return false;
    }else{
     jQuery('.order-search-list').show();
     return false;
    }
  });
});*/
</script>