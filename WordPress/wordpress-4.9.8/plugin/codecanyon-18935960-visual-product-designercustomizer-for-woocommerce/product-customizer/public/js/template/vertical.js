// Sticky Plugin v1.0.4 for jQuery
// =============
// Author: Anthony Garand
// Improvements by German M. Bravo (Kronuz) and Ruud Kamphuis (ruudk)
// Improvements by Leonardo C. Daronco (daronco)
// Created: 02/14/2011
// Date: 07/20/2015
// Website: http://stickyjs.com/
// Description: Makes an element on the page stick on the screen as you scroll
//              It will only set the 'top' and 'position' of your element, you
//              might need to adjust the width in some cases.

(function (factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD. Register as an anonymous module.
        define(['jquery'], factory);
    } else if (typeof module === 'object' && module.exports) {
        // Node/CommonJS
        module.exports = factory(require('jquery'));
    } else {
        // Browser globals
        factory(jQuery);
    }
}(function ($) {
    var slice = Array.prototype.slice; // save ref to original slice()
    var splice = Array.prototype.splice; // save ref to original slice()

    var defaults = {
            topSpacing: 0,
            bottomSpacing: 0,
            stopScrollingEl: '.sticky-stop',
            className: 'is-sticky',
            wrapperClassName: 'sticky-wrapper',
            center: false,
            getWidthFrom: '',
            widthFromWrapper: true, // works only when .getWidthFrom is empty
            responsiveWidth: false,
            zIndex: 'inherit'
        },
        $window = $(window),
        $document = $(document),
        sticked = [],
        windowHeight = $window.height(),
        scroller = function() {
            var scrollTop = $window.scrollTop(),
                documentHeight = $document.height(),
                dwh = documentHeight - windowHeight,
                extra = (scrollTop > dwh) ? dwh - scrollTop : 0;

            for (var i = 0, l = sticked.length; i < l; i++) {
                var s = sticked[i],
                    elementTop = s.stickyWrapper.offset().top,
                    etse = elementTop - s.topSpacing - extra;

                //update height in case of dynamic content
                s.stickyWrapper.css('height', s.stickyElement.outerHeight());

                if (scrollTop <= etse) {
                    if (s.currentTop !== null) {
                        s.stickyElement
                            .css({
                                'width': '',
                                'position': '',
                                'top': '',
                                'z-index': ''
                            });
                        s.stickyElement.parent().removeClass(s.className);
                        s.stickyElement.trigger('sticky-end', [s]);
                        s.currentTop = null;
                    }
                }
                else {
                    var stopElHeight = $(s.stopScrollingEl).length ? $(s.stopScrollingEl).offset().top : documentHeight;
                    var newTop = stopElHeight - s.stickyElement.outerHeight()
                        - s.topSpacing - s.bottomSpacing - scrollTop - extra;
                    if (newTop < 0) {
                        newTop = newTop + s.topSpacing;
                    } else {
                        newTop = s.topSpacing;
                    }
                    if (s.currentTop !== newTop) {
                        var newWidth;
                        if (s.getWidthFrom) {
                            padding =  s.stickyElement.innerWidth() - s.stickyElement.width();
                            newWidth = $(s.getWidthFrom).width() - padding || null;
                        } else if (s.widthFromWrapper) {
                            newWidth = s.stickyWrapper.width();
                        }
                        if (newWidth == null) {
                            newWidth = s.stickyElement.width();
                        }
                        s.stickyElement
                            .css('width', newWidth)
                            .css('position', 'fixed')
                            .css('top', newTop)
                            .css('z-index', s.zIndex);

                        s.stickyElement.parent().addClass(s.className);

                        if (s.currentTop === null) {
                            s.stickyElement.trigger('sticky-start', [s]);
                        } else {
                            // sticky is started but it have to be repositioned
                            s.stickyElement.trigger('sticky-update', [s]);
                        }

                        if (s.currentTop === s.topSpacing && s.currentTop > newTop || s.currentTop === null && newTop < s.topSpacing) {
                            // just reached bottom || just started to stick but bottom is already reached
                            s.stickyElement.trigger('sticky-bottom-reached', [s]);
                        } else if(s.currentTop !== null && newTop === s.topSpacing && s.currentTop < newTop) {
                            // sticky is started && sticked at topSpacing && overflowing from top just finished
                            s.stickyElement.trigger('sticky-bottom-unreached', [s]);
                        }

                        s.currentTop = newTop;
                    }

                    // Check if sticky has reached end of container and stop sticking
                    var stickyWrapperContainer = s.stickyWrapper.parent();
                    var unstick = (s.stickyElement.offset().top + s.stickyElement.outerHeight() >= stickyWrapperContainer.offset().top + stickyWrapperContainer.outerHeight()) && (s.stickyElement.offset().top <= s.topSpacing);

                    if( unstick ) {
                        s.stickyElement
                            .css('position', 'absolute')
                            .css('top', '')
                            .css('bottom', 0)
                            .css('z-index', '');
                    } else {
                        s.stickyElement
                            .css('position', 'fixed')
                            .css('top', newTop)
                            .css('bottom', '')
                            .css('z-index', s.zIndex);
                    }
                }
            }
        },
        resizer = function() {
            windowHeight = $window.height();

            for (var i = 0, l = sticked.length; i < l; i++) {
                var s = sticked[i];
                var newWidth = null;
                if (s.getWidthFrom) {
                    if (s.responsiveWidth) {
                        newWidth = $(s.getWidthFrom).width();
                    }
                } else if(s.widthFromWrapper) {
                    newWidth = s.stickyWrapper.width();
                }
                if (newWidth != null) {
                    s.stickyElement.css('width', newWidth);
                }
            }
        },
        methods = {
            init: function(options) {
                return this.each(function() {
                    var o = $.extend({}, defaults, options);
                    var stickyElement = $(this);

                    var stickyId = stickyElement.attr('id');
                    var wrapperId = stickyId ? stickyId + '-' + defaults.wrapperClassName : defaults.wrapperClassName;
                    var wrapper = $('<div></div>')
                        .attr('id', wrapperId)
                        .addClass(o.wrapperClassName);

                    stickyElement.wrapAll(function() {
                        if ($(this).parent("#" + wrapperId).length == 0) {
                            return wrapper;
                        }
                    });

                    var stickyWrapper = stickyElement.parent();

                    if (o.center) {
                        stickyWrapper.css({width:stickyElement.outerWidth(),marginLeft:"auto",marginRight:"auto"});
                    }

                    if (stickyElement.css("float") === "right") {
                        stickyElement.css({"float":"none"}).parent().css({"float":"right"});
                    }

                    o.stickyElement = stickyElement;
                    o.stickyWrapper = stickyWrapper;
                    o.currentTop    = null;

                    sticked.push(o);

                    methods.setWrapperHeight(this);
                    methods.setupChangeListeners(this);
                });
            },

            setWrapperHeight: function(stickyElement) {
                var element = $(stickyElement);
                var stickyWrapper = element.parent();
                if (stickyWrapper) {
                    stickyWrapper.css('height', element.outerHeight());
                }
            },

            setupChangeListeners: function(stickyElement) {
                if (window.MutationObserver) {
                    var mutationObserver = new window.MutationObserver(function(mutations) {
                        if (mutations[0].addedNodes.length || mutations[0].removedNodes.length) {
                            methods.setWrapperHeight(stickyElement);
                        }
                    });
                    mutationObserver.observe(stickyElement, {subtree: true, childList: true});
                } else {
                    if (window.addEventListener) {
                        stickyElement.addEventListener('DOMNodeInserted', function() {
                            methods.setWrapperHeight(stickyElement);
                        }, false);
                        stickyElement.addEventListener('DOMNodeRemoved', function() {
                            methods.setWrapperHeight(stickyElement);
                        }, false);
                    } else if (window.attachEvent) {
                        stickyElement.attachEvent('onDOMNodeInserted', function() {
                            methods.setWrapperHeight(stickyElement);
                        });
                        stickyElement.attachEvent('onDOMNodeRemoved', function() {
                            methods.setWrapperHeight(stickyElement);
                        });
                    }
                }
            },
            update: scroller,
            unstick: function(options) {
                return this.each(function() {
                    var that = this;
                    var unstickyElement = $(that);

                    var removeIdx = -1;
                    var i = sticked.length;
                    while (i-- > 0) {
                        if (sticked[i].stickyElement.get(0) === that) {
                            splice.call(sticked,i,1);
                            removeIdx = i;
                        }
                    }
                    if(removeIdx !== -1) {
                        unstickyElement.unwrap();
                        unstickyElement
                            .css({
                                'width': '',
                                'position': '',
                                'top': '',
                                'float': '',
                                'z-index': ''
                            })
                        ;
                    }
                });
            }
        };

    // should be more efficient than using $window.scroll(scroller) and $window.resize(resizer):
    if (window.addEventListener) {
        window.addEventListener('scroll', scroller, false);
        window.addEventListener('resize', resizer, false);
    } else if (window.attachEvent) {
        window.attachEvent('onscroll', scroller);
        window.attachEvent('onresize', resizer);
    }

    $.fn.sticky = function(method) {
        if (methods[method]) {
            return methods[method].apply(this, slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method ) {
            return methods.init.apply( this, arguments );
        } else {
            $.error('Method ' + method + ' does not exist on jQuery.sticky');
        }
    };

    $.fn.unstick = function(method) {
        if (methods[method]) {
            return methods[method].apply(this, slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method ) {
            return methods.unstick.apply( this, arguments );
        } else {
            $.error('Method ' + method + ' does not exist on jQuery.sticky');
        }
    };
    $(function() {
        setTimeout(scroller, 0);
    });
}));


(function ($, window) {

    var ShopFilter = function () {

        // - Init
        var CLASS = {
            'selected': 'is-selected',
            'hasPicture': 'has-picture',
            'optionImg': 'shop-filter__viewport__option-img',
            'sliderButton' : 'shop-filter__viewport__slider-button'
        }

        var S = {
            'viewport': '.shop-filter__viewport',
            'uploadInput': '.shop-filter__form-field',
            'uploader': '.shop-filter__form-file-uploader',
            'filter_option': '.shop-filter__option',
            'filter_component': '.shop-filter__component',
            'elemColor': '[data-sf-elem="color"]',
            'renderPicture': '.shop-filter__viewport__picture',
            'renderText': '.shop-filter__viewport__text',
            // Renders
            'renderPrice': '[data-sf-render="price"]',
            'renderComponent': '[data-sf-render="component"]',
            'renderComponentSubtitle': '[data-sf-render="component-subtitle"]',
            'renderComponentIcon': '[data-sf-render="component-icon"]',
            'renderComponentCounter': '[data-sf-render="component-counter"]',
            'renderOptionImg': '[data-sf-render="optionImg"]',
            'groupOptions': '[data-sf-group="options"]',
            'groupComponents': '[data-sf-group="components"]',
            'elemComponent': '[data-sf-elem="component"]',
            'elemOption': '[data-sf-elem="option"]',
            'init': '[data-sf-onload]',
        }

        var _this = this,
            totalPrice = 0,
            currentComponent = null,
            currentComponentId = null,
            currentOption = null;
        var currentOptionGroup = null;

        // - Rules
        var Rules = function (elem) {
            var me = this,
                rules = [],
                triggerComponents = [],
                triggerOptions = [],
                targetComponents = [],
                targetOptions = [];


            me.init = function (elem) {
                rules = window.shopFilterRules;
            }

            me.checkViewport = function (allViewport, opt_id, action) {
                $.each(allViewport, function(i, elem) {
                    if ($(elem).data("sf-render") === opt_id) $(elem).css("display", action);
                })
            }

            me.checkRule = function (elem, forceAction) {
                if (!$(elem).data()) {
                    return;
                }
                var data = $(elem).data().sfData;
                var $target = null;

                rules.forEach(function (rule, index) {
                    if (rule.trigger.option_id == data.option_id) {

                        $el = $(S.elemOption).filter(function () {
                            return ($(this).data().sfData.option_id == data.option_id)
                        });
                        var isChecked = $el.get(0).checkedOption;

                        if (rule.target.component_id) {
                            $target = $(S.elemComponent).filter(function () {
                                return ($(this).data().sfComponentId == rule.target.component_id)
                            });

                            if (rule.target.action == 'select' && isChecked) {
                                _this.pickComponent($target.get(0));
                            }
                        }

                        if (rule.target.option_id) {
                            $target = $(S.elemOption).filter(function () {
                                return ($(this).data().sfData.option_id == rule.target.option_id)
                            });

                            if (rule.target.action == 'select') {
                                if (!$target.get(0).checkedOption && isChecked || $target.get(0).checkedOption && !isChecked) {
                                    _this.pickOption($target.get(0), false);
                                    shopFilterValidation();
                                }
                            }
                        }
                        var allViewports = $(".shop-filter__viewport__text, .shop-filter__viewport__picture");
                        var targetComponent = '';
                        var action = (forceAction && forceAction == 'show') ?  'show' : rule.target.action;
                        if (action == 'hide' && isChecked) {
                            if (rule.target.component_id) {
                                $target.closest('.shop-filter__item').find(S.filter_option).each(function () {
                                    $(this).removeClass(CLASS.selected);
                                    me.checkViewport(allViewports, $(this).data("sf-data").option_id, "none")
                                });
                                targetComponent = $target;
                            }
                            if (rule.target.option_id) {
                                targetComponent = $($target).closest('.shop-filter__item').find(S.elemComponent);
                                $target.removeClass(CLASS.selected);
                                me.checkViewport(allViewports, rule.target.option_id, "none")
                            }
                            me.hideTarget(targetComponent);
                            $target.hide();
                        } else if (rule.target.action == 'show' && isChecked) {
                            if (rule.target.component_id) {
                                $target.closest('.shop-filter__item').find(S.filter_option).each(function () {
                                    me.checkViewport(allViewports, $(this).data("sf-data").option_id, "block")
                                });
                            } else {
                                me.checkViewport(allViewports, rule.target.option_id, "block")
                            }
                            $target.show()
                        }

                        if (forceAction && forceAction == 'show') {
                            $target.show()
                        }
                    }
                })
            }

            me.checkReverseRule = function (elem) {
                if (!$(elem).data()) {
                    return;
                }
                var data = $(elem).data().sfData;
                var $target = null;
                rules.forEach(function (rule, index) {
                    if (rule.trigger.option_id == data.option_id && rule.reverse == 1) {

                        $el = $(S.elemOption).filter(function () {
                            return ($(this).data().sfData.option_id == data.option_id)
                        });

                        if (rule.target.component_id) {
                            $target = $(S.elemComponent).filter(function () {
                                return ($(this).data().sfComponentId == rule.target.component_id)
                            });
                        }

                        if (rule.target.option_id) {
                            $target = $(S.elemOption).filter(function () {
                                return ($(this).data().sfData.option_id == rule.target.option_id)
                            });
                        }

                        if (rule.target.action == 'hide') {
                            $target.show()
                        } else if (rule.target.action == 'show') {
                            if (rule.target.component_id) {
                                $target.closest('.shop-filter__item').find(S.filter_option).each(function () {
                                    $(this).removeClass(CLASS.selected);
                                });
                                targetComponent = $target;
                            }
                            if (rule.target.option_id) {
                                targetComponent = $($target).closest('.shop-filter__item').find(S.elemComponent);
                                $target.removeClass(CLASS.selected);
                            }
                            me.hideTarget(targetComponent)
                            $target.hide()
                        }

                    }
                })
            }

            me.hideTarget = function (targetComponent) {
                var text = targetComponent.data().sfData.placeholderText;
                var image = targetComponent.data().sfData.placeholderIcon;
                $(targetComponent).find('.shop-filter__component__subtitle').html(text);
                $(targetComponent).find('.shop-filter__component__icon-selected').attr('src', image);
                $('img[data-sf-component-id="' + targetComponent.attr('data-sf-component-id') + '"]').remove();
                shopFilterValidation();
            }

            me.hideIfRuleShow = function () {
                var $target = null;
                rules.forEach(function (rule, index) {
                    if (rule.target.action == 'show') {
                        if (rule.target.component_id) {
                            $target = $(S.elemComponent).filter(function () {
                                return ($(this).data().sfComponentId == rule.target.component_id)
                            });
                        }
                        if (rule.target.option_id) {
                            $target = $(S.elemOption).filter(function () {
                                return ($(this).data().sfData.option_id == rule.target.option_id)
                            });
                        }
                        $target.hide();
                    }
                });
            }
        }


        var ShopFrontRules = new Rules();

        // - Trigger onLoad events
        $(function () {
            ShopFrontRules.init()
            ShopFrontRules.checkRule(null);
            ShopFrontRules.hideIfRuleShow();
            $(S.init).each(function () {
                $(this).trigger('onload')
            })
            $(S.elemColor).each(function () {
                shopFilter.updateTextColor('', $(this).data('option_id'), '#' + $(this).data('color'));
            });
            $('.is-default').each(function() {
                ShopFrontRules.checkRule($(this))
            });


        });

        $(S.viewport).sticky();

        // - Private

        // - Fit text
        var fitText = function (box, font_height) {
            var width = box.width(),
                html = '<span style="white-space:nowrap"></span>',
                line = box.wrapInner(html).children(),
                n = 100;
            if (font_height && Number(font_height)>0) {
                box.css({"font-size": Number(font_height)})
            } else {
                box.css({
                    'font-size': n
                });

                while (line.width() > width) {
                    box.css({
                        'font-size': --n
                    });
                }
            }
            box.text(line.text());
        }

        var getCurrentGroup = function (elem) {
            var $group = $(elem).data('sf-elem') == 'component' ? S.groupComponents : S.groupOptions;
            var $elem = $(elem).data('sf-elem') == 'component' ? S.elemComponent : S.elemOption;
            return $(elem).closest($group).find($elem)
        }

        var setMultiple = function (elem) {
            if (typeof elem.multiple == 'undefined') {
                elem.multiple = $(elem).closest('[data-sf-multiple]').length ? true : false;
            }
        }

        var setMultipleForce = function (elem) {
            elem.multiple = true
        }

        var setRadio = function (elem) {
            if (typeof elem.radio === 'undefined') {
                elem.radio = $(elem).closest('[data-sf-radio]').length ? true : false;
            }
        }

        var setChecked = function (elem) {
            if (elem.multiple) {
                elem.checkedOption = !elem.checkedOption;
            } else {

                getCurrentGroup(elem).each(function () {
                    $(this).get(0).checkedOption = false
                })

                elem.checkedOption = true
            }
        }

        var createImg = function (src, id, zIndex, cls, group) {
            zIndex = zIndex ? zIndex : 1;
            return $('<img/>', {
                'src': src,
                'data-sf-component-id': id,
                'class': cls ? cls : CLASS.optionImg,
                'data-sf-group': group
            }).css('z-index', zIndex);
        }

        var renderOptionImg = function (srcObj, zIndex) {
            var slide = -1;
            $.each(srcObj, function(key, src) {
                if (slide === -1 && src.length > 0) {
                    slide = key;
                    currentSlideNumber = slide + 1;
                    setSliderPosition();
                }
                var viewportImg = $('[data-sf-slide="' + key + '"]').find(S.renderOptionImg);
                var currentImg = $('[data-sf-slide="'+key+'"]').find('.' + CLASS.optionImg).filter('[data-sf-component-id="' + currentComponentId + '"]');

                if (currentOption.radio) {
                    var elem_in_group = $(currentOption).closest('.shop-filter__options-list').find('.shop-filter__radio');
                    elem_in_group.each(function() {
                        currentImg.filter('[data-sf-group="'+$(currentOption).data('sf-group')+'"]').remove()
                    });
                    viewportImg.append(createImg(src, currentComponentId, zIndex, '', $(currentOption).data('sf-group')));

                }else if (currentOption.multiple) {
                    if (currentOption.checkedOption) {
                        viewportImg.append(createImg(src, currentComponentId, zIndex))
                    } else {
                        currentImg.filter('[src="'+src+'"]').remove()
                    }
                } else {
                    if ( currentImg.length < 1 ) {
                        viewportImg.append(createImg(src, currentComponentId, zIndex))
                    } else {
                        currentImg.prop('src', src)
                    }
                }
            });
        }

        var getGroupPrice = function () {
            var price = 0;
            currentOptionGroup.find(S.elemOption).each(function () {
                if (this.checkedOption) {
                    price += parseFloat($(this).data('sf-data').price)
                }
            });
            return price.toFixed(2)
        }

        var getGroupCounter = function () {
            var counter = 0;
            currentOptionGroup.find(S.elemOption).each(function () {
                if (this.checkedOption) {
                    counter++
                }
            });
            return counter
        }

        var renderComponentSubtitle = function () {
            var title = '',
                counter = getGroupCounter(),
                price = getGroupPrice(),
                data = $(currentComponent).data('sf-data'),
                $subtitle = $(currentComponent).find(S.renderComponentSubtitle);

            if (counter == 1) {
                currentOptionGroup.find(S.elemOption).each(function () {
                    if (this.checkedOption) {
                        title = $(this).data('sf-data').title
                    }
                })
                $subtitle.html(title)
            } else if (counter > 1) {
                $subtitle.html(data.counterText + price)
            } else {
                $subtitle.html(data.placeholderText)
            }
        }

        var renderComponentIcon = function () {
            var src = '',
                counter = getGroupCounter(),
                data = $(currentComponent).data('sf-data'),
                $icon = $(currentComponent).find(S.renderComponentIcon);

            if (counter == 1) {
                currentOptionGroup.find(S.elemOption).each(function () {
                    if (this.checkedOption) {
                        src = $(this).data('sf-data').icon
                    }
                })
                $icon.show().prop('src', src)
            } else if (counter > 1) {
                $icon.hide()
            } else {
                $icon.show().prop('src', data.placeholderIcon)
            }
        }

        var renderComponentCounter = function () {
            var counter = getGroupCounter(),
                $counter = $(currentComponent).find(S.renderComponentCounter);
            if (counter > 1) {
                $counter.show().text(counter)
            } else {
                $counter.hide()
            }
        }


        // - Public
        _this.updatePicture = function (event, position) {
            var target = event.target,
                file = target.files[0]
            if (file) {
                var img = new Image();
                var fileReader = new FileReader();
                fileReader.onload = function (e) {
                    img.src = e.target.result;
                    var uploaderGroup = $(target).closest(S.uploader);
                    var slide = $('#'+position).data('sf-data').slide;
                    var el = $('.shop-filter__viewport__slider').find('[data-sf-slide="'+slide+'"]').find('[data-sf-render="' + position + '"]');
                    img.style.width = '100%';
                    img.style.height = '100%';
                    el.html(img);
                    uploaderGroup.addClass(CLASS.hasPicture);
                    uploaderGroup.find(S.uploadInput).prop('value', file.name);
                    uploaderGroup.find('.shop-filter__form-field').attr('data-image', img.src);
                    shopFilterValidation();
                }
                fileReader.readAsDataURL(file);
                ShopFrontRules.checkRule($(event.target).closest(S.elemOption).get(0))
            }
        }

        _this.removePicture = function (event, position) {
            var uploaderGroup = $(event.target).closest(S.uploader);
            $('[data-sf-render="' + position + '"]').empty();
            uploaderGroup.removeClass(CLASS.hasPicture);
            uploaderGroup.find(S.uploadInput).prop('value', '');
            uploaderGroup.find('.shop-filter__form-field').attr('data-image', '');
            ShopFrontRules.checkRule($(event.target).closest(S.elemOption).get(0), 'show');
            shopFilterValidation();
        }

        _this.updateText = function (event, position, text) {
            var slide = $('#'+position).data('sf-data').slide;
            currentSlideNumber = parseInt(slide)+1;
            setSliderPosition();
            var el = $('.shop-filter__viewport__slider').find('[data-sf-slide="'+slide+'"]').find('[data-sf-render="' + position + '"]');
            el.text(text);
            var fontSize = $(event.target).closest(S.elemOption).data("sf-data").font_size;
            fitText(el, fontSize);

            if (text && text.length) {
                ShopFrontRules.checkRule($(event.target).closest(S.elemOption).get(0))
            } else {
                ShopFrontRules.checkRule($(event.target).closest(S.elemOption).get(0), 'show')
            }
            shopFilterValidation();
        }

        _this.updateTextFont = function (position, font) {
            var slide = $('#'+position).data('sf-data').slide;
            currentSlideNumber = parseInt(slide)+1;
            setSliderPosition();
            $('[data-sf-render="' + position + '"]').css('font-family', font);
        }

        _this.updateTextColor = function (event, position, color) {
            var slide = $('#'+position).data('sf-data').slide;
            currentSlideNumber = parseInt(slide)+1;
            setSliderPosition();
            if (event) {
                $(event.target).closest(S.elemColor).css('background-color', color);
            } else {
                $(".color_"+position).css('background-color', color);
            }
            $('[data-sf-render="' + position + '"]').css('color', color);
            shopFilterValidation();
        }

        _this.selected = function (elem) {
            setMultiple(elem);
            setRadio(elem);
            setChecked(elem);
            if ($(elem).data('sf-elem') === 'component') {
                $(S.groupOptions).removeClass(CLASS.selected);
                $(S.groupOptions).filter('[data-sf-component-id="'+$(elem).data('sf-component-id')+'"]').addClass(CLASS.selected)
            }
            if (elem.radio) {
                var elem_in_group = (elem.multiple) ?
                    $(elem).closest('.shop-filter__options-list').find('.shop-filter__radio') :
                    $(elem).closest('.shop-filter__options__table').find('.shop-filter__radio');

                elem_in_group.each(function() {
                    $(this).removeClass(CLASS.selected);
                });
                $(elem).addClass(CLASS.selected)
            } else if (elem.multiple) {
                $(elem).toggleClass(CLASS.selected)
            } else {
                var old_elem = getCurrentGroup(elem);
                $(old_elem).each(function () {
                    ShopFrontRules.checkReverseRule($(this));
                })
                getCurrentGroup(elem).removeClass(CLASS.selected);
                $(elem).addClass(CLASS.selected)
            }
        }

        _this.pickComponent = function (elem) {
            currentComponent = elem;
            _this.selected(elem);
            ShopFrontRules.checkRule(elem)
        }


        _this.pickOption = function (elem) {
            var data = $(elem).data('sf-data');
            currentOption = elem;
            currentOptionGroup = $(elem).closest(S.groupOptions);
            currentComponentId = currentOptionGroup.data('sf-component-id');
            currentComponent = $(S.elemComponent).filter('[data-sf-component-id="' + currentComponentId + '"]')

            _this.selected(elem);
            renderComponentSubtitle(data.title);
            renderComponentIcon();
            renderComponentCounter();
            renderOptionImg(data.src, data.zIndex);
            if (!$(elem).find('input').length) {
                ShopFrontRules.checkRule(elem)
            }
        }
        var setSliderPosition = function() {
            if (currentSlideNumber) {
                $('.'+CLASS.sliderButton).eq(parseInt(currentSlideNumber)-1).addClass('active').siblings().removeClass('active');
                var $slider = $('[data-sf-slider]'),
                    slideSize = (currentSlideNumber - 1) * $slider.width();
                $slider.css({'transform': 'translateX('+ -slideSize +'px)'});
            }
        }

        _this.slideViewportPicture = function ( elem, slideNumber ) {
            var $slider = $('[data-sf-slider]'),
                slideSize = (parseInt(slideNumber)) * $slider.width();

            $(elem).addClass('active').siblings().removeClass('active');
            $slider.css({'transform': 'translateX('+ -slideSize +'px)'});
        }

        // - Bind Events
        $(document).on('click', S.elemComponent, function () {
            _this.pickComponent(this);
        })

        $(document).on('click', S.elemOption, function () {
            _this.pickOption(this);
            shopFilterValidation();
        })

        $(document).on('click', S.elemColor, function () {
            $(this).toggleClass('opened')
        });

    }

    // - Run
    window.shopFilter = new ShopFilter();
})(jQuery, window)
