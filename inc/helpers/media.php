<?php

/**
 * Crop an image from the Media Library to a custom size.
 */
function mytheme_crop_image( $attachment_id, $width = null, $height = null, $crop = true) {
    if (!$width || !$height) {
        return wp_get_attachment_url($attachment_id);
    }

    $original_path = get_attached_file($attachment_id);
    
    if (!$original_path || !file_exists($original_path)) {
        return false;
    }

    $upload_info = wp_upload_dir();
    $upload_dir = $upload_info['basedir'];
    $upload_url = $upload_info['baseurl'];

    $file_info = pathinfo($original_path);
    $new_filename = $file_info['filename'] . '-' . $width . 'x' . $height . '.' . $file_info['extension'];
    $new_path = $upload_dir . str_replace(basename($original_path), $new_filename, str_replace($upload_dir, '', $original_path));

    if (file_exists($new_path)) {
        return str_replace($upload_dir, $upload_url, $new_path);
    }

    $image_editor = wp_get_image_editor($original_path);
    if (!is_wp_error($image_editor)) {
        $image_editor->resize($width, $height, $crop);
        $saved = $image_editor->save($new_path);

        if (!is_wp_error($saved) && $saved) {
            $cropped_files = get_post_meta($attachment_id, '_custom_cropped_files', true);
            if (!is_array($cropped_files)) {
                $cropped_files = [];
            }

            if (!in_array($new_path, $cropped_files)) {
                $cropped_files[] = $new_path;
                update_post_meta($attachment_id, '_custom_cropped_files', $cropped_files);
            }

            return str_replace($upload_dir, $upload_url, $new_path);
        }
    }
    return false;
}

/**
 * Get image by size
 */
function mytheme_get_image_by_size($attachment_id, $width = null, $height = null, $attrs = []) {
    $image_url = $attachment_id === 0 ? \Elementor\Utils::get_placeholder_image_src() : mytheme_crop_image($attachment_id, $width, $height, true);
    if (!$image_url) {
        return '';
    }        
    if (empty($attrs['alt'])) {
        $attrs['alt'] = get_post_meta($attachment_id, '_wp_attachment_image_alt', true);
        $attrs['alt'] = !empty($attrs['alt']) ? $attrs['alt'] : get_the_title($attachment_id);
    }
    if (empty($attrs['loading'])) {
        $attrs['loading'] = 'lazy';
    }
    $attrs_str = '';
    foreach ($attrs as $name => $value) {
        $attrs_str .= ' ' . esc_attr($name) . '="' . esc_attr($value) . '"';
    }
    if ($width && $height) {
        $attrs_str .= ' width="' . esc_attr($width) . '"';
        $attrs_str .= ' height="' . esc_attr($height) . '"';
    }

    return '<img src="' . esc_url($image_url) . '"' . $attrs_str . '>';
}

/**
 * Display image by size
 */
function mytheme_the_image_by_size($attachment_id, $width = null, $height = null, $attrs = []) {
    echo wp_kses_post( mytheme_get_image_by_size($attachment_id, $width, $height, $attrs));
}