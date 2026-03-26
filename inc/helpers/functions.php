<?php

if ( ! function_exists( 'mytheme_debug' ) ) {
    function mytheme_debug( $data, $die = false, $label = null ) {
        echo '<pre style="
            background: #1e1e1e;
            color: #dcdcdc;
            padding: 15px;
            margin: 15px 0;
            border-radius: 6px;
            font-size: 14px;
            line-height: 1.4;
            overflow: auto;
        ">';

        if ( $label ) {
            echo "<strong style='color:#9cdcfe;'>[{$label}]</strong>\n";
        }

        if ( is_array( $data ) || is_object( $data ) ) {
            print_r( $data );
        } else {
            var_dump( $data );
        }

        echo '</pre>';

        if ( $die ) {
            die();
        }
    }
}

if ( ! function_exists( 'mytheme_get_template' ) ) {
    function mytheme_get_template( $slug, $args = [] ) {
        $template_file = $slug . '.php';
        $template_path = locate_template( $template_file );

        if ( ! $template_path ) {
            return;
        }

        if ( ! empty( $args ) && is_array( $args ) ) {
            extract( $args, EXTR_SKIP );
        }

        include $template_path;
    }
}