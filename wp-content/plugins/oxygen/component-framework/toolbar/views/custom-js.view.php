<div class="oxygen-sidebar-code-editor-wrap">
	<textarea ui-codemirror="{
	      lineNumbers: true,
	      mode: 'javascript',
	      type: 'custom-js',
	      onLoad : codemirrorLoaded
	    }" <?php $this->ng_attributes('custom-js','model'); ?>></textarea>
	<div class="oxygen-code-error-container"></div>
</div>

<div class="oxygen-control-row oxygen-control-row-bottom-bar oxygen-control-row-bottom-bar-code-editor">
	<a href="#" class="oxygen-code-editor-apply"
		ng-click="iframeScope.applyComponentJS()">
		<?php _e("Apply Code", "oxygen"); ?>
	</a>
	<a href="#" class="oxygen-code-editor-expand"
		data-collapse="<?php _e("Collapse Editor", "oxygen"); ?>" data-expand="<?php _e("Expand Editor", "oxygen"); ?>"
		ng-click="toggleSidebar()">
		<?php _e("Expand Editor", "oxygen"); ?>
	</a>
</div>