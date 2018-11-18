function collectPreview() {
    checkType();
    var a = "#customizer-preview";
    jQuery(a).html("");
    jQuery(".customizer-default").each(function () {
        if (1 == jQuery(this).val()) {
            var b = jQuery(this).closest(".customizer-option-row").find(".customizer-option-image img").attr("data-original");
            b || (b = jQuery(this).closest(".customizer-option-row").find(".customizer-option-image img").attr("src")), b && jQuery(a).append('<img src="' + b + '" class="layer">')
        }
    })
    //add custom positions
    jQuery('tr.text-image-options .image_options tr.customizer-option-row').each(function () {
        jQuery(a).append('<div style="border:1px dashed #fff;position: absolute;left:' +
            jQuery(this).find('.customizer-option-left').val() + '%;top:' +
            jQuery(this).find('.customizer-option-top').val() + '%;width:' +
            jQuery(this).find('.customizer-option-width').val() + '%;transform: rotate(' +
            jQuery(this).find('.customizer-option-transform').val() + 'deg);height:' +
            jQuery(this).find('.customizer-option-height').val() + '%;' +
            ';color:#fff;">' + jQuery(this).find('.customizer-option-name').val() + '</div>');
    });
}

function hideProductImages() {
    jQuery(".customizer_meta").each(function () {
        var a = jQuery(this).parent().parent();
        a.find("td.thumb .wc-order-item-thumbnail").hide()
    })
}

function customizerRandomNumber() {
    return Math.round(new Date().getTime() + (Math.random() * 100));
}

function applyMinWidth() {
    customizer_get_max_height(jQuery("#customizer-preview img"), jQuery("#customizer-preview"));
}

function customizer_get_max_height(a, b) {
    var c = 0;
    a.each(function () {
        jQuery(this).height() > c && (c = jQuery(this).height())
    }), b.css("height", c)

    if(c ==0){
        b.css("height", 500);
    }
}

function checkType() {
    jQuery(".customizer-component-type").each(function () {
        var a = jQuery(this).parent().parent();
        var b = a.find(".customizer-multiple");
        var c = a.find("td.image_option, th.image_option");
        var d = a.find("td.text_option, th.text_option");
        var e = a.find("td.custom_text_option, th.custom_text_option");
        "image" != jQuery(this).val() ? (b.val(0), b.attr("disabled", !0), c.hide(), d.show(), a.addClass('text-image-options')) : (b.attr("disabled", !1), c.show(), d.hide(), a.removeClass('text-image-options'));
        "custom_text" == jQuery(this).val() ? e.show() : e.hide();
    })

    jQuery('.customizer-rule-scope').each(function () {
        var tr = jQuery(this).parent().parent();
        var target_component = tr.find(".customizer-rule-target-component");
        var target_option = tr.find(".customizer-rule-target-option");

        "component" == jQuery(this).val() ? (target_option.attr("disabled", !0), target_option.hide(), target_component.show(), target_component.attr("disabled", !1)) : (target_component.attr("disabled", !0), target_component.hide(), target_option.show(), target_option.attr("disabled", !1))
    });
}

function check_all_import_entities() {
    if (jQuery('#customizer_import_mark_all').is(':checked')) {
        jQuery('.import_entity_type').attr('checked', true);
    } else {
        jQuery('.import_entity_type').attr('checked', false);
    }
}

function checkShowFirstSlide() {
    var slide0 = jQuery("input[name='customizer-settings[slides][0]']");
    if (slide0) {
        if (jQuery("select[name='customizer-settings[use_product_image_single]']").val() == 1) {
            slide0.parent().parent().parent().hide()
        } else { slide0.parent().parent().parent().show() }
    }
}

var extractFieldNames = function(fieldName, expression, keepFirstElement)
{
    expression = expression || /([^\]\[]+)/g;
    keepFirstElement = keepFirstElement || false;

    var elements = [];
    while((searchResult = expression.exec(fieldName)))
    {
        elements.push(searchResult[0]);
    }

    if (!keepFirstElement && elements.length > 0) elements.shift();

    return elements;
}

var attachProperties = function(target, properties, value)
{
    var currentTarget = target;
    var propertiesNum = properties.length;
    var lastIndex = propertiesNum - 1;
    var currentProperty = null;
    for (var i = 0; i < propertiesNum; ++i)
    {
        currentProperty = properties[i];
        if (currentTarget[currentProperty] === undefined)
        {
            currentTarget[currentProperty] = (i === lastIndex) ? value : {};
        }
        currentTarget = currentTarget[currentProperty];
    }
}
var convertFormDataToObject = function(form) {
    var currentField = null;
    var currentProperties = null;
    // result of this function
    var data = {};
    // get array of fields that exist in this form
    var fields = form.serializeArray();
    for (var i = 0; i < fields.length; ++i)
    {
        currentField = fields[i];
        currentProperties = extractFieldNames(currentField.name);
        var fieldName = currentField.name;
        var fieldValue = currentField.value;
        if(fieldName.indexOf('option_description') || fieldName.indexOf('component_description')){
            fieldValue = escapeHtml(fieldValue);
        }
        attachProperties(data, currentProperties, fieldValue);
    }
    return data;
}
function escapeHtml(unsafe) {
    return unsafe
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

function fixSortOrderComponent() {
    var sort_order = 0;
    jQuery('input.customizer-component-sort').each(function(){
            jQuery(this).val(sort_order);
            sort_order++;
    })
    sort_order = 0;
    jQuery('input.customizer-option-sort').each(function(){
        jQuery(this).val(sort_order);
        sort_order++;
    })
}

!function (a) {
    jQuery(document).ready(function () {
        collectPreview();
        hideProductImages();
        jQuery("#customizer-preview img").each(applyMinWidth);
        jQuery("#customizer-components-table tbody").sortable({placeholder: "ui-state-highlight"});
        jQuery("#customizer-components-table tbody").disableSelection();

        jQuery("#customizer-rules-table tbody").sortable({placeholder: "ui-state-highlight"});
        jQuery("#customizer-rules-table tbody").disableSelection();

        jQuery('#customizer_import_mark_all').click(check_all_import_entities);

        check_all_import_entities();
        checkShowFirstSlide();

        jQuery("#publish").on('click', function(e) {
            if(jQuery('.customizer-settings-template').length) {
                var form = jQuery("#post");
                fixSortOrderComponent();
                var data = convertFormDataToObject(form);

                jQuery(form).find(':input[name^=customizer]').each(function() {
                    jQuery(this).attr('disabled', 'disabled');
                });

                var el = document.createElement("input");
                el.type = "hidden";
                el.name = "customizer-component-json";
                el.value = JSON.stringify(data);
                jQuery(form).append(el);
            }
        })
    }),
        jQuery(document).on("click", ".add_component_button", function () {
        var b = a(this).siblings("#customizer-components-table").find("tbody").first(), c = customizerRandomNumber(),
            d = component_row_template.replace(new RegExp("{{id}}", "g"), c);
        b.append(d);
        var e = b.children(".component-row").last().find("a.customizer-modal-trigger");
        e.length && a.each(e, function (b, c) {
            var d = a(this).data("modalid"), e = "customizer-modal-" + customizerRandomNumber();
            a(this).attr("data-target", "#" + e), a("#" + d).attr("id", e)
        })
    }), jQuery(document).on("click", ".remove_component", function () {
        var a = jQuery(this).parent().parent();
        a.remove();
        collectPreview();
    }), jQuery(document).on("click", ".add_option_button.image_options", function () {
        var b = a(this).siblings("table").find("tbody").first(), c = customizerRandomNumber(),
            d = option_row_template.replace(new RegExp("{{id}}", "g"), c), e = jQuery(this).attr("data-component");
        d = d.replace(new RegExp("{{component_id}}", "g"), e), b.append(d);
        collectPreview();
    }), jQuery(document).on("click", ".remove_option", function () {
        var optionVal = jQuery(this).parent().parent();
        optionVal = jQuery(optionVal).find(".customizer-option-id").val();
        jQuery('input[value="' + optionVal + '"]').each(function () {
            jQuery(this).parent().remove();
        });
        collectPreview();
    }), jQuery(document).on("click", ".add-image", function (b) {
        b.preventDefault();
        var c = a(this), d = wp.media({
            title: "Please set the picture",
            button: {text: "Select picture(s)"},
            multiple: !1
        }).on("select", function () {
            var a = d.state().get("selection");
            a.map(function (a) {
                a = a.toJSON(), c.parent().find("input[type=hidden]").val(a.url.replace(UPLOAD_URL, '')), c.parent().find(".image-preview").html("<img src='" + a.url + "'>")
            })
        }).open()
    }), jQuery(document).on("click", ".delete-image", function (b) {
        return b.preventDefault(), a(this).parent().find(".image-preview").html(""), a(this).parent().find("input[type=hidden]").val(""), !1
    }), jQuery(document).on("change", ".customizer-default", function () {
        collectPreview(), applyMinWidth()
    }), jQuery(document).on("change", ".customizer-component-type", function () {
        collectPreview();
    })
    jQuery(document).on("change", ".text_option input", function () {
        collectPreview();
    })

    jQuery(document).on("click", ".add_rule_button", function () {
        var b = a(this).siblings("table").find("tbody").first();
        c = customizerRandomNumber();
        d = rule_row_template.replace(new RegExp("{{id}}", "g"), c);
        b.append(d);
        collectPreview();

    });
    jQuery(document).on("change", ".customizer-rule-scope", function () {
        collectPreview();
    });
    jQuery(document).on("change", ".text_option input", function () {
        collectPreview();
    })
    jQuery(document).on("change", "select[name='customizer-settings[use_product_image_single]']", function () {
        checkShowFirstSlide();
    })

}(jQuery);