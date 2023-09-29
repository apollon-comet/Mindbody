<div id="ct-sidepanel" class="ct-panel-elements-managers" ng-show="showSidePanel" ng-class="{'ct-sidepanel-show':showSidePanel}">
	<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" style="position: absolute; width: 0; height: 0; overflow: hidden;" version="1.1">
		<defs>
			<symbol id="oxy-icon-cross" viewBox="0 0 32 32">
				<title>cross</title>
				<path d="M31.708 25.708c-0-0-0-0-0-0l-9.708-9.708 9.708-9.708c0-0 0-0 0-0 0.105-0.105 0.18-0.227 0.229-0.357 0.133-0.356 0.057-0.771-0.229-1.057l-4.586-4.586c-0.286-0.286-0.702-0.361-1.057-0.229-0.13 0.048-0.252 0.124-0.357 0.228 0 0-0 0-0 0l-9.708 9.708-9.708-9.708c-0-0-0-0-0-0-0.105-0.104-0.227-0.18-0.357-0.228-0.356-0.133-0.771-0.057-1.057 0.229l-4.586 4.586c-0.286 0.286-0.361 0.702-0.229 1.057 0.049 0.13 0.124 0.252 0.229 0.357 0 0 0 0 0 0l9.708 9.708-9.708 9.708c-0 0-0 0-0 0-0.104 0.105-0.18 0.227-0.229 0.357-0.133 0.355-0.057 0.771 0.229 1.057l4.586 4.586c0.286 0.286 0.702 0.361 1.057 0.229 0.13-0.049 0.252-0.124 0.357-0.229 0-0 0-0 0-0l9.708-9.708 9.708 9.708c0 0 0 0 0 0 0.105 0.105 0.227 0.18 0.357 0.229 0.356 0.133 0.771 0.057 1.057-0.229l4.586-4.586c0.286-0.286 0.362-0.702 0.229-1.057-0.049-0.13-0.124-0.252-0.229-0.357z"></path>
			</symbol>
		</defs>
	</svg>
	<div class="ct-elements-managers-body">
		
		<div class="ct-tab-panel ct-dom-tree-tab ct-active" ng-show="isShowTab('sidePanel','DOMTree')">
			<div class="oxygen-sidepanel-header-row">
				<?php _e("Structure","oxygen"); ?>
				<svg class="oxygen-close-icon"
					ng-click="switchTab('sidePanel','DOMTree')"><use xlink:href="#oxy-icon-cross"></use></svg>
			</div>

			<div class="ct-elements-managers-top clearfix">
				<div class="ct-elements-managers-top-item ct-button ct-butt-expand-all"
					ng-click="iframeScope.expandAllNodes()">
					<?php _e("Expand All", "component-theme"); ?>
				</div>
				<div class="ct-elements-managers-top-item ct-button ct-butt-collapse-all"
					ng-click="iframeScope.collapseAllNodes()">
					<?php _e("Collapse All", "component-theme"); ?>
				</div>
			</div>
			<div id="ct-dom-tree">
				<!-- Content added by Angular -->
			</div>
		</div>
		<!-- .ct-dom-tree-tab -->

		<div class="ct-tab-panel ct-history-tab ct-active" ng-show="isShowTab('sidePanel','History')">
			<div class="oxygen-sidepanel-header-row">
				<?php _e("History","oxygen"); ?>
				<svg class="oxygen-close-icon" ng-click="switchTab('sidePanel','History')">
                    <use xlink:href="#oxy-icon-cross"></use>
                </svg>
			</div>

			<div class="ct-elements-managers-top clearfix" ng-show="iframeScope.canUndo || iframeScope.canRedo">
				<div class="ct-elements-managers-top-item ct-button"
					ng-click="iframeScope.undoManager.clear()">
					<?php _e("Clear All", "component-theme"); ?>
				</div>
			</div>
			<div id="ct-history">
                <div class="ct-history-empty" ng-hide="iframeScope.canUndo || iframeScope.canRedo">
                    <span>There are no undo points</span>
                </div>
				
                <div class="ct-history-items" ng-show="iframeScope.canUndo || iframeScope.canRedo">
                    <div class="ct-history-item" ng-class="{'active': iframeScope.firstHistoryItem.active}" ng-click="iframeScope.doFirstHistoryItem()">
                        <span>{{iframeScope.firstHistoryItem.name}}</span><time>{{iframeScope.firstHistoryItem.time}}</time>
                    </div>
                    <div class="ct-history-item" ng-repeat="historyItem in iframeScope.historyItems" ng-class="{'active': historyItem.active}" ng-click="iframeScope.undoManager.do(historyItem.index)">
                        <span>{{historyItem.name}}</span><time>{{historyItem.time}}</time>
                    </div>
                </div>
			</div>
		</div>
		<!-- .ct-dom-tree-tab -->

		<div class="ct-tab-panel ct-style-sheets-tab" ng-if="isShowTab('sidePanel','styleSheets')">

			<div class="oxygen-sidepanel-header-row">
				<?php _e("Stylesheets","oxygen"); ?>
				<svg class="oxygen-close-icon"
					ng-click="switchTab('sidePanel','styleSheets')"><use xlink:href="#oxy-icon-cross"></use></svg>
			</div>

			<div class="ct-elements-managers-top clearfix">
				<div class="ct-elements-managers-top-item">
					<div class="ct-elements-managers-buttons">
						<div class="ct-button ct-icon-right"
							ng-click="iframeScope.addStyleSheet()">
							<?php _e("Add Stylesheet", "component-theme"); ?>
							<span class="ct-icon ct-plus-icon"></span>
						</div>
					</div>
				</div>
				<div class="ct-elements-managers-top-item">
					<div class="ct-elements-managers-buttons">
						<div class="ct-button ct-icon-right"
							ng-click="iframeScope.addStyleSheet(true)">
							<?php _e("Add Folder", "component-theme"); ?>
							<span class="ct-icon ct-plus-icon"></span>
						</div>
					</div>
				</div>
			</div>

			<div class="ct-elements-managers-bottom ct-elements-managers-stylesheets">

				<script type="text/ng-template" id="styleSheet">
					{{stylesheet.name}}
				    <div class="ct-node-options">
        				<span class="ct-node-details"
        					ng-if="iframeScope.isDev()"
        					ng-click="iframeScope.showStyleSheetDialog(stylesheet);" >
        					Add to Design Set
        				</span>
						<span class="ct-icon ct-cssjs-icon"
							ng-click="iframeScope.setStyleSheetToEdit(stylesheet)"
							title="<?php _e("Highlight selector", "component-theme"); ?>">
							</span>

						<!-- <span class="ct-icon ct-copy-item-icon"></span> -->
						<span class="ct-icon ct-delete-icon"
							ng-click="iframeScope.deleteStyleSheet(stylesheet,$event)"
							title="<?php _e("Delete stylesheet", "component-theme"); ?>">
							</span>
						
					</div>
				</script>

				<script type="text/ng-template" id="cssFolderMenu">
					{{folder.name}}
				    <div class="ct-node-options">
						
						<span class="ct-icon ct-settings-icon"
							ng-click="iframeScope.cssFolderMenuOpen = (iframeScope.cssFolderMenuOpen == folder.id ? false : folder.id)">
						</span>

						<div ng-show="iframeScope.cssFolderMenuOpen == folder.id" class="ct-css-folder-menu">
							<ul>
								<li ng-click='iframeScope.toggleCSSFolder(folder)'>{{folder.status === 1 ? 'Disable': 'Enable' }}</li>
								<li ng-click="iframeScope.deleteStyleSheet(folder,$event)"><?php _e("Delete Folder", "component-theme"); ?></li>
							</ul>	
						</div>
					</div>
					<div class="ct-expand-butt" ng-show="(iframeScope.styleSheets | filter : { folder: '!'} | filter: iframeScope.equalsTo('parent', folder.id)).length > 0" ng-click="iframeScope.expandedFolder[folder.id]=!iframeScope.expandedFolder[folder.id]"><span class="ct-icon"></span></div>
				</script>

				<h3 class="ct-css-section-label">Enabled</h3>
				<!--uncategorized folder-->
				<div>

					<div class="ct-css-node-header ct-css-node-folder ct-node-options-active"
						ng-class="{'ct-css-node-hilite': iframeScope.selectedNodeType=='cssfolder' && iframeScope.selectedCSSFolder===null, 'ct-css-node-folder-menu': iframeScope.cssFolderMenuOpen === null, 'ct-style-set-expanded':iframeScope.expandedFolder[0]}"
						ng-click="iframeScope.setActiveCSSFolder(null); iframeScope.setSelectedCSSFolder(null)"> 
						Uncategorized
						<div class="ct-node-options">
						
							<span class="ct-icon ct-settings-icon"
								ng-click="iframeScope.cssFolderMenuOpen = (iframeScope.cssFolderMenuOpen === null ? false : null)">
							</span>

							<div ng-show="iframeScope.cssFolderMenuOpen === null" class="ct-css-folder-menu">
								<ul>
									<li ng-click='iframeScope.toggleUncategorizedStyleSheets(true); iframeScope.cssFolderMenuOpen = false; iframeScope.classesCached = false; iframeScope.outputCSSOptions()'>Disable</li>
								</ul>	
							</div>
						</div>
						<div ng-show="(iframeScope.styleSheets | filter : { folder: '!', parent: 0 }).length > 0" class="ct-expand-butt" ng-click="iframeScope.expandedFolder[0]=!iframeScope.expandedFolder[0]"><span class="ct-icon"></span></div>
					</div>
					<div class="ct-style-set-child-selector" ng-show="iframeScope.expandedFolder[0]">
						<div class="ct-css-node-header ct-node-options-active ct-css-node-stylesheet"
							ng-repeat="stylesheet in iframeScope.styleSheets | filter : { folder: '!', parent: 0 } track by stylesheet.id" ng-include="'styleSheet'"
							ng-click="iframeScope.setSelectedCSSFolder(null); iframeScope.setStyleSheetToEdit(stylesheet)"
							ng-class="{'ct-css-node-hilite': iframeScope.selectedNodeType=='stylesheet' && iframeScope.stylesheetToEdit.id===stylesheet.id, 'ct-css-node-folder-menu': iframeScope.cssFolderMenuOpen === stylesheet.id}">
						</div>
					</div>
					
				</div>
				
				<!--rest of the folders-->
				<div
					ng-repeat="folder in iframeScope.styleSheets | filter : { folder: 1, status: 1 } track by folder.id"
					ng-class="{'ct-css-node-folder-menu': iframeScope.cssFolderMenuOpen === folder.id, 'ct-css-node-folder-menu': iframeScope.cssFolderMenuOpen === folder.id}">

					<div class="ct-css-node-header ct-node-options-active ct-css-node-folder"
						ng-class="{'ct-css-node-hilite': iframeScope.selectedNodeType=='cssfolder' && iframeScope.selectedCSSFolder==folder.id, 'ct-css-node-folder-menu': iframeScope.cssFolderMenuOpen === folder.id, 'ct-style-set-expanded':iframeScope.expandedFolder[folder.id]}"
						ng-click="iframeScope.setActiveCSSFolder(folder.id); iframeScope.setSelectedCSSFolder(folder.id)" ng-include="'cssFolderMenu'">
					</div>
					<div class="ct-style-set-child-selector" ng-show="iframeScope.expandedFolder[folder.id]">
						<div class="ct-css-node-header ct-node-options-active ct-css-node-stylesheet"
							ng-click="iframeScope.setSelectedCSSFolder(folder.id); iframeScope.setStyleSheetToEdit(stylesheet)"
							data-id="{{folder.id}}"
							ng-repeat="stylesheet in iframeScope.styleSheets | filter : { folder: '!', parent: folder.id} track by stylesheet.id"  ng-if="stylesheet.parent > 0" ng-include="'styleSheet'"
							ng-class="{'ct-css-node-hilite': iframeScope.selectedNodeType=='stylesheet' && iframeScope.stylesheetToEdit.id==stylesheet.id, 'ct-css-node-folder-menu': iframeScope.cssFolderMenuOpen === stylesheet.id}">
						</div>
					</div>
				</div>

				<h3 class="ct-css-section-label">Disabled</h3>
				<!--disabled uncategorized folder-->
				<div> 
					<div class="ct-css-node-header ct-css-node-folder ct-node-options-active"
						ng-class="{'ct-css-node-hilite': iframeScope.selectedNodeType == 'cssfolder' && iframeScope.selectedCSSFolder===-1, 'ct-css-node-folder-menu': iframeScope.cssFolderMenuOpen === -1, 'ct-style-set-expanded':iframeScope.expandedFolder['uncategorized']}"
						ng-click="iframeScope.setActiveCSSFolder(-1); iframeScope.setSelectedCSSFolder(-1)"> 
						Uncategorized
						<div class="ct-node-options">
						
							<span class="ct-icon ct-settings-icon"
								ng-click="iframeScope.cssFolderMenuOpen = (iframeScope.cssFolderMenuOpen === -1 ? false : -1)">
							</span>

							<div ng-show="iframeScope.cssFolderMenuOpen === -1" class="ct-css-folder-menu">
								<ul>
									<li ng-click='iframeScope.toggleUncategorizedStyleSheets(); iframeScope.cssFolderMenuOpen = false; iframeScope.classesCached = false; iframeScope.outputCSSOptions()'>Enable</li>
								</ul>	
							</div>
						</div>
						<div ng-show="(iframeScope.styleSheets | filter : { folder: '!', parent: -1 }).length > 0" class="ct-expand-butt" ng-click="iframeScope.expandedFolder['uncategorized']=!iframeScope.expandedFolder['uncategorized']"><span class="ct-icon"></span></div>
					</div>
					<div class="ct-style-set-child-selector" ng-show="iframeScope.expandedFolder['uncategorized']">
						<div class="ct-css-node-header ct-node-options-active ct-css-node-stylesheet"
							ng-click="iframeScope.setSelectedCSSFolder(-1); iframeScope.setStyleSheetToEdit(stylesheet)"
							ng-repeat="stylesheet in iframeScope.styleSheets | filter : { folder: '!', parent: -1 } track by stylesheet.id" ng-include="'styleSheet'"
							ng-class="{'ct-css-node-hilite': iframeScope.selectedNodeType == 'stylesheet' && iframeScope.stylesheetToEdit.id === stylesheet.id, 'ct-css-node-folder-menu': iframeScope.cssFolderMenuOpen === stylesheet.id}">
						</div>
					</div>

				</div>

				<!--disabled folders-->
				<div
					ng-repeat="folder in iframeScope.styleSheets | filter : { folder: 1, status: 0 } track by folder.id"
					ng-class="{'ct-css-node-folder-menu': iframeScope.cssFolderMenuOpen === folder.id, 'ct-css-node-folder-menu': iframeScope.cssFolderMenuOpen === folder.id}">
					
					<div class="ct-css-node-header ct-node-options-active ct-css-node-folder" 
						ng-class="{'ct-css-node-hilite': iframeScope.selectedNodeType == 'cssfolder' && iframeScope.selectedCSSFolder==folder.id, 'ct-css-node-folder-menu': iframeScope.cssFolderMenuOpen === folder.id, 'ct-style-set-expanded':iframeScope.expandedFolder[folder.id]}"
						ng-click="iframeScope.setActiveCSSFolder(folder.id); iframeScope.setSelectedCSSFolder(folder.id)" ng-include="'cssFolderMenu'"> 
					</div>
					<div class="ct-style-set-child-selector" ng-show="iframeScope.expandedFolder[folder.id]">
						<div class="ct-css-node-header ct-node-options-active ct-css-node-stylesheet"
							ng-click="iframeScope.setSelectedCSSFolder(folder.id); iframeScope.setStyleSheetToEdit(stylesheet)"
							ng-repeat="stylesheet in iframeScope.styleSheets | filter : { folder: '!', parent: folder.id } | filter: iframeScope.greaterThan('parent', 0) track by stylesheet.id" ng-include="'styleSheet'"
							ng-class="{'ct-css-node-hilite': iframeScope.selectedNodeType == 'stylesheet' && iframeScope.stylesheetToEdit.id==stylesheet.id,'ct-css-node-folder-menu': iframeScope.cssFolderMenuOpen === stylesheet.id}">
						</div>
					</div>
				</div>
				
			</div>
		</div>
		<!-- .ct-style-sheets-tab -->

		<div class="ct-tab-panel ct-selectors-tab" ng-if="isShowTab('sidePanel','selectors')">
			<div class="oxygen-sidepanel-header-row">
				<?php _e("Selectors","oxygen"); ?>
				<svg class="oxygen-close-icon"
					ng-click="switchTab('sidePanel','selectors')"><use xlink:href="#oxy-icon-cross"></use></svg>
			</div>
			<div class="ct-elements-managers-top clearfix">
				<div class="ct-elements-managers-top-item">
					<div class="ct-elements-managers-buttons">
						<div class="ct-button ct-icon-right"
							ng-click="iframeScope.addSelectorItem()">
							<?php _e("Add Selector", "component-theme"); ?>
							<span class="ct-icon ct-plus-icon"></span>
						</div>
					</div>
				</div>
				<div class="ct-elements-managers-top-item">
					<div class="ct-elements-managers-buttons">
						<div class="ct-button ct-icon-right"
							ng-click="iframeScope.addSelectorFolder()">
							<?php _e("Add Folder", "component-theme"); ?>
							<span class="ct-icon ct-plus-icon"></span>
						</div>
					</div>
				</div>
			</div>
			<div class="ct-elements-managers-bottom">
				<div class="ct-css-node">



<script type="text/ng-template" id="styleFolderMenu">
	<span class="ct-icon ct-settings-icon"
	ng-click="iframeScope.selectorFolderMenuOpen = (iframeScope.selectorFolderMenuOpen == folder.key ? false : folder.key)">
	</span>


	<div ng-show="iframeScope.selectorFolderMenuOpen == folder.key" class="ct-css-folder-menu">
		<ul>
			<li ng-click='iframeScope.toggleSelectorFolder(folder); iframeScope.selectorFolderMenuOpen = false; iframeScope.classesCached = false; iframeScope.outputCSSOptions()'>{{folder.status === 1 ? 'Disable': 'Enable' }}</li>
			<li ng-click="iframeScope.deleteSelectorFolder(folder.key,$event); iframeScope.selectorFolderMenuOpen = false"><?php _e("Delete Folder", "component-theme"); ?></li>
		</ul>	
	</div>
</script>

<?php
						
						/**
						 * Add-ons hook
						 *
						 * @since 1.4
						 */

						do_action("oxygen_sidepanel_before_classes");
					?>








<h3 class="ct-css-section-label">Enabled</h3>

<div class="ct-css-node-folder">
	<div class="ct-css-node-header ct-node-options-active" ng-click="iframeScope.setSelectedSelectorFolder(null)"
		ng-class="{'ct-css-node-hilite': iframeScope.selectedNodeType == 'selectorfolder' && iframeScope.selectedSelectorFolder == null, 'ct-css-node-folder-menu': iframeScope.selectorFolderMenuOpen === null, 'ct-style-set-expanded':iframeScope.expandedSelectorFolder[0]}">
		<span class="ct-icon ct-dom-parent-icon"></span>
		Uncategorized
		<div class="ct-node-options">
			<span class="ct-icon ct-settings-icon"
			ng-click="iframeScope.selectorFolderMenuOpen = (iframeScope.selectorFolderMenuOpen === null ? false : null)">
			</span>

			<div ng-show="iframeScope.selectorFolderMenuOpen === null" class="ct-css-folder-menu">
				<ul>
					<li ng-click='iframeScope.toggleUncategorizedFolderContents(true); iframeScope.selectorFolderMenuOpen = false; iframeScope.classesCached = false; iframeScope.outputCSSOptions()'>Disable</li>
				</ul>	
			</div>

		</div>
		<div class="ct-expand-butt" ng-show="(iframeScope.objectToArrayObject(iframeScope.styleSets) | filter : { parent: '!' }).length > 0 || (iframeScope.objectToArrayObject(iframeScope.classes) | filter: {parent: '!'}).length > 0" ng-click="iframeScope.expandedSelectorFolder[0]=!iframeScope.expandedSelectorFolder[0]"><span class="ct-icon"></span></div>
	</div>

	<div class="ct-style-set-child-selector" ng-show="iframeScope.expandedSelectorFolder[0]">
		<?php
			do_action('oxygen_sidepanel_uncategorized_stylesets');
		?>

		
		<div class="ct-css-node-header ct-node-options-active ct-css-node-stylesheet"
			ng-repeat="class in iframeScope.objectToArrayObject(iframeScope.classes) | filter: {parent: '!'} track by class.key"
			ng-click="iframeScope.setCustomSelectorToEdit('.'+class.key);"
			ng-class="{'ct-css-node-hilite': iframeScope.selectedNodeType == 'class' && iframeScope.currentClass == class.key, 'ct-active-selector':selectorToEdit=='.'+class.key}">
			.{{class.key}}
			<div class="ct-node-options">
				<span class="ct-icon ct-visible-icon"
					ng-click="iframeScope.highlightSelector(true,'.'+class.key,$event)"
					title="<?php _e("Highlight selector", "component-theme"); ?>">
					</span>
				<!-- <span class="ct-icon ct-copy-item-icon"></span> -->
				<span class="ct-icon ct-delete-icon"
					title="<?php _e("Delete class and all references", "component-theme"); ?>"
					ng-click="iframeScope.tryDeleteClass(class.key,$event)"></span>
			</div>
		</div>
	</div>
</div>


<div class="ct-css-node-folder" ng-repeat="folder in iframeScope.objectToArrayObject(iframeScope.styleFolders) | filter:{ status:1 } track by folder.key"
	ng-class="{'ct-css-node-folder-menu': iframeScope.selectorFolderMenuOpen === folder.key}">
	<div class="ct-css-node-header ct-node-options-active" ng-click="iframeScope.setSelectedSelectorFolder(folder.key)"
		ng-class="{'ct-css-node-hilite': iframeScope.selectedNodeType=='selectorfolder' && iframeScope.selectedSelectorFolder == folder.key, 'ct-css-node-folder-menu': iframeScope.selectorFolderMenuOpen === folder.key, 'ct-style-set-expanded':iframeScope.expandedSelectorFolder[folder.key]}">
		<span class="ct-icon ct-dom-parent-icon"></span>
		{{folder.key}}
		<div class="ct-node-options">
			<ng-include src="'styleFolderMenu'"></ng-include>
		</div>
		<div class="ct-expand-butt" ng-show="(iframeScope.objectToArrayObject(iframeScope.classes) | filter: {parent: folder.key}).length > 0 || (iframeScope.objectToArrayObject(iframeScope.styleSets) | filter : { parent: folder.key }).length > 0" ng-click="iframeScope.expandedSelectorFolder[folder.key]=!iframeScope.expandedSelectorFolder[folder.key]"><span class="ct-icon"></span></div>
	</div>
	<div class="ct-style-set-child-selector" ng-show="iframeScope.expandedSelectorFolder[folder.key]">
		<?php
			do_action('oxygen_sidepanel_categorized_stylesets');
		?>

		<div class="ct-css-node-header ct-node-options-active ct-css-node-stylesheet"
			ng-repeat="class in iframeScope.objectToArrayObject(iframeScope.classes) | filter: {parent: folder.key} track by class.key"
			ng-click="iframeScope.setCustomSelectorToEdit('.'+class.key);"
			ng-class="{'ct-css-node-hilite': iframeScope.selectedNodeType == 'class' && iframeScope.currentClass == class.key, 'ct-active-selector':selectorToEdit=='.'+class.key}">
			.{{class.key}}
			<div class="ct-node-options">
				<span class="ct-icon ct-visible-icon"
					ng-click="iframeScope.highlightSelector(true,'.'+class.key,$event)"
					title="<?php _e("Highlight selector", "component-theme"); ?>">
					</span>
				<!-- <span class="ct-icon ct-copy-item-icon"></span> -->
				<span class="ct-icon ct-delete-icon"
					title="<?php _e("Delete class and all references", "component-theme"); ?>"
					ng-click="iframeScope.tryDeleteClass(class.key,$event)"></span>
			</div>
		</div>
	</div>
</div>


<h3 class="ct-css-section-label">DISABLED</h3>

<div class="ct-css-node-folder">
	<div class="ct-css-node-header ct-node-options-active" ng-click="iframeScope.setSelectedSelectorFolder(-1)"
		ng-class="{'ct-css-node-hilite': iframeScope.selectedNodeType=='selectorfolder' && iframeScope.selectedSelectorFolder == -1, 'ct-css-node-folder-menu': iframeScope.selectorFolderMenuOpen == -1, 'ct-style-set-expanded':iframeScope.expandedSelectorFolder['uncategorized']}">
		<span class="ct-icon ct-dom-parent-icon"></span>
		Uncategorized
		<div class="ct-node-options">
			<span class="ct-icon ct-settings-icon"
			ng-click="iframeScope.selectorFolderMenuOpen = (iframeScope.selectorFolderMenuOpen === -1 ? false : -1)">
			</span>


			<div ng-show="iframeScope.selectorFolderMenuOpen === -1" class="ct-css-folder-menu">
				<ul>
					<li ng-click='iframeScope.toggleUncategorizedFolderContents(); iframeScope.selectorFolderMenuOpen = false; iframeScope.classesCached = false; iframeScope.outputCSSOptions()'>Enable</li>
				</ul>	
			</div>
		</div>
		<div class="ct-expand-butt" ng-show="(iframeScope.objectToArrayObject(iframeScope.classes) | filter: {parent: -1}).length > 0 || (iframeScope.objectToArrayObject(iframeScope.styleSets) | filter : { parent: -1 }).length > 0" ng-click="iframeScope.expandedSelectorFolder['uncategorized']=!iframeScope.expandedSelectorFolder['uncategorized']"><span class="ct-icon"></span></div>
	</div>

	<div class="ct-style-set-child-selector" ng-show="iframeScope.expandedSelectorFolder['uncategorized']">
	
		<?php
			do_action('oxygen_sidepanel_uncategorized_stylesets', -1);
		?>

		<div class="ct-css-node-header ct-node-options-active ct-css-node-stylesheet"
			ng-repeat="class in iframeScope.objectToArrayObject(iframeScope.classes) | filter: {parent: -1} track by class.key"
			ng-click="iframeScope.setCustomSelectorToEdit('.'+class.key);"
			ng-class="{'ct-css-node-hilite': iframeScope.selectedNodeType == 'class' && iframeScope.currentClass == class.key, 'ct-active-selector':selectorToEdit=='.'+class.key}">
			.{{class.key}}
			<div class="ct-node-options">
				<span class="ct-icon ct-visible-icon"
					ng-click="iframeScope.highlightSelector(true,'.'+class.key,$event)"
					title="<?php _e("Highlight selector", "component-theme"); ?>">
					</span>
				<!-- <span class="ct-icon ct-copy-item-icon"></span> -->
				<span class="ct-icon ct-delete-icon"
					title="<?php _e("Delete class and all references", "component-theme"); ?>"
					ng-click="iframeScope.tryDeleteClass(class.key,$event)"></span>
			</div>
		</div>
	</div>
</div>

<div class="ct-css-node-folder" ng-repeat="folder in iframeScope.objectToArrayObject(iframeScope.styleFolders) | filter:{ status:0 } track by folder.key"
	ng-class="{'ct-css-node-folder-menu': iframeScope.selectorFolderMenuOpen === folder.key}">
	<div class="ct-css-node-header ct-node-options-active" ng-click="iframeScope.setSelectedSelectorFolder(folder.key)"
		ng-class="{'ct-css-node-hilite': iframeScope.selectedNodeType=='selectorfolder' && iframeScope.selectedSelectorFolder == folder.key, 'ct-css-node-folder-menu': iframeScope.selectorFolderMenuOpen === folder.key, 'ct-style-set-expanded':iframeScope.expandedSelectorFolder[folder.key]}">
		<span class="ct-icon ct-dom-parent-icon"></span>
		{{folder.key}}
		<div class="ct-node-options">
			<ng-include src="'styleFolderMenu'"></ng-include>
		</div>
		<div class="ct-expand-butt" ng-show="(iframeScope.objectToArrayObject(iframeScope.classes) | filter: {parent: folder.key}).length > 0 || (iframeScope.objectToArrayObject(iframeScope.styleSets) | filter : { parent: folder.key }).length > 0" ng-click="iframeScope.expandedSelectorFolder[folder.key]=!iframeScope.expandedSelectorFolder[folder.key]"><span class="ct-icon"></span></div>
	</div>

	<div class="ct-style-set-child-selector" ng-show="iframeScope.expandedSelectorFolder[folder.key]">
		<?php
			do_action('oxygen_sidepanel_categorized_stylesets');
		?>

		<div class="ct-css-node-header ct-node-options-active ct-css-node-stylesheet"
			ng-repeat="class in iframeScope.objectToArrayObject(iframeScope.classes) | filter: {parent: folder.key} track by class.key"
			ng-click="iframeScope.setCustomSelectorToEdit('.'+class.key);"
			ng-class="{'ct-css-node-hilite': iframeScope.selectedNodeType == 'class' && iframeScope.currentClass == class.key, 'ct-active-selector':selectorToEdit=='.'+class.key}">
			.{{class.key}}
			<div class="ct-node-options">
				<span class="ct-icon ct-visible-icon"
					ng-click="iframeScope.highlightSelector(true,'.'+class.key,$event)"
					title="<?php _e("Highlight selector", "component-theme"); ?>">
					</span>
				<!-- <span class="ct-icon ct-copy-item-icon"></span> -->
				<span class="ct-icon ct-delete-icon"
					title="<?php _e("Delete class and all references", "component-theme"); ?>"
					ng-click="iframeScope.tryDeleteClass(class.key,$event)"></span>
			</div>
		</div>
	</div>
</div>















				
					
					<?php
					
						/**
						 * Add-ons hook
						 *
						 * @since 1.4
						 */
					
						do_action("oxygen_sidepanel_after_classes");
					?>
				</div>
			</div>
		</div>
		<!-- .ct-selectors-tab -->

	</div>
</div><!-- .ct-panel-elements-managers -->