<?php
//Page Slug Body Class
function ssg_page_body_class( $classes ) {
global $post;
if ( isset( $post ) ) {
$classes[] = $post->post_name;
}
return $classes;
}
add_filter( 'body_class', 'ssg_page_body_class' );


//Add Browser to Body Class
add_filter('body_class','ssg_browser_body_class');
function ssg_browser_body_class($classes) {
  global $is_lynx, $is_gecko, $is_IE, $is_opera, $is_NS4, $is_safari, $is_chrome, $is_iphone;

  if($is_lynx) $classes[] = 'lynx';
  elseif($is_gecko) $classes[] = 'gecko';
  elseif($is_opera) $classes[] = 'opera';
  elseif($is_NS4) $classes[] = 'ns4';
  elseif($is_safari) $classes[] = 'safari';
  elseif($is_chrome) $classes[] = 'chrome';
  elseif($is_IE) $classes[] = 'ie';
  else $classes[] = 'unknown';

  if($is_iphone) $classes[] = 'iphone';
  return $classes;
}

// Add User Type to Body Class in both Admin & Front
function ssg_user_body_class($classes) {
global $current_user;
$user_role = array_shift($current_user->roles);
$classes[] = $user_role;
return $classes;
}
function ssg_user_body_class_admin($classes) {
global $current_user;
$user_role = array_shift($current_user->roles);
$classes .= $user_role;
return $classes;
}

add_filter('body_class','ssg_user_body_class');
add_filter('admin_body_class', 'ssg_user_body_class_admin');



/**
 * Snippet Name: Add parent page slug to the body class
 * Snippet URL: http://www.wpcustoms.net/snippets/add-parent-page-slug-body-class/
 */
 // example usage: .section-about { background: red; }
function wpc_body_class_section($classes) {
    global $wpdb, $post;
    if (is_page()) {
        if ($post->post_parent) {
            $parent  = end(get_post_ancestors($current_page_id));
        } else {
            $parent = $post->ID;
        }
        $post_data = get_post($parent, ARRAY_A);
        $classes[] = 'section-' . $post_data['post_name'];
    }
    return $classes;
}
add_filter('body_class','wpc_body_class_section');





?>