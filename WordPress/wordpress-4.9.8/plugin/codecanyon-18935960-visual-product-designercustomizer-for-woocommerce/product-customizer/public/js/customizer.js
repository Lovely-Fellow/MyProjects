function shopFilterValidation() {
    var e = jQuery(".row-customizer .woocommerce-Price-currencySymbol").html(),
        t = jQuery(".row-customizer .woocommerce-Price-amount.amount"), i = parseFloat(DEFAULT_PRODUCT_PRICE), o = 0,
        r = [];
    jQuery(".shop-filter__option.is-selected, .shop-filter__option-container.is-selected").each(function () {
        "image" == jQuery(this).data("sf-data").type && (jQuery(this).data("sf-data").price && (o += parseFloat(jQuery(this).data("sf-data").price)), r.push(jQuery(this).data("sf-data").option_id))
    });
    var a = customizer_check_custom_fields(!1), s = customizer_check_custom_fields(!0), c = parseFloat(i + o + s),
        u = jQuery(".customizer_add_to_cart_button");
    r = {
        options: r,
        text_options: a
    }, jQuery("#" + CUSTOMIZER_OPTIONS_INPUT_NAME).val(JSON.stringify(r)), c ? u.show() : u.hide(), t.html('<span class="woocommerce-Price-currencySymbol">' + e + "</span> " + c.toFixed(2))
}

function applyMinWidth() {
    // customizer_get_max_height(jQuery(".customizer-thumbnail-image"), jQuery(this).parent());
    customizer_get_max_height(jQuery(".pic-wrapper img"), jQuery(".pic-wrapper"));
    customizer_get_max_height(jQuery(".shop-filter__viewport__slider img"), jQuery(".shop-filter__viewport__slider"));
    jQuery("[data-sf-onload]").each(function () {
        jQuery(this).trigger("onload")
    }), jQuery('[data-sf-group="options"]').css("max-height", jQuery(".shop-filter__viewport").height())
}

function customizer_get_max_height(e, t) {
    var i = 0;
    e.each(function () {
        jQuery(this).height() > i && (i = jQuery(this).height());
    }), t.css("height", i);
}

function customizer_check_custom_fields(e) {
    var t = 0, i = [], k = [];
    jQuery(".shop-filter__viewport__text:visible, .shop-filter__viewport__picture:visible").each(function (index, elem) {
        k.push(jQuery(elem).data('sf-render'));
    });
    return jQuery("input.shop-filter_custom_field").each(function () {
        if (0 != jQuery(this).val().length || jQuery(this).attr('data-image')) {
            var e = jQuery(this).closest(".shop-filter__option");
            if ((e.length && jQuery.inArray(e.data('sf-data').option_id, k) !== -1)) {
                t += parseFloat(e.data("sf-data").price);
                var o = {
                    option_id: e.data("sf-data").option_id,
                    value: jQuery(this).val(),
                    font: jQuery(".font_" + e.data("sf-data").option_id).val(),
                    color: jQuery(".color_" + e.data("sf-data").option_id).css("backgroundColor"),
                    type: e.data("sf-data").type,
                    image: jQuery(this).attr("data-image"),
                    size: jQuery('[data-sf-slide="' + e.data("sf-data").slide + '"]').find(jQuery('[data-sf-render="' + e.data("sf-data").option_id + '"]')).css("font-size"),
                    slide: e.data("sf-data").slide
                };
                i.push(o);
            }
        }
    }), e ? t : i
}

function customizer_apply_saved_values() {
    var e = jQuery("#customizer_saved_options").val();
    if (e && e.length > 0) {
        var t = JSON.parse(e.replace(/\\"/g, '"'));
        jQuery.each(t.options, function (e, t) {
            jQuery('[data-option_id="' + t + '"] img').click()
        });
        jQuery.each(t.text_options, function (e, t) {
            if (t.type === 'custom_text') {
                jQuery('#' + t.option_id).find('input').val(t.value).trigger('keyup');
                jQuery('.font_' + t.option_id).val(t.font).find('option').filter(function (index) {
                    return t.font === jQuery(this).val();
                }).trigger('change');
                jQuery('#' + t.option_id + ' .shop-filter__option__color-elem').filter(function (index) {
                    return t.color === jQuery(this).css('background-color');
                }).trigger('click');
                jQuery('.shop-filter__viewport__text').filter(function (index) {
                    return jQuery(this).data('sf-render') === t.option_id;
                }).css("font-size", t.size);
            }
            if (t.type === 'custom_image') {
                var img = jQuery('<img>');
                img.attr('src', t.image);
                var div = jQuery('.shop-filter__viewport__picture').filter(function (index) {
                    return jQuery(this).data('sf-render') === t.option_id;
                });
                div.prepend(img);
                var div2 = jQuery('.shop-filter__option').filter(function (index) {
                    return jQuery(this).data('sf-data').option_id === t.option_id;
                }).find('.shop-filter__form-field');
                div2.parent().addClass('has-picture');
                div2.attr('data-image', t.image).val('pic');
                shopFilterValidation();
            }
        });
    }
}

var customizer_currency_symbol = jQuery(".woocommerce-Price-currencySymbol").html(),
    CUSTOMIZER_OPTIONS_INPUT_NAME = "customizer_selected_options";
jQuery(document).ready(function () {
    jQuery(".shop-filter__option.is-default, .shop-filter__option-container.is-default, .shop-filter__radio.is-default").each(function () {
        jQuery(this).trigger("click")
    });

    customizer_apply_saved_values();
    jQuery("#customizer_get_the_link").click(function () {
        return jQuery(".customizer-save-block").fadeToggle("slow"), !1
    });
    jQuery(".pic-wrapper .shop-filter__viewport img, .customizer-thumbnail-image,.shop-filter__viewport img").each(applyMinWidth);
    jQuery(".customizer_price_symbol").html(customizer_currency_symbol);
    jQuery(".customizer-preload-image img").load(applyMinWidth);
});
