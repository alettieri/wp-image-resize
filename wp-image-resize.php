<?php 

    function get_image_thumb( $src, $opts = array() ) {

            $default = array(
                "size" => array( 525, 525 ),
                "q" => 95,
                "crop" => false
            );


            $opts = (array) $opts;
            $opts = array_merge( $default, $opts );
            $size = $opts[ "size" ];

            $w = $size[0];
            $h = $size[1];
            $quality = $opts[ "q" ];
            $crop = $opts[ "crop" ];


            $cache = md5( $src . "$w-$h" );
            $uploads = wp_upload_dir();

            $thumb_url = $uploads["baseurl"] . "/cache/$cache.jpg";
            
            $cache_dir = $uploads['basedir'] . "/cache";
            $thumb_dir = $cache_dir;

            $thumb_file = $thumb_dir . "/$cache.jpg";

            if( !dir( $cache_dir ) )
                mkdir( $cache_dir, 0755, true );

            if( !is_file( $thumb_file ) ) {

                
                $editor = wp_get_image_editor( $src );

                if( !is_wp_error( $editor ) ) {
                
                    $editor->resize( $w, $h, $crop );
                    $editor->set_quality( $quality );
                    $editor->save( $thumb_file );
                
                } else {

                    $thumb_url = $src;  
                
                }

            } 

            return $thumb_url;

        }