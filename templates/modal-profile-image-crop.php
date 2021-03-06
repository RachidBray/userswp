<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
global $uwp_template_args;
extract($uwp_template_args);
?>
<div class="uwp-bs-modal uwp_fade uwp_show" id="uwp-<?php echo $type; ?>-modal">
	<div class="uwp-bs-modal-dialog">
		<div class="uwp-bs-modal-content">
			<div class="uwp-bs-modal-header">
				<h4 class="uwp-bs-modal-title">
					<?php
					if ($type == 'avatar') {
						echo __( 'Change your profile photo', 'userswp' );
					} else {
						echo __( 'Change your cover photo', 'userswp' );
					}
					?>
				</h4>
				<button type="button" class="close uwp-modal-close" data-type="<?php echo $type; ?>" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			</div>
			<div class="uwp-bs-modal-body">
				<div id="uwp-bs-modal-notice"></div>
				<div align="center">
					<img src="<?php echo $image_url; ?>" id="uwp-<?php echo $type; ?>-to-crop" />
				</div>
			</div>
			<div class="uwp-bs-modal-footer">
				<div class="uwp-<?php echo $type; ?>-crop-p-wrap">
					<div id="<?php echo $type; ?>-crop-actions">
						<form class="uwp-crop-form" method="post">
							<input type="hidden" name="x" value="" id="<?php echo $type; ?>-x" />
							<input type="hidden" name="y" value="" id="<?php echo $type; ?>-y" />
							<input type="hidden" name="w" value="" id="<?php echo $type; ?>-w" />
							<input type="hidden" name="h" value="" id="<?php echo $type; ?>-h" />
							<input type="hidden" id="uwp-<?php echo $type; ?>-crop-image" name="uwp_crop" value="<?php echo $image_url; ?>" />
							<input type="hidden" name="uwp_crop_nonce" value="<?php echo wp_create_nonce( 'uwp-crop-nonce' ); ?>" />
							<input type="submit" name="uwp_<?php echo $type; ?>_crop" value="<?php echo __('Apply', 'userswp'); ?>" class="button button-primary" id="save_uwp_<?php echo $type; ?>" />
						</form>
					</div>
				</div>
				<button type="button" data-type="<?php echo $type; ?>" class="button uwp_modal_btn uwp-modal-close" data-dismiss="modal"><?php echo __( 'Cancel', 'userswp' ); ?></button>
			</div>
		</div>
	</div>
</div>