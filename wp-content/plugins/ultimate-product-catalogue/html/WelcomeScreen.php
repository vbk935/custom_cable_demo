<?php
	$Currency_Symbol = get_option("UPCP_Currency_Symbol");
	$Color = get_option("UPCP_Color_Scheme");
	$Links = get_option("UPCP_Product_Links");
	$Product_Search = get_option("UPCP_Product_Search");
?>
<div class='upcp-welcome-screen'>
	<?php  if (!isset($_GET['exclude'])) { ?>
	<div class='upcp-welcome-screen-header'>
		<h1><?php _e('Welcome to the Ultimate Product Catalog Plugin', 'ultimate-product-catalogue'); ?></h1>
		<p><?php _e('Thanks for choosing the Ultimate Product Catalog! The following will help you get started with the setup of your catalog by creating your first product, category and catalog, as well as adding your catalog to a page and configuring a few key options.', 'ultimate-product-catalogue'); ?></p>
	</div>
	<?php } ?>

	<div class='upcp-welcome-screen-box upcp-welcome-screen-categories upcp-welcome-screen-open' data-screen='categories'>
		<h2><?php _e('1. Create Categories', 'ultimate-product-catalogue'); ?></h2>
		<div class='upcp-welcome-screen-box-content'>
			<p><?php _e('Categories let you organize your products in a way that is easy for you - and your customers - to find.', 'ultimate-product-catalogue'); ?></p>
			<div class='upcp-welcome-screen-created-categories'>
				<div class='upcp-welcome-screen-add-category-name upcp-welcome-screen-box-content-divs'><label><?php _e('Category Name:', 'ultimate-product-catalogue'); ?></label><input type='text' /></div>
				<div class='upcp-welcome-screen-add-category-description upcp-welcome-screen-box-content-divs'><label><?php _e('Category Description:', 'ultimate-product-catalogue'); ?></label><textarea></textarea></div>
				<div class='upcp-welcome-screen-add-category-button'><?php _e('Add Category', 'ultimate-product-catalogue'); ?></div>
				<div class="upcp-welcome-clear"></div>
				<div class="upcp-welcome-screen-show-created-categories">
					<h3><?php _e('Created Categories:', 'ultimate-product-catalogue'); ?></h3>
					<div class="upcp-welcome-screen-show-created-categories-name"><?php _e('Name', 'ultimate-product-catalogue'); ?></div>
					<div class="upcp-welcome-screen-show-created-categories-description"><?php _e('Description', 'ultimate-product-catalogue'); ?></div>
				</div>
			</div>
			<div class='upcp-welcome-screen-next-button upcp-welcome-screen-next-button-not-top-margin' data-nextaction='catalogue'><?php _e('Next Step', 'ultimate-product-catalogue'); ?></div>
			<div class='clear'></div>
		</div>
	</div>
	
<?php  if (!isset($_GET['exclude'])) { ?>
	<div class='upcp-welcome-screen-box upcp-welcome-screen-catalogue' data-screen='catalogue'>
		<h2><?php _e('2. Create a Catalog', 'ultimate-product-catalogue'); ?></h2>
		<div class='upcp-welcome-screen-box-content'>
			<p><?php _e('You can make multiple catalogs, but one catalog with all of your categories is a great place to start.', 'ultimate-product-catalogue'); ?></p>
			<div class='upcp-welcome-screen-catalogue'>
				<div class='upcp-welcome-screen-add-catalogue-name upcp-welcome-screen-box-content-divs'><label><?php _e('Catalog Name:', 'ultimate-product-catalogue'); ?></label><input type='text' /></div>
				<div class='upcp-welcome-screen-add-catalogue-categories'><h3><?php _e('Include Categories:', 'ultimate-product-catalogue'); ?></h3><br /></div>
				<div class='upcp-welcome-screen-add-catalogue-button'><?php _e('Create Catalog', 'ultimate-product-catalogue'); ?></div>
			</div>
			<div class="upcp-welcome-clear"></div>
			<div class='upcp-welcome-screen-next-button' data-nextaction='shop'><?php _e('Next Step', 'ultimate-product-catalogue'); ?></div>
			<div class='upcp-welcome-screen-previous-button' data-previousaction='categories'><?php _e('Previous Step', 'ultimate-product-catalogue'); ?></div>
			<div class='clear'></div>
		</div>
	</div>

	<div class='upcp-welcome-screen-box upcp-welcome-screen-shop' data-screen='shop'>
		<h2><?php _e('3. Add a Shop Page', 'ultimate-product-catalogue'); ?></h2>
		<div class='upcp-welcome-screen-box-content'>
			<p><?php _e('You can create a dedicated shop page below, or skip this step and add your catalog to a page you\'ve already created manually.', 'ultimate-product-catalogue'); ?></p>
			<div class='upcp-welcome-screen-shop'>
				<div class='upcp-welcome-screen-add-shop-name upcp-welcome-screen-box-content-divs'><label><?php _e('Page Title:', 'ultimate-product-catalogue'); ?></label><input type='text' value='Shop' /></div>
				<div class='upcp-welcome-screen-add-shop-button'><?php _e('Create Page', 'ultimate-product-catalogue'); ?></div>
			</div>
			<div class="upcp-welcome-clear"></div>
			<div class='upcp-welcome-screen-next-button' data-nextaction='options'><?php _e('Next Step', 'ultimate-product-catalogue'); ?></div>
			<div class='upcp-welcome-screen-previous-button' data-previousaction='catalogue'><?php _e('Previous Step', 'ultimate-product-catalogue'); ?></div>
			<div class='clear'></div>
		</div>
	</div>

	<div class='upcp-welcome-screen-box upcp-welcome-screen-options' data-screen='options'>
		<h2><?php _e('4. Set Key Options', 'ultimate-product-catalogue'); ?></h2>
		<div class='upcp-welcome-screen-box-content'>
			<p><?php _e('Options can always be changed later, but here are a few that a lot of users want to set for themselves.', 'ultimate-product-catalogue'); ?></p>
			<table class="form-table">
				<tr>
					<th><?php _e('Currency Symbol', 'ultimate-product-catalogue'); ?></th>
					<td>
						<div class='upcp-welcome-screen-option'>
							<fieldset>
								<legend class="screen-reader-text"><span><?php _e("Currency Symbol", 'ultimate-product-catalogue')?></span></legend>
								<label><input type='text' name='currency_symbol' value='<?php echo $Currency_Symbol; ?>'/></label>
								<p><?php _e("What currency symbol, if any, should be displayed before or after the price? Leave blank for none.", 'ultimate-product-catalogue')?></p>
							</fieldset>
						</div>
					</td>
				</tr>
				<tr>
					<th><?php _e('Catalog Color', 'ultimate-product-catalogue'); ?></th>
					<td>
						<div class='upcp-welcome-screen-option'>
							<fieldset><legend class="screen-reader-text"><span><?php _e("Catalog Color", 'ultimate-product-catalogue')?></span></legend>
								<label title='Blue' class='ewd-upcp-admin-input-container'><input type='radio' name='color_scheme' value='Blue' <?php if($Color == "Blue") {echo "checked='checked'";} ?> /><span class='ewd-upcp-admin-radio-button'></span> <span><?php _e("Blue", 'ultimate-product-catalogue')?></span></label><br />		
								<label title='Black' class='ewd-upcp-admin-input-container'><input type='radio' name='color_scheme' value='Black' <?php if($Color == "Black") {echo "checked='checked'";} ?> /><span class='ewd-upcp-admin-radio-button'></span> <span><?php _e("Black", 'ultimate-product-catalogue')?></span></label><br />			
								<label title='Grey' class='ewd-upcp-admin-input-container'><input type='radio' name='color_scheme' value='Grey' <?php if($Color == "Grey") {echo "checked='checked'";} ?> /><span class='ewd-upcp-admin-radio-button'></span> <span><?php _e("Grey", 'ultimate-product-catalogue')?></span></label><br />
								<p><?php _e("Set the color of the image and border elements", 'ultimate-product-catalogue')?></p>
							</fieldset>
						</div>
					</td>
				</tr>
				<tr>
					<th><?php _e('Product Links', 'ultimate-product-catalogue'); ?></th>
					<td>
						<div class='upcp-welcome-screen-option'>
							<fieldset>
								<legend class="screen-reader-text"><span><?php _e("Product Links", 'ultimate-product-catalogue')?></span></legend>
								<label title='Same' class='ewd-upcp-admin-input-container'><input type='radio' name='product_links' value='Same' <?php if($Links == "Same") {echo "checked='checked'";} ?> /><span class='ewd-upcp-admin-radio-button'></span> <span><?php _e("Open in Same Window", 'ultimate-product-catalogue')?></span></label><br />
								<label title='New' class='ewd-upcp-admin-input-container'><input type='radio' name='product_links' value='New' <?php if($Links == "New") {echo "checked='checked'";} ?> /><span class='ewd-upcp-admin-radio-button'></span> <span><?php _e("Open in New Window", 'ultimate-product-catalogue')?></span></label><br />
								<!--<label title='External'><input type='radio' name='product_links' value='External' <?php if($Links == "External") {echo "checked='checked'";} ?> /> <span><?php _e("Open External Links Only in New Window", 'ultimate-product-catalogue')?></span></label><br />-->
								<p><?php _e("Should external product links open in a new window?", 'ultimate-product-catalogue')?></p>
							</fieldset>
						</div>
					</td>
				</tr>
				<tr>
					<th><?php _e('Product Search', 'ultimate-product-catalogue'); ?></th>
					<td>
						<div class='upcp-welcome-screen-option'>
							<fieldset>
								<legend class="screen-reader-text"><span><?php _e("Product Search", 'ultimate-product-catalogue')?></span></legend>
								<label title='None' class='ewd-upcp-admin-input-container'><input type='radio' name='product_search' value='none' <?php if($Product_Search == "none") {echo "checked='checked'";} ?> /><span class='ewd-upcp-admin-radio-button'></span> <span><?php _e("None", 'ultimate-product-catalogue')?></span></label><br />
								<label title='Name' class='ewd-upcp-admin-input-container'><input type='radio' name='product_search' value='name' <?php if($Product_Search == "name") {echo "checked='checked'";} ?> /><span class='ewd-upcp-admin-radio-button'></span> <span><?php _e("Name Only", 'ultimate-product-catalogue')?></span></label><br />
								<label title='Name-and-Desc' class='ewd-upcp-admin-input-container'><input type='radio' name='product_search' value='namedesc' <?php if($Product_Search == "namedesc") {echo "checked='checked'";} ?> /><span class='ewd-upcp-admin-radio-button'></span> <span><?php _e("Name and Description", 'ultimate-product-catalogue')?></span></label><br />
								<label title='Name-Desc-and-Cust' class='ewd-upcp-admin-input-container'><input type='radio' name='product_search' value='namedesccust' <?php if($Product_Search == "namedesccust") {echo "checked='checked'";} ?> /><span class='ewd-upcp-admin-radio-button'></span> <span><?php _e("Name, Description and Custom Fields", 'ultimate-product-catalogue')?></span></label><br />
								<p><?php _e("Set the 'Product Search' text box to search either product name, product name and description or product name, description and custom fields (slowest option)", 'ultimate-product-catalogue')?></p>
							</fieldset>
						</div>
					</td>
				</tr>
			</table>

			<div class='upcp-welcome-screen-save-options-button'><?php _e('Save Options', 'ultimate-product-catalogue'); ?></div>
			<div class="upcp-welcome-clear"></div>

			<div class='upcp-welcome-screen-next-button' data-nextaction='products'><?php _e('Next Step', 'ultimate-product-catalogue'); ?></div>
			<div class='upcp-welcome-screen-previous-button' data-previousaction='shop'><?php _e('Previous Step', 'ultimate-product-catalogue'); ?></div>
			<div class='clear'></div>
		</div>
	</div>
<?php } ?>
	<div class='upcp-welcome-screen-box upcp-welcome-screen-products' data-screen='products'>
		<h2><?php echo (isset($_GET['exclude']) ? '2.' : '5.') . __(' Add Products', 'ultimate-product-catalogue'); ?></h2>
		<div class='upcp-welcome-screen-box-content'>
			<p><?php isset($_GET['exclude']) ? '' : printf(__('Want more options (product images, sub-categories,  etc)? You can create products using the <a href="%s">dedicated product builder</a> instead.', 'ultimate-product-catalogue'), esc_url('admin.php?page=UPCP-options&Action=UPCP_Add_Product_Screen')); ?></p>
			<div class='upcp-welcome-screen-created-products'>
				<div class='upcp-welcome-screen-add-product-image upcp-welcome-screen-box-content-divs'>
					<label><?php _e('Product Image:', 'ultimate-product-catalogue'); ?></label>
					<div class='upcp-welcome-screen-image-preview-container'>
						<div class='upcp-hidden upcp-welcome-screen-image-preview'>
							<img />
						</div>
						<input type='hidden' name='product_image_url' />
						<input id="Welcome_Item_Image_button" class="button" type="button" value="Upload Image" />
					</div>
				</div>
				<div class='upcp-welcome-screen-add-product-name upcp-welcome-screen-box-content-divs'><label><?php _e('Product Name:', 'ultimate-product-catalogue'); ?></label><input type='text' /></div>
				<div class='upcp-welcome-screen-add-product-description upcp-welcome-screen-box-content-divs'><label><?php _e('Product Description:', 'ultimate-product-catalogue'); ?></label><textarea></textarea></div>
				<div class='upcp-welcome-screen-add-product-category upcp-welcome-screen-box-content-divs'><label><?php _e('Product Category:', 'ultimate-product-catalogue'); ?></label><select></select></div>
				<div class='upcp-welcome-screen-add-product-price upcp-welcome-screen-box-content-divs'><label><?php _e('Product Price:', 'ultimate-product-catalogue'); ?></label><input type='text' /></div>
				<div class='upcp-welcome-screen-add-product-button'><?php _e('Add Product', 'ultimate-product-catalogue'); ?></div>
				<div class="upcp-welcome-clear"></div>
				<div class="upcp-welcome-screen-show-created-products">
					<h3><?php _e('Created Products:', 'ultimate-product-catalogue'); ?></h3>
					<div class="upcp-welcome-screen-show-created-products-image"><?php _e('Image', 'ultimate-product-catalogue'); ?></div>
					<div class="upcp-welcome-screen-show-created-products-name"><?php _e('Name', 'ultimate-product-catalogue'); ?></div>
					<div class="upcp-welcome-screen-show-created-products-description"><?php _e('Description', 'ultimate-product-catalogue'); ?></div>
					<div class="upcp-welcome-screen-show-created-products-price"><?php _e('Price', 'ultimate-product-catalogue'); ?></div>
				</div>
			</div>
			<div class="upcp-welcome-clear"></div>
			<div class='upcp-welcome-screen-previous-button' data-previousaction='options'><?php _e('Previous Step', 'ultimate-product-catalogue'); ?></div>
			<div class='upcp-welcome-screen-finish-button' data-nextaction='shop'><a href='admin.php?page=UPCP-options'><?php _e('Finish', 'ultimate-product-catalogue'); ?></a></div>
			<div class='clear'></div>
		</div>
	</div>

	<div class='upcp-welcome-screen-skip-container'>
		<a href='admin.php?page=UPCP-options'><div class='upcp-welcome-screen-skip-button'><?php _e('Skip Setup', 'ultimate-product-catalogue'); ?></div></a>
	</div>
</div>