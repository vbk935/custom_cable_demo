jQuery(document).ready(function() {
	jQuery('.upcp-welcome-screen-box h2').on('click', function() {
		var page = jQuery(this).parent().data('screen');
		UPCP_Toggle_Welcome_Page(page);
	});

	jQuery('.upcp-welcome-screen-next-button').on('click', function() {
		var page = jQuery(this).data('nextaction');
		UPCP_Toggle_Welcome_Page(page);
	});

	jQuery('.upcp-welcome-screen-previous-button').on('click', function() {
		var page = jQuery(this).data('previousaction');
		UPCP_Toggle_Welcome_Page(page);
	});

	jQuery('.upcp-welcome-screen-add-category-button').on('click', function() {

		jQuery('.upcp-welcome-screen-show-created-categories').show();

		var category_name = jQuery('.upcp-welcome-screen-add-category-name input').val();
		var category_description = jQuery('.upcp-welcome-screen-add-category-description textarea').val();

		jQuery('.upcp-welcome-screen-add-category-name input').val('');
		jQuery('.upcp-welcome-screen-add-category-description textarea').val('');

		var data = 'category_name=' + category_name + '&category_description=' + category_description + '&action=upcp_welcome_add_category';
		jQuery.post(ajaxurl, data, function(response) {
			var HTML = '<div class="upcp-welcome-screen-category">';
			HTML += '<div class="upcp-welcome-screen-category-name">' + category_name + '</div>';
			HTML += '<div class="upcp-welcome-screen-category-description">' + category_description + '</div>';
			HTML += '</div>';

			jQuery('.upcp-welcome-screen-show-created-categories').append(HTML);

			var category = JSON.parse(response); 
			jQuery('.upcp-welcome-screen-add-catalogue-categories').append('<input type="checkbox" value="' + category.category_id + '" checked /> ' + category.category_name + '<br />');
			jQuery('.upcp-welcome-screen-add-product-category select').append('<option value="' + category.category_id + '">' + category.category_name + '</option>');
		});
	});

	jQuery('.upcp-welcome-screen-add-catalogue-button').on('click', function() {
		var catalogue_name = jQuery('.upcp-welcome-screen-add-catalogue-name input').val();

		var categories = [];
		jQuery('.upcp-welcome-screen-add-catalogue-categories input').each(function() {
			categories.push(jQuery(this).val());
		});

		jQuery('.upcp-welcome-screen-add-catalogue-name input').val('');

		var data = 'catalogue_name=' + catalogue_name + '&categories=' + JSON.stringify(categories) + '&action=upcp_welcome_add_catalogue';
		jQuery.post(ajaxurl, data, function(response) {});

		UPCP_Toggle_Welcome_Page('shop');
	});

	jQuery('.upcp-welcome-screen-add-shop-button').on('click', function() {
		var shop_title = jQuery('.upcp-welcome-screen-add-shop-name input').val();

		var data = 'shop_title=' + shop_title + '&action=upcp_welcome_add_shop_page';
		jQuery.post(ajaxurl, data, function(response) {});

		UPCP_Toggle_Welcome_Page('options');
	});

	jQuery('.upcp-welcome-screen-save-options-button').on('click', function() {
		var currency_symbol = jQuery('input[name="currency_symbol"]').val();
		var color_scheme = jQuery('input[name="color_scheme"]:checked').val();
		var product_links = jQuery('input[name="product_links"]:checked').val();
		var product_search = jQuery('input[name="product_search"]:checked').val();

		var data = 'currency_symbol=' + currency_symbol + '&color_scheme=' + color_scheme + '&product_links=' + product_links + '&product_search=' + product_search + '&action=upcp_welcome_set_options';
		jQuery.post(ajaxurl, data, function(response) {
			jQuery('.upcp-welcome-screen-save-options-button').after('<div class="upcp-save-message"><div class="upcp-save-message-inside">Options have been saved.</div></div>');
			jQuery('.upcp-save-message').delay(2000).fadeOut(400, function() {jQuery('.upcp-save-message').remove();});
		});
	});

	jQuery('.upcp-welcome-screen-add-product-button').on('click', function() {

		jQuery('.upcp-welcome-screen-show-created-products').show();

		var product_name = jQuery('.upcp-welcome-screen-add-product-name input').val();
		var product_image = jQuery('.upcp-welcome-screen-add-product-image input[name="product_image_url"]').val();
		var product_description = jQuery('.upcp-welcome-screen-add-product-description textarea').val();
		var product_category = jQuery('.upcp-welcome-screen-add-product-category select').val();
		var product_price = jQuery('.upcp-welcome-screen-add-product-price input').val();

		jQuery('.upcp-welcome-screen-add-product-name input').val('');
		jQuery('.upcp-welcome-screen-image-preview').addClass('upcp-hidden');
		jQuery('.upcp-welcome-screen-add-product-image input[name="product_image_url"]').val('');
		jQuery('.upcp-welcome-screen-add-product-description textarea').val('');
		jQuery('.upcp-welcome-screen-add-product-price input').val('');

		var data = 'product_name=' + product_name + '&product_image=' + product_image + '&product_description=' + product_description + '&product_category=' + product_category + '&product_price=' + product_price + '&action=upcp_welcome_add_product';
		jQuery.post(ajaxurl, data, function(response) {
			var HTML = '<div class="upcp-welcome-screen-product">';
			HTML += '<div class="upcp-welcome-screen-product-image"><img src="' + product_image + '" /></div>';
			HTML += '<div class="upcp-welcome-screen-product-name">' + product_name + '</div>';
			HTML += '<div class="upcp-welcome-screen-product-description">' + product_description + '</div>';
			HTML += '<div class="upcp-welcome-screen-product-price">' + product_price + '</div>';
			HTML += '</div>';

			jQuery('.upcp-welcome-screen-show-created-products').append(HTML);
		});
	});
});

function UPCP_Toggle_Welcome_Page(page) {
	jQuery('.upcp-welcome-screen-box').removeClass('upcp-welcome-screen-open');
	jQuery('.upcp-welcome-screen-' + page).addClass('upcp-welcome-screen-open');
}