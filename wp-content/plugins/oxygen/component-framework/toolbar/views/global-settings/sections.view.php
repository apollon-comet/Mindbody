					
					<?php $this->settings_breadcrumbs(	
							__('Sections & Columns','oxygen'),
							__('Global Styles','oxygen'),
							'default-styles'); ?>

					<div class="oxygen-control-row">
						<div class='oxygen-control-wrapper'>
							<label class='oxygen-control-label'><?php _e("Section Container Padding","oxygen"); ?></label>
							<div class='oxygen-control'>
								<div class='oxygen-four-sides-measure-box'>
									<div class='oxygen-measure-box'>
										<input type="text" spellcheck="false"
											data-option="container-padding-top"
											ng-model="iframeScope.globalSettings.sections['container-padding-top']"
											ng-model-options="{ debounce: 10 }"/>
                                        <?php $this->global_measure_box_unit_selector("global", "sections.container-padding-top", "px,%,em") ?>
									</div>
									<div class='oxygen-four-sides-measure-box-left-right'>
										<div class='oxygen-measure-box'>
											<input type="text" spellcheck="false"
												data-option="container-padding-left"
												ng-model="iframeScope.globalSettings.sections['container-padding-left']"
												ng-model-options="{ debounce: 10 }"/>
                                        <?php $this->global_measure_box_unit_selector("global", "sections.container-padding-left", "px,%,em") ?>
										</div><div class='oxygen-measure-box'>
											<input type="text" spellcheck="false"
												data-option="container-padding-right"
												ng-model="iframeScope.globalSettings.sections['container-padding-right']"
												ng-model-options="{ debounce: 10 }"/>
                                        <?php $this->global_measure_box_unit_selector("global", "sections.container-padding-right", "px,%,em") ?>
										</div>
									</div>
									<div class='oxygen-measure-box'>
										<input type="text" spellcheck="false"
											data-option="container-padding-bottom"
											ng-model="iframeScope.globalSettings.sections['container-padding-bottom']"
											ng-model-options="{ debounce: 10 }"/>
                                        <?php $this->global_measure_box_unit_selector("global", "sections.container-padding-bottom", "px,%,em") ?>
									</div>
									<div class="oxygen-apply-all-trigger">
										<?php _e("apply all »", "oxygen"); ?>
									</div>
								</div>
								<!-- .oxygen-four-sides-measure-box -->
							</div>
						</div>
					</div>


					<div class="oxygen-control-row">
						<div class='oxygen-control-wrapper'>
							<label class='oxygen-control-label'><?php _e("Columns Padding","oxygen"); ?></label>
							<div class='oxygen-control'>
								<div class='oxygen-four-sides-measure-box'>
									<div class='oxygen-measure-box'>
										<input type="text" spellcheck="false"
											data-option="padding-top"
											ng-model="iframeScope.globalSettings.columns['padding-top']"
											ng-model-options="{ debounce: 10 }"/>
										<?php $this->global_measure_box_unit_selector("global", "columns.padding-top", "px,%,em") ?>
									</div>
									<div class='oxygen-four-sides-measure-box-left-right'>
										<div class='oxygen-measure-box'>
											<input type="text" spellcheck="false"
												data-option="padding-left"
												ng-model="iframeScope.globalSettings.columns['padding-left']"
												ng-model-options="{ debounce: 10 }"/>
												<?php $this->global_measure_box_unit_selector("global", "columns.padding-left", "px,%,em") ?>
										</div><div class='oxygen-measure-box'>
											<input type="text" spellcheck="false"
												data-option="padding-right"
												ng-model="iframeScope.globalSettings.columns['padding-right']"
												ng-model-options="{ debounce: 10 }"/>
												<?php $this->global_measure_box_unit_selector("global", "columns.padding-right", "px,%,em") ?>
										</div>
									</div>
									<div class='oxygen-measure-box'>
										<input type="text" spellcheck="false"
											data-option="padding-bottom"
											ng-model="iframeScope.globalSettings.columns['padding-bottom']"
											ng-model-options="{ debounce: 10 }"/>
										<?php $this->global_measure_box_unit_selector("global", "columns.padding-bottom", "px,%,em") ?>
									</div>
									<div class="oxygen-apply-all-trigger">
										<?php _e("apply all »", "oxygen"); ?>
									</div>
								</div>
								<!-- .oxygen-four-sides-measure-box -->
							</div>
						</div>
					</div>