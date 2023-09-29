<div class="wrap">

	<h2><?php _e("SVG Sets", "component-theme"); ?></h2>

	<?php do_action('oxygen_vsb_before_settings_page');?>

	<form action="" method="post" enctype="multipart/form-data">

		<?php if ( ! empty( $svg_sets ) ) : ?>

		<h3><?php _e("Uploaded Sets", "component-theme"); ?></h3>
			<?php foreach ( $svg_sets as $name => $set ) : ?>

				<?php echo sanitize_text_field( $name ); ?>
				<?php if( FALSE === array_search( $name, $builtin_sets ) ): ?>
					<button type="submit" class="button button-small" value="<?php echo sanitize_text_field( $name ); ?>" name="ct_delete_svg_set"><?php _e("Delete", "component-theme"); ?></button>
				<?php endif; ?>
				<br/>

			<?php endforeach; ?>

		<?php endif; ?>

		<h3><?php _e("Add New Set", "component-theme"); ?></h3>


		<?php _e("Name of the Set", "component-theme"); ?> <input type="text" name="ct_svg_set_name"><br/>
		<?php _e("SVG file to upload", "component-theme"); ?> <input type="file" name="ct_svg_set_file" id="ct-svg-set-file">
		<p>
			<input type="submit" class="button button-primary" value="<?php _e("Submit", "component-theme"); ?>" name="submit">
		</p>
	</form>
</div>