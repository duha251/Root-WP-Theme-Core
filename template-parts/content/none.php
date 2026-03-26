<p>
<?php
    if ( is_home() && current_user_can( 'publish_posts' ) ) {
        echo esc_html__('Ready to publish your first post?', 'mindverse'); 
        echo '<a href="'.esc_url( admin_url( 'post-new.php' ) ).'">'.esc_html__('Get started here', 'mindverse').'</a>';
    } elseif ( is_search() ) {
        esc_html_e( 'Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'mindverse' ); 
        get_search_form();
    } else {
        esc_html_e( 'It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps searching can help.', 'mindverse' );
        get_search_form();
    }; ?>
</p>