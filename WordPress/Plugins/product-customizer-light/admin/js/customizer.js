function collectPreview(){checkType();var e="#customizer-preview";jQuery(e).html(""),jQuery(".customizer-default").each(function(){if(1==jQuery(this).val()){var t=jQuery(this).closest(".customizer-option-row").find(".customizer-option-image img").attr("data-original");t||(t=jQuery(this).closest(".customizer-option-row").find(".customizer-option-image img").attr("src")),t&&jQuery(e).append('<img src="'+t+'" class="layer">')}})}function hideProductImages(){jQuery(".customizer_meta").each(function(){jQuery(this).parent().parent().find("td.thumb .wc-order-item-thumbnail").hide()})}function customizerRandomNumber(){return Math.round((new Date).getTime()+100*Math.random())}function applyMinWidth(){customizer_get_max_height(jQuery("#customizer-preview img"),jQuery("#customizer-preview"))}function customizer_get_max_height(e,t){var i=0;e.each(function(){jQuery(this).height()>i&&(i=jQuery(this).height())}),t.css("height",i)}function checkType(){jQuery(".customizer-component-type").each(function(){var e=jQuery(this).parent().parent(),t=e.find(".customizer-multiple"),i=e.find("td.image_option, th.image_option");t.attr("disabled",!1),i.show(),e.removeClass("text-image-options")})}!function(e){jQuery(document).ready(function(){e(".add_component_button").siblings("table").children("tbody").children("tr").length>15&&e(".add_component_button").hide(),collectPreview(),hideProductImages(),jQuery("#customizer-preview img").each(applyMinWidth),jQuery("#customizer-components-table tbody").sortable({placeholder:"ui-state-highlight"}),jQuery("#customizer-components-table tbody").disableSelection()}),jQuery(document).on("click",".add_component_button",function(){var t=e(this).siblings("table").find("tbody").first(),i=customizerRandomNumber(),n=component_row_template.replace(new RegExp("{{id}}","g"),i);t.append(n);var o=t.children(".component-row").last().find("a.customizer-modal-trigger");o.length&&e.each(o,function(t,i){var n=e(this).data("modalid"),o="customizer-modal-"+customizerRandomNumber();e(this).attr("data-target","#"+o),e("#"+n).attr("id",o)}),e(this).siblings("table").children("tbody").children("tr").length>15&&e(this).hide()}),jQuery(document).on("click",".remove_component",function(){jQuery(".add_component_button").show(),jQuery(this).parent().parent().remove(),collectPreview()}),jQuery(document).on("click",".add_option_button",function(){var t=e(this).siblings("table").find("tbody").first(),i=customizerRandomNumber(),n=option_row_template.replace(new RegExp("{{id}}","g"),i),o=jQuery(this).attr("data-component");n=n.replace(new RegExp("{{component_id}}","g"),o),t.append(n),collectPreview()}),jQuery(document).on("click",".remove_option",function(){jQuery(this).parent().parent().remove(),collectPreview()}),jQuery(document).on("click",".add-image",function(t){t.preventDefault();var i=e(this),n=wp.media({title:"Please set the picture",button:{text:"Select picture(s)"},multiple:!1}).on("select",function(){n.state().get("selection").map(function(e){e=e.toJSON(),i.parent().find("input[type=hidden]").val(e.url),i.parent().find(".image-preview").html("<img src='"+e.url+"'>")})}).open()}),jQuery(document).on("click",".delete-image",function(t){return t.preventDefault(),e(this).parent().find(".image-preview").html(""),e(this).parent().find("input[type=hidden]").val(""),!1}),jQuery(document).on("change",".customizer-default",function(){collectPreview(),applyMinWidth()}),jQuery(document).on("change",".customizer-component-type",function(){collectPreview()})}(jQuery);