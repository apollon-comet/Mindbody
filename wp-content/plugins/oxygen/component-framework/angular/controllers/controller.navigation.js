/**
 * UI to navigate Componenets Tree: DOM Tree, breadcrumbs, up level buttons, ...
 * 
 */

CTFrontendBuilder.controller("ControllerNavigation", function($scope, $parentScope, $http, $timeout) {

    $scope.openFolders  = {};
    $scope.toggledNodes = [];

    /**
     * Generate breadcrumbs
     * 
     * @since 0.1.2
     */

    $scope.generateBreadcrumbs = function(key, item) {

        // temporary disabled
        return false;

        if ( item.name == "ct_link" ) {
            $scope.findComponentItem($scope.componentsTree.children, item.options.ct_parent, $scope.generateBreadcrumbs);
            return false;
        }

        var activate    = ' ng-click="activateComponent(' + key + ', \'' + item.name + '\', $event)"',
            name        = $scope.niceNames[item.name],
            activeClass = "";

        if ( item.name == "ct_woocommerce" ) {
                hookName = $scope.getWooCommerceHookNiceName(item.options.original['hook_name']);
                name += hookName;
            }

        if ( key == $scope.component.active.id ) {
            activeClass     = "ct-breadcrumb-current";
            activate        = "";
        }

        var breadcrumb =    '<span class="ct-breadcrumb ' + activeClass + '"' + activate + '>' +
                                name +
                            '</span>';

        // if not root and not root's child
        if ( item.options && item.id != 0 ) {

            if ( $scope.componentsBreadcrumbs == "" ) {
                // add parent element and current element
                $scope.componentsBreadcrumbs = breadcrumb;
            }
            else {
                // prepend parents
                $scope.componentsBreadcrumbs = breadcrumb + ' &gt; ' + $scope.componentsBreadcrumbs;
            }

            $scope.findComponentItem($scope.componentsTree.children, item.options.ct_parent, $scope.generateBreadcrumbs);
        }
        else {
            $scope.componentsBreadcrumbs = '<span class="ct-breadcrumb" ng-click="activateComponent(0, \'root\', $event)">Root</span> &gt; ' + $scope.componentsBreadcrumbs;
        }
    }


    /**
     * returns the default Title for a component to display in the DOMTree entry
     *
     * @since 0.3.3
     * @author gagan goraya
     */

    $scope.calcDefaultComponentTitle = function(item, nameOnly) {
        
        var niceName = $scope.niceNames[item.name];

        if ( item.name == "ct_reusable" ) {
            niceName += " (post: " + item.options.view_id + ")";
        }
        else if ( item.name == "ct_woocommerce" ) {
            hookName = $scope.getWooCommerceHookNiceName(item.options.original['hook_name']);
            niceName += hookName;
        }
        
        if(!nameOnly)
            niceName += " (#" + item.id + ")";

        return niceName;
    }

    $scope.setComponentCategory = function(id, category, $event) {
        var item = $scope.findComponentItem($scope.componentsTree.children, id, $scope.getComponentItem);
        var existingCategory = item['options']['ct_category'];

        if(existingCategory === category) {
            delete item['options']['ct_category'];    
            delete $scope.component.options[id]['ct_category'];
        }
        else {
            item['options']['ct_category'] = category;
            $scope.component.options[id]['ct_category'] = category;
        }

        jQuery($event.target).closest('.ct-more-options-expanded').removeClass("ct-more-options-expanded");
    }

    /**
     * Generate DOM Tree Navigator
     * 
     * @since 0.1.5
     */

    $scope.generateDOMTreeNavigator = function(node, searchId, DOMTree, isChild, reorder) {

        if ($scope.component.options[99999]==undefined) {
            $scope.component.options[99999] = [];
        }

        $scope.component.options[99999]["nicename"] = "Button Area";

        if(typeof(isChild) === 'undefined')
            isChild = false;

        var isBrake = false;

        angular.forEach(node.children, function(item, index) {
            
            if ( !isBrake ) {

                if (undefined === searchId || item.id == searchId) {

                    var id              = item.id,
                        name            = item.name,
                        classes         = "",
                        nodeOptions     = "",
                        nodeDetails     = "",
                        ngClassNode     = ' ng-class="{\'ct-dom-tree-node-expanded\' : toggledNodes['+id+'],\'ct-dom-tree-child\' : hasParent('+id+')}"}',
                        ngClassAnchor   = ' ng-class="{\'ct-dom-tree-node-selected\' : parentScope.isActiveId('+id+')}"',
                        apiData         = "";

                    if (name=="oxy_header_left"||name=="oxy_header_center"||name=="oxy_header_right"){
                        activateID = item.options.ct_parent;
                        activateName = 'oxy_header_row';
                        classes += " dom-tree-disabled";
                    }
                    else {
                        activateID = id;
                        activateName = name;
                    }

                    var niceName = $scope.calcDefaultComponentTitle(item),
                        activate    = ' ng-mousedown="parentScope.triggerCodeMirrorBlur('+id+');" ng-click="activateComponent('+activateID+', \'' + activateName + '\',$event); parentScope.scrollToComponent(\''+item.options.selector+'\');"',
                        title       = "";

                    if ( item.id == 99999 ) {
                        activate = "";
                    }

                    if (CtBuilderAjax.userEditOnly=="true" && !$scope.isElementEnabledForUser(item.name) && item.name === "ct_code_block"){
                        activate = "";
                        classes += " ct-dom-tree-node-disabled";
                    }

                    if ( !item.children ) {
                        classes += " ct-dom-tree-no-children";
                    }

                    if (    item.name == "ct_columns"   ||
                            item.name == "ct_new_columns" || 
                            item.name == "ct_column"    || 
                            item.name == "ct_section"   || 
                            item.name == "ct_ul"        || 
                            item.name == "ct_link"      || 
                            (item.name == 'ct_inner_content' && (!CtBuilderAjax['query'] || !CtBuilderAjax['query']['post_type'] || CtBuilderAjax['query']['post_type'] !== 'ct_template') ) ||
                            item.name == "ct_div_block" ||
                            item.name == "oxy_dynamic_list" ||
                            item.name == "ct_nestable_shortcode" ||
                            item.name == "oxy_icon_box_button_area" ||
                            item.name == "oxy_tabs" ||
                            item.name == "oxy_tabs_contents" ||
                            item.name == "oxy_tab" ||
                            item.name == "oxy_tab_content" ||
                            item.name == "oxy_toggle" ||
                            item.name == "oxy_header" ||
                            item.name == "ct_if_wrap" ||
                            item.name == "ct_else_wrap" ||
                            item.name == "oxy_header" ||
                            item.name == "oxy_header_left" || item.name == "oxy_header_center" || item.name == "oxy_header_right" ||
                            //item.name == "oxy_icon_box" || item.name == "oxy_pricing_box" ||
                            item.name == "ct_slider" || item.name == "ct_slide" ||
                            item.name == "ct_modal" ) {
                        classes += " ct-accept-drops";
                    }

                    // API components that support drops
                    if ( $scope.isAPIComponent(item.name) ) {
                        if ( $scope.componentsTemplates[name]['nestable'] ) {
                            classes += " ct-accept-drops";
                        }
                    }

                    // add it for all components
                    apiData += " data-component-name='"+item.name+"' ";
                    
                    // All these is not very clever TODO: make it simpler
                    if ( item.name == "ct_columns" ) {
                        classes += " ct-sortable-columns";
                    }

                    if ( item.name == "ct_column" ) {
                        classes += " ct-sortable-column";
                    }

                    if ( item.name == "ct_ul" ) {
                        classes += " ct-sortable-ul";
                    }

                    if ( item.name == "ct_li" ) {
                        classes += " ct-sortable-li";
                    }

                    if ( item.name == "ct_if_wrap" ) {
                        classes += " ct-sortable-if-wrap";
                    }

                    if ( item.name == "oxy_dynamic_list" ) {
                        classes += " ct-sortable-dynamic-list";
                    }

                    if ( item.name == "ct_else_wrap" ) {
                        classes += " ct-sortable-else-wrap";
                    }

                    if ( item.name == "ct_slider" ) {
                        classes += " ct-sortable-slider";
                    }

                    if ( item.name == "ct_modal" ) {
                        classes += " ct-sortable-modal";
                    }

                    if ( item.name == "ct_slide" ) {
                        classes += " ct-sortable-slide";
                    }

                    if ( item.name == "ct_section" ) {
                        classes += " ct-sortable-section";
                    }

                    if ( item.name == "ct_link" ) {
                        classes += " ct-sortable-link";
                    }

                    if ( item.name == "ct_link_text" ) {
                        classes += " ct-sortable-link-text";
                    }

                    if( item.name == "ct_new_columns" ) {
                        classes += " ct-sortable-new-columns"
                    }

                    if ( item.name == "ct_div_block" ) {
                        classes += " ct-sortable-div-block"
                    }

                    if ( item.name == "oxy_header" ) {
                        classes += " ct-sortable-header"
                    }

                    if ( item.name == "oxy_header_row" ) {
                        classes += " ct-sortable-header-row"
                    }

                    if ( item.name == "oxy_tabs") {
                        classes += " ct-sortable-tabs"
                    }

                    if ( item.name == "oxy_tabs_contents" ) {
                        classes += " ct-sortable-tabs-contents"
                    }

                    if ( item.name == "oxy_tab") {
                        classes += " ct-sortable-tab"
                    }

                    if ( item.name == "oxy_tab_content" ) {
                        classes += " ct-sortable-tab-content"
                    }

                    if ( item.name != "ct_span" && item.name != "oxy_header_left" && item.name != "oxy_header_right" && item.name != "oxy_header_center" 
                         && item.options.oxy_builtin != 'true') {
                        classes += " ct-draggable";
                    }

                    if ( item.name != "ct_span" && item.options.oxy_builtin != 'true') {
                        var reUsable = 
                                        '<li ng-show="isCanComponentize('+id+',\''+name+'\');"'+
                                            'ng-click="saveReusable('+id+')">'+
                                            'Make Re-Usable'+
                                        '</li>',

                            duplicate = 
                                        '<li ng-click="duplicateComponent('+id+',\''+name+'\','+item.options['ct_parent']+')">'+
                                           'Duplicate'+
                                        '</li>',

                            wrapWithDiv = 
                                        '<li ng-click="wrapComponentWith(\'ct_div_block\','+id+','+item.options['ct_parent']+')">'+
                                            'Wrap with &#60;div&#62;'+
                                        '</li>';
                    }
                    else {
                        var reUsable = '',
                            duplicate = '',
                            wrapWithDiv = '';
                    }

                    var copyToBlock = '';
                    if( angular.element('body').hasClass('ct_connection_active') && (item.name == "ct_section" || item.name =="ct_div_block") ) {
                        copyToBlock = '<li '+
                            'ng-click="saveReusable('+id+', true)">'+
                            'Copy to Block'+
                            '</li>'
                    }


                    if ($scope.component.options[item.id]==undefined) {
                        $scope.component.options[item.id] = {};
                    }
                    
                    $scope.component.options[item.id]['nicename'] = ($scope.component.options[item.id]['nicename'] && $scope.component.options[item.id]['nicename'].trim() !== '') ? $scope.component.options[item.id]['nicename'] : niceName;
                    
                    $scope.component.options[item.id]['ct_parent'] = item.options['ct_parent']

                    if(parseInt(item.id) < 100000) {

                        if ( item.name != "oxy_header_left" && item.name != "oxy_header_right" && item.name != "oxy_header_center" && item.name != "ct_if_wrap" && item.name != "ct_else_wrap") {
                            var removeIcon = '<img src="'+CtBuilderAjax.oxyFrameworkURI + '/toolbar/UI/oxygen-icons/structure-pane/delete.svg" class="" title="Remove Component" ng-click="removeComponentWithUndo('+id+',\''+name+'\''+','+item.options['ct_parent']+')"/>';
                        }
                        else {
                            var removeIcon = '';
                        }

                        if (CtBuilderAjax.userEditOnly=="true" && !$scope.isElementEnabledForUser(item.name)) {
                            removeIcon = '';
                        }

                        var categoriesList = false;

                        if(CtBuilderAjax.componentCategories && CtBuilderAjax.componentCategories.length > 0) {
                            categoriesList = '';
                           

                            for(key in CtBuilderAjax.componentCategories) { 
                                categoriesList += '<li ng-class="{\'active\': component.options[component.active.id][\'ct_category\'] === \''+CtBuilderAjax.componentCategories[key]+'\'}" ng-click="showCategorize=false; setComponentCategory('+item.id+', \''+CtBuilderAjax.componentCategories[key]+'\', $event)">'+CtBuilderAjax.componentCategories[key]+'</li>';
                            }

                        }

                        var showIcon = '<span ng-show="getOption(\'conditionspreview\','+id+')!==\'0\'" ng-click="setOptionModel(\'conditionspreview\', \'0\', '+id+',\''+name+'\''+'); parentScope.evalGlobalConditions('+id+'); parentScope.evalGlobalConditionsInList()" class="ct-always-hide-icon"><img src="' + CtBuilderAjax.oxyFrameworkURI + '/toolbar/UI/oxygen-icons/structure-pane/visible.svg" title="Always Hide"/></span>';
                        var hideIcon = '<span ng-show="getOption(\'conditionspreview\','+id+')===\'0\'" ng-click="setOptionModel(\'conditionspreview\', \'2\', '+id+',\''+name+'\''+'); parentScope.evalGlobalConditions('+id+'); parentScope.evalGlobalConditionsInList()" class="ct-always-show-icon"><img src="' + CtBuilderAjax.oxyFrameworkURI + '/toolbar/UI/oxygen-icons/structure-pane/visible.svg" title="Always Show"/></span>';
                        
                        var showCategorize = angular.element('body').hasClass('ct_connection_active') && categoriesList && (parseInt(item.options.ct_parent) === 0 || parseInt(item.options.ct_parent) > 100000);
                        
                        if ( item.id != 99999 && item.options.oxy_builtin != 'true' ) {

                            nodeOptions = '<div class="ct-node-options">'+
                                                showIcon + hideIcon +
                                                '<img src="' + CtBuilderAjax.oxyFrameworkURI + '/toolbar/UI/oxygen-icons/structure-pane/edit.svg" class="ct-more-options-icon" ng-click="showCategorize=false"/>'+
                                                removeIcon +
                                                '<div class="ct-more-options-container">'+
                                                    '<div class="ct-more-options">'+
                                                        '<ul ng-show="showCategorize!=true">'+
                                                            reUsable+
                                                            copyToBlock+
                                                            duplicate+
                                                            wrapWithDiv+
                                                            '<li ng-click="setEditableFriendlyName('+id+')">'+
                                                                'Rename'+
                                                            '</li>' + ( showCategorize ?
                                                            '<li ng-click="showCategorize=true">' +
                                                                'Categorize' +
                                                            '</li>': '') +
                                                        '</ul>'+ (showCategorize ? '<ul ng-show="showCategorize==true"><li ng-click="showCategorize=false">< Back</li>'+categoriesList+'</ul>': '')+
                                                    '</div>'+
                                                '</div>'+
                                            '</div>';

                            nodeOptions += '<div class="ct-node-options-none-hovered">'+
                                            '<span class="ct-always-show-icon" ng-show="getOption(\'conditionspreview\','+id+')===\'0\'"><img src="' + CtBuilderAjax.oxyFrameworkURI + '/toolbar/UI/oxygen-icons/structure-pane/visible.svg" title="Always Show"/></span>' +
                                            '</div>';
                        }

                        DOMTree.HTML += 
                                '<div ng-attr-tree-actual-id="' + item.actualID + '" ng-attr-tree-id="' + item.id + '"' + ngClassNode + ' id="ct-dom-tree-node-' + item.id + '" class="ct-dom-tree-node ' + classes + '" '+apiData+' ng-Style="parentScope.isActiveId('+id+')?{zIndex: 9999}:{}">' +
                                    
                                    '<div class="ct-dom-tree-bottom-dash-cover"></div>'+
                                    '<div class="ct-dom-tree-left-dash-cover"></div>'+

                                    '<div ng-attr-node-id="' + id + 
                                        '" class="ct-dom-tree-node-anchor ct-dom-tree-node-type-general ct-dom-tree-name"' + 
                                        ngClassAnchor + activate + '>'+                                 
                                        '<div class="ct-dom-tree-node-header" '+
                                            'ng-class="{\'ct-handle\': editableFriendlyName!='+id+(showCategorize?', \'categorized-component\': component.options[\''+item.id+'\'][\'ct_category\']':'')+'}">'+
                                            '<div class="ct-expand-butt" ng-click="toggleNode('+id+',$event)">'+
                                                '<span class="ct-icon"></span>'+
                                            '</div>'+
                                            '<span class="ct-icon ct-node-type-icon"></span>' +
                                            '<i class="ct-icon-if-conditions" ng-if="(component.options['+id+'][\'model\'][\'globalconditions\'] && component.options['+id+'][\'model\'][\'globalconditions\'].length > 0) || component.options['+id+'][\'model\'][\'conditionspreview\'] === \'0\'"></i>'+                                            
                                            '<span class="ct-nicename" ng-if="editableFriendlyName!='+id+'" ng-bind="component.options[\''+item.id+'\'][\'nicename\']" ></span>' + 
                                            '<span class="ct-nicename ct-nicename-editing" ng-blur="setEditableFriendlyName(0)" ng-change="updateFriendlyName('+id+')" ng-if="editableFriendlyName=='+id+'" contenteditable="true" data-plaintext="true" data-defaulttext="'+ niceName +'" ng-Model="component.options[\''+item.id+'\'][\'nicename\']" focus-me="true" ></span>' + 
                                            nodeOptions +
                                        '</div>'+
                                        '<div class="ct-dom-tree-horizontal-dash"></div>'+
                                    '</div>';
                    }
                    // go deeper in Components Tree
                    if ( item.children ) {
                        if (item.name == "oxy_icon_box" && parseInt(item.id) < 100000){
                            console.log(item.id)
                            item = angular.copy(item);
                            var buttonArea = angular.copy(item);
                            buttonArea.name = "oxy_icon_box_button_area";
                            buttonArea.actualID = buttonArea.id;
                            buttonArea.id = 99999;
                            buttonArea.children.splice(0,1);
                            item.children.splice(1, 0, buttonArea);
                            item.children.length = 2;
                        }
                        $scope.generateDOMTreeNavigator(item, undefined, DOMTree, true); //this last true signifies, that its a child tree
                    }
                    if(parseInt(item.id) < 100000)
                        DOMTree.HTML += "</div>";

                    if ( undefined === searchId ) {
                        $scope.componentsTreeNavigator = DOMTree.HTML;
                    }

                    //  lets use the component options to store friendly editable names
                   
                    //  if the nicename exists in component tree for this item, load it from there
                    $scope.findComponentItem($scope.componentsTree.children, item.id, $scope.loadComponentNiceName);
                }
                // go deeper in Components Tree
                else {
                    if ( item.children ) {
                        $scope.generateDOMTreeNavigator(item, searchId, DOMTree);
                    }
                }

                if (item.id == searchId) {

                    // stop forEach
                    isBrake = true;
                    
                    // remove old node
                    $scope.removeDOMTreeNavigatorNode(item.id);

                    // get parent node to insert
                    
                    var parentId = item.options['ct_parent'] < 100000 ? item.options['ct_parent'] : 0;

                    // set innner content id as parent
                    if (parentId===0&&$scope.innerContentRoot&&$scope.innerContentRoot.id) {
                        parentId = $scope.innerContentRoot.id;
                    }

                    var parentNode = window.parent.document.getElementById("ct-dom-tree-node-"+parentId);
                    
                    parentNode = angular.element(parentNode);

                    // compile and insert HTML to DOM Tree navigator

                    $scope.cleanInsert(DOMTree.HTML, parentNode, index+3, reorder);

                    // reload sortable
                    jQuery(parentNode).removeClass("ct-dom-tree-no-children");
                }
            }
        });
    }


    /**
     * Update DOM Tree Navigator
     * 
     * @since 0.1.5
     */

    $scope.updateDOMTreeNavigator = function(id, reorder) {
        
        // get container to insert
        var navigatorContainer = window.parent.document.getElementById("ct-dom-tree");

        if ( !navigatorContainer ) {
            return false;
        }

        if ( !$parentScope.showSidePanel ) {
            //return false;
        }

        if ($scope.log) {
            console.log("updateDOMTreeNavigator()", id);
        }
        $scope.functionStart("updateDOMTreeNavigator");

        var DOMTree = {};
            DOMTree.HTML = "";

        var activate    = ' ng-mousedown="triggerCodeMirrorBlur(0);" ng-click="activateComponent(0, \'root\')"',
            ngClass     = ' ng-class="{\'ct-dom-tree-node-selected\' : parentScope.isActiveId(0)}"';

        var rootID = 0;

        // set innner content id as root
        if ($scope.innerContentRoot&&$scope.innerContentRoot.id) {
            rootID = $scope.innerContentRoot.id;
        }

        // init tree navigator
        var body = window.parent.document.getElementsByTagName("BODY")[0];
        var treeNavigatorHTML = 
            "<div id=\"ct-dom-tree-node-"+rootID+"\" ng-attr-tree-id=\""+rootID+"\" dragula=\"'ct-dom-tree'\" class=\"oxy-dom-tree-root ct-accept-drops ct-elements-managers-bottom\">"+
                "<span></span><span></span>"+ // fake elements for index offset
                "<div " + activate + ngClass + " ng-attr-tree-id=\""+rootID+"\" ng-click=\"activateComponent(0, 'root')\" class=\"ct-dom-tree-body-anchor ct-dom-tree-node-anchor ct-dom-tree-parent\">" +
                    "<span class=\"ct-icon ct-dom-parent-icon\"></span>" + (body.classList.contains('ct_inner')?"Inner Content":"Body") +
                "</div>";

        // create jqLite element and clear HTML
        if (undefined === id) {
            navigatorContainer = angular.element(navigatorContainer);
            navigatorContainer.empty();
        }
        
        // generate
        $scope.functionStart("generateDOMTreeNavigator");
        if(!reorder)
            $scope.generateDOMTreeNavigator($scope.componentsTree, id, DOMTree, false);
        $scope.functionEnd("generateDOMTreeNavigator");
        
        // close navigator
        treeNavigatorHTML += ( DOMTree.HTML + "</div></div>" );
        
        if (undefined === id) {
            // compile and insert HTML to DOM Tree navigator
            $scope.cleanInsert(treeNavigatorHTML, navigatorContainer);
        }

        $scope.functionEnd("updateDOMTreeNavigator");
    }
    

    /**
     * Remove single node from DOM Tree Navigator
     * 
     * @since 0.2.5
     */
    
    $scope.removeDOMTreeNavigatorNode = function(id) {

        if ($scope.log) {
            console.log("try removeDOMTreeNavigatorNode()",id);
        }

        // remove old node
        var node =  window.parent.document.getElementById("ct-dom-tree-node-"+id);
        node = angular.element(node);

        var parentNode = jQuery(node).parent();

        if (node.length > 0 && $scope.log) {
            console.log("DOMTreeNavigatorNode() removed", id, node);
        }
        
        node.remove();

        if ( node.scope() !== undefined ) {
            node.scope().$destroy();
        }
        
        node = null;     

        if ( jQuery(parentNode).children().length <= 3 ) { // there is always 3 children: anchor, dashed vertical and horizontla
            jQuery(parentNode).addClass("ct-dom-tree-no-children");
        }
    }


    /**
     * Toggle DOM Tree navigator node
     *
     * @since 0.1.5
     */

    $scope.toggleNode = function(id,$event) {

        // Fix for nested ng-click
        if (typeof $event != 'undefined') {
            angular.element("#ct-sidepanel").trigger($event.type);
            $event.stopPropagation();
        }

        // prevent toggle DOM node on drag&drop
        if ($scope.latestDroppedDOMParent==id) {
            $scope.latestDroppedDOMParent = null;
            return
        }
        
        /*var nodeName = $event.currentTarget,
            id = nodeName.attributes['ng-attr-node-id'].value;*/
        var element = angular.element('div[ng-attr-tree-id='+id+']', window.parent.document);

        if(typeof($scope.toggledNodes[id]) === 'undefined' && element.hasClass('ct-dom-tree-node-expanded')) {
            $scope.toggledNodes[id] = false;
            angular.element('div[ng-attr-tree-id='+id+']', window.parent.document).removeClass('ct-dom-tree-node-expanded');
        }
        else
            $scope.toggledNodes[id] = !$scope.toggledNodes[id];
    }


    /**
     * Expand all nodes in DOM Tree
     *
     * @since 0.3.0
     */

    $scope.expandAllNodes = function() {
        
        for(var id in $scope.component.options) { 
            if ($scope.component.options.hasOwnProperty(id)) {
                $scope.toggledNodes[id] = true;
            }
        }
    }


    /**
     * Collapse all nodes in DOM Tree
     *
     * @since 0.3.0
     */

    $scope.collapseAllNodes = function() {

        $scope.toggledNodes = [];
    }


    /**
     * Toggle all node parents and scroll DOM Tree to this element
     *
     * @since 0.3.0
     */

    $scope.highlightDOMNode = function(id, $event) {
        
        if ($scope.log) {
            console.log("highlightDOMNode()", id);
        }

        if(id > 100000)  { // this is a component in the outer template, while trying to edit inner content (no need to hilite this)
            return;
        }

        if ($parentScope.isShowTab('sidePanel','DOMTree') && $parentScope.showSidePanel) {
            // continue
        }
        else {
            return false;
        }

        if ($event) {
            $event.stopPropagation();
        }

        jQuery("#ct-dom-tree-node-"+id, window.parent.document).parents(".ct-dom-tree-node:not(.ct-dom-tree-node-expanded)").each(function(){
            var parentId = jQuery(this).attr("ng-attr-tree-id");
            $scope.toggledNodes[parentId] = true;
        });

        var timeout = $timeout(function() {
            
            var rootID = 0;
            // set innner content id as root
            if ($scope.innerContentRoot&&$scope.innerContentRoot.id) {
                rootID = $scope.innerContentRoot.id;
            }
            
            var container           = jQuery("#ct-dom-tree-node-"+rootID, window.parent.document),
                containerScrollTop  = container.scrollTop(),
                target              = jQuery("#ct-dom-tree-node-"+id, window.parent.document);

            if (!target.offset()) {
                return;
            }

            // scroll back to default top 0
            container.scrollTop(0);

            // get target offset
            var targetOffsetTop = target.offset().top;

            // scroll back to saved position
            container.scrollTop(containerScrollTop);

            // finally animate
            container.stop().animate({
                scrollTop: targetOffsetTop - container.offset().top - 120
            }, 500);
            
            // cancel timeout
            $timeout.cancel(timeout);
        }, 500, false);
    }


    $scope.openLoadFolder = function(id, name, showDesignSets, $event) {
        
        if($scope.experimental_components[name]['fresh']) { // if it is the first time
            
            if(showDesignSets) {
                angular.element($event.target).addClass('oxygen-small-progress');
            }

            $scope.getComponentsListFromSource(id, name, 
                
                function(id) { 

                    if(showDesignSets) {
                        $parentScope.tabs['components']=[]; 
                        angular.element($event.target).removeClass('oxygen-small-progress');
                    }

                    $scope.openFolder(id, name); 
                    $parentScope.applyMenuAim();
                }
            )
        }
        else {
            
            if(showDesignSets) {
                $parentScope.tabs['components']=[]; 
            }

            $scope.openFolder(id, name);
        }

    }

    /**
     * Show folder's content by its id
     * 
     * @since 0.4.0
     * @author Ilya K.
     */
    
    $scope.openFolder = function(id, name) {

        $scope.closeAllFolders();
        
        if(typeof(name) === 'undefined') {
            $scope.openFolders[id] = true;
        }
        else {
            $scope.openFolders[id] = name;   
        }

        if(id === 'categories-categories') {
            // make an ajax call to load components from all the source sites
            if(typeof($scope.libraryCategories) === 'undefined' || $scope.libraryCategories === null) {
                $scope.libraryCategories = {};
                $scope.libraryPages = {};
                $scope.getStuffFromSource($scope.processLibraryStuff);
            }
        }
        
        var timeout = $timeout(function() {
            jQuery(".oxygen-folder-"+id+" .ct-add-item-button-image", window.parent.document).each( function() {
                jQuery(this).attr("src",jQuery(this).data("src")); 
            });
        }, 0, false);

        $parentScope.applyMenuAim();
    }


    $scope.processLibraryStuff = function(data) {
        
        var data = JSON.parse(data);

        var items = data['items'];
        var key = data['key'];
        var next = parseInt(data['next']);

        if(items) {
            var components = items['components']; // deal with pages later

            if(components) {
                _.each(components, function(item) {

                    var category = item['category'];
                    
                    if(typeof(category) === 'undefined') {
                        category = 'Other'
                    }

                    $scope.libraryCategories[category] = $scope.libraryCategories[category] || {};
                    $scope.libraryCategories[category]['slug'] = btoa(category).replace(/=/g, '');
                    $scope.libraryCategories[category]['contents'] = $scope.libraryCategories[category]['contents'] || [];
                    $scope.libraryCategories[category]['contents'].push(item);

                });
            }

            var pages = items['pages']; // deal with pages later

            if(pages) {
                _.each(pages, function(item) {

                    if(item['type'] !== 'ct_template') {

                        var category = item['category'];
                        
                        if(typeof(category) === 'undefined') {
                            category = 'Other'
                        }

                        $scope.libraryPages[category] = $scope.libraryPages[category] || {};
                        $scope.libraryPages[category]['slug'] = btoa(category).replace(/=/g, '');
                        $scope.libraryPages[category]['contents'] = $scope.libraryPages[category]['contents'] || [];

                        $scope.libraryPages[category]['contents'].push(item);
                    }

                });
            }
        }


        $scope.getStuffFromSource($scope.processLibraryStuff, next);

        $parentScope.applyMenuAim();        

    }

    /**
     * Close all folders
     * 
     * @since 0.4.0
     * @author Ilya K.
     */
    
    $scope.closeAllFolders = function(id) {

        $scope.openFolders = {};
    }


    /**
     * Check if folder open
     * 
     * @since 0.4.0
     * @author Ilya K.
     */
    
    $scope.isShowFolder = function(id) {
        
        return ( $scope.openFolders[id] ) ? true : false;
    }


    /**
     * Check if has any open folder
     * 
     * @since 2.0
     * @author Ilya K.
     */
    
    $scope.hasOpenFolders = function() {

        return ( Object.keys($scope.openFolders).length > 0 ) ? true : false;
    }

});