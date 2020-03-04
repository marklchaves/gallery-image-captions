<?php 

/*
Plugin Name: Gallery Image Captions
Plugin URI: https://caughtmyeye.dev
Description: Creates a filter to customise WordPress gallery image captions.
Version: 0.0.1
Author: mark l chaves
Author URI: https://caughtmyeye.dev/about/
*/

/**
 * Get Image Meta
 */
function get_image_meta($postvar = NULL)
{
    if (!isset($postvar)) {
        global $post;
        $postvar = $post->ID;
    }

    $attachment = get_post($postvar);

    return array(
        'alt' => get_post_meta($attachment->ID, '_wp_attachment_image_alt', true),
        'caption' => $attachment->post_excerpt,
        'description' => $attachment->post_content,
        'href' => get_permalink($attachment->ID),
        'src' => $attachment->guid,
        'title' => $attachment->post_title
    );
}

/**
 * Custom Filter for Gallery Image Captions
 */
function my_gallery_image_caption($attachment_id, $captiontag, $selector, $itemtag) {

    $id = $attachment_id;

    $my_image_meta = get_image_meta($id);
    
    // Here's where to customise the caption content.
    // This example uses the meta title and caption. You can display any value
    // from the $my_image_meta array. You can add your own HTML too.
    return "<{$captiontag} class='wp-caption-text gallery-caption' id='{$selector}-{$id}'>
    Image Title: ". $my_image_meta['title'] . "<br>Image Caption: ". $my_image_meta['caption'] . "</{$captiontag}></{$itemtag}>";

}
add_filter('gallery_image_caption', 'my_gallery_image_caption', 10, 4);

/**
 * Override the WordPress core media.php gallery_shortcode().
 */
remove_shortcode('gallery', 'gallery_shortcode');
add_shortcode('gallery', 'my_gallery_shortcode');

/**
 * Builds the Gallery shortcode output.
 *
 * This implements the functionality of the Gallery Shortcode for displaying
 * WordPress images on a post.
 *
 * @since 2.5.0
 *
 * @staticvar int $instance
 *
 * @param array $attr {
 *     Attributes of the gallery shortcode.
 *
 *     @type string       $order      Order of the images in the gallery. Default 'ASC'. Accepts 'ASC', 'DESC'.
 *     @type string       $orderby    The field to use when ordering the images. Default 'menu_order ID'.
 *                                    Accepts any valid SQL ORDERBY statement.
 *     @type int          $id         Post ID.
 *     @type string       $itemtag    HTML tag to use for each image in the gallery.
 *                                    Default 'dl', or 'figure' when the theme registers HTML5 gallery support.
 *     @type string       $icontag    HTML tag to use for each image's icon.
 *                                    Default 'dt', or 'div' when the theme registers HTML5 gallery support.
 *     @type string       $captiontag HTML tag to use for each image's caption.
 *                                    Default 'dd', or 'figcaption' when the theme registers HTML5 gallery support.
 *     @type int          $columns    Number of columns of images to display. Default 3.
 *     @type string|array $size       Size of the images to display. Accepts any valid image size, or an array of width
 *                                    and height values in pixels (in that order). Default 'thumbnail'.
 *     @type string       $ids        A comma-separated list of IDs of attachments to display. Default empty.
 *     @type string       $include    A comma-separated list of IDs of attachments to include. Default empty.
 *     @type string       $exclude    A comma-separated list of IDs of attachments to exclude. Default empty.
 *     @type string       $link       What to link each image to. Default empty (links to the attachment page).
 *                                    Accepts 'file', 'none'.
 * }
 * @return string HTML content to display gallery.
 */
function my_gallery_shortcode($attr)
{
    $post = get_post();

    static $instance = 0;
    $instance++;

    if (!empty($attr['ids'])) {
        // 'ids' is explicitly ordered, unless you specify otherwise.
        if (empty($attr['orderby'])) {
            $attr['orderby'] = 'post__in';
        }
        $attr['include'] = $attr['ids'];
    }

    /**
     * Filters the default gallery shortcode output.
     *
     * If the filtered output isn't empty, it will be used instead of generating
     * the default gallery template.
     *
     * @since 2.5.0
     * @since 4.2.0 The `$instance` parameter was added.
     *
     * @see my_gallery_shortcode()
     *
     * @param string $output   The gallery output. Default empty.
     * @param array  $attr     Attributes of the gallery shortcode.
     * @param int    $instance Unique numeric ID of this gallery shortcode instance.
     */
    $output = apply_filters('post_gallery', '', $attr, $instance);

    if (!empty($output)) {
        return $output;
    }

    $html5 = current_theme_supports('html5', 'gallery');
    $atts  = shortcode_atts(
        array(
            'order'      => 'ASC',
            'orderby'    => 'menu_order ID',
            'id'         => $post ? $post->ID : 0,
            'itemtag'    => $html5 ? 'figure' : 'dl',
            'icontag'    => $html5 ? 'div' : 'dt',
            'captiontag' => $html5 ? 'figcaption' : 'dd',
            'columns'    => 3,
            'size'       => 'thumbnail',
            'include'    => '',
            'exclude'    => '',
            'link'       => '',
        ),
        $attr,
        'gallery'
    );

    $id = intval($atts['id']);

    if (!empty($atts['include'])) {
        $_attachments = get_posts(
            array(
                'include'        => $atts['include'],
                'post_status'    => 'inherit',
                'post_type'      => 'attachment',
                'post_mime_type' => 'image',
                'order'          => $atts['order'],
                'orderby'        => $atts['orderby'],
            )
        );

        $attachments = array();
        foreach ($_attachments as $key => $val) {
            $attachments[$val->ID] = $_attachments[$key];
        }
    } elseif (!empty($atts['exclude'])) {
        $attachments = get_children(
            array(
                'post_parent'    => $id,
                'exclude'        => $atts['exclude'],
                'post_status'    => 'inherit',
                'post_type'      => 'attachment',
                'post_mime_type' => 'image',
                'order'          => $atts['order'],
                'orderby'        => $atts['orderby'],
            )
        );
    } else {
        $attachments = get_children(
            array(
                'post_parent'    => $id,
                'post_status'    => 'inherit',
                'post_type'      => 'attachment',
                'post_mime_type' => 'image',
                'order'          => $atts['order'],
                'orderby'        => $atts['orderby'],
            )
        );
    }

    if (empty($attachments)) {
        return '';
    }

    if (is_feed()) {
        $output = "\n";
        foreach ($attachments as $att_id => $attachment) {
            $output .= wp_get_attachment_link($att_id, $atts['size'], true) . "\n";
        }
        return $output;
    }

    $itemtag    = tag_escape($atts['itemtag']);
    $captiontag = tag_escape($atts['captiontag']);
    $icontag    = tag_escape($atts['icontag']);
    $valid_tags = wp_kses_allowed_html('post');
    if (!isset($valid_tags[$itemtag])) {
        $itemtag = 'dl';
    }
    if (!isset($valid_tags[$captiontag])) {
        $captiontag = 'dd';
    }
    if (!isset($valid_tags[$icontag])) {
        $icontag = 'dt';
    }

    $columns   = intval($atts['columns']);
    $itemwidth = $columns > 0 ? floor(100 / $columns) : 100;
    $float     = is_rtl() ? 'right' : 'left';

    $selector = "gallery-{$instance}";

    $gallery_style = '';

    /**
     * Filters whether to print default gallery styles.
     *
     * @since 3.1.0
     *
     * @param bool $print Whether to print default gallery styles.
     *                    Defaults to false if the theme supports HTML5 galleries.
     *                    Otherwise, defaults to true.
     */
    if (apply_filters('use_default_gallery_style', !$html5)) {
        $type_attr = current_theme_supports('html5', 'style') ? '' : ' type="text/css"';

        $gallery_style = "
		<style{$type_attr}>
			#{$selector} {
				margin: auto;
			}
			#{$selector} .gallery-item {
				float: {$float};
				margin-top: 10px;
				text-align: center;
				width: {$itemwidth}%;
			}
			#{$selector} img {
				border: 2px solid #cfcfcf;
			}
			#{$selector} .gallery-caption {
				margin-left: 0;
			}
			/* see gallery_shortcode() in wp-includes/media.php */
		</style>\n\t\t";
    }

    $size_class  = sanitize_html_class($atts['size']);
    $gallery_div = "<div id='$selector' class='gallery galleryid-{$id} gallery-columns-{$columns} gallery-size-{$size_class}'>";

    /**
     * Filters the default gallery shortcode CSS styles.
     *
     * @since 2.5.0
     *
     * @param string $gallery_style Default CSS styles and opening HTML div container
     *                              for the gallery shortcode output.
     */
    $output = apply_filters('gallery_style', $gallery_style . $gallery_div);

    $i = 0;

    foreach ($attachments as $id => $attachment) {

        $attr = (trim($attachment->post_excerpt)) ? array('aria-describedby' => "$selector-$id") : '';

        if (!empty($atts['link']) && 'file' === $atts['link']) {
            $image_output = wp_get_attachment_link($id, $atts['size'], false, false, false, $attr);
        } elseif (!empty($atts['link']) && 'none' === $atts['link']) {
            $image_output = wp_get_attachment_image($id, $atts['size'], false, $attr);
        } else {
            $image_output = wp_get_attachment_link($id, $atts['size'], true, false, false, $attr);
        }

        $image_meta = wp_get_attachment_metadata($id);

        $orientation = '';

        if (isset($image_meta['height'], $image_meta['width'])) {
            $orientation = ($image_meta['height'] > $image_meta['width']) ? 'portrait' : 'landscape';
        }

        $output .= "<{$itemtag} class='gallery-item'>";
        $output .= "
			<{$icontag} class='gallery-icon {$orientation}'>
				$image_output
			</{$icontag}>";

        /**
         * Custom Gallery Image Caption Filter
         * 
         * I want to use the actual media
         * attachment meta. Not sure why WordPress
         * defaults to the gallery image caption.
         *  
         * $id is the attachment ID (i.e., media file).
         *
         * ~mlc 16 February 2020
         */
        $filtered_caption =
            apply_filters('gallery_image_caption', $id, $captiontag, $selector, $itemtag);

        // Custom: If filtered then use the new content.
        if (!empty($filtered_caption)) {
            $output .= $filtered_caption;

            // Custom: Else use the default WordPress caption logic.
        } else {

            if ($captiontag && trim($attachment->post_excerpt)) {
                $output .= "
					<{$captiontag} class='wp-caption-text gallery-caption' id='$selector-$id'>" . wptexturize($attachment->post_excerpt) . "
					</{$captiontag}>";
            }

            $output .= "</{$itemtag}>";
        } // Custom: else

        if (!$html5 && $columns > 0 && ++$i % $columns === 0) {
            $output .= '<br style="clear: both" />';
        }
    }

    if (!$html5 && $columns > 0 && $i % $columns !== 0) {
        $output .= "
			<br style='clear: both' />";
    }

    $output .= "
		</div>\n";

    return $output;
}
