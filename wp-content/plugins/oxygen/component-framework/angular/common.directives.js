
var CTCommonDirectives = angular.module('CTCommonDirectives', []);

CTCommonDirectives.factory('ctScopeService', function() {
    var mem = {};
    return {
        store: function(key, val) {
            mem[key] = val;
        },
        get: function(key) {
            return mem[key];
        }
    }
});

CTCommonDirectives.factory('ctOxyCache', function() {
    var mem = {};
    return {
        store: function(key, val) {
            mem[key] = val;
        },
        get: function(key) {
            return mem[key];
        }
    }
});

CTCommonDirectives.directive("ctiriscolorpicker", function() {
    return {
        restrict: "A",
        require: "ngModel",
        scope: {
            ctiriscallback: '=',
            gradientindex: '='
        },
        
        link: function(scope, element, attrs, ngModel) {
            var debounceChange = false;
            setTimeout(function(){
                element.alphaColorPicker({
                    color: scope.$parent.iframeScope.getGlobalColorValue(ngModel.$modelValue),
                    change: function(ui) {                        
                        if(element.val().length == ui.color.toString().length || element.val().length === 0) {
                            if(!debounceChange) {
                                debounceChange = setTimeout(function() {
                                    if (scope.$parent.globalColorToEdit.id!==undefined) {
                                        // update global color
                                        scope.$parent.updateGlobalColorValue(element.ctColorPicker('color'));
                                        scope.$parent.globalColorChange();
                                    } else {
                                        // update regular component setting
                                        ngModel.$setViewValue(scope.$parent.iframeScope.getGlobalColorValue(element.ctColorPicker('color')));
                                    }
                                    clearTimeout(debounceChange);
                                    debounceChange = false;
                                }, 100);
                            }
                        }
                        if(scope.ctiriscallback) {
                            scope.ctiriscallback();
                        }
                    }
                });

                var modelString = attrs.ngModel;

                if(typeof(scope.gradientindex) !== 'undefined') {
                    modelString = modelString.replace('$index', scope.gradientindex);
                }

                scope.$parent.$watch(modelString, function( newVal ) {

                    var niceName = scope.$parent.iframeScope.getGlobalColorNiceName(newVal),
                        colorPicker = element.closest('.oxygen-color-picker');

                    jQuery('.oxy-global-color-label', colorPicker).remove();

                    if (niceName) {
                        colorPicker.removeClass('oxy-not-global-color-value').children('input').prop( "disabled", true )
                            .after("<span class='oxy-global-color-label' title='"+niceName+"'>"+niceName+"<span class='oxy-global-color-label-remove'>x</span></span>")
                        
                        // update alpha value
                        // use acp_get_alpha_value_from_color() function code from alpha-color-picker.js
                        var alphaVal;
                        // Remove all spaces from the passed in value to help our RGBa regex.
                        var value = scope.$parent.iframeScope.getGlobalColorValue(newVal).replace( / /g, '' );
                        if ( value.match( /rgba\(\d+\,\d+\,\d+\,([^\)]+)\)/ ) ) {
                            alphaVal = parseFloat( value.match( /rgba\(\d+\,\d+\,\d+\,([^\)]+)\)/ )[1] ).toFixed(2) * 100;
                            alphaVal = parseInt( alphaVal );
                        } else {
                            alphaVal = 100;
                        }

                        var alphaSlider = element.closest('.wp-picker-container').find('.alpha-slider');

                        // use acp_update_alpha_value_on_alpha_slider() function code from alpha-color-picker.js
                        alphaSlider.slider( 'value', alphaVal );
                        alphaSlider.find( '.ui-slider-handle' ).text( alphaVal.toString() );    
                    }
                    else {
                        colorPicker.addClass('oxy-not-global-color-value').children('input').prop( "disabled", false );
                        scope.$parent.$parent.activeGlobalColor = {};
                    }

                    colorPicker.addClass('oxy-not-empty-color-value');
                    if ((!newVal || newVal === "") && !ngModel.$modelValue ) {
                        colorPicker.removeClass('oxy-not-empty-color-value');
                        // unset background color
                        element.closest('.wp-picker-container').find('.wp-color-result').css("background-color","");
                        return;
                    }

                    element.ctColorPicker('color', scope.$parent.iframeScope.getGlobalColorValue(newVal));
                });
                
                scope.$apply();
                
            }, 0);

        }
    }
});



CTCommonDirectives.directive("ctdynamicdata", function($compile, ctScopeService) {
    return {
        restrict: "A",
        replace: true,
        scope: {
            data: "=",
            callback: "=",
            noshadow: "=",
            backbutton: "=",
            optionname: "=",
        },
        link: function(scope, element, attrs) {

            angular.element('body').on('click', '.oxy-dynamicdata-popup-background, .oxygen-data-close-dialog', function() {
                angular.element('#ctdynamicdata-popup').remove();
                angular.element('.oxy-dynamicdata-popup-background').remove();
            });

            scope.dynamicDataModel = {};
            scope.showOptionsPanel = { item: false };
            scope.processCallback = function(item, dataitem, showOptions) {
                if(showOptions /*&& dataitem.properties && dataitem.properties.length > 0*/) {
                   scope.showOptionsPanel.item = item.name+item.data+dataitem.data;
                   if(item.type == "button") {
                       if (typeof scope.dynamicDataModel['settings_path'] === 'undefined') scope.dynamicDataModel['settings_path'] = item.data;
                       else scope.dynamicDataModel['settings_path'] = scope.dynamicDataModel['settings_path'] + "/" + item.data;
                   }

                }

                if(scope.callback && (!item.properties || item.properties.length == 0 )) {
                    
                    var shortcode = '[oxygen data="'+dataitem.data+'"';
                    
                    var finalVals = {};

                    var checkProperties = function(property){
                        if(scope.dynamicDataModel.hasOwnProperty(property.data) && scope.dynamicDataModel[property.data].trim !== undefined &&
                            scope.dynamicDataModel[property.data].trim()!=='' &&
                            !property.helper && scope.dynamicDataModel[property.data] !== property.nullVal && scope.fieldIsVisible( property )) {
                            finalVals[property.data] = scope.dynamicDataModel[property.data];
                        }
                        _.each(property.properties, function(property) {
                            checkProperties( property );
                        });
                    };

                    _.each(dataitem.properties, function(property) {
                        checkProperties( property );
                    });

                    _.each(finalVals, function(property, key) {
                        property = property.replace(/'/g, "__SINGLE_QUOTE__");
                        shortcode+=' '+key+'="'+property+'"';
                    })

                    if(dataitem['append']) {
                        shortcode+=' '+dataitem['append'];
                    }

                    if (typeof scope.dynamicDataModel['settings_path'] !== 'undefined') {
                        shortcode+=' settings_path="'+scope.dynamicDataModel['settings_path']+'"';
                    }

                    shortcode+=']';
                    if (scope.optionname!=undefined) {
                        scope.callback(shortcode, scope.optionname);
                    }
                    else {
                        scope.callback(shortcode)
                    };

                    angular.element('#ctdynamicdata-popup').remove();
                    angular.element('.oxy-dynamicdata-popup-background').remove();
                }
                //scope.dynamicDataModel={};
            }

            scope.applyChange = function(property) {
                if(property.change) {
                    eval(property.change);
                }
            }

            /*
            * Get the user back to the root panel
            * */
            scope.back = function( localScope ) {
                scope.dynamicDataModel={};
                scope.showOptionsPanel.item = false;
            }

            /*
            * Determines if a field should be visible by evaluating the dynamic condition, if set
            * */
            scope.fieldIsVisible = function( item ) {
                if( typeof item.show_condition === 'undefined' ) return true;
                return scope.$eval( item.show_condition );
            }

            /*
            * Recursive function that determines if a child panel is visible, in order to make the parent one visible too
            */
            scope.isChildPanelVisible = function( item, dataitem ) {
                if( !scope.showOptionsPanel.item ) return false;
                if( item.properties ) {
                    var result = false;
                    for( var i = item.properties.length -1; i >=0; i--) {
                        if( scope.showOptionsPanel.item === item.properties[i].name + item.properties[i].data + dataitem.data ) {
                            return true;
                        } else if( item.properties[i].properties ) {
                            result = scope.isChildPanelVisible( item.properties[ i ], dataitem );
                            if(result) return true;
                        }
                    }
                    return result;
                } else return false;
            }

            /*
            * Determines if the current panel is a navigation-only panel, to know if we should make the "INSERT" button visible or not
            */
            scope.isNavigationOnlyPanel = function( item ) {
                var result = true;
                for( var i = item.properties.length -1; i >= 0; i-- ){
                    if( item.properties[i].type != 'button' && item.properties[i].type != "heading" && item.properties[i].type != "label" ){
                        result = false;
                        break;
                    }
                }
                return result;
            };

            element.on('click', function() {
                var componentID = scope.$parent.$parent.iframeScope.component.active.id;

                var component = scope.$parent.$parent.iframeScope.getComponentById(componentID);
                
                var repeater = component.parent().closest('.oxy-dynamic-list');

                var repeaterID = false;

                while(repeater && repeater.length > 0) {
                    repeaterID = parseInt(repeater.attr('ng-attr-component-id'));
                    if(scope.$parent.$parent.iframeScope.component.options[repeaterID]['original']['use_acf_repeater']) {
                        
                        repeater = false;
                    }
                    else {
                        repeaterID = false;
                        repeater = repeater.parent().closest('.oxy-dynamic-list');
                    }
                }

                
                scope.$parent.$parent.acfRepeaterDynamicDialogProcess(repeaterID);
                

                scope.showOptionsPanel.item = false;
                scope.dynamicDataModel={};
                angular.element('body #ctdynamicdata-popup').remove();
                angular.element('body .oxy-dynamicdata-popup-background').remove();
                
                var template = '<div class="oxy-dynamicdata-popup-background"></div>'+
                        '<div id="ctdynamicdata-popup" class="oxygen-data-dialog'+(scope.noshadow?' ct-global-conditions-add-modal':'')+'">'+
                        '<h1>Insert Dynamic Data</h1>'+
                        '<div>';

                if(CtBuilderAjax.freeVersion) {
                    template+= '<div style="border: 4px solid #7046db;-webkit-font-smoothing: antialiased;background-color: white;width: 100%;margin-bottom: 16px;padding: 24px;display: flex;line-height: 1.4;flex-direction: column;align-items: center;">'+
                        '<h2 style="'+
                        'color: black;'+
                        'font-weight: 400;'+
                        'text-align: center;'+
                        'line-height: 1.2;'+
                        'font-size: 21px;'+
                    '">Dynamic Data requires Oxygen Pro.</h2>'+
                        '<a target="_blank" href="https://oxygenbuilder.com/upgrade-to-pro/?utm_source=free-version&utm_medium=in-plugin&utm_content=dynamic-data" style="'+
                        'padding-top: 11px;'+
                        'padding-bottom: 11px;'+
                        'padding-right: 24px;'+
                        'padding-left: 24px;'+
                        'color: white;'+
                        'background-color: #7046db;'+
                        '-webkit-font-smoothing: antialiased;'+
                        '-moz-osx-font-smoothing: grayscale;'+
                        'border-radius: 3px;'+
                        'font-weight: 500;'+
                        'text-decoration: none;'+
                        'box-shadow: 0px 1px 0px 0px #4016ab;'+
                        'margin-top: 16px;'+
                    '">Get Oxygen Pro</a>'+
                        '</div>';
                }
                if(scope.backbutton) {
                    template += '<div class="oxygen-data-back-button oxygen-data-close-dialog">&lt; BACK</div>';
                }
                            template+= '<div class="oxygen-data-dialog-data-picker"'+
                                    'ng-repeat="item in data">'+
                                    '<h2>{{item.name}}</h2>'+
                                    '<ul>'+
                                        '<li ng-repeat="dataitem in item.children" ng-mouseup="processCallback(dataitem, dataitem, true); $event.stopPropagation();">'+
                                            '<span>{{dataitem.name}}</span>'+
                                            '<div ng-if="dataitem.properties" ng-show="showOptionsPanel.item === dataitem.name+dataitem.data+dataitem.data || isChildPanelVisible( dataitem, dataitem )" class="oxygen-data-dialog-options" ng-mouseup="$event.stopPropagation();">'+
                                                '<h1>{{dataitem.name}} Options</h1>'+
                                                '<div>'+
                                                    '<div class="oxygen-data-back-button" ng-mouseup="back()">&lt; BACK</div>'+
                                                    '<div ng-repeat="property in dataitem.properties" ng-class="{inline: property.type==\'button\'}">'+
                                                        '<div  ng-include="\'dynamicDataRecursiveDialog\'" ng-class="{inline: property.type==\'button\'}"></div>'+
                                                    '</div>'+
                                                '</div>'+
                                                '<div class="oxygen-apply-button" ng-mouseup="processCallback(item, dataitem)" ng-show="!isNavigationOnlyPanel(dataitem)">INSERT</div>'+
                                            '</div>'+
                                        '</li>'+
                                    '</ul>'+
                                '</div>'+
                            '</div>'+
                        '</div>';

                var compiledElement = $compile(template)(scope);

                scope.$parent.$parent.oxygenUIElement.append(compiledElement);

                scope.$apply();
            })
        }
    }
});
//var hardLimit = 0;
CTCommonDirectives.directive("ctdynamiclist", function($compile, ctScopeService) {
    return {
        restrict: "A",
        replace: true,
        scope: {
            dynamicListOptions: "=",
            dynamicActions: "=",
        },
        link: function(scope, element, attrs) {

            var id = parseInt(element.attr('ng-attr-component-id'));

            var spanIDs = {};

            function setFocus(event) {
                var activeOriginal = angular.element(event.target).closest('.oxy_repeater_original');

                if(activeOriginal.closest('#'+element.attr('id')).length < 1) {
                    activeOriginal = false;
                }

                if(!activeOriginal || activeOriginal.length < 1) {
                    // scroll to the original component
                    scope.$parent.$parent.parentScope.scrollToComponent(element.children().attr('id'), 100);
                }
            }

            element.on('click', setFocus);

            angular.extend(scope.dynamicActions['actions'][id], {
                action: function(instanceId, componentID, doit, virtualTreeItem, data) {

                    instanceId = parseInt(instanceId);

                    if(typeof(doit) === 'undefined') {
                        return;
                    }
                    
                    startProcessing(instanceId, componentID, virtualTreeItem, data);
                }

            });


            function loadData(item, parent, data) {

                if(typeof(item.source_id) === 'undefined') {
                    item.source_id = item.id;
                }

                if(typeof(spanIDs[item.source_id]) !== 'undefined') { // if an id was already determined for a nested span
                    item.id = spanIDs[item.source_id];
                }
                else {
                    item.id = scope.$parent.$parent.recycleIDs.length > 0 ? scope.$parent.$parent.recycleIDs.shift() : scope.$parent.$parent.component.id++;
                }

                item.options['ct_id'] = item.id;

                // clean up the options
                scope.$parent.$parent.component.options[item.id] = {id: {}, model:{}, original:{}}; 

                if(typeof(parent) !== 'undefined') {
                    item['options']['ct_parent'] = parent;
                }

                var dataPiece = data.find(function(dp) {
                    return dp['ct_id'] === item.source_id;
                })

                if(dataPiece && dataPiece['ct_content']) {
                    item['options']['ct_content'] = dataPiece['ct_content'];

                    // if it contains a reference to a span

                    item['options']['ct_content'] = item['options']['ct_content'].replace(/\<span id\=\"ct-placeholder-([^\"]*)\"\>\<\/span\>/ig, function(match, id) {
                        var newID = scope.$parent.$parent.recycleIDs.length > 0 ? scope.$parent.$parent.recycleIDs.shift() : scope.$parent.$parent.component.id++;

                        spanIDs[id] = newID;

                        return '<span id="ct-placeholder-'+newID+'"></span>'
                    });
                }

                if(dataPiece && dataPiece['renderedHTML']) {
                    item['options']['renderedHTML'] = dataPiece['renderedHTML'];
                }

                if(dataPiece && dataPiece['original']) {
                    item['options']['original'] = dataPiece['original'];
                }

                if ( item.name == "oxy_dynamic_list" ) {    
                    scope.$parent.dynamicListActions.actions[item.id] = {};
                }

                // if(item.name === 'oxy_dynamic_list') {
                //     // remove the children, we dont want the list to reproduce here
                //     delete(item['children']);
                // }
                
                scope.$parent.$parent.dynamicListTrees['forcleanup'][id].push(item.id);
                
                if(item.children && item.children.length > 0) {
                    item.children.forEach(function(child) {
                        loadData(child, item.id, data);
                    })
                }
            }

            function collectModels(children) {
                
                var models = [];

                children.forEach(function(child) {
                    
                    var model = angular.element.extend(true, {}, child['options']);

                    if(child['name'] === 'oxy_dynamic_list') {
                        model['children'] = collectModels(child.children)
                    }

                    model['name'] = child['name'];

                    var componentTemplate = scope.$parent.$parent.componentsTemplates[model['name']];

                    if( componentTemplate) {
                        if(componentTemplate.phpCallback) {
                            model['renderedHTML'] = true;
                            model['component'] = child;
                        }
                    }

                    models.push(model);

                    if(child['name'] !== 'oxy_dynamic_list' && child.children) {
                        models = models.concat(collectModels(child.children));
                    }
                });

                return models;
            }

            function generateList(results, holder, pagination) {
                
                if(scope.$parent.$parent.dynamicListTrees['forcleanup'][id] && scope.$parent.$parent.dynamicListTrees['forcleanup'][id].length > 0) {
                    cleanOldData(scope.$parent.$parent.dynamicListTrees['forcleanup'][id]);
                }

                scope.$parent.$parent.dynamicListTrees['forcleanup'][id] = [];
                scope.$parent.$parent.dynamicListTrees['trees'][id] = [];

                results.forEach(function(result) {
                    var clone = JSON.parse(JSON.stringify(holder));
                    loadData(clone, null, result);
                    scope.$parent.$parent.dynamicListTrees['trees'][id].push(clone);
                })

                element.attr('data-for-id', id);

                var original = element.children('.oxy_repeater_original');
                
                if(original.length < 1) {
                    original = element.children().first();
                    original.addClass('oxy_repeater_original');
                }

                original.siblings().remove();

                scope.$parent.buildComponentsFromTree( scope.$parent.$parent.dynamicListTrees['trees'][id], null, false, element); 

                original.insertBefore(element.children().first());

                //if any of the children contain a repeater, run it as well
                scope.$parent.$parent.dynamicListTrees['trees'][id].forEach(function(item, index) {
                    runChildLists(item, results, index);
                });

                scope.$parent.$parent.adjustResizeBox();

                scope.$parent.$parent.$emit('oxy-dynamic-list-'+id);

                // all the children inside this repeater, if any is an API element and has_js, then rebuild its dom so that the required js loads and executes.
                if(holder && holder.children) {
                    findAndLoadJS(holder.children);
                }

                if(pagination) {
                    element.append(angular.element('<div class="oxy-repeater-pages-wrap" style="opacity:1">').append(angular.element('<div class="oxy-repeater-pages">').append(pagination)));
                }
            }


            function findAndLoadJS(items) {
                items.forEach(function(item) {
                    if(scope.$parent.componentsTemplates[item.name] && scope.$parent.componentsTemplates[item.name].has_js) {
                        scope.$parent.rebuildDOM(item.id);
                    } else if(item['children']) {
                        findAndLoadJS[item['children']];
                    }
                });
                scope.$parent.componentsTemplates['']
            }


            function runChildLists(item, results, index) {

                if(item.children) {
                    item.children.forEach(function(child) {
                        if(child.name == "oxy_dynamic_list") {
                            
                            var data = results[index].find(function(thing) {
                                return thing.ct_id === child.source_id;
                            });
                            
                            scope.$parent.$parent.dynamicListAction(child.id, 1, true, child, data['children']);
                            
                            
                        } else {
                            runChildLists(child, results, index);
                        }
                    });
                }

            }

            function cleanOldData(children) {
                
                    children.forEach(function(item) {
                        // delete idStyles
                        delete(scope.$parent.$parent.cache.idStyles[item]);

                        // put ID in the pool to be used for another instance
                        scope.$parent.$parent.recycleIDs.push(item);

                    });

            }

            function startProcessing(instanceId, componentID, virtualTreeItem, data) {

                var id = parseInt(element.attr('ng-attr-component-id'));

                // return if it is single mode
                if(scope.$parent.$parent.component.options[id]['model']['listrendertype']) {
                    var original = element.children().first();
                    original.addClass('oxy_repeater_original');
                    return;
                }
                
                if(instanceId !== id) {
                    return;    
                }
                var listComponent = null;

                if(virtualTreeItem) {
                    listComponent = virtualTreeItem;
                }
                else {
                    listComponent = scope.$parent.findComponentItem(scope.$parent.componentsTree.children, id, scope.$parent.getComponentItem);
                }

                if(!listComponent || !listComponent.children || listComponent.children.length < 1) {
                    return;
                }

                var holder = listComponent.children[0];
                

                
                if(virtualTreeItem) {
                    generateList(data, holder);
                }
                else {
                    // recursively collect models of all the items under a tree
                    var models = collectModels(listComponent.children);
                    // send the models along with the query data of the list component to receive an array of resolved models.

                    // if is an acf query, and has a ancestor repeater, then send that ancestor's query data

                    var parentRepeaterID = false;

                    // its an acf repeater, use parent repeater's query instead or default page query
                    var repeater = element;
                    var acfRepeaterFields = [];
                    var count = 0;
                    while(repeater && repeater.length > 0) {

                        var repeaterID = parseInt(repeater.attr('ng-attr-component-id'));
                        if(scope.$parent.$parent.component.options[repeaterID]['original']['use_acf_repeater']) {
                            repeater = repeater.parent().closest('.oxy-dynamic-list');
                            if(count > 0) { // so that the current repeater, if ACF, not be included
                                acfRepeaterFields.push(scope.$parent.$parent.component.options[repeaterID]['original']['acf_repeater']);
                            }
                        } else {
                            parentRepeaterID = parseInt(repeater.attr('ng-attr-component-id'));
                            repeater = false;
                        }
                        count++;
                    }


                    scope.$parent.getDynamicDataFromQuery(id, models, generateList, holder, parentRepeaterID, acfRepeaterFields);
                }
                //console.log(container.html());
                
            }

            // queue for execution once the DOM is built out of the tree            
            scope.$parent.$parent.dynamicListTrees['runMainOnLoad'].push(id);


        }
    }
})



CTCommonDirectives.directive("dyncontenteditable", function($compile, $timeout,$interval, ctScopeService) {

    return {
        restrict: "A",
        link: function(scope, element, attrs) {
            element.bind("dblclick", function(e) {

                e.stopPropagation();
                
                //scope = scope.$parent;
                // replace with the original element, so that it can be edited
                var itemID = element.attr('ng-attr-component-id');

                var component = scope.findComponentItem(scope.componentsTree.children, itemID, scope.getComponentItem);

                if(component) {

                    var insertedElement;
                    // if it is a span inside an editable element, it needs to be inserted inline of the surrounding text
                    if(component.name=='ct_span') {
                        
                        // add this attribute to all other clones so that those are not considered for operations
                        angular.element('[ng-attr-component-id='+itemID+']').attr('disabled', 'disabled');
                        element.removeAttr('disabled'); // this also got disabled in the above step

                        scope.buildComponentsFromTree([component], null, false, element);

                        insertedElement = element.children();

                        element.replaceWith(insertedElement);

                    } else {
                        // its just a dom element, it requires a different method for being inserted at the exact index

                        // add this attribute to the siblings so that the cloned elements inside the list are not considered for operartion
                        angular.element('[ng-attr-component-id='+itemID+']').attr('disabled', 'disabled');
                        element.removeAttr('disabled');

                        var parent = element.parent();
                        var index = element.index();
                        
                        var container = angular.element('<div>').css('display', 'none');
                        parent.append(container);

                        scope.buildComponentsFromTree([component], null, false, container);
                        
                        insertedElement = container.children();

                        // var offset = element.offset();

                        // insertedElement.css({'position': 'absolute', 'z-index': '999999'});

                        // insertedElement.offset(offset);

                        insertedElement.insertBefore(element);
                        
                        //element.css('opacity', 0);
                        element.css('display', 'none');
                        
                        element.attr('disabled', 'disabled');
                        
                        container.remove();                        
                        
                    }

                    setTimeout(function() {
                        insertedElement.trigger('dblclick');
                    }, 100);
                }
            });

        }
    }
})

CTCommonDirectives.directive("ctrendernestableshortcode", function($http) {
    return {
        restrict: "A",
        link: function(scope, element, attrs) {
            
            var id = parseInt(element.attr('ng-attr-component-id'));

            var callback = function(shortcode, contents) {
                
                if(typeof(contents) !== 'undefined') {
                    contents = contents.split('_#wrapped_content_replacer#_');
                    scope.$parent.component.options[id].model['wrapping_start'] = contents[0];
                    scope.$parent.component.options[id].model['wrapping_end'] = contents[1];
                } else {
                    scope.$parent.component.options[id].model['wrapping_start'] = '';
                    scope.$parent.component.options[id].model['wrapping_end'] = '';
                }

                scope.$parent.setOption(id, 'ct_nestable_shortcode', 'wrapping_start');
                scope.$parent.setOption(id, 'ct_nestable_shortcode', 'wrapping_end');
                
                scope.$parent.rebuildDOM(id);

            }
            
            var renderContent = function() {
                setTimeout(function() {
                    if(!scope.$parent) {
                        return;
                    }

                    var shortcode = scope.$parent.component.options[id].id['wrapping_shortcode'];

                    if(!shortcode) {
                        return;
                    }

                    var matches = [];

                    shortcode.replace(/\[([^\s\]]{1,})[^\]]*\]/ig, function(match, match2) {
                        matches.push(match);
                        matches.push(match2);
                        return '';
                    });

                    var shortcode_data = {
                        original: {
                            full_shortcode: matches[0]+"_#wrapped_content_replacer#_[/"+matches[1]+']',
                        }
                    }

                    scope.renderShortcode(id, 'ct_shortcode', callback, shortcode_data);
                }, 0);
            }

            var debounceChange = false;

            scope.$watch(element.attr('ct-nestable-shortcode-model'), function( newVal, oldVal ) {
                
                if(debounceChange === false && oldVal !== newVal) {
                    debounceChange = setTimeout(function() {
                        renderContent();   
                        debounceChange = false; 
                    }, 500)
                }                
            });

            //renderContent();
            
        }
    }
})

CTCommonDirectives.directive("ctevalconditions", function() {
    return {
        restrict: "A",
        link: function(scope, element, attrs) {
            
            setTimeout(function() {
                var id = parseInt(element.attr('ng-attr-component-id'));
                scope.parentScope.evalGlobalConditions(id);
            }, 0);
        }
    }
})

CTCommonDirectives.directive("ctrenderoxyshortcode", function($http, ctOxyCache) {
    return {
        restrict: "A",
        require: "ngModel",
        link: function(scope, element, attrs, ngModel) {
            
            var callback = function(shortcode, contents) {
                //ctOxyCache.store(shortcode, contents);
                element.html(contents);
            }

            setTimeout(function() {
                var id = parseInt(element.attr('ng-attr-component-id'));
                var shortcode = scope.$parent.getOption('ct_content', id);
                var shortcode_data = {
                    original: {
                        full_shortcode: shortcode
                    }
                }

                // add specific class only for content dynamic data
                if (shortcode.indexOf("data='content'")>0) {
                    
                    // hack needed to properly update components class in components tree
                    scope.$parent.currentClass = "oxy-stock-content-styles";
                    
                    scope.addClassToComponent(id,'oxy-stock-content-styles',false)
                    
                    // hack needed to properly update components class in components tree
                    scope.$parent.currentClass = false;
                }

                // var contents = ctOxyCache.get(shortcode);
                // if(contents) {
                //     callback(shortcode, contents);
                // }
                // else {
                //element.html(' ');
                scope.renderShortcode(id, 'ct_shortcode', callback, shortcode_data);

               // }
            }, 0);
        }
    }
})

/**
 * Make HTML5 "contenteditable" support ng-module
 * To enforce plain text mode, use attr data-plaintext="true"
 */

CTCommonDirectives.directive("contenteditable", function($timeout,$interval, ctScopeService) {

    return {
        restrict: "A",
        require: "ngModel",
        link: function(scope, element, attrs, ngModel) {

            element.unbind("paste input");

            function read() {
                ngModel.$setViewValue(element.html());
            }

            function getCaretPosition() {
                
                if(window.getSelection) {
                    selection = window.getSelection();
                    if(selection.rangeCount) {
                        range = selection.getRangeAt(0);
                        return(element.text().length-range.endOffset);
                    }
                }
            }

            function setCaretPosition(caretOffsetRight) {
                var range, selection;

                if(document.createRange) {
                    range = document.createRange();
                    if(element.get(0) && element.get(0).childNodes[0]) {
                        var offset = element.text().length;
                        
                        range.setStart(element.get(0), 0);
                        
                        if(caretOffsetRight > 0 && caretOffsetRight <= offset) {
                            offset -= caretOffsetRight;
                        }
                        range.setEnd(element.get(0).childNodes[0], offset);
                        range.collapse(false);
                        selection = window.getSelection();
                        selection.removeAllRanges();
                        selection.addRange(range);
                        
                    }
                    
                }
                else if(document.selection) {
                    range = document.body.createTextRange();
                    if(element.get(0) && element.get(0).childNodes[0]) {
                        var offset = element.text().length;
                            
                        range.setStart(element.get(0), 0);
                        
                        if(caretOffsetRight > 0 && caretOffsetRight <= offset) {
                            offset -= caretOffsetRight;
                        }
                        range.setEnd(element.get(0).childNodes[0], offset);
                        range.collapse(false);
                        range.select();
                    }
                }
            }

            ngModel.$render = function() {

                element.html(ngModel.$viewValue || "");

            };

            // save element content
            element.bind("input", function(e, paste) {

                scope.$apply(read);

                // if it is plaintext mode, replace any html formatting, only in paste mode
                if(paste && typeof(attrs['plaintext']) !== 'undefined' && attrs['plaintext'] === "true") {
                    
                    if(jQuery('<span>').html(element.html()).text().trim() !== element.html().trim().replace('&nbsp;', '')) {
                       // var caretPosition = getCaretPosition();
                       // element.html(jQuery('<span>').html(element.html()).text());
                       // setCaretPosition(caretPosition);
                        element.html(element.text());
                    }

                    ngModel.$setViewValue(element.text());
                }

                // if default text is provided and current text is blank. populate with defaulttext
                if(element.html().trim() === '' && typeof(attrs['defaulttext']) !== 'undefined' && attrs['defaulttext'].trim() !== '') {
                    element.text(attrs['defaulttext']);
                }

                // timeout for angular
                var timeout = $timeout(function() {
                    var dascope = scope,
                        optionName = attrs['optionname'] || "ct_content";

                    if(scope.iframeScope)
                        dascope = scope.iframeScope; 
                    dascope.setOption(dascope.component.active.id, dascope.component.active.name, optionName);
                    $interval.cancel(timeout);
                }, 20, false);
            })

            // trick to update content after paste event performed
            element.bind("paste", function() {
                setTimeout(function() {element.trigger("input", 'paste');}, 0);
            });

            // if data-plaintext is NOT set to "true"
            if(typeof(attrs['plaintext']) === 'undefined' || attrs['plaintext'] !== "true") {

                // Prevent link wrapper click while editting content
                element.bind("click", function(e) {
                    e.preventDefault();
                });

                // enable content editing on double click
                element.bind("dblclick", function(e) {

                    e.stopPropagation();

                    var parentScope = ctScopeService.get('scope').parentScope,
                        optionName = attrs['optionname'] || "ct_content";

                    // before enabling edit content,
                    var content = scope.getOption(optionName);
                    scope.contentEditableData.original = content;
                    scope.dynamicSpanCycleIDs = [];
                    content = content.replace(/\<span id\=\"ct-placeholder-([^\"]*)\"\>\<\/span\>/ig, function(match, id) {
                        
                        var oxy = scope.component.options[parseInt(id)]['model']['ct_content'];

                        var containsOxy = oxy.match(/\[oxygen[^\]]*\]/ig);

                        if(containsOxy) {
                            scope.$parent.dynamicSpanCycleIDs.push(parseInt(id));
                            scope.removeComponentById(parseInt(id), 'span', scope.component.active.id);
                            return oxy;
                        }
                        else {
                            
                            return match;
                        }

                    });

                    scope.setOptionModel(optionName, content, scope.component.active.id, scope.component.active.name)
                    scope.contentEditableData.beingEdited = content;
                    parentScope.enableContentEdit(element);
                    scope.$apply();
                });

                // format as <p> on enter/return press
                if ( element[0].attributes['ng-attr-paragraph'] ) {
                    element.bind('keypress', function(e){
                        if ( e.keyCode == 13 ) {
                            document.execCommand('formatBlock', false, 'p');
                        }
                    });
                }
                else {
                    // format as <br/>
                    element.bind('keypress', function(e){
                        if ( e.keyCode == 13 ) { 
                            document.execCommand('insertHTML', false, '<br><br>');
                            return false;
                        }
                    });
                }
            } 
            // else if it is plaintext mode
            else {
                // we do not need line breaks
                element.bind('keypress', function(e){
                    
                    if ( e.keyCode == 13 ) { 
                        element.blur();
                        return false;
                    }
                });
            }
            
            // if ngBlur is provided
            if(typeof(attrs['ngBlur']) !== 'undefined' || attrs['ngBlur'] !== "") {
                element.bind('blur', function() {
                    var timeout = $timeout(function() {
                        scope.$apply(attrs.ngBlur);
                        $interval.cancel(timeout);
                    }, 0, false);
                })
            }

        }
    };
});

/**
 * Helps an input text field gain focus based on a condition
 * 
 * @since 0.3.3
 * @author Gagan Goraya
 *
 * usage: <input type="text" focus-me="booleanValue" />
 */
 
CTCommonDirectives.directive('focusMe', function($timeout) {
  return {
    scope: { trigger: '=focusMe' },
    link: function(scope, element) {
      scope.$watch('trigger', function(value) {
        if(value === true) { 
          $timeout(function() {
            element[0].focus();
            scope.trigger = false;
          });
        }
      });
    }
  };
});
