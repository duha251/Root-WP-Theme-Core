<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class MyTheme_Admin_User {

    private $meta_key = 'mytheme_user_avatar_id';

    public function __construct() {
        add_action( 'show_user_profile', [ $this, 'render_avatar_field' ] );
        add_action( 'edit_user_profile', [ $this, 'render_avatar_field' ] );

        add_action( 'personal_options_update', [ $this, 'save_avatar_field' ] );
        add_action( 'edit_user_profile_update', [ $this, 'save_avatar_field' ] );

        add_action( 'admin_enqueue_scripts', [ $this, 'load_media_assets' ] );

        add_filter( 'get_avatar', [ $this, 'display_custom_meta_avatar' ], 10, 5 );

        add_action( 'show_user_profile', [ $this, 'display_user_social_fields' ] );
        add_action( 'edit_user_profile', [ $this, 'display_user_social_fields' ] );

        add_action( 'personal_options_update', [ $this, 'save_user_social_fields' ] );
        add_action( 'edit_user_profile_update', [ $this, 'save_user_social_fields' ] );
    }

    public function load_media_assets( $hook ) {
        if ( ! in_array( $hook, array( 'profile.php', 'user-edit.php' ) ) ) return;
        wp_enqueue_media();
    }

    public function render_avatar_field( $user ) {
        $avatar_id = get_user_meta( $user->ID, $this->meta_key, true );
        $preview_url = $avatar_id ? wp_get_attachment_image_url( $avatar_id, 'thumbnail' ) : '';
        ?>
        <div class="mytheme-avatar-section">
            <h3><?php esc_html_e( 'MyTheme Custom Profile', 'mytheme' ); ?></h3>
            <table class="form-table">
                <tr>
                    <th><label for="mytheme_custom_avatar"><?php esc_html_e( 'Profile Picture', 'mytheme' ); ?></label></th>
                    <td>
                        <div id="avatar-preview-wrapper" style="margin-bottom: 10px;">
                            <?php if ( $preview_url ) : ?>
                                <img src="<?php echo esc_url( $preview_url ); ?>" style="width: 90px; height: 90px; object-fit: cover; border-radius: 4px; border: 1px solid #ccc;">
                            <?php endif; ?>
                        </div>
                        <input type="hidden" name="mytheme_user_avatar_id" id="mytheme_user_avatar_id" value="<?php echo esc_attr( $avatar_id ); ?>" />
                        <button type="button" class="button" id="mv_upload_btn"><?php esc_html_e( 'Choose Image', 'mytheme' ); ?></button>
                        <button type="button" class="button" id="mv_remove_btn" style="<?php echo ! $avatar_id ? 'display:none;' : ''; ?>"><?php esc_html_e( 'Remove', 'mytheme' ); ?></button>
                        <p class="description"><?php esc_html_e( 'This image will be used as a priority over Gravatar.', 'mytheme' ); ?></p>
                    </td>
                </tr>
            </table>
        </div>

        <script>
            jQuery(document).ready(function($){
                var mv_frame;
                $('#mv_upload_btn').on('click', function(e){
                    e.preventDefault();
                    if (mv_frame) { mv_frame.open(); return; }
                    mv_frame = wp.media({ title: 'Select Custom Avatar', multiple: false });
                    mv_frame.on('select', function(){
                        var selection = mv_frame.state().get('selection').first().toJSON();
                        $('#mytheme_user_avatar_id').val(selection.id);
                        var thumb = selection.sizes.thumbnail ? selection.sizes.thumbnail.url : selection.url;
                        $('#avatar-preview-wrapper').html('<img src="'+thumb+'" style="width:90px; height:90px; object-fit:cover; border-radius:50%; border:1px solid #ccc;">');
                        $('#mv_remove_btn').show();
                    });
                    mv_frame.open();
                });
                $('#mv_remove_btn').on('click', function(){
                    $('#mytheme_user_avatar_id').val('');
                    $('#avatar-preview-wrapper').empty();
                    $(this).hide();
                });
            });
        </script>
        <?php
    }

    public function save_avatar_field( $user_id ) {
        if ( ! current_user_can( 'edit_user', $user_id ) ) return;
        
        if ( isset( $_POST['mytheme_user_avatar_id'] ) ) {
            update_user_meta( $user_id, $this->meta_key, sanitize_text_field( $_POST['mytheme_user_avatar_id'] ) );
        }
    }


    public function display_custom_meta_avatar( $avatar, $id_or_email, $size, $default, $alt ) {
        $user_id = 0;
        if ( is_numeric( $id_or_email ) ) {
            $user_id = (int) $id_or_email;
        } elseif ( is_object( $id_or_email ) && ! empty( $id_or_email->user_id ) ) {
            $user_id = (int) $id_or_email->user_id;
        } elseif ( is_string( $id_or_email ) ) {
            $user = get_user_by( 'email', $id_or_email );
            $user_id = $user ? $user->ID : 0;
        }

        if ( $user_id ) {
            $custom_avatar_id = get_user_meta( $user_id, $this->meta_key, true );
            
            if ( $custom_avatar_id ) {
                $img_data = wp_get_attachment_image_src( $custom_avatar_id, array( $size, $size ) );
                if ( $img_data ) {
                    $avatar = sprintf(
                        '<img alt="%1$s" src="%2$s" class="avatar avatar-%3$d photo" height="%3$d" width="%3$d" style="object-fit:cover; border-radius:50%%;">',
                        esc_attr( $alt ),
                        esc_url( $img_data[0] ),
                        (int) $size
                    );
                }
            }
        }
        return $avatar;
    }


    /**
     * Display Fields Social Link by Redux Framework
     */
    public function display_user_social_fields( $user ) {
        $social_list = mytheme()->get_theme_option('user_social_name', []);
        if ( empty( $social_list ) ) {
            return;
        };
        ?>
        <h3><?php _e('MyTheme Social Networks', 'mytheme'); ?></h3>
        <table class="form-table">
            <?php foreach ( $social_list as $social ) : 
                $id = sanitize_title($social); 
                $meta_key = 'user_social_' . $id;
                ?>
                <tr>
                    <th><label for="<?php echo esc_attr( $meta_key ); ?>"><?php echo esc_html($social); ?></label></th>
                    <td>
                        <input type="url" name="<?php echo esc_attr( $meta_key ); ?>" id="<?php echo esc_attr( $meta_key ); ?>" 
                            value="<?php echo esc_attr( get_the_author_meta( $meta_key, $user->ID ) ); ?>" 
                            class="regular-text" /><br />
                        <span class="description"><?php printf( __('Enter your %s URL profile', 'mytheme'), $social ); ?></span>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
        <?php
    }

    /**
     * Save Fields Social Link by Redux Framework
     */
    function save_user_social_fields( $user_id ) {
        if ( ! current_user_can( 'edit_user', $user_id ) ) {
             return false;
        };

        $social_list = mytheme()->get_theme_option('user_social_name', []);

        foreach ( $social_list as $social ) {
            $id = sanitize_title($social); 
            $meta_key = 'user_social_' . $id;
            
            if ( isset( $_POST[$meta_key] ) ) {
                update_user_meta( $user_id, $meta_key, esc_url_raw( $_POST[$meta_key] ) );
            }
        }
    }
}

new MyTheme_Admin_User();