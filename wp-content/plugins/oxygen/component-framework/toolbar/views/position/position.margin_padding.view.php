<div class="oxygen-control-row">
	<div class='oxygen-control-wrapper'
		ng-if="isActiveName('ct_section')">
		<label class='oxygen-control-label'><?php _e("Container Padding", "component-theme"); ?></label>
		
		<div class='oxygen-control'>

			<div class='oxygen-four-sides-measure-box'>

				<?php $this->measure_box('container-padding-top','px,%,em',true); ?>

				<div class='oxygen-four-sides-measure-box-left-right'>

					<?php $this->measure_box('container-padding-left','px,%,em',true); ?>
					<?php $this->measure_box('container-padding-right','px,%,em',true); ?>

				</div>

				<?php $this->measure_box('container-padding-bottom','px,%,em',true); ?>

				<div class="oxygen-apply-all-trigger">
					<?php _e("apply all »", "oxygen"); ?>
				</div>

			</div>
			<!-- .oxygen-four-sides-measure-box -->
		</div>
		<!-- .oxygen-control -->
	</div>

	<div class='oxygen-control-wrapper'
		ng-if="!isActiveName('ct_section')">
		<label class='oxygen-control-label'><?php _e("Padding", "component-theme"); ?></label>
		
		<div class='oxygen-control'>

			<div class='oxygen-four-sides-measure-box'>

				<?php $this->measure_box('padding-top','px,%,em',true,true,"model,change,keypress"); ?>

				<div class='oxygen-four-sides-measure-box-left-right'>

					<?php $this->measure_box('padding-left','px,%,em',true,true,"model,change,keypress"); ?>
					<?php $this->measure_box('padding-right','px,%,em',true,true,"model,change,keypress"); ?>

				</div>

				<?php $this->measure_box('padding-bottom','px,%,em',true,true,"model,change,keypress"); ?>

				<div class="oxygen-apply-all-trigger">
					<?php _e("apply all »", "oxygen"); ?>
				</div>

			</div>
			<!-- .oxygen-four-sides-measure-box -->
		</div>
		<!-- .oxygen-control -->
	</div>

	<div class='oxygen-control-wrapper' ng-show='iframeScope.component.active.name != "ct_section"'>
		<label class='oxygen-control-label'><?php _e("Margin", "component-theme"); ?></label>
		
		<div class='oxygen-control'>

			<div class='oxygen-four-sides-measure-box'>

				<?php $this->measure_box('margin-top','',true,true,"model,change,keypress"); ?>

				<div class='oxygen-four-sides-measure-box-left-right'>

					<?php $this->measure_box('margin-left','',true,true,"model,change,keypress"); ?>
					<?php $this->measure_box('margin-right','',true,true,"model,change,keypress"); ?>

				</div>

				<?php $this->measure_box('margin-bottom','',true,true,"model,change,keypress"); ?>

				<div class="oxygen-apply-all-trigger">
					<?php _e("apply all »", "oxygen"); ?>
				</div>

			</div>
			<!-- .oxygen-four-sides-measure-box -->
		</div>
		<!-- .oxygen-control -->
	</div>
</div>