(function ($, window) {
    var ShopFilter = function () {

        // - Init
        var CLASS = {
            'selected': 'is-selected',
            'hidden': 'sf_is-hidden',
            'hasPicture': 'has-picture',
            'optionImg': 'shop-filter__viewport__option-img',
            'sliderButton' : 'shop-filter__viewport__slider-button'
        }

        var S = {
            'viewport': '.shop-filter__viewport',
            'renderPrice': '[data-sf-render="price"]',
            'elemComponent': '[data-sf-elem="component"]',
            'renderComponent': '[data-sf-render="component"]',
            'renderOptionImg': '[data-sf-render="optionImg"]',
            'groupOptions': '[data-sf-group="options"]',
            'elemOption': '[data-sf-elem="option"]',
            'init': '[data-sf-onload]',

            'uploadInput': '.shop-filter__form-field',
            'uploader': '.shop-filter__form-file-uploader',
            'renderPicture': '.shop-filter__viewport__picture',
            'renderPicture-right': '[data-sf-render="pictureRight"]',
            'renderPicture-left': '[data-sf-render="pictureLeft"]',
            'renderPicture-top': '[data-sf-render="pictureTop"]',
            'renderText': '.shop-filter__viewport__text',
            'renderText-right': '[data-sf-render="textRight"]',
            'renderText-left': '[data-sf-render="textLeft"]',
            'renderText-top': '[data-sf-render="textTop"]',
            'elemText': '[data-sf-elem="text"]',
            'elemColor': '[data-sf-elem="color"]',
            'filter_option': '.shop-filter__option-container'
        }

        var _this = this,
            totalPrice = 0,
            currentComponent = null,
            currentComponentId = null,
            currentOption = null;

        var Carousel = function () {
            var _this = this;
            var stepMade = 0,
                scrollSize = 0,
                carouselEl = $(S.elemComponent).parent(),
                carouselWidth = carouselEl.width(),
                slideWidth = $(S.elemComponent).outerWidth(true),
                slidesWidth = 0,
                visibleItemsIndexFirst = 0,
                visibleItemsIndexLast = parseInt(carouselWidth / slideWidth) - 1,
                activeItemIndex = 0,
                itemIndexLast = $(S.elemComponent).length - 1;

            $(S.elemComponent).each(function () {
                slidesWidth += $(this).outerWidth(true)
            });

            var scroll = function () {
                scrollSize = -(stepMade * slideWidth);
                carouselEl.css({
                    'text-indent': scrollSize
                })
            }


            _this.slideTo = function (index, stepSize) {
                activeItemIndex = index ? index : activeItemIndex;
                var stepSize = stepSize ? stepSize : 1;

                if (activeItemIndex == 0 || activeItemIndex == itemIndexLast || slidesWidth < carouselWidth) {
                    return
                }

                if (activeItemIndex <= visibleItemsIndexFirst) {
                    stepMade -= stepSize;
                    visibleItemsIndexFirst -= stepSize;
                    visibleItemsIndexLast -= stepSize;
                    scroll()
                }

                if (activeItemIndex >= visibleItemsIndexLast) {
                    stepMade += stepSize;
                    visibleItemsIndexFirst += stepSize;
                    visibleItemsIndexLast += stepSize;
                    scroll()
                }
            }
            _this.slideToStart = function () {
                if (slidesWidth < carouselWidth) {
                    return
                }
                stepMade = 0;
                visibleItemsIndexFirst = 0;
                visibleItemsIndexLast = parseInt(carouselWidth / slideWidth) - 1;
                scroll()
            }
            _this.slideToEnd = function () {
                if (slidesWidth < carouselWidth) {
                    return
                }
                stepMade = itemIndexLast - visibleItemsIndexLast;
                visibleItemsIndexFirst = itemIndexLast - visibleItemsIndexLast;
                visibleItemsIndexLast = itemIndexLast;
                scroll()
            }
        }

        // - Run carousel
        _this.carousel = new Carousel();

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

            me.checkRule = function (elem, forceAction) {
                if (!$(elem).data()) {
                    return;
                }
                var data = $(elem).data().sfData;
                if (!data) {
                    return;
                }
                var $target = null;

                rules.forEach(function (rule, index) {
                    if (rule.trigger.option_id == data.option_id) {

                        $el = $(S.elemOption).filter(function () {
                            return ($(this).data().sfData.option_id == data.option_id)
                        });

                        var isChecked = $el.get(0).checked;

                        if (rule.target.component_id) {
                            $target = $(S.elemComponent).filter(function () {
                                return ($(this).data().sfComponentId == rule.target.component_id)
                            });

                            if (rule.target.action == 'select' && (isChecked
                                || (isReverse && !isChecked))) {
                                _this.pickComponent($target.get(0), false);
                                shopFilterValidation();
                            }
                        }

                        if (rule.target.option_id) {
                            $target = $(S.elemOption).filter(function () {
                                return ($(this).data().sfData.option_id == rule.target.option_id)
                            });

                            if (rule.target.action == 'select' && (isChecked
                                || (isReverse && !isChecked))) {
                                if (!$target.get(0).checked && isChecked || $target.get(0).checked && !isChecked) {
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
                                targetComponent = $($target).closest('.shop-filter__options-wrapper');
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
                                targetComponent = $($target).closest('.shop-filter__options-wrapper');
                                $target.removeClass(CLASS.selected);
                            }
                            me.hideTarget(targetComponent);
                            $target.hide()
                        }

                    }
                })
            }

            me.hideTarget = function (targetComponent) {
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

            me.checkViewport = function (allViewport, opt_id, action) {
                $.each(allViewport, function(i, elem) {
                    var viewport = $(elem).data("sf-render");
                    if (viewport === opt_id) $(elem).css("display", action);
                })
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
            });
            $(S.groupOptions).css('max-height', $(S.viewport).height());
            $(S.elemColor).each(function () {
                shopFilter.updateTextColor('', $(this).data('option_id'), '#' + $(this).data('color'));
            });
            $('.is-default').each(function() {
                ShopFrontRules.checkRule($(this))
            });
        })

        // - Private
        var showOptions = function () {
            var id = $(currentComponent).data('sf-component-id');
            $(S.groupOptions).addClass(CLASS.hidden).filter('[data-sf-component-id="' + id + '"]').removeClass(CLASS.hidden);
        }

        var renderComponent = function (elem) {
            $(S.renderComponent).html(elem)
            showOptions()
        }

        var renderPrice = function () {
            $(S.renderPrice).text(totalPrice.toFixed(2))
        }

        var setMultiple = function (elem) {
            if (typeof elem.multiple == 'undefined') {
                elem.multiple = $(elem).closest('[data-sf-multiple]').length ? true : false;
            }
        }

        var setRadio = function (elem) {
            if (typeof elem.radio === 'undefined') {
                elem.radio = $(elem).closest('[data-sf-radio]').length ? true : false;
            }
        }

        var setChecked = function (elem) {
            if (elem.multiple) {
                elem.checked = elem.checked ? false : true;
            } else {
                $(elem).closest(S.groupOptions).find(S.elemOption).each(function () {
                    $(this).get(0).checked = false
                })
                elem.checked = true
            }
        }

        var createImg = function (src, id, zIndex, cls, group) {
            var zIndex = zIndex ? zIndex : 1;
            return $('<img/>', {
                'src': src,
                'data-sf-component-id': id,
                'class': cls ? cls : CLASS.optionImg,
                'data-sf-group': group
            }).css('z-index', zIndex);
        }

        var renderOptionImg = function (srcObj, componentId, zIndex) {
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
                    var elem_in_group = $(currentOption).closest('.shop-filter__options-wrapper').find('[data-sf-group="'+$(currentOption).data('sf-group')+'"]') ;
                    elem_in_group.each(function() {
                        currentImg.filter('[data-sf-group="'+$(currentOption).data('sf-group')+'"]').remove()
                    });
                    viewportImg.append(createImg(src, currentComponentId, zIndex, '', $(currentOption).data('sf-group')));

                }else if (currentOption.multiple) {
                    if (currentOption.checked) {
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


        // - Public
        _this.selected = function (elem) {
            setMultiple(elem);
            setRadio(elem);
            setChecked(elem);

            if (elem.radio) {
                var elem_in_group = (elem.multiple) ?
                    $(elem).closest('.shop-filter__options-wrapper').find('[data-sf-group="'+$(elem).data('sf-group')+'"]') :
                    $(elem).closest('.shop-filter__options-wrapper').find('.shop-filter__radio');

                elem_in_group.each(function() {
                    $(this).removeClass(CLASS.selected);
                });
                $(elem).addClass(CLASS.selected)
            } else if (elem.multiple) {
                $(elem).toggleClass(CLASS.selected)
            } else {
                $(elem).siblings().removeClass(CLASS.selected);
                $(elem).siblings().each(function () {
                    if ($(this).data().sfData) {
                        ShopFrontRules.checkReverseRule($(this));
                    }
                })
                $(elem).closest(S.groupOptions).find(S.elemOption).removeClass(CLASS.selected)
                ShopFrontRules.checkReverseRule($(elem).closest(S.groupOptions).find(S.elemOption));
                $(elem).addClass(CLASS.selected)
            }
        }

        _this.updatePrice = function () {
            totalPrice = 0
            $(S.elemOption).each(function () {
                if ($(this).get(0).checked) {
                    totalPrice += parseFloat($(this).data('sf-data').price)
                }
            })
            renderPrice();
        }

        _this.pickComponent = function (elem, applyRule) {
            applyRule = (typeof applyRule !== 'undefined') ? applyRule : true;

            _this.selected(elem)
            currentComponent = elem;
            var component = $(elem).clone().html();
            renderComponent(component);
            _this.carousel.slideTo($(currentComponent).index());
            if (applyRule) {
                ShopFrontRules.checkRule(elem)
            }
        }

        _this.pickNextComponent = function () {
            var nextComponent = $(currentComponent);
            do {
                if ($(currentComponent).is(':last-child')) {
                    nextComponent = $(currentComponent).siblings().first();
                    _this.carousel.slideToStart();
                } else {
                    nextComponent = $(nextComponent).next();
                }
            } while ($(nextComponent).css('display') == 'none');
            _this.pickComponent(nextComponent)
        }

        _this.pickPrevComponent = function () {
            var prevComponent = $(currentComponent);
            do {
                if ($(currentComponent).is(':first-child')) {
                    prevComponent = $(currentComponent).siblings().last();
                    _this.carousel.slideToEnd();
                } else {
                    prevComponent = $(prevComponent).prev();
                }

            } while ($(prevComponent).css('display') == 'none');
            _this.pickComponent(prevComponent)
        }

        _this.pickOption = function (elem, applyRule) {
            applyRule = (typeof applyRule !== 'undefined') ? applyRule : true;

            currentOption = elem;
            _this.selected(elem);
            var data = $(elem).data('sf-data');
            currentOptionGroup = $(elem).closest(S.groupOptions);
            currentComponentId = currentOptionGroup.data('sf-component-id');
            data.componentId = $(elem).closest(S.groupOptions).data('sf-component-id');
            _this.updatePrice(data.price);
            renderOptionImg(data.src, data.componentId, data.zIndex);
            if (!$(elem).find('input').length && applyRule) {
                ShopFrontRules.checkRule(elem)
            }
        }

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
                    var slide = $('#'+position).data('sf-data').slide;
                    currentSlideNumber = parseInt(slide)+1;
                    setSliderPosition();
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
            fitText(el,fontSize);
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

        var setSliderPosition = function() {
            if (currentSlideNumber) {
                $('.'+CLASS.sliderButton).eq(parseInt(currentSlideNumber)-1).addClass('active').siblings().removeClass('active');
                var $slider = $('[data-sf-slider]'),
                    slideSize = (currentSlideNumber - 1) * $slider.width();
                $slider.css({'transform': 'translateX('+ -slideSize +'px)'});
            }
        }

        _this.slideViewportPicture = function ( elem, slideNumber ) {
            currentSlideNumber = parseInt(slideNumber)+1;
            setSliderPosition();
        }

        // - Bind Events
        $(document).on('click', S.elemComponent, function () {
            _this.pickComponent(this)
        })

        $(document).on('click', S.elemOption, function () {
            _this.pickOption(this);
            shopFilterValidation();
        })

        // - Bind Events
        $(document).on('click', S.elemColor, function () {
            $(this).toggleClass('opened')
        });

    }

    // - Run
    window.shopFilter = new ShopFilter();

})(jQuery, window)
