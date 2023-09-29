CTFrontendBuilderUI.controller("ControllerDragnDrop", function($scope, $timeout, dragulaService) {

	var dragBottomBubble = null,
        expandTimeout = null,
        setExpandTrigger = 0;
    
    /**
     * Set options
     */

    var navHash;

    var hashCode = function(value) {
        var hash = 0;
        if (value.length == 0) return hash;
        for (i = 0; i < value.length; i++) {
            char = value.charCodeAt(i);
            hash = ((hash<<5)-hash)+char;
            hash = hash & hash; // Convert to 32bit integer
        }
        return hash;
    } 

    $scope.$parent.$on('iframe-init', function(e, iframeScope) {

        if (CtBuilderAjax.userCanFullAccess!="true"&&CtBuilderAjax.userCanDragNDrop!="true") {
            return;
        }

        dragulaService.options($scope.iframeScope, 'ct-dom-tree', {
            
            isContainer: function (el) {
                
                //console.log('isContainer', el, dragBottomBubble);
                if ( el.classList.contains('ct-accept-drops') && dragBottomBubble ) {
                    return true;
                }
                else if ( el.classList.contains('ct-draggable') ) {
                    dragBottomBubble = true;
                    return false;
                }
                else {
                    return false;
                }

            },

            moves: function(el, ctn, target) {

                //console.log(el, ctn, target);
                
                dragBottomBubble = null;

                // don't let the div inside a repeater be moved around
                if(ctn.classList.contains('ct-sortable-dynamic-list') && el.classList.contains('ct-sortable-div-block')) {
                    return false;
                }

                // don't let drag out the last header row from header
                if (ctn.classList.contains('ct-sortable-header')&&jQuery(ctn).children().length < 5)
                    return false;

                // jQuery closest is used to make sure that clicking on any child element of the .ct-handle such as text, icons initiates the drag as well
                return target.classList.contains('ct-draggable') || jQuery(target).closest('.ct-handle').length > 0; 

            },

            accepts: function (el, target, source, sibling) {

                //console.log(el);
                
                var elId = parseInt(el.getAttribute('ng-attr-tree-id'));

                // collapse the child elements hierarchy
                // if($scope.iframeScope.toggledNodes[elId])
                //     $scope.iframeScope.toggledNodes[elId] = false;


                // if the target is no more an expandable one, clear the expand timeout
                if(expandTimeout && parseInt(target.getAttribute('ng-attr-tree-id')) > 0 && parseInt(target.getAttribute('ng-attr-tree-id')) !== setExpandTrigger) {
                    clearTimeout(expandTimeout);
                    expandTimeout = false;
                    setExpandTrigger = 0;

                }

                // Here decision is made to accept the drop inside a nestable container as a child or a sibling
                if(target.classList.contains('ct-accept-drops') && parseInt(target.getAttribute('ng-attr-tree-id')) > 0) {
                    
                    var targetId = parseInt(target.getAttribute('ng-attr-tree-id'));
                    var targetAnchor = jQuery('#ct-dom-tree-node-'+targetId+' >.ct-dom-tree-node-anchor');
                    
                    // if the target node is not expanded, expand it.
                    if(setExpandTrigger === 0 && !$scope.iframeScope.toggledNodes[targetId] && targetId !== parseInt(el.getAttribute('ng-attr-tree-id'))) {
                        
                        setExpandTrigger = targetId;

                        expandTimeout = setTimeout(function() {
                                targetAnchor.find('.ct-expand-butt').trigger('click');
                                setExpandTrigger = 0;
                            }, 1000);

                    }

                    // test if the drop is intended to be a child
                    var targetX = jQuery(target).offset().left;
                    var ghostX = jQuery('.gu-mirror').offset().left;
                    if(ghostX - targetX < 16)
                        return false;
                }

                //console.log(el, target, source, sibling);
                //console.log(angular.element(target).closest('.ct-sortable-section').length);
                
                // don't allow to insert before anchor
                if( sibling && sibling.classList.contains('ct-dom-tree-node-anchor') ) {
                    return false;
                }

                // don't allow to insert 'li' to any components but 'ul'
                if( el.classList.contains('ct-sortable-li') && !target.classList.contains('ct-sortable-ul') ) {
                    return false;
                }

                // do not allow to insert anything under a repeater directly unless its a div 
                if( target.classList.contains('ct-sortable-dynamic-list') && !el.classList.contains('ct-sortable-div-block')) {
                    return false;
                }

                // do not allow to insert a second element directly under the repeater
                if( target.classList.contains('ct-sortable-dynamic-list') && el.classList.contains('ct-sortable-div-block')) {
                    var targetId = parseInt(target.getAttribute('ng-attr-tree-id'));
                    var element = iframeScope.getComponentById(targetId);
                    if(element.length > 0 && element.children().length > 0) {
                        return false;
                    }
                }

                // don't allow to insert any components to 'ul' but 'li'
                if( !el.classList.contains('ct-sortable-li') && target.classList.contains('ct-sortable-ul') ) {
                    return false;
                }

                // don't allow to insert 'slide' to any components but 'slider'
                if( el.classList.contains('ct-sortable-slide') && !target.classList.contains('ct-sortable-slider') ) {
                    return false;
                }

                // don't allow to insert any components to 'slider' but 'slide'
                if( !el.classList.contains('ct-sortable-slide') && target.classList.contains('ct-sortable-slider') ) {
                    return false;
                }
                
                // don't allow to insert 'column' to any components but 'columns'
                if( el.classList.contains('ct-sortable-column') && !target.classList.contains('ct-sortable-columns') ) {
                    return false;
                }

                // don't allow to insert any components to 'columns' but 'column'
                if( !el.classList.contains('ct-sortable-column') && target.classList.contains('ct-sortable-columns') ) {
                    return false;
                }

                // don't allow to insert any components to 'new columns' but 'div'
                if( !el.classList.contains('ct-sortable-div-block') && target.classList.contains('ct-sortable-new-columns') ) {
                    return false;
                }

                // don't allow to insert any components to 'tabs' but 'tab'
                if( !el.classList.contains('ct-sortable-tab') && target.classList.contains('ct-sortable-tabs') ) {
                    return false;
                }

                // don't allow to insert any components to and 'tabs_contents' but 'tab_content'
                if( !el.classList.contains('ct-sortable-tab-content') && target.classList.contains('ct-sortable-tabs-contents') ) {
                    return false;
                }

                // don't allow to insert 'tabs' components to 'tabs'
                if( (el.classList.contains('ct-sortable-tabs')||el.classList.contains('ct-sortable-tabs-contents')) && 
                    (jQuery(target).parents('.ct-sortable-tabs').length||jQuery(target).parents('.ct-sortable-tabs-contents').length) ) {
                    return false;
                }

                // don't allow to insert 'tab' component to anything but 'tabs'
                if( el.classList.contains('ct-sortable-tab') && !target.classList.contains('ct-sortable-tabs') ) {
                    return false;
                }

                // don't allow to insert 'tab-content' component to anything but 'tabs-contents'
                if( el.classList.contains('ct-sortable-tab-content') && !target.classList.contains('ct-sortable-tabs-contents') ) {
                    return false;
                }

                // don't allow to insert 'grid-cell' to any components but 'grid'
                if( el.classList.contains('ct-sortable-grid-cell') && !target.classList.contains('ct-sortable-grid') ) {
                    return false;
                }

                // don't allow to insert any components to 'grid' but 'grid-cell'
                if( !el.classList.contains('ct-sortable-grid-cell') && target.classList.contains('ct-sortable-grid') ) {
                    return false;
                }

                // don't allow to insert 'section' inside any other 'section'
                if( el.classList.contains('ct-sortable-section') && angular.element(target).closest('.ct-sortable-section').length > 0 ) {
                    return false;
                }

                // don't allow to insert 'section' inside 'header builder'
                if( el.classList.contains('ct-sortable-section') && angular.element(target).closest('.ct-sortable-header').length > 0 ) {
                    return false;
                }

                // don't allow to insert 'section' inside 'slider'
                if( el.classList.contains('ct-sortable-section') && angular.element(target).closest('.ct-sortable-slider').length > 0 ) {
                    return false;
                }

                // don't allow to insert 'header builder' inside any other 'section'
                if( el.classList.contains('ct-sortable-header') && angular.element(target).closest('.ct-sortable-section').length > 0 ) {
                    return false;
                }

                // don't allow to insert 'header builder' inside 'header builder'
                if( el.classList.contains('ct-sortable-header') && angular.element(target).closest('.ct-sortable-header').length > 0 ) {
                    return false;
                }

                // don't allow to insert 'link wrapper' inside any other 'link wrapper'
                if( el.classList.contains('ct-sortable-link') && angular.element(target).closest('.ct-sortable-link').length > 0 ) {
                    return false;
                }

                // don't allow to insert 'text link' inside any other 'link wrapper'
                if( el.classList.contains('ct-sortable-link-text') && angular.element(target).closest('.ct-sortable-link').length > 0 ) {
                    return false;
                }

                if( target.classList.contains('gu-transit') || !target.classList.contains('ct-accept-drops')) {
                    return false;
                }

                // don't allow to insert any components to 'header' but 'header row'
                if( !el.classList.contains('ct-sortable-header-row') && target.classList.contains('ct-sortable-header') ) {
                    return false;
                }

                // don't allow to insert 'header_row' to any component but 'header'
                if( el.classList.contains('ct-sortable-header-row') && !target.classList.contains('ct-sortable-header') ) {
                    return false;
                }

                // check API components for any parent/child restrictions
                var elName = jQuery(el).attr('data-component-name'),
                    targetName = jQuery(target).attr('data-component-name');
                if (!$scope.iframeScope.canBeChild(targetName, elName)) {
                    return false;
                }

                // don't allow to insert any component inside itself at any deep
                if( angular.element(target).closest('[data-component-name="'+elName+'"]').length > 0 ) {
                    // any other components that can be inserted to itself?
                    // allow to extend this list with API components?
                    var exceptions = ['ct_div_block'];
                    if (exceptions.indexOf(elName) < 0 ) {
                        return false;
                    }
                }

                var componentId         = jQuery(el).attr("ng-attr-tree-id"),
                    component           = $scope.iframeScope.getComponentById(componentId),
                    targetComponentId   = jQuery(target).attr("ng-attr-tree-id"),
                    targetComponent     = $scope.iframeScope.getComponentById(targetComponentId);

                // don't allow to insert any component with 'section' inside any other component inside 'section' except the Inner Content
                if( !jQuery(component).is(".ct-inner-content") && jQuery(component).find(".ct-section").length > 0 && jQuery(targetComponent).closest(".ct-section").length > 0 ) {
                   return false;
                }

                // don't allow to insert any component with 'link wraper/text link' inside any other component inside 'link wrapper'
                if( jQuery(component).find(".ct-link").length > 0 && jQuery(targetComponent).closest(".ct-link").length > 0 ) {
                   return false;
                }
                if( jQuery(component).find(".ct-link-text").length > 0 && jQuery(targetComponent).closest(".ct-link").length > 0 ) {
                   return false;
                }

                // don't allow to insert modals inside any other modal
                if( el.classList.contains('ct-sortable-modal') && angular.element(target).closest('.ct-sortable-modal').length > 0 ) {
                    return false;
                }
                
                return true;
            },
            revertOnSpill: true,
            mirrorContainer: document.body

        });


        /**
         * Drop Event
         */
        
        $scope.iframeScope.$on('ct-dom-tree.drop', function (e, el, endParent, startParent) {

            // fix offset
        	var newKey          = el.index() - 3,
                startParentId   = startParent[0].attributes['ng-attr-tree-id'].value,
                endParentId     = endParent[0].attributes['ng-attr-tree-id'].value;

            // save to prevent toggle DOM node on drag&drop
            $scope.iframeScope.latestDroppedDOMParent = endParentId;

            // make changes to Components Tree
            $scope.iframeScope.componentsReorder(el, newKey, startParentId, endParentId, startParent, endParent);

            
            // if the receiving item is an oxy list or has an ancestor that is an oxy list, rebuild the whole oxy list
            var component = $scope.iframeScope.getComponentById(endParentId);
            var oxyList = component.closest('.oxy-dynamic-list');
            if(oxyList.length > 0) {
                $scope.iframeScope.updateRepeaterQuery(parseInt(oxyList.attr('ng-attr-component-id')))
            }
            
            // expand new parent after drop
            jQuery(endParent).removeClass("ct-dom-tree-no-children");

            // collapse old parent if no children left
            if ( jQuery(startParent).children().length <= 3 ) { // there is always 3 children: anchor, dashed vertical and horizontla
                jQuery(startParent).addClass("ct-dom-tree-no-children");
            }

            $scope.iframeScope.updateDOMTreeNavigator(el[0].attributes['ng-attr-tree-id'].value);

            $scope.iframeScope.adjustResizeBox();

        });

        $scope.iframeScope.$on('ct-dom-tree.drag', function(e, item, source) {
            var rootID = 0;
            // set innner content id as root
            if ($scope.iframeScope.innerContentRoot&&$scope.iframeScope.innerContentRoot.id) {
                rootID = $scope.iframeScope.innerContentRoot.id;
            }
            navHash = hashCode(angular.element('div#ct-dom-tree-node-'+rootID).html());
        });

        $scope.iframeScope.$on('ct-dom-tree.cancel', function(e, item, source) {
            var id = item[0].attributes['ng-attr-tree-id'].value;
            var parentId = angular.element(item).parent().closest('.ct-dom-tree-node').attr('ng-attr-tree-id');

            var rootID = 0;
            // set innner content id as root
            if ($scope.iframeScope.innerContentRoot&&$scope.iframeScope.innerContentRoot.id) {
                rootID = $scope.iframeScope.innerContentRoot.id;
            }

            if(navHash !== hashCode(jQuery('div#ct-dom-tree-node-'+rootID).html())) {
                if(parentId > 0)
                    $scope.iframeScope.updateDOMTreeNavigator(parentId);
                else
                    $scope.iframeScope.updateDOMTreeNavigator();
            }

            // var item = $scope.iframeScope.findComponentItem($scope.iframeScope.componentsTree.children, id, $scope.iframeScope.getComponentItem);

            // if(parentId !== item.options['ct_parent']) {

            //     if(item.options['ct_parent'] > 0)
            //         $scope.iframeScope.updateDOMTreeNavigator(item.options['ct_parent']);
            //     else
            //         $scope.iframeScope.updateDOMTreeNavigator();

            // }
        });


        /**
         * Drag end event
         */

        $scope.iframeScope.$on('ct-dom-tree.dragend', function (e, el) {
            
            dragBottomBubble = null;
        });

    });

})