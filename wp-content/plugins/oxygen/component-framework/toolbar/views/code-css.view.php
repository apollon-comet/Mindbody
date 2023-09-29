<div class="oxygen-sidebar-code-editor-wrap">
  <textarea ui-codemirror="{
      lineNumbers: true,
      newlineAndIndent: false,
      mode: 'css',
      type: 'css',
      onLoad : codemirrorLoaded
    }" <?php $this->ng_attributes('code-css','model'); ?>
    ng-change="iframeScope.applyCodeBlockCSS();"></textarea>
</div>

<div class="oxygen-control-row oxygen-control-row-bottom-bar oxygen-control-row-bottom-bar-code-editor">
  <a href="#" class="oxygen-code-editor-apply"
    ng-click="iframeScope.applyCodeBlockCSS()">
    <?php _e("Apply Code", "oxygen"); ?>
  </a>
  <a href="#" class="oxygen-code-editor-expand"
    data-collapse="<?php _e("Collapse Editor", "oxygen"); ?>" data-expand="<?php _e("Expand Editor", "oxygen"); ?>"
    ng-click="toggleSidebar()">
    <?php _e("Expand Editor", "oxygen"); ?>
  </a>
</div>