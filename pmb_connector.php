<?php 
    /*
    Plugin Name: PMB connector
    Plugin URI: http://bu.uco-bs.com
    Description: Plugin for displaying PMB (php my bibli) notices
    Author: BU UCO BS
    Version: 1.0
    Author URI: http://bu.uco-bs.com
    */
// les includes des connecteur PMB

    /* 
    pmb_function.php -> les fct basiques
    */
include_once("/pmb_function.php");
define( 'PMBC_URL', plugin_dir_url ( __FILE__ ) ); 
define( 'PMBC_DIR', plugin_dir_path( __FILE__ ) ); //dossier principal du plugin

class wp_pmbc_plugin extends WP_Widget {

// constructor
function wp_pmbc_plugin() {
    parent::WP_Widget(false, $name = __('PMB Connector Widget', 'wp_widget_plugin') );
}

// widget form creation
function form($instance) {
// Check values
if( $instance) {
     $title = esc_attr($instance['title']);
     $text = esc_attr($instance['text']);
     $textarea = esc_textarea($instance['textarea']);
} else {
     $title = '';
     $text = '';
     $textarea = '';
}
?>
<p>
<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Widget Title on site :', 'wp_widget_plugin'); ?></label>
<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
</p>

<p>
<label for="<?php echo $this->get_field_id('text'); ?>"><?php _e('month before :', 'wp_widget_plugin'); ?></label>
<input class="widefat" id="<?php echo $this->get_field_id('text'); ?>" name="<?php echo $this->get_field_name('text'); ?>" type="text" value="<?php echo $text; ?>" />
</p>

<p>
<label for="<?php echo $this->get_field_id('textarea'); ?>"><?php _e('Inline DIV css:', 'wp_widget_plugin'); ?></label>
<textarea class="widefat" id="<?php echo $this->get_field_id('textarea'); ?>" name="<?php echo $this->get_field_name('textarea'); ?>"><?php echo $textarea; ?></textarea>
</p>
<?php
}

// update widget
function update($new_instance, $old_instance) {
      $instance = $old_instance;
      // Fields
      $instance['title'] = strip_tags($new_instance['title']);
      $instance['text'] = strip_tags($new_instance['text']);
      $instance['textarea'] = strip_tags($new_instance['textarea']);
     return $instance;
}
// new in pmb display widget
function widget($args, $instance) {
  extract( $args );
  // these are the widget options
  $title = apply_filters('widget_title', $instance['title']);
  $text = $instance['text'];
  $textarea = $instance['textarea'];
  echo $before_widget;
  // Display the widget
  // Check if title is set
  if ( $title ) {
      echo $before_title . $title . $after_title;
   }
  // pmbLoad html produit du html et pmbGetRandomNews renvoie id aléatoire pmb d'un notice livre saisie depuis la date defini
  // calculons la date x mois avant aujourd'hui
  $maDate = date("Y-m-d", strtotime('-'.$text.' month')); 
  $pp = pmbLoad('html','id',pmbGetRandomNews($maDate));
  // truc de dingue remplacement du style inline ici en mode pmb
  $pp = str_replace('!!STYLE!!', 'style="'.$textarea.'"', $pp);
  echo($pp); // affiche ze l'html du coup car le pmbLoad le formate
  echo $after_widget;
 }
}
function pmbNotice($incomingfrompost){
$ifp = (object) $incomingfrompost;
$pp = pmbLoad('notice',$ifp->type,$_GET['notice']);
}
function pmbCarousel($incomingfrompost){
$ifp = (object) $incomingfrompost;
//var_dump($ifp);
$pp = htmlCarousel($ifp->type,$ifp->max,$ifp->value,$_GET['params']);
// truc de dingue remplacement du style inline ici en mode pmb
$pp = str_replace('!!CONTENER!!', $ifp->contener, $pp);
$pp = str_replace('!!SLIDE!!', $ifp->slide, $pp);
$pp = str_replace('!!ARROWLEFT!!', $ifp->arrowleft, $pp);
$pp = str_replace('!!ARROWRIGHT!!', $ifp->arrowright, $pp);
echo $pp;
}
function pmbc_admin() {
    include('pmbc_admin.php');
}
function pmbc_admin_actions() {
    add_options_page("PMB connector", "PMB connector", 1, "PMB connector", "pmbc_admin");
}
/* MES BOUTONS EDITEURS */
function pmbc_add_buttons() {
  global $typenow;
  // on active le plugin pour les articles et les pages
  if(! in_array($typenow, array('post', 'page')))
    return ;  
  // ce filtre permet d'ajouter du javascript arbitraire à l'éditeur de WP
  add_filter('mce_external_plugins', 'pmbc_btn_logos_plg');
    
  // On ajoute notre bouton à la première ligne de boutons
  add_filter('mce_buttons', 'pmbc_btn_logos');
}
function pmbc_btn_logos_plg($plugin_array) {
  $plugin_array['pmbc_btn_logos'] = plugins_url('mcebuttons/pmbclogos.js.php', __FILE__);
  return $plugin_array;
}
function pmbc_btn_logos($buttons) {
  array_push($buttons, 'pmbc_btn_logos');
  return $buttons;
}

// charger les scripts JS perso ou pas !! et des feuilles de style de plugins, widget et autres shortcodes
/**
 * Enqueue a script with jQuery as a dependency.
 */
function mes_js() {
  // Carousel de PMBc
  $chem = PMBC_URL.'carousel';
  wp_enqueue_script( 'jssor', $chem . '/js/jssor.slider.mini.js', array( 'jquery' ) );
  wp_enqueue_script( 'params',$chem . '/js/params.js', array( 'jquery' ) );
  wp_enqueue_style( 'carousel.css',$chem . '/carousel.css');
}

// registered ACTION Wordpress
add_shortcode('pmbNotice', 'pmbNotice');
add_shortcode('pmbCarousel', 'pmbCarousel');
add_action('admin_menu', 'pmbc_admin_actions');
add_action( 'wp_enqueue_scripts', 'mes_js' );
// register widget
add_action('widgets_init', create_function('', 'return register_widget("wp_pmbc_plugin");'));
// register editor buttons
add_action('admin_head', 'pmbc_add_buttons');
?>