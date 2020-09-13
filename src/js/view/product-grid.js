var ProductGrid = function ($scope, $) {

	// pagination

	$('.eael-woo-pagination', $scope).on('click', 'a', function (e) {
		e.preventDefault();

		var $this       = $(this),
		    nth         = $this.data('pnumber'),
		    lmt         = $this.data('plimit'),
		    ajax_url    = localize.ajaxurl,
		    args        = $this.data('args'),
		    settings    = $this.data('settings'),
		    widgetid    = $this.data('widgetid'),
		    widgetclass = ".elementor-element-" + widgetid;


		$.ajax({
			url: ajax_url,
			type: 'post',
			data: {
				action: 'woo_product_pagination_product',
				number: nth,
				limit: lmt,
				args: args,
				settings: settings
			},
			// beforeSend	: function(){
			// 	$(widgetclass+" .eael-product-grid .products").html("<li style='text-align:center;'>Loading please " +
			// 		"wait...!</li>");
			// },
			success: function (response) {
				// console.log(response);
				$(widgetclass + " .eael-product-grid .products").html(response);
				$(widgetclass + " .woocommerce-product-gallery").each(function () {
					$(this).wc_product_gallery();
				});

			}
		});

		$.ajax({
			url: ajax_url,
			type: 'post',
			data: {
				action: 'woo_product_pagination',
				number: nth,
				limit: lmt,
				args: args,
				settings: settings
			},
			// beforeSend	: function(){
			// 	$(widgetclass+" .eael-product-grid .products").html("<li style='text-align:center;'>Loading please " +
			// 		"wait...!</li>");
			// },
			success: function (response) {
				$(widgetclass + " .eael-product-grid .eael-woo-pagination").html(response);
			}
		});


	});

	// handle add to cart for quick view
	$scope.on('click', '.eael-product-popup-details .single_add_to_cart_button', function (e) {
		e.preventDefault();
		var $this        = $(this),
		    product_id   = $(this).val(),
		    variation_id = $('input[name="variation_id"]').val() || '',
		    quantity     = $('input[name="quantity"]').val(),
		    items        = $this.closest('form.cart.grouped_form'),
		    product_data = [];
		items            = items.serializeArray();
		if (items.length > 0) {
			items.forEach((item, index) => {
				var p_id = parseInt(item.name.replace(/[^\d.]/g, ''), 10);
				if (item.name.indexOf('quantity[') >= 0 && item.value != '' && p_id > 0) {
					product_data[product_data.length] = {
						'product_id': p_id,
						'quantity': item.value,
						'variation_id': 0,
					};
				}
			});
		} else {
			product_data[0] = {
				'product_id': product_id,
				'quantity': quantity,
				'variation_id': variation_id,
			}
		}
		$this.removeClass('eael-addtocart-added');
		$this.addClass('eael-addtocart-loading');
		$.ajax({
			url: localize.ajaxurl,
			type: 'post',
			data: {
				action: 'eael_product_add_to_cart',
				product_data: product_data,
				eael_add_to_cart_nonce: localize.nonce
			},
			success: function (response) {
				if (response.success) {
					$(document.body).trigger('wc_fragment_refresh');
					$this.removeClass('eael-addtocart-loading');
					$this.addClass('eael-addtocart-added');
				}
			}
		});
	})
};

jQuery(window).on("elementor/frontend/init", function () {
	elementorFrontend.hooks.addAction("frontend/element_ready/eicon-woocommerce.default", ProductGrid);
});