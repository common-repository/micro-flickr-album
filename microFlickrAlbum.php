<?php
/*
      Plugin Name: Micro Flickr Album
      Plugin URI: http://www.phptoys.com/
      Description: Displays Flickr photos on your site.
      Author: Php Toys
      Version: 1.0
      Author URI: http://www.phptoys.com/
*/
    function microFlickrAlbum_widget($args) {
        extract($args);
    
        $options = get_option("widget_mfa");
        if (empty($options))
        {
            $title = "Micro Flickr Album";
            $tags  = "sea, sand, caribe, sunshine";
            $nr    = 5;
        } else {
            $title = $options['mfa_title'];
            $tags  = $options['mfa_tags'];
            $nr    = $options['mfa_nr'];
        }
        
        echo $before_widget.$before_title;
        echo "{$title}";
        echo $after_title.$after_widget;
        
        $content = getFlickrRSS($tags);
        $imageList = getImages($content,$nr);

        echo '<table align="center">';
        foreach ($imageList as $value) {
            echo '<tr><td><img src="'.$value.'" border="1" /></td></tr>';
        }
        echo "</table>";         


    }

    function init_microFlickrAlbum(){
        register_sidebar_widget("Micro Flickr Album", "microFlickrAlbum_widget");     
        
        if ( !function_exists('register_sidebar_widget') || !function_exists('register_widget_control') )  return;
        
        register_sidebar_widget('Micro Flickr Album', 'microFlickrAlbum_widget');
        register_widget_control('Micro Flickr Album', 'microFlickrAlbum_admin', 350, 200);
    }
    
    function microFlickrAlbum_admin()
    {
        if ($_POST['mfa_submit']=='1'){
            $options['mfa_title'] = $_POST['mfa_title'];
            $options['mfa_tags']  = $_POST['mfa_tags'];
            $options['mfa_nr']    = $_POST['mfa_nr'];
            update_option("widget_mfa",$options);
        }

        $options = get_option("widget_mfa");
    
        if (empty($options))
        {
            $title = "Micro Flickr Album";
            $tags  = "sea, sand, caribe, sunshine";
            $nr    = 5;
        } else {
            $title = $options['mfa_title'];
            $tags  = $options['mfa_tags'];
            $nr    = $options['mfa_nr'];
        }

        echo "<div style='height: 200px'>
                <p style='text-align: left'>Title<br/>
                <input type='text' name='mfa_title' value='{$title}'><br/>
                <p style='text-align: left'>Tags<br/>
                <input type='text' name='mfa_tags' value='{$tags}'><br/>
                <p style='text-align: left'>Number of images<br/>
                <input type='text' name='mfa_nr' value='{$nr}'><br/>
                </p>
                <input type='hidden' name='mfa_submit' value='1'>
            </div>";
    }    


function getFlickrRSS($tags){
    
   $url = "http://api.flickr.com/services/feeds/photos_public.gne?format=rss2&tags=".$tags;
   
   if ($fp = fopen($url, 'r')) {
      $content = '';
        
      while ($line = fread($fp, 1024)) {
         $content .= $line;
      }
   }

   return $content;  
}

function getImages($rssContent,$imageNumber){
    $before = '<media:thumbnail url="';
    $after  = '"';
    $imageList = array();
    
    $oldPos = 0;
    $startPos=0;
    
    do {
        $oldPos   = $startPos;        
        $startPos = strpos($rssContent,$before,$startPos) + strlen($before);
        $endPos   = strpos($rssContent,$after,$startPos);
        $imageList[] = substr($rssContent,$startPos,$endPos-$startPos);
    } while (($startPos > $oldPos) && (sizeof($imageList)<$imageNumber));
       
    return $imageList; 
}



    add_action("plugins_loaded", "init_microFlickrAlbum");
?>

