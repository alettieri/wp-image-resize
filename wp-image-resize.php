<?php 

/**
 * Returns file extension or false, if it's not supported
 *
 * @param string url or path to image
 * @return string
 */
function get_extension( $src ) {

    $type = wp_check_filetype( $src );

    return ( isset( $type[ "ext" ] ) ) ? $type[ "ext" ] : false;
}


/**
 * Returns cached, modified image. 
 * If image is not cached, it will create a modified image, cache it,
 * then returns src to modified image.
 * 
 * 
 * @param string $src Url or File path to image
 * @param array $opts { {'w'=>int, 'h'=>int, 'q'=>int, 'crop'=>bool} } or "w=int&h=int&q=int&crop=bool"
 * @return string
 */
function get_image_thumb( $src, $opts = null ) {
    
    //
    // Default Paramter values
    //
    $defaults = array(
        "w" => PHP_INT_MAX, // Won't resize if image width is smaller than default width
        "h" => PHP_INT_MAX, // Won't resize if image height is smaller than default height
        "q" => 95,
        "crop" => false
    );

    //
    // The default thumbnail url
    //
    $thumb_url  = $src;
    
    //
    // Get the extension
    //
    $ext        = get_extension( $src );

    //
    // If we can't determine the extension, don't even bother trying.
    //
    if( !$ext ) {
        return $thumb_url;
    }

    // Merge default with passed in options
    $opts       = wp_parse_args( $opts, $defaults );

    // Extract paramater values
    extract( $opts, EXTR_SKIP );
    
    // Width
    $w          = ( isset( $w ) && is_int( intval( $w ) ) ) ? intval( $w ) : $defaults[ "w" ];

    // Height
    $h          = ( isset( $h ) && is_int( intval( $h ) ) ) ? intval( $h ) : $defaults[ "h" ];

    // Quality
    $q          = ( isset( $q ) && is_int( intval( $q ) ) ) ? intval( $q ) : $defaults[ "q" ];

    // Crop
    $crop       = ( isset( $crop ) && $crop ) ? $crop : $defaults[ "crop" ];

    // Generate Unique Cache file
    $cache      = md5( $src . "$w-$h-$q-$crop" );
    
    // WordPress uploads directory (works with multi-site)
    $uploads    = wp_upload_dir();

    // Cache directory path
    $cache_dir  = $uploads[ "basedir" ] . "/cache";

    // Reset the default thumbnail url, in case it's cached.
    $thumb_url  = $uploads[ "baseurl" ] . "/cache/$cache.$ext";
    
    // Thumbnail physical directory
    $thumb_dir  = $cache_dir;

    // Thumbnail physical filename
    $thumb_file = $thumb_dir . "/$cache.$ext";


    //
    // Generate 'cache' directory if it doesn't exist yet.
    //
    if( !dir( $cache_dir ) ) {
        mkdir( $cache_dir, 0755, true );
    }


    //
    // Check to see if the file is cached. If not, generate the resized file.
    //
    if( !is_file( $thumb_file ) ) {

        //
        // Get the image editor object
        //
        $editor = wp_get_image_editor( $src );

        if( !is_wp_error( $editor ) ) {
                
            //
            // Resize the image
            //
            $editor->resize( $w, $h, $crop );
            
            //
            // Set the image quality
            //
            $editor->set_quality( $q );
            
            //
            // Save the modified file.
            //
            $editor->save( $thumb_file );

        } else {
            //
            // Something didn't go right with the editor.
            // Return original image src.
            //
            $thumb_url = $src;
        }

    }

    //
    // Return thumbnail src
    //
    return $thumb_url;

}