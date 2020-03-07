# Gallery Image Captions (GIC)

## Synopsis

This **plugin** allows for the customisation of WordPress gallery image captions. With **GIC**, we can display the title, caption, and description image metadata. We can also change/filter the rendering HTML to whatever we want.

After installing and activating GIC, simply write your filter and add the WordPress `gallery` shortcode to your page. That's it!

## Motivation

The default WordPress gallery shortcode will only display the **caption** from the media's attachment property. Sometimes it's nice to display more like the title&mdash;even the description.

The **GIC plugin** overrides the WordPress gallery shortcode function to create a [hook](https://developer.wordpress.org/plugins/hooks/). With this _hook_ we can do a little bit more than just displaying the caption.

Some premium themes hide the caption completely. This leaves photography lovers like me scratching their head and spending precious time cobbling together makeshift caption blocks.

## Official Plugin Page

Scope out the Gallery Image Captions plugin in the official [WordPress plugins directory](https://wordpress.org/plugins/gallery-image-captions/).

<a href="https://wordpress.org/plugins/gallery-image-captions/"><img src="https://user-images.githubusercontent.com/4658423/76144889-70a08280-607c-11ea-8928-ee0576567807.png" alt="GIC Logo" width="300px"></a>

---

## Default WordPress `gallery` Shortcode Results

This is what we get out-of-the box using the `gallery` shortcode.

![Default WordPress Gallery Image Caption Example 1](https://raw.githubusercontent.com/marklchaves/gallery-image-captions/master/assets/screenshot-1.jpg)

![Default WordPress Gallery Image Caption Example 2](https://raw.githubusercontent.com/marklchaves/gallery-image-captions/master/assets/screenshot-2.jpg)

---

## Custom Filter

The **crux** of this plugin is the ability to filter the gallery image caption. The `galimgcaps_gallery_image_caption` hook makes this possible. 

For the usage examples below, this is the filter used.

```php
/**
 * Custom Filter for Gallery Image Captions
 */
function mlc_gallery_image_caption($attachment_id, $captiontag, $selector, $itemtag) {

    $id = $attachment_id;

    // Grab the meta from the GIC plugin.
    $my_image_meta = galimgcaps_get_image_meta($id);
    
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
add_filter('galimgcaps_gallery_image_caption', 'mlc_gallery_image_caption', 10, 4);
```

Feel free to use this filter code as a starter template. After activating the GIC plugin, add the code above to your child theme's `functions.php` file. Rename the function and tweak the return string to suit your needs.

---

## Usage Example 1

### Shortcode

For starters, let's display one full-size image using a `<p></p>` tag for the caption tag.

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

![Custom Gallery Image Caption Example 1](https://raw.githubusercontent.com/marklchaves/gallery-image-captions/master/assets/screenshot-3.jpg)

## Usage Example 2

### Shortcode

1. **A 1x2 gallery using an H4 for the caption tag:** `[gallery size="large" columns="2" link="file" ids="109,106" captiontag="h4"]`

2. **A 1x3 gallery using an H4 for the caption tag:** `[gallery size="medium" columns="3" link="file" ids="109,106,108" captiontag="blockquote"]`

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

![Custom Gallery Image Caption Example 2](https://raw.githubusercontent.com/marklchaves/gallery-image-captions/master/assets/screenshot-4.jpg)

---

## Example of How to Insert Links to the Image File and the Media Attachment Page

![Links to Image File and Media Attachment Page](https://raw.githubusercontent.com/marklchaves/gallery-image-captions/master/assets/screenshot-5.jpg)

### Filter Used for Above Example

```php
    return "<{$captiontag} class='wp-caption-text gallery-caption' id='{$selector}-{$id}'>" .
            "Title: " . $my_image_meta['title'] . "<br>" .
            "Caption: " . $my_image_meta['caption'] . "<br>" . 
            "Description: ". $my_image_meta['description'] . "<br>" .
            "<a href='" . $my_image_meta['src'] . "'>Download Image</a><br>" .
            "<a href='" . $my_image_meta['href'] . "'>View Attachment</a><br>" .
        "</{$captiontag}></{$itemtag}>";
```

---

## Responsive CSS Example

I recommend adding the following media queries if you use galleries with more than one image. The two media queries below will stack 1x2 and 1x3 galleries into an nx1 or nx2 column as needed.

```css
/* Media Queries for Responsive Galleries */

/**
 * Styling based on article "How To: Style Your WordPress Gallery"
 * by Par Nicolas.
 * 
 * https://theme.fm/how-to-style-your-wordpress-gallery/
 */

/* Mobile Portrait Breakpoint - 1 column */
@media only screen and (max-width: 719.998px) {
    .gallery-columns-2 .gallery-item,
	.gallery-columns-3 .gallery-item { 
	 width: 100% !important; 
  }
}

/* Mobile Landscape and Tablet Breakpoints - 2 columns */
@media only screen and (min-width: 720px) and (max-width: 1024px) {
  .gallery-columns-3 .gallery-item { 
	 width: 50% !important; 
  }
}
```

---

## Support My Work

<a href='https://ko-fi.com/D1D7YARD' target='_blank'><img height='36' style='border:0px;height:36px;' src='https://az743702.vo.msecnd.net/cdn/kofi5.png?v=2' border='0' alt='Buy Me a Coffee at ko-fi.com' /></a>
