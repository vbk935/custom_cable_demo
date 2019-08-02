<?php if (is_page(array (9,13,15,124) ) ) { ?>

<?php /*?><br /><br /><br /><br /><br /><br /><br />

<h2>Button Group</h2>
	<div class="row page-submenu">
		<div class="col-xs-12 clearfix">
		<?php if(is_page(9)) {$who_disabled = 'disabled="disabled"';} ?>
		<?php if(is_page(13)) {$culture_disabled = 'disabled="disabled"';} ?>
		<?php if(is_page(15)) {$history_disabled = 'disabled="disabled"';} ?>
		<?php if(is_page(124)) {$team_disabled = 'disabled="disabled"';} ?>
			<div class="btn-group"> 
				<a href="http://208.109.106.56/~tyrexmfg/tyr/about-us/who-we-are/" class="btn btn-default" <? echo $who_disabled; ?>>Who We Are</a> 
				<a href="http://208.109.106.56/~tyrexmfg/tyr/about-us/history/" class="btn btn-default" <? echo $culture_disabled; ?>>Our Culture</a> 
				<a href="http://208.109.106.56/~tyrexmfg/tyr/about-us/culture/" class="btn btn-default" <? echo $history_disabled; ?>>Our History</a> 
				<a href="http://208.109.106.56/~tyrexmfg/tyr/about-us/team/" class="btn btn-default" <? echo $team_disabled; ?>>Our Team</a> 
			</div>
		</div>
	</div>



    
    
<h2>Tabs</h2> <?php */?>   
<ul class="nav nav-tabs" role="tablist" style="margin:40px 0">
  <li class="active"><a href="#">Who We Are</a></li>
  <li><a href="#">Our Culture</a></li>
  <li><a href="#">Our History</a></li>
  <li><a href="#">Our Team</a></li>
</ul>  


<?php /*?> 		<?php
			include_once (STYLESHEETPATH . '/library/bootstrap-wp-navwalker.php');
			wp_nav_menu( array(
				'menu'              => 'main-menu',
				'theme_location'    => 'main-menu',
				'menu_class'        => 'nav nav-tabs',
				//'fallback_cb'       => 'wp_bootstrap_navwalker::fallback',
				'walker'            => new wp_bootstrap_navwalker())
			);
		?>
<?php */?>

    
<?php } ?>