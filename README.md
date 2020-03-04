# Gallery Image Captions (GIC)

## Synopsis

This **plugin** allows the customisation of WordPress gallery image captions. With **GIC**, we can display the title, caption, and description image attributes. We can also change/filter the rendering HTML to whatever we want.

After installing and activating GIC, simply write your filter and add the WordPress `gallery` shortcode to your page. That's it!

## Motivation

The default WordPress gallery shortcode will only display the **caption** from the media's attachment property. Sometimes it's nice to display more like the title&mdash;even the description.

The **GIC plugin** overrides the WordPress gallery shortcode function to create a [hook](https://developer.wordpress.org/plugins/hooks/). With this _hook_ we can do a little bit more than just displaying the caption.

Some premium themes hide the caption completely. This leaves photography lovers like me scratching their head and spending precious time cobbling together makeshift caption blocks.

---

## Default WordPress `gallery` Shortcode Results

![Default WordPress Gallery Image Caption Example 1](https://raw.githubusercontent.com/marklchaves/gallery-image-captions/master/assets/default-gallery-image-captions1-1280w.jpg)

![Default WordPress Gallery Image Caption Example 2](https://raw.githubusercontent.com/marklchaves/gallery-image-captions/master/assets/default-gallery-image-captions2-1280w.jpg)

---

## Custom Filter

The **crux** of this plugin is the ability to filter the gallery image caption. The `gallery_image_caption` hook makes this possible. 

For the usage examples below, this is the filter used.

```php
/**
 * Custom Filter for Gallery Image Captions
 */
function mlc_gallery_image_caption($attachment_id, $captiontag, $selector, $itemtag) {

    $id = $attachment_id;

    // Grab the meta from the GIC plugin.
    $my_image_meta = get_image_meta($id);
    
    /**
     * Here's where to customise the caption content.
     * 
     * This example uses the meta title, caption, and description. 
     * 
     * You can display any value from the $my_image_meta array. 
     * You can add your own HTML too.
     */
    return "<{$captiontag} class='wp-caption-text gallery-caption' id='{$selector}-{$id}'>" .
            "Title: " . $my_image_meta['title'] . "<br>" .
            "Caption: " . $my_image_meta['caption'] . "<br>". 
            "Description: ". $my_image_meta['description'] . 
        "</{$captiontag}></{$itemtag}>";

}
add_filter('gallery_image_caption', 'mlc_gallery_image_caption', 10, 4);
```

Feel free to use this filter code as a starter template. After activating the GIC plugin, add the code above to your child theme's `functions.php` file. Rename the function and tweak the return string to suit your needs.

---

## Usage Example 1

### Shortcode

For starters, let's use a `<p></p>` tag for the caption tag.

`[gallery size="full" columns="1" link="file" ids="114" captiontag="p"]`

### Styling

Let's override the generated styles with our own style for one particular image.

```css
/* Targeting a Specific Image */

/* Add some padding all around. */
#gallery-1 .gallery-item, 
#gallery-1 .gallery-item p {
    padding: 1%;
}

/* Add some moody background with typewriter font. */
#gallery-1 .gallery-item {
    color: whitesmoke;
    background-color: black;
    font-size: 1.25rem;
    font-family: Courier, monospace;
    text-align: left !important;
}
```

### Result

![Custom Gallery Image Caption Example 1](https://raw.githubusercontent.com/marklchaves/gallery-image-captions/master/assets/custom-gallery-image-captions1-1280w.jpg)

## Usage Example 2

### Shortcode

1. `[gallery size="large" columns="2" link="file" ids="109,106" captiontag="h4"]`

2. `[gallery size="medium" columns="3" link="file" ids="109,106,108" captiontag="blockquote"]`

Did you notice that `<blockquote></blockquote>` is used in the second shortcode. Let's give it try just for _kicks_.

### Styling

```css
/* 1. Style the H4 Used in the Caption Example */
h4 {
	color: #777777 !important;
	font-size: 1.2rem !important;
	font-family: Helvetica, Arial, sans-serif !important;
}
```

```css
/* 2. Help Align the Blockquote */
#gallery-3 .gallery-caption {
    margin-left: 40px !important;
}
```

### Result

![Custom Gallery Image Caption Example 2](https://raw.githubusercontent.com/marklchaves/gallery-image-captions/master/assets/custom-gallery-image-captions2-1280w.jpg)
