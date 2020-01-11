<?php
/*
*
*Plugin Name: Customisation plugin
*Description: Add customisations for my thesis' WP installation
*Version: 0.9
*Author: Heracles Michailidis
*Author URI: ir4klis.github.io
*Text Domain: thesis
*Domain Path: /languages
*
*/

// Loads text domain for this plugin
function customisation_textdomain () {
    load_plugin_textdomain('thesis', false, basename( dirname( __FILE__ ) ) . '/languages/' );
}
add_action( 'plugins_loaded', 'customisation_textdomain' );

//  Remove unneded admin dashboard pages
function remove_pages() {
    global $user_ID;

    remove_menu_page( 'edit-comments.php' ); //Comments
    remove_menu_page( 'edit.php' ); //Posts
    remove_menu_page( 'metaslider' ); //Posts
 
    if ( !current_user_can( 'administrator' ) ) {
        remove_menu_page( 'index.php' ); 
    }
}
add_action( 'admin_menu', 'remove_pages' );

// Removes WP widgets
remove_action( 'welcome_panel', 'wp_welcome_panel' );
function remove_dashboard_widgets() {
    global $wp_meta_boxes;
 
    unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_quick_press']);
    unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_incoming_links']);
    unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_right_now']);
    unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_plugins']);
    unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_recent_drafts']);
    unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_recent_comments']);
    unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_primary']);
    unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_secondary']);
    unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_activity']);

}
add_action('wp_dashboard_setup', 'remove_dashboard_widgets' );

// Removes SO news WP widgets
function so_remove_dashboard_news() {
	remove_meta_box( 'so-dashboard-news', 'dashboard', 'normal' );
}
add_action( 'admin_menu', 'so_remove_dashboard_news' );

// Hides WP version from admin footer
function wp_admin_footer_ver_hide() {
    remove_filter( 'update_footer', 'core_update_footer' ); 
}
add_action( 'admin_menu', 'wp_admin_footer_ver_hide' );

function remove_footer_admin () {

    echo '<span id="footer-thankyou">'.__( 'Developed by ', 'thesis' ).'<a href="https://www.linkedin.com/in/heracles-michailidis/" target="_blank">'. __( 'Heracles Michailidis', 'thesis' ).'</a> - '. __( 'Supervised by ', 'thesis' ) .'<a href="http://iiwm.teikav.edu.gr/iinew/Faculty%20Members/eleftherios-moisiadis/" target="_blank">'. __( 'Dr. Moussiades Eleftherios', 'thesis' ) .'</a> </span>';
}
add_filter('admin_footer_text', 'remove_footer_admin');

// Adds custom 'Welcome to WP!' widget
function custom_wp_welcome_panel() { ?>

  <div class="welcome-panel-content">
  <h2> <?php  _e( 'Welcome to online exam service!', 'thesis' ); ?> </h2>
  <p class="about-description"><?php _e( 'Here are some links to help you start', 'thesis' ); ?></p>
  <div class="welcome-panel-column-container">
  
  <div class="welcome-panel-column">
      <h3><?php _e( 'Get Started' ); ?></h3>
      <a class="button button-primary button-hero load-customize hide-if-no-customize" href="<?php echo admin_url('?page=ai-quiz-home'); ?>"><?php _e( 'Create a questions catalogue', 'thesis' ); ?></a>
      <p class="hide-if-no-customize"><?php echo __( 'Or ' , 'thesis').'<a href="'. admin_url('admin.php?page=ai-quiz-export') .'">'.__('import / export existing material', 'thesis').'</a>'  ?></p>
  </div>
 
  <div class="welcome-panel-column">
    <h3><?php _e( 'Next Steps' ); ?></h3>
    <ul>
      <li><?php printf( '<a href="%s" class="welcome-icon welcome-add-page">' . __( 'Add additional pages' ) . '</a>', admin_url( 'post-new.php?post_type=page' ) ); ?></li>
      <li><?php printf( '<a href="%s" class="welcome-icon welcome-view-site">' . __( 'View your site' ) . '</a>', home_url( '/' ) ); ?></li>
    </ul>
  </div>
  
  <div class="welcome-panel-column welcome-panel-last">
    <h3><?php _e( 'More Actions' ); ?></h3>
    <ul>
      <li><?php printf( '<a href="%s" class="welcome-icon dashicons-editor-help">' . __( 'Help and documentation', 'thesis' ) . '</a>', admin_url('admin.php?page=ai-quiz-help') ); ?></li>
      <li><div class="welcome-icon welcome-widgets-menus"><?php echo '<a href="' . admin_url( 'admin.php?page=ai-quiz-settings' ) . '">' . __( 'Manage settings', 'thesis' ) . '</a>';?></div></li>
      <li><div class="welcome-icon dashicons-admin-users"><?php echo '<a href="' . admin_url( 'users.php' ) . '">' . __( 'Manage users', 'thesis' ) . '</a>';?></div></li>
    </ul>
  </div>
 
  </div>
  </div>
  <?php
}
add_action( 'welcome_panel', 'custom_wp_welcome_panel' );


// Shows an overview of exams content
function qtl_dashboard_widgets() {
global $wp_meta_boxes;
 
wp_add_dashboard_widget('qtl_widget', __('Overview', 'thesis'), 'overview_dashboard_qtl');
}
 
function overview_dashboard_qtl() {
    global $wpdb;
    $table_quiz = $wpdb->prefix . "AI_Quiz_tblQuizzes";
    $table_question = $wpdb->prefix . "AI_Quiz_tblQuestions";
    $table_question_pots = $wpdb->prefix . "AI_Quiz_tblQuestionPots";

    $quizes = $wpdb->get_var( 'SELECT COUNT(quizID) FROM '.$table_quiz);
    $questions = $wpdb->get_var( 'SELECT COUNT(questionID) FROM '.$table_question);
    $question_pots = $wpdb->get_var( 'SELECT COUNT(potID) FROM '.$table_question_pots);
 
    echo '<h3>'.__('There are available', 'thesis').'</h3>';
    echo '<ul>';
        echo'<li>'.$quizes.' <a style="text-decoration:none" href="'. admin_url( 'admin.php?page=ai-quiz-quiz-list' ).'">'.__('Tests', 'thesis').'</a></li>';
 
        if($question_pots != 1){
          echo'<li>'.$question_pots.' <a style="text-decoration:none" href="'. admin_url( '?page=ai-quiz-home' ).'">'.__('Question Pots', 'thesis').'</a></li>';
        }
        else{
            echo'<li>'.$question_pots.' <a style="text-decoration:none" href="'. admin_url( '?page=ai-quiz-home' ).'">'.__('Question Pot', 'thesis').'</a></li>';
        }
        if($questions != 1){
        echo'<li>'. $questions.' '.__('Questions', 'thesis').'</li>';
        }
        else{
            echo'<li>'. $questions.' '.__('Question', 'thesis').'</li>';
        }
    echo'</ul>';
   

}
add_action('wp_dashboard_setup', 'qtl_dashboard_widgets');

// Shows an overview of users
function users_widget() {
    
    global $wp_meta_boxes;
     
    wp_add_dashboard_widget('usr_cnt_widget', __('User overview', 'thesis'), 'cnt_usr');
}  

// User overview widget
function cnt_usr() {
    global $wpdb;

    $user_count = $wpdb->get_var( 'SELECT COUNT(ID) FROM wp_users');
    $last_user_name = $wpdb->get_var( 'SELECT user_login FROM wp_users ORDER BY ID DESC LIMIT 1');
  
    if ( get_option( 'users_can_register' ) == TRUE ){ 
        echo '<h3>'. __('User self registration is <b>active</b>', 'thesis').'</h3>';
    
    } 
    else{
        echo '<h3>'. __('User self registration is <b>inactive</b>', 'thesis').'</h3>';
    }
  
    echo '<ul>';
  
    echo '<li>'. __('Number of registred users', 'thesis').' : <b>'. $user_count .'</b></li>';
    echo '<li>'. __('Last registered user is', 'thesis').' : <b>'. $last_user_name .'</b></li>';
  
    echo '</ul>';
    if ( get_option( 'users_can_register' ) == TRUE ){ 
        echo '<h3>'. __('You can ', 'thesis').'<b><a href="'. admin_url( 'options-general.php').'">'.__('disable', 'thesis').'</a></b>' .__(' user self registration from general settings', 'thesis').'</h3>';
    
    } 
    else{
        echo '<h3>'. __('You can ', 'thesis').'<b><a href="'. admin_url( 'options-general.php').'">'.__('enable', 'thesis').'</a></b>' .__(' user self registration from general settings', 'thesis').'</h3>';
    }
}
add_action('wp_dashboard_setup', 'users_widget');


// Loads custom CSS file for Login screen
function login_css()
{
    echo '<link rel="stylesheet" type="text/css" href="' . get_bloginfo('stylesheet_directory') . '/login/custom-login-style.css" />';


}
add_action('login_head', 'login_css');

// custom login logo url
function edit_login_logo_url() {
    return get_bloginfo( 'url' );
}
add_filter( 'login_headerurl', 'edit_login_logo_url' );
  
// custom login logo title
function custom_login_logo_url_title() {
    return 'Default Site Title';
}
add_filter( 'login_headertitle', 'custom_login_logo_url_title' );

// Redirect all frontend to login for logged out users
function login_redirect( $redirect_to, $request, $user )
{
    global $user;
    if( isset( $user->roles ) && is_array( $user->roles ) ) {
      if( in_array( "administrator", $user->roles ) ) {
         return $redirect_to;
        } 
        else {
             return home_url();
        }
    }
    else {
        return $redirect_to;
    }
}
// add_filter("login_redirect", "login_redirect", 10, 3);

// Custom login message
function login_message( $message ) {
    if ( empty($message) ){
        return '<h2 class="loginwhite">Συνδεθείτε για να συνεχίσετε</h2> <br>';
     
    } else {
        return $message;
    }
}
add_filter( 'login_message', 'login_message' );

// Custom login screen headertitle
function custom_headertitle() {

    echo '<br><br><h2 class="loginwhite">Καλώς ήρθατε στην διαδικτυακή υπηρεσία εξετάσεων</h2>';
}
add_action( 'login_headertitle', 'custom_headertitle' );

// Custom login screen footer copyright text
function copyright_footer() {
 
    // echo '<p class="loginwhite">©2019 - Heracles Michailidis </p>';
    echo '<p class="loginwhite">'.__( 'Developed by ', 'thesis' ).'<a style="color:white; text-decoration:none;" href="https://www.linkedin.com/in/heracles-michailidis/" target="_blank">'. __( 'Heracles Michailidis', 'thesis' ).'</a> - '. __( 'Supervised by ', 'thesis' ) .'<a style="color:white; text-decoration:none;" href="http://iiwm.teikav.edu.gr/iinew/Faculty%20Members/eleftherios-moisiadis/" target="_blank">'. __( 'Dr. Moussiades Eleftherios', 'thesis' ) .'</a>  </p>';

}
add_action( 'login_footer', 'copyright_footer' );

//  Disables comments on media
function filter_media_comment_status( $open, $post_id ) {
    $post = get_post( $post_id );
    if( $post->post_type == 'attachment' ) {
        return false;
    }
    return $open;
}
add_filter( 'comments_open', 'filter_media_comment_status', 10 , 2 );

// Removes WP logo
function remove_wplogo() {
    global $wp_admin_bar;
    $wp_admin_bar->remove_menu( 'wp-logo' );
    $wp_admin_bar->remove_menu('comments');
    $wp_admin_bar->remove_node( 'new-post' );
    $wp_admin_bar->remove_node( 'new-link' );
    if ( !current_user_can( 'administrator' ) ) {
        $wp_admin_bar->remove_menu('site-name');
    }

}
add_action( 'wp_before_admin_bar_render', 'remove_wplogo', 0 );

// add home link to admin bar for non admins
function add_nonadmin_home($admin_bar){
    if ( !current_user_can( 'administrator' ) ) {
        $admin_bar->add_menu( array(
            'id'    => 'non_admin_home',
            'title' => 'exam.devguides.net',
            'href'  => home_url(),
            'meta'  => array(
                'title' => __('Home', 'thesis'),            
            ),
        ));
    }
}
add_action('admin_bar_menu', 'add_nonadmin_home', 100);

// Fetch all semester pages from database and emit them via shortcode
function get_semesters(){
    global $wpdb;

    $semesters = $wpdb->get_results( "SELECT * FROM `wp_posts` WHERE `post_type` = 'page' AND `post_status` = 'publish' AND `post_title` LIKE '%Εξάμηνο'");
    ob_start();
    foreach ($semesters as $semester) {
        echo "<h2><a href=\"".esc_url( get_permalink($semester->ID) )."\">". $semester->post_title ."</a></h2>";
    }
    $buffer = ob_get_contents();
    ob_clean();
    if(!$buffer){
        $buffer = "<h2>". __('There are no semesters created, yet.', 'thesis')."</h2>";
    }

 return $buffer;

}
add_shortcode('semesters', 'get_semesters');

// Fetch all semester child pages(pages that contain the tests) from database and emit them via shortcode
function get_semester_child(){
    global $wpdb;
    global $post;
    $page_id = $post->ID;
     
   
    $semester_child = $wpdb->get_results( "SELECT * FROM `wp_posts` WHERE `post_type` = 'page' AND `post_status` = 'publish' AND `post_parent` = ".$page_id);
    ob_start();
    foreach ($semester_child as $child) {
        echo "<h2><a href=\"".esc_url( get_permalink($child->ID) )."\">". $child->post_title ."</a></h2>";
    }
    $buffer = ob_get_contents();
    ob_clean();
    if(!$buffer){
        $buffer = "<h2>". __('There are no exams in the semester, for now.', 'thesis')."</h2>";
    }

return $buffer;

}
add_shortcode('semester_exams', 'get_semester_child');

//  Combat FOUC in WordPress
function kill_fouc () {
    ?>
        <style type="text/css">
            .hidden {display:none;}
        </style>
        <script type="text/javascript">
         jQuery('html').addClass('hidden');
	            
	 jQuery(document).ready(function($) {		            
	    $('html').removeClass('hidden');	            
	 });  
        </script>
    <?php

}
add_action('wp_head', 'kill_fouc');

//disable xmlrpc server for security
add_filter('xmlrpc_enabled', '__return_false');