WP-Image-Resize
=====================

A helper function that resizes images using WordPress' built in [wp_get_image_editor](http://codex.wordpress.org/Function_Reference/wp_get_image_editor) function. 

# Function:
    get_image_thumb( $src, $opts );

## Parameters:
- $src - Image src (url or path)
- $opts (optional) - Resize options

### $opts:

    array (
        
        "size" => array( 500, 200 ), // w x h
        "q" => 100, // Quality
        "crop" => false // Crop image

    );


### $opts - Default Values:

    array (
        
        "size" => array( 525, 525 ), // w x h
        "q" => 95, // Quality
        "crop" => false // Crop image

    );


## Example:

    $id = get_post_thumbnail_id( get_the_ID() );
    $src = wp_get_attachment_image_src( $id, $size );
    $src = $src[0];

    $newImage = get_image_thumb( $src );
