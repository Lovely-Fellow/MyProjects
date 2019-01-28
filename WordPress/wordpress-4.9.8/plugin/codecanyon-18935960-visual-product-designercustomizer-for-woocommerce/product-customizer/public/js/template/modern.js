(function($, window){

    var ShopFilter = function () {

        // - Init
        var CLASS = {
            'selected'     : 'is-selected',
            'hasPicture'   : 'has-picture',
            'optionImg'    : 'shop-filter__viewport__option-img',
            'sliderButton' : 'shop-filter__viewport__slider-button'
        }

        var S = {
            'scroller'                : '.shop-filter__scroller',
            'popupIframe'             : '.shop-filter__popup-iframe',
            'popupHtml'               : '.shop-filter__popup-html',
            'popupImage'              : '.shop-filter__popup-image',
            'viewport'                : '.shop-filter__viewport',
            'uploadInput'             : '.shop-filter__form-field',
            'uploader'                : '.shop-filter__form-file-uploader',
            'filter_option'           : '.shop-filter__option',
            'filter_component'        : '.shop-filter__component',
            'elemText'                : '[data-sf-elem="text"]',
            'elemColor'               : '[data-sf-elem="color"]',
            'renderPicture'           : '.shop-filter__viewport__picture',
            'renderText'              : '.shop-filter__viewport__text',
            // Radio buttons
            'radioBtn'                : '[data-sf-radio-btn]',
            'radioBtnGroup'           : '[data-sf-radio-btn-group]',
            // Carouse
            'carousel'                : '.shop-filter__components__carousel',
            'carouselPrev'            : '.shop-filter__components__carousel-prev',
            'carouselNext'            : '.shop-filter__components__carousel-next',
            'carouselScroller'        : '.shop-filter__components__carousel__scroller',
            // Renders
            'renderPrice'             : '[data-sf-render="price"]',
            'renderComponent'         : '[data-sf-render="component"]',
            'renderComponentSubtitle' : '[data-sf-render="component-subtitle"]',
            'renderComponentIcon'     : '[data-sf-render="component-icon"]',
            'renderComponentCounter'  : '[data-sf-render="component-counter"]',
            'renderOptionImg'         : '[data-sf-render="optionImg"]',
            'groupOptions'            : '[data-sf-group="options"]',
            'groupComponents'         : '[data-sf-group="components"]',
            'elemComponent'           : '[data-sf-elem="component"]',
            'elemOption'              : '[data-sf-elem="option"]',
            'init'                    : '[data-sf-onload]',
        }

        var _this = this,
            totalPrice = 0,
            currentComponent = null,
            currentComponentId = null,
            currentSlideNumber = null,
            currentOption = null,
            currentOptionGroup = null;

        // - Carousel
        var Carousel = function () {
            var _this = this;
            var $carousel = $(S.carousel),
                $carouselScroller = $(S.carouselScroller),
                itemSize = 50,
                visibleItems = 7,
                itemIndexLast = $carouselScroller.children().length;
            activeItemIndex = 0,
                enableToScroll = itemIndexLast - visibleItems;

            if (enableToScroll > 1) {
                $carousel.addClass('is-enabled')
            }

            var scroll = function () {
                var scrollSize = activeItemIndex * itemSize;
                $carouselScroller.css({
                    'transform': 'translateY('+ scrollSize + 'px' +')'
                })
            }

            _this.goTo = function (dir) {
                activeItemIndex = activeItemIndex + dir

                if ( activeItemIndex <= visibleItems - itemIndexLast ) {
                    activeItemIndex = visibleItems - itemIndexLast
                }

                if ( activeItemIndex >= 0 ) {
                    activeItemIndex = 0
                }

                scroll()
            }

            _this.next = function () {
                _this.goTo(1)
            }

            _this.prev = function () {
                _this.goTo(-1)
            }
        }

        // - Run carousel
        _this.carousel = new Carousel();

        // - Rules
        var Rules = function (elem) {
            var me = this,
                rules = [],
                triggerComponents = [],
                triggerOptions    = [],
                targetComponents  = [],
                targetOptions     = [];


            me.init = function (elem) {
                rules = window.shopFilterRules || [];
            }

            me.checkRule = function (elem, forceAction) {
                if (!elem) {
                    return
                }
                var type = $(elem).data().sfElem;
                var data = $(elem).data().sfData;
                var $target = null;

                rules.forEach(function(rule, index){

                    if (rule.trigger.option_id == data.option_id) {
                        $el = $(S.elemOption).filter(function() {
                            return ($(this).data().sfData.option_id == data.option_id)
                        });

                        var isChecked = $el.get(0).checked;

                        if (rule.target.component_id) {
                            $target = $(S.elemComponent).filter(function() {
                                return ($(this).data().sfComponentId == rule.target.component_id)
                            });

                            if (rule.target.action == 'select' && isChecked) {
                                _this.pickComponent($target.get(0), false);
                            }
                        }

                        if (rule.target.option_id) {
                            $target = $(S.elemOption).filter(function() {
                                return ($(this).data().sfData.option_id == rule.target.option_id)
                            });

                            if (rule.target.action == 'select') {
                                if (!$target.get(0).checked && isChecked || $target.get(0).checked && !isChecked) {
                                    _this.pickOption($target.get(0), false);
                                    // shopFilterValidation();
                                }
                            }
                        }

                        var allViewports = $(".shop-filter__viewport__text, .shop-filter__viewport__picture");
                        var targetComponent = '';
                        var action = (forceAction && forceAction == 'show') ?  'show' : rule.target.action;
                        if (action == 'hide' && isChecked) {
                            if (rule.target.component_id) {
                                $('.shop-filter__options-wrapper[data-sf-component-id="'+rule.target.component_id+'"]').find(S.filter_option).each(function () {
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
                                $('.shop-filter__options-wrapper[data-sf-component-id="'+rule.target.component_id+'"]').find(S.filter_option).each(function () {
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
                        var allViewports = $(".shop-filter__viewport__text, .shop-filter__viewport__picture");

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
                            if (rule.target.component_id) {
                                $('.shop-filter__options-wrapper[data-sf-component-id="'+rule.target.component_id+'"]').find(S.filter_option).each(function () {
                                    me.checkViewport(allViewports, $(this).data().sfData.option_id, "block")
                                });
                            } else {
                                me.checkViewport(allViewports, rule.target.option_id, "block")
                            }
                            $target.show()
                        } else if (rule.target.action == 'show') {
                            if (rule.target.component_id) {
                                $('.shop-filter__options-wrapper[data-sf-component-id="'+rule.target.component_id+'"]').find(S.filter_option).each(function () {
                                    $(this).removeClass(CLASS.selected);
                                    me.checkViewport(allViewports, $(this).data().sfData.option_id, "none")
                                });
                                targetComponent = $target;
                            }
                            if (rule.target.option_id) {
                                targetComponent = $($target).closest('.shop-filter__options-wrapper');
                                $target.removeClass(CLASS.selected);
                                me.checkViewport(allViewports, rule.target.option_id, "none")
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
        $(function() {
            ShopFrontRules.init()
            $(S.init).each(function(){
                if (this.onload) {
                    $(this).trigger('onload')
                }
                if (this.onclick) {
                    $(this).trigger('onclick')
                }
            })
        });

        // - Init Plugins
        // Popups
        $(function() {
            // - Html
            $(S.popupHtml).magnificPopup({
                type: 'inline',
                mainClass: 'mfp-fade',
                midClick: true // Allow opening popup on middle mouse click. Always set it to true if you don't provide alternative source in href.
            });
            // - Images
            $(S.popupImage).magnificPopup({
                type: 'image',
                mainClass: 'mfp-fade',
                midClick: true // Allow opening popup on middle mouse click. Always set it to true if you don't provide alternative source in href.
            });
            // - Iframe
            $(S.popupIframe).magnificPopup({
                disableOn: 700,
                type: 'iframe',
                mainClass: 'mfp-fade',
                removalDelay: 160,
                preloader: false,
                fixedContentPos: false,
                midClick: true // Allow opening popup on middle mouse click. Always set it to true if you don't provide alternative source in href.
            });
        });

        // Scroll
        $(function() {
            // baron(S.scroller)
            $(S.scroller).mCustomScrollbar(
                {scrollInertia: 0}
            )
        })
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
            var $elem  = $(elem).data('sf-elem') == 'component' ? S.elemComponent : S.elemOption;
            return $(elem).closest($group).find($elem)
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

        var setMultipleForce = function (elem) {
            elem.multiple = true
        }

        var setChecked = function (elem) {
            if (elem.radio) {
                var elem_in_group = $(elem).closest('.shop-filter__groups-wrapper').find('.shop-filter__radio');
                elem_in_group.each(function(rad) {
                    $(rad).prop('checked', false);
                });
                elem.checked = true;
            } else if ( elem.multiple ) {
                elem.checked = elem.checked ? false : true;
            } else {
                getCurrentGroup(elem).each(function(){
                    $(this).get(0).checked = false
                })

                elem.checked = true
            }
        }

        var createImg = function (src, id, zIndex, cls, group) {
            var zIndex = zIndex ? zIndex : 1;
            return $('<img/>', {
                'src'   : src,
                'data-sf-component-id': id,
                'class' : cls ? cls : CLASS.optionImg,
                'data-sf-group': group
            }).css('z-index', zIndex);
        }

        var renderOptionImg = function(srcObj, zIndex) {
            var slide = -1;
            $.each(srcObj, function(key, src) {
                if (slide === -1 && src.length > 0) {
                    slide = key;
                    currentSlideNumber = slide + 1;
                    setSliderPosition();
                }
                var viewportImg = $('[data-sf-slide="'+key+'"]').find(S.renderOptionImg);
                var currentImg = viewportImg.find('.' + CLASS.optionImg).filter('[data-sf-component-id="'+currentComponentId+'"]');

                if (currentOption.radio) {
                    var elem_in_group = $(currentOption).closest('.shop-filter__groups-wrapper').find('.shop-filter__radio');
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

        var getGroupPrice = function () {
            var price = 0;
            currentOptionGroup.find(S.elemOption).each(function(){
                if (this.checked) {
                    price += parseFloat($(this).data('sf-data').price)
                }
            });
            return price.toFixed(2)
        }

        var getGroupCounter = function () {
            var counter = 0;
            currentOptionGroup.find(S.elemOption).each(function(){
                if (this.checked) {
                    counter++
                }
            });
            return counter
        }

        var renderComponentSubtitle = function() {
            var title     = '',
                counter   = getGroupCounter(),
                price     = getGroupPrice(),
                data      = $(currentComponent).data('sf-data'),
                $subtitle = $(currentComponent).find(S.renderComponentSubtitle);

            if (counter == 1) {
                currentOptionGroup.find(S.elemOption).each(function(){
                    if(this.checked) {
                        title = $(this).data('sf-data').title
                    }
                })
                $subtitle.html(title)
            } else if ( counter > 1 ) {
                $subtitle.html(data.counterText + price)
            } else {
                $subtitle.html(data.placeholderText)
            }
        }

        var renderComponentIcon = function() {
            var src = '',
                counter = getGroupCounter(),
                data    = $(currentComponent).data('sf-data'),
                $icon = $(currentComponent).find(S.renderComponentIcon);

            if (counter == 1) {
                currentOptionGroup.find(S.elemOption).each(function(){
                    if(this.checked) {
                        src = $(this).data('sf-data').icon
                    }
                })
                $icon.show().prop('src', src)
            } else if ( counter > 1 ) {
                $icon.hide()
            } else {
                $icon.show().prop('src', data.placeholderIcon)
            }
        }

        var renderComponentCounter = function() {
            var counter   = getGroupCounter(),
                $counter  = $(currentComponent).find(S.renderComponentCounter);
            if (counter > 1) {
                $counter.show().text(counter)
            } else{
                $counter.hide()
            }
        }


        // - Public
        _this.updatePicture = function ( el, position ) {
            var target = el,
                file = target.files[0]
            if (file) {
                var img = new Image();
                var fileReader = new FileReader();
                fileReader.onload = function (e) {
                    img.src = e.target.result;
                    img.style.width = '100%';
                    img.style.height = '100%';
                    var uploaderGroup = $(target).closest(S.uploader);
                    var slide = $('#'+position).data('sf-data').slide;
                    var elem = $('.shop-filter__viewport__slider').find('[data-sf-slide="'+slide+'"]').find('[data-sf-render="' + position + '"]');
                    elem.html(img);
                    uploaderGroup.addClass(CLASS.hasPicture);
                    uploaderGroup.find(S.uploadInput).prop('value', file.name);
                    uploaderGroup.find('.shop-filter__form-field').attr('data-image', img.src);
                    currentSlideNumber = parseInt(slide)+1;
                    setSliderPosition();
                    shopFilterValidation();
                }
                fileReader.readAsDataURL(file);
                ShopFrontRules.checkRule($(event.target).closest('[data-sf-elem="option"]').get(0))
            }
        }

        _this.removePicture = function ( el, position ) {
            var uploaderGroup = $(el).closest(S.uploader);
            $('[data-sf-render="'+position+'"]').empty();
            uploaderGroup.removeClass(CLASS.hasPicture);
            uploaderGroup.find(S.uploadInput).prop('value', '');
            uploaderGroup.find('.shop-filter__form-field').attr('data-image', '');
            ShopFrontRules.checkRule($(event.target).closest('[data-sf-elem="option"]').get(0), 'show');
            shopFilterValidation();
        }

        _this.updateText = function ( position, text ) {
            var slide = $('#'+position).data('sf-data').slide;
            currentSlideNumber = parseInt(slide)+1;
            setSliderPosition();
            var el = $('.shop-filter__viewport__slider').find('[data-sf-slide="'+slide+'"]').find('[data-sf-render="' + position + '"]');
            el.text(text);
            var fontSize = $(event.target).closest(S.elemOption).data("sf-data").font_size;
            fitText(el, fontSize);

            if (text && text.length) {
                ShopFrontRules.checkRule($(event.target).closest('[data-sf-elem="option"]').get(0))
            } else {
                ShopFrontRules.checkRule($(event.target).closest('[data-sf-elem="option"]').get(0), 'show')
            }
            shopFilterValidation();
        }

        _this.updateTextFont = function ( position, font ) {
            var slide = $('#'+position).data('sf-data').slide;
            currentSlideNumber = parseInt(slide)+1;
            setSliderPosition();
            $('[data-sf-render="'+position+'"]').css('font-family', font);
        }

        _this.updateTextColor = function ( el, position, color ) {
            var slide = $('#'+position).data('sf-data').slide;
            currentSlideNumber = parseInt(slide)+1;
            setSliderPosition();
            if (el) {
                $(el).closest(S.elemColor).css('background-color', color);
            } else {
                $(".color_"+position).css('background-color', color);
            }
            $('[data-sf-render="' + position + '"]').css('color', color);
            shopFilterValidation();
        }

        _this.selected = function (elem) {
            setMultiple(elem)
            setRadio(elem);
            setChecked(elem)
            if ($(elem).data('sf-elem') == 'component') {
                $(S.groupOptions).removeClass(CLASS.selected)
                $(S.groupOptions).filter('[data-sf-component-id="'+$(elem).data('sf-component-id')+'"]').addClass(CLASS.selected)
            }
            if (elem.radio) {
                var elem_in_group = (elem.multiple) ?
                    $(elem).closest('.shop-filter__groups-wrapper').find('.shop-filter__radio') :
                    $(elem).closest('.shop-filter__options-list').find('.shop-filter__radio');

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

        _this.pickComponent = function(elem, applyRule) {
            applyRule = (typeof applyRule !== 'undefined') ? applyRule : true;

            currentComponent = elem;
            _this.selected(elem);
            if (applyRule) {
                ShopFrontRules.checkRule(elem)
            }
        }


        _this.pickOption = function(elem, applyRule) {
            applyRule = (typeof applyRule !== 'undefined') ? applyRule : true;

            var data = $(elem).data('sf-data');
            currentOption = elem;
            _this.selected(elem);
            currentOptionGroup = $(elem).closest(S.groupOptions);
            currentComponentId = currentOptionGroup.data('sf-component-id');
            currentComponent = $(S.elemComponent).filter('[data-sf-component-id="'+currentComponentId+'"]')

            // renderComponentSubtitle(data.title);
            // renderComponentIcon();
            // renderComponentCounter();
            renderOptionImg(data.src, data.zIndex);
            _this.updatePrice(data.price);

            if (!$(elem).find('input').length && applyRule) {
                ShopFrontRules.checkRule(elem)
            }
        }

        _this.updatePrice = function () {
            totalPrice = 0;
            $(S.elemOption).each(function () {
                if ($(this).get(0).checked) {
                    totalPrice += parseFloat($(this).data('sf-data').price)
                }
            })
            renderPrice();
        }

        var renderPrice = function () {
            $(S.renderPrice).text(totalPrice.toFixed(2))
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
        $(document).on('click', S.elemComponent, function(){
            _this.pickComponent(this)
        })

        $(document).on('click', S.elemOption, function(){
            _this.pickOption(this);
            shopFilterValidation();
        })

        $(document).on('resize', function(){
            setSliderPosition();
        });

        $(document).on('click', S.elemColor, function(){
            $(this).toggleClass('opened')
        });

        var initRadioBtnGroups = function() {
            $(S.radioBtnGroup).addClass('sf_is-hidden')

            $(S.radioBtn).each(function(){
                var id = $(this).data('sf-radio-btn')
                if (this.checked) {
                    var group = $(S.radioBtnGroup).filter('[data-sf-radio-btn-group="'+id+'"]');
                    group.removeClass('sf_is-hidden');
                }
            })
        }

        initRadioBtnGroups()
        $(document).on('change', S.radioBtn, function(){
            initRadioBtnGroups()
        })
    }

    // - Run
    window.shopFilter = new ShopFilter();

})(jQuery, window)
