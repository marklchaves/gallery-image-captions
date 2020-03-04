# Gallery Image Captions (GIC)

Plugin to create a filter hook to customise WordPress gallery image captions.

The default gallery shortcode will only display the **caption** that's set in the media's attachment property. Sometimes it would be nice to display the title too&mdash;maybe even the description.

This plugin overrides the WordPress gallery shortcode function to create a hook in the WordPress so we can do a little bit more than just displaying the caption.

---

## Usage Examples

### Shortcode

Let's just use a `<p>` tag for the caption tag.

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

### Shortcode

`[gallery size="large" columns="2" link="file" ids="109,106" captiontag="h4"]`

Notice that `<blockquote></blockquote>` can be used. Just for fun.

`[gallery size="medium" columns="3" link="file" ids="109,106,108" captiontag="blockquote"]`

### Styling

```css
/* Style the H4 Used in the Caption Example */
h4 {
	color: #777777 !important;
	font-size: 1.2rem !important;
	font-family: Helvetica, Arial, sans-serif !important;
}
```

### Result