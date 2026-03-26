
<main>

	<div class="pxl-dashboard-wrap">

		<?php mytheme_get_template( '/inc/admin/views/admin-tabs' ); ?>
	 
		<div class="pxl-row">
			<div class="pxl-col pxl-col-4">
				<div class="pxl-dsb-box-wrap pxl-dsb-box featured-box">
					<h4 class="pxl-dsb-title-heading"><?php esc_html_e( 'Unlock Premium Features', 'mytheme' ); ?></h4>
					<?php mytheme_get_template( '/inc/admin/views/admin-featured' ); ?>
				</div>
			</div>    
		 	<div class="pxl-col pxl-col-4">
		 		<div class="pxl-dsb-box-wrap pxl-dsb-box activation-box">
			 		<h4 class="pxl-dsb-title-heading"><?php esc_html_e( 'Theme Activation', 'mytheme' ); ?></h4>
					<?php mytheme_get_template( '/inc/admin/views/admin-registration' ); ?>
				</div>
			</div>	
			<div class="pxl-col pxl-col-4">
				<div class="pxl-dsb-box-wrap pxl-dsb-box system-info-box">
					<h4 class="pxl-dsb-title-heading"><?php esc_html_e( 'System status', 'mytheme' ); ?></h4>
					<?php mytheme_get_template( '/inc/admin/views/admin-system-info' ); ?>
				</div>
			</div> 
	 		 
		</div> 
 
	</div> 

</main>
