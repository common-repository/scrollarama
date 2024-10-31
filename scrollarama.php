<?php
/**
 * Plugin Name: Scrollarama
 * Plugin URI: http://www.maltpress.co.uk
 * Description: Rotates posts using jQuery cycle
 * Version: 1.1.1
 * Author: Adam Maltpress
 * Author URI: http://www.maltpress.co.uk
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

/**
 * Add function to widgets_init that'll load our widget.
 *
 * @since 1.0
 */
add_action( 'widgets_init', 'postrotate_load_widgets' );

/**
 * Register our widget.
 *
 * @since 1.0
 */
function postrotate_load_widgets() {
	register_widget( 'Post_Rotate' );
}

/* add scripts and styles to header */
add_action('wp_head',  'pr_add_styles');
$pr_plugin_directory = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__));
$jquery_cycle = $pr_plugin_directory . 'scripts/jquery.cycle.all.min.js';
wp_register_script('jquery_cycle', $jquery_cycle);
wp_enqueue_script('jquery');
wp_enqueue_script('jquery_cycle');

/**
 * determines background image
 *
 * determines background image based on post attachment. If no attachment, use a fallback
 *
 * @param integer $looped_ID ID of the post we want the attachement from
 */

function pr_determineBackground ($looped_ID) {
    # get attachment if available
    $args_attach = array(
            'post_type' => 'attachment',
            'numberposts' => null,
            'post_status' => null,
            'post_parent' => $looped_ID
    );
    $attachments = get_posts($args_attach);

    if(get_post_meta($looped_ID, 'slider', true) != '') { # if there's an image set in custom fields
        $background = "url(" . get_post_meta($looped_ID, 'slider', true) . ") center no-repeat;";
    }
    else if ($attachments) { # no custom field, but post has attachment
        $attachment = $attachments[0];
        $background = "url(" . $attachment->guid . ") center no-repeat;";
    } else { # no image at all
        $background = "none";
    }
    echo $background;
}


/**
 * Truncates text
 *
 * Truncates text blocks. Remember to use strip_tags() on the string you wish to truncate to avoid unpleasantness.
 *
 * Original PHP code by Chirp Internet: {@link http://www.chirp.com.au}
 *
 * @param string $string string to truncate
 * @param string $limit number of characters to truncate to
 * @param string $mt_break character to truncate to
 * @param string $mt_pad text to add to string to indicate truncation
 */


function pr_myTruncate($string, $limit, $mt_break=".", $mt_pad="...") {
    // return with no change if string is shorter than $limit
    if(strlen($string) <= $limit) return $string;
    // is $break present between $limit and the end of the string?
    if(false !== ($mt_breakpoint = strpos($string, $mt_break, $limit))) {
        if($mt_breakpoint < strlen($string) - 1) { $string = substr($string, 0, $mt_breakpoint) . $mt_pad;
        }
    }
    return $string;
}

/**
 * Shows posts on page
 *
 * This function takes the number of posts to show and category and creates a dynamic loop from these. Excerpt is truncated using myTruncate function
 *
 * @param array $category_num category IDs to show (since 1.1, this is an array of possible categories)
 * @param integer $post_number  number of posts to show in loop
 * @param integer $pr_width width for cycle element
 * @param integer $pr_height height for cycle element
 */

function pr_showPosts($category_num, $post_number, $pr_width, $pr_height) {
   # make the array of cat IDs into a string
    // test it's an array
    // @since 1.1.1
    if (is_array($category_num)) {
        $category_string = implode(',',$category_num);
    } else {
        $category_string = $category_num;
    }

    # create a loop
   $pr_args = array(
       'cat' => $category_string,
       'posts_per_page' => $post_number
   );
   $widget_query = new WP_Query($pr_args);

   if($widget_query->have_posts()) {
       echo "<div class='pr_side_slider' style='";
       if ($pr_width != '') {
           echo "width: " . $pr_width . "px;";
       }
       if ($pr_height != '') {
           echo "height: " . $pr_height . "px;";
       }

       echo "'>";


   while ($widget_query->have_posts()) : $widget_query->the_post();
   $thisPost = $widget_query->post->ID;

   ?>
    <div class="pr_single_story" style="background: <?php pr_determineBackground($thisPost); ?>;">
        <div class="pr_wrapper">
            <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
            <p><?php 
                
            echo pr_myTruncate(strip_tags(get_the_excerpt()), 50, ' ');
            ?></p>
        </div>
    </div>
   <?php endwhile;
   echo "</div>";
   }
}

/**
 * Adds styles to header
 * 
 * used with add_action, adds style declaration in to wp_head
 */

function pr_add_styles() {
    $pr_plugin_directory = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__));
    echo "<link href='" . $pr_plugin_directory . "styles/scrollarama_style.css' type='text/css' rel='stylesheet' media='screen' />";
    
}

/**
 * Adds cycle function call to page
 *
 * Adds the cycle function call; can't be added to wp_head as this hook doesn't take parameters.
 * For documentation on jQuery cycle plugin and the parameters it accepts, see
 * {@link http://jquery.malsup.com/cycle/options.html}
 *
 * @param string $pr_effects jQuery cycle effect to be used
 * @param int $pr_transition added in 1.1, transition speed (how fast transition is)
 * @param int $pr_timeout added in 1.1, timeout speed (how long each transition shows)
 * @param string $pr_custom_attributes added in 1.1, add your own jQuery cycle custom attributes
 */

function pr_add_cycle($pr_effects, $pr_transition, $pr_timeout, $pr_custom_attributes) {
    $pr_output = "<script type='text/javascript'>";
    $pr_output .= "jQuery(document).ready(function() {";
    $pr_output .= "jQuery('.pr_side_slider').cycle({" ;
    $pr_output .= "fx: '" . $pr_effects . "'";
    if ($pr_transition != '') {
        $pr_output .= ", speed: " . $pr_transition;
    }
    if ($pr_timeout != '') {
        $pr_output .= ", timeout: " . $pr_timeout;
    }
    if ($pr_custom_attributes != '') {
        $pr_output .= prcheck_custom($pr_custom_attributes);
    }
    $pr_output .= "});});</script>";
    echo $pr_output;
}

/**
 * Checks custom input for naughtiness, leading comma, trailing comma etc
 *
 * This function takes custom input strings and makes sure there's a leading comma in the format we expect,
 * and can be extended to carry out other checks.
 *
 * @since 1.1
 * @param string $pr_stringtocheck the string to check out
 */

function prcheck_custom($pr_stringtocheck) {
    // first check for an initial comma:
    if (substr($pr_stringtocheck, 0, 1) == ",") {
        $pr_checkedstring = $pr_stringtocheck;
    } else {
        // we'll add a space in here for neatness, but it's not vital
        $pr_checkedstring = ", " . $pr_stringtocheck;
    }
    // now chop the trailing comma, although again this isn't vital
    // remember that we're now working with $pr_checkedstring
    if (substr($pr_checkedstring, -1) == ",") {
        $pr_checkedstring = substr($pr_checkedstring, 0, -1);
    }
    // now just clean it up with strip_tags (this should already have been done, but we'll catch it again):
    return strip_tags($pr_checkedstring);
}

/**
 * Post Rotate Widget class.
 * This class handles everything that needs to be handled with the widget:
 * the settings, form, display, and update.
 *
 * @since 1.0
 */

class Post_Rotate extends WP_Widget {

    /**
     * Widget setup.
     */
    function Post_Rotate() {
            /* Widget settings. */
            $widget_ops = array( 'classname' => 'Scrollarama', 'description' => __('Uses jQuery Cycle to rorate latest posts', 'Scrollarama') );

            /* Widget control settings. */
            $control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'scrollarama' );

            /* Create the widget. */
            $this->WP_Widget( 'scrollarama', __('Scrollarama', 'Scrollarama'), $widget_ops, $control_ops );
    }

    /**
     * How to display the widget on the screen.
     */
    function widget( $args, $instance ) {
            extract( $args );

            /* Our variables from the widget settings. */
            $title = apply_filters('widget_title', $instance['title'] );

            /* before widget */

            echo $before_widget;

            /* Display the widget title if one was input (before and after defined by themes). */
            if ( $title )
                    echo $before_title . $title . $after_title;

           /* widget output */
            $category_show = $instance['category'];
            $post_number_show = $instance['post_number'];
            $pr_width = $instance['pr_width'];
            $pr_height = $instance['pr_height'];
            $pr_effects = $instance['pr_effects'];
            $pr_transition = $instance['pr_transition'];
            $pr_timeout = $instance['pr_timeout'];
            $pr_custom_attributes = $instance['pr_custom_attributes'];
            pr_add_cycle($pr_effects, $pr_transition, $pr_timeout, $pr_custom_attributes);
            pr_showPosts($category_show, $post_number_show, $pr_width, $pr_height);

            /* After widget (defined by themes). */

            echo $after_widget;
    }

    /**
     * Update the widget settings.
     */
    function update( $new_instance, $old_instance ) {
            $instance = $old_instance;

            /* Strip tags for title and effects to remove HTML (important for text inputs). */
            $instance['title'] = strip_tags( $new_instance['title'] );
            $instance['category'] = $new_instance['category'];
            $instance['post_number'] = $new_instance['post_number'];
            $instance['pr_width'] = $new_instance['pr_width'];
            $instance['pr_height'] = $new_instance['pr_height'];
            $instance['pr_effects'] = strip_tags($new_instance['pr_effects']);
            $instance['pr_transition'] = strip_tags($new_instance['pr_transition']);
            $instance['pr_timeout'] = strip_tags($new_instance['pr_timeout']);
            $instance['pr_custom_attributes'] = strip_tags($new_instance['pr_custom_attributes']);
            return $instance;
    }

    /**
     * Displays the widget settings controls on the widget panel.
     */
    function form( $instance ) {

            /* Set up some default widget settings. */
            $defaults = array(
                    'title' => __('Latest posts', 'Latest posts'),
                    'category' => array ('1'),
                    'post_number' => '3',
                    'pr_height' => '200',
                    'pr_width' => '',
                    'pr_effects' => 'fade',
                    'pr_timeout' => '',
                    'pr_transition' => '',
                    'pr_custom_attributes' => ''
                );
            $instance = wp_parse_args( (array) $instance, $defaults );
            ?>

            <!-- Widget Title: Text Input -->
            <p>
                    <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:', 'hybrid'); ?></label>
                    <input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:100%;" />
            </p>

            <!-- Category: Select box -->
            <p>
                    <label for="<?php echo $this->get_field_id( 'category' ); ?>"><?php _e('Category (select multiple categories using Ctrl and/or shift):', 'hybrid'); ?></label>
                    
                    <select id="<?php echo $this->get_field_id( 'category' ); ?>" name="<?php echo $this->get_field_name( 'category' ); ?>[]" style="width:100%; height: auto !important;" multiple="multiple" size="5">
                        <?php $args=array(
                                  'orderby' => 'name',
                                  'order' => 'ASC'
                                  );
                                $categories=get_categories($args);
                                  foreach($categories as $category) { ?>
                        <option value="<?php echo $category->term_id; ?>" <?php if (in_array ($category->term_id,$instance['category'])) {echo " selected"; } ?> ><?php echo $category->name; ?></option>
                        <?php } ?>
                    </select>
            </p>

            <!-- Number of posts: Select box -->
            <p>
                    <label for="<?php echo $this->get_field_id( 'post_number' ); ?>"><?php _e('Number of posts:', 'hybrid'); ?></label>

                    <select id="<?php echo $this->get_field_id( 'post_number' ); ?>" name="<?php echo $this->get_field_name( 'post_number' ); ?>" style="width:100%;">
                        <option value="1"<?php if ($instance['post_number'] == 1) {echo " selected"; } ?>>1</option>
                        <option value="2"<?php if ($instance['post_number'] == 2) {echo " selected"; } ?>>2</option>
                        <option value="3"<?php if ($instance['post_number'] == 3) {echo " selected"; } ?>>3</option>
                        <option value="4"<?php if ($instance['post_number'] == 4) {echo " selected"; } ?>>4</option>
                        <option value="5"<?php if ($instance['post_number'] == 5) {echo " selected"; } ?>>5</option>
                        <option value="6"<?php if ($instance['post_number'] == 6) {echo " selected"; } ?>>6</option>
                        <option value="7"<?php if ($instance['post_number'] == 7) {echo " selected"; } ?>>7</option>
                        <option value="8"<?php if ($instance['post_number'] == 8) {echo " selected"; } ?>>8</option>
                        <option value="9"<?php if ($instance['post_number'] == 9) {echo " selected"; } ?>>9</option>
                        <option value="10"<?php if ($instance['post_number'] == 10) {echo " selected"; } ?>>10</option>
                    </select>
            </p>

            <!-- Cycle effects: Select box -->
            <p>
                Select a slider effect. To preview each effect, see <a href="http://jquery.malsup.com/cycle/browser.html" target="_blank">jQuery Cycle effects browser</a>
            </p>
            <p>
                    <label for="<?php echo $this->get_field_id( 'pr_effects' ); ?>"><?php _e('Slider effect:', 'hybrid'); ?></label>
                    <select id="<?php echo $this->get_field_id( 'pr_effects' ); ?>" name="<?php echo $this->get_field_name( 'pr_effects' ); ?>" style="width:100%;">
                        <option value="blindX"<?php if ($instance['pr_effects'] == 'blindX') {echo " selected"; } ?>>blindX</option>
                        <option value="blindY"<?php if ($instance['pr_effects'] == 'blindY') {echo " selected"; } ?>>blindY</option>
                        <option value="blindZ"<?php if ($instance['pr_effects'] == 'blindZ') {echo " selected"; } ?>>blindZ</option>
                        <option value="cover"<?php if ($instance['pr_effects'] == 'cover') {echo " selected"; } ?>>cover</option>
                        <option value="curtainX"<?php if ($instance['pr_effects'] == 'curtainX') {echo " selected"; } ?>>curtainX</option>
                        <option value="curtainY"<?php if ($instance['pr_effects'] == 'curtainY') {echo " selected"; } ?>>curtainY</option>
                        <option value="fade"<?php if ($instance['pr_effects'] == 'fade') {echo " selected"; } ?>>fade</option>
                        <option value="fadeZoom"<?php if ($instance['pr_effects'] == 'fadeZoom') {echo " selected"; } ?>>fadeZoom</option>
                        <option value="growX"<?php if ($instance['pr_effects'] == 'growX') {echo " selected"; } ?>>growX</option>
                        <option value="growY"<?php if ($instance['pr_effects'] == 'growY') {echo " selected"; } ?>>growY</option>
                        <option value="none"<?php if ($instance['pr_effects'] == 'none') {echo " selected"; } ?>>none</option>
                        <option value="scrollUp"<?php if ($instance['pr_effects'] == 'scrollUp') {echo " selected"; } ?>>scrollUp</option>
                        <option value="scrollDown"<?php if ($instance['pr_effects'] == 'scrollDown') {echo " selected"; } ?>>scrollDown</option>
                        <option value="scrollLeft"<?php if ($instance['pr_effects'] == 'scrollLeft') {echo " selected"; } ?>>scrollLeft</option>
                        <option value="scrollRight"<?php if ($instance['pr_effects'] == 'scrollRight') {echo " selected"; } ?>>scrollRight</option>
                        <option value="scrollHorz"<?php if ($instance['pr_effects'] == 'scrollHorz') {echo " selected"; } ?>>scrollHorz</option>
                        <option value="scrollVert"<?php if ($instance['pr_effects'] == 'scrollVert') {echo " selected"; } ?>>scrollVert</option>
                        <option value="shuffle"<?php if ($instance['pr_effects'] == 'shuffle') {echo " selected"; } ?>>shuffle</option>
                        <option value="slideX"<?php if ($instance['pr_effects'] == 'slideX') {echo " selected"; } ?>>slideX</option>
                        <option value="slideY"<?php if ($instance['pr_effects'] == 'slideY') {echo " selected"; } ?>>slideY</option>
                        <option value="toss"<?php if ($instance['pr_effects'] == 'toss') {echo " selected"; } ?>>toss</option>
                        <option value="turnUp"<?php if ($instance['pr_effects'] == 'turnUp') {echo " selected"; } ?>>turnUp</option>
                        <option value="turnDown"<?php if ($instance['pr_effects'] == 'turnDown') {echo " selected"; } ?>>turnDown</option>
                        <option value="turnLeft"<?php if ($instance['pr_effects'] == 'turnLeft') {echo " selected"; } ?>>turnLeft</option>
                        <option value="turnRight"<?php if ($instance['pr_effects'] == 'turnRight') {echo " selected"; } ?>>turnRight</option>
                        <option value="uncover"<?php if ($instance['pr_effects'] == 'uncover') {echo " selected"; } ?>>uncover</option>
                        <option value="wipe"<?php if ($instance['pr_effects'] == 'wipe') {echo " selected"; } ?>>wipe</option>
                        <option value="zoom"<?php if ($instance['pr_effects'] == 'zoom') {echo " selected"; } ?>>zoom</option>
                    </select>
            </p>

            <!-- Timeout: Text Input -->
            <p>
                    <label for="<?php echo $this->get_field_id( 'pr_timeout' ); ?>"><?php _e('Timeout - time each slide will show for (in milliseconds, i.e. 3000 = 3 seconds):', 'hybrid'); ?></label>
                    <input id="<?php echo $this->get_field_id( 'pr_timeout' ); ?>" name="<?php echo $this->get_field_name( 'pr_timeout' ); ?>" value="<?php echo $instance['pr_timeout']; ?>" style="width:80%;" />
            </p>

            <!-- Speed: Text Input -->
            <p>
                    <label for="<?php echo $this->get_field_id( 'pr_transition' ); ?>"><?php _e('Transition speed - how long the effect takes to complete (in milliseconds, i.e. 3000 = 3 seconds):', 'hybrid'); ?></label>
                    <input id="<?php echo $this->get_field_id( 'pr_transition' ); ?>" name="<?php echo $this->get_field_name( 'pr_transition' ); ?>" value="<?php echo $instance['pr_transition']; ?>" style="width:80%;" />
            </p>

            <!-- Custom attributes: Textarea -->
            <p>
                    <label for="<?php echo $this->get_field_id( 'pr_custom_attributes' ); ?>"><?php _e('Custom jQuery Cycle options - see <a href="http://jquery.malsup.com/cycle/options.html">jQuery Cycle options reference</a>. Add as comma separated key:value pairs - for example, "pause: 1, :', 'hybrid'); ?></label><br />
                    <textarea id="<?php echo $this->get_field_id( 'pr_custom_attributes' ); ?>" name="<?php echo $this->get_field_name( 'pr_custom_attributes' ); ?>" cols="30" rows="4"><?php echo $instance['pr_custom_attributes']; ?></textarea>
            </p>


            <!-- width: Text Input -->
            <p>
                    <label for="<?php echo $this->get_field_id( 'pr_width' ); ?>"><?php _e('Width (DO NOT include units, size in px only):', 'hybrid'); ?></label>
                    <input id="<?php echo $this->get_field_id( 'pr_width' ); ?>" name="<?php echo $this->get_field_name( 'pr_width' ); ?>" value="<?php echo $instance['pr_width']; ?>" style="width:80%;" />px
            </p>
            <!-- Height: Text Input -->
            <p>
                    <label for="<?php echo $this->get_field_id( 'pr_height' ); ?>"><?php _e('Height (DO NOT include units, size in px only):', 'hybrid'); ?></label>
                    <input id="<?php echo $this->get_field_id( 'pr_height' ); ?>" name="<?php echo $this->get_field_name( 'pr_height' ); ?>" value="<?php echo $instance['pr_height']; ?>" style="width:80%;" />px
            </p>
    <?php
    }     
} ?>