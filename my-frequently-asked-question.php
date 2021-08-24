<?php
/**
 * Plugin Name: My-Frequently-Asked-Questions
 * Plugin URL: http://www.sandeepahir.com
 * Description: My Faq plugin is a simple and quick way to display faqs in your theme using shortcodes { [my_faqs] or [my_faqs limit="10"], [init_faqs category_list="1"] }.
 * Version: 1.0
 * Author: Sandeepkumar Ahir
 * Author URI: http://www.sandeepahir.com
 * Requires at least: 3.8
 */
 
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/*
 * Register FAQ post type
 */

function my_faqs_register_faqs() {
	
    $labels = array(
        'name'				=> _x( 'FAQs', 'my-faqs' ),
        'singular_name' 		=> _x( 'New', 'my-faqs' ),
        'menu_name' 			=> _x( 'FAQs', 'my-faqs' ),
        'all_items'			=> _x( 'All FAQs', 'my-faqs' ),
        'add_new' 			=> _x( 'Add New', 'FAQs', 'my-faqs' ),
        'add_new_item' 			=> _x( 'Add New FAQs', 'my-faqs' ),
        'edit_item' 			=> __( 'Edit FAQs', 'my-faqs' ),
        'new_item' 			=> __( 'New FAQs', 'my-faqs' ),
        'view_item' 			=> __( 'View FAQs', 'my-faqs' ),
        'search_items' 			=> __( 'Search FAQs', 'my-faqs' ),
        'not_found' 			=> __( 'No FAQs found', 'my-faqs' ),
        'not_found_in_trash'     	=> __( 'No FAQs found in Trash', 'my-faqs' ),
        'parent_item_colon'             => '',
        'public' 			=> true    
    );
    
    $args = array(
        'labels'			=> $labels,
        'show_in_menu'			=> true,
        'public'			=> true,
        'capability_type'               => 'post',
        'rewrite'			=> false,
        'supports'			=> array( 'title', 'editor', 'author', 'category' ),
        'hierarchical'			=> false,
        'show_ui'			=> true,
        'show_in_nav_menus'             => true,
        'query_var'			=> true,
        'rewrite'			=> array( 'slug' => 'my-faqs' ),
        'has_archive'			=> false 
    );

    register_post_type( 'my-faqs', $args );
		
}
add_action( 'init', 'my_faqs_register_faqs' );

function my_faqs_register_taxonomy_faqs(){
	
    $labels = array(
        'name'             		=> _x( 'Categories', 'my-faqs' ),
        'singular_name'            	=> _x( 'Category', 'my-faqs' ),
        'search_items'                  => __( 'Search FAQs Categories', 'my-faqs' ),
        'all_items'               	=> __( 'All FAQs Categories', 'my-faqs' ),
        'parent_item'                   => __( 'Parent FAQs Category', 'my-faqs' ),
        'parent_item_colon'             => __( 'Parent FAQs Category:', 'my-faqs' ),
        'edit_item'                 	=> __( 'Edit FAQs Category', 'my-faqs' ),
        'update_item'            	=> __( 'Update FAQs Category', 'my-faqs' ),
        'add_new_item'              	=> __( 'Add New FAQs Category', 'my-faqs' ),
        'new_item_name'                 => __( 'New FAQs Category Name', 'my-faqs' ),
        'menu_name'             	=> __( 'FAQs Category', 'my-faqs' ),
    );

    $args = array(
        'hierarchical'      	=> true,
        'labels'            	=> $labels,
        'show_ui'           	=> true,
        'show_admin_column' 	=> true,
        'query_var'         	=> true,
        'rewrite'           	=> array( 'slug' => 'my-faqs-cat' ),
    );

    register_taxonomy( 'my-faqs-cat', array( 'my-faqs' ), $args );
}
add_action( 'init', 'my_faqs_register_taxonomy_faqs' );

/*
 * Add [my_faqs] or [my_faqs limit="10"] shortcode
 * Add [my_faqs category_list="1"] shortcode
 */
 
function my_faqs_shortcode( $atts, $content = null ) {
	
    $taxonomy_name = 'my-faqs-cat';

    extract( shortcode_atts( array(
        "limit" 		=> '',
        "category" 		=> '',
        "category_list" 	=> '',
    ), $atts ) );
	
    // Define limit
    $posts_per_page = "-1";

    if( $limit != "" ) { 
        $posts_per_page = $limit; 
    }

    // Define Category
    $cat = '';

    if( $category != "" ) { 
        $cat = array(
            'taxonomy'	=> $taxonomy_name,
            'field'   	=> 'term_id',
            'terms'   	=> $category,
        );	
    }

    // Define category_list
    $cat_list = 0;

    if( $category_list == "1" ) { 
        $cat_list = 1; 
    }

    ob_start();

    // Create the Query
    $post_type 		= 'my-faqs';
    $orderby 		= 'post_date';
    $order              = 'DESC';
    $i 			= 1;
	
    if( $cat_list == "0" ) {

        $query = new WP_Query( 
                    array ( 
                        'post_type'             => $post_type,
                        'posts_per_page'        => $posts_per_page,
                        'post_status'           => 'publish',
                        'orderby'               => $orderby, 
                        'order'                 => $order,
                        'no_found_rows'         => 1,
                        'tax_query'             => array( $cat ),
                    ) 
        );	

        //Get post type count
        $post_count = $query->post_count;

        // Displays Custom post info		
        if( $post_count > 0 ) {

            $termchildren = get_term_children( $category, $taxonomy_name );

            $left_width	 = "width: 80%; float: left;";
            $right_width = "width: 20%; float: left;";

            if( $cat == '' || ( sizeof( $termchildren ) ) == 0 ){
                $left_width	 = "width: 100%;";
                $right_width = "";
            }

        ?>

            <div class="faq-page">

                <div class="faq-left" style="<?php echo $left_width; ?>">

                    <ul class="faq-list">	

                    <?php 
                        while ( $query->have_posts() ) : $query->the_post(); 

                            $allsubcat = get_the_terms( get_the_ID(), $taxonomy_name ); 

                            $i++;	
                            $subcatname = "";
	
                    ?>

                            <li <?php echo $subcatname; ?>>
                                <div class="title_content">
                                    <h3 class="faq_title"><?php the_title(); ?></h3>
                                    <div class="faq_content"><?php echo get_the_content(); ?></div>
                                </div>	
                            </li>
			
                    <?php endwhile; ?>
					
                    </ul>
					
                </div>

                <?php if( $cat != '' && sizeof( $termchildren ) > 0 ){ ?>
					
                        <div class="faq-right" style="<?php echo $left_width; ?>">
					
                        <?php
                            echo '<div class="cat-title">Category</div>';
                            echo '<ul class="cat-list">';
                            if( sizeof( $termchildren ) > 1 ) echo '<li class="-1" id="-1"><a href="javascript:;">All<span>'. ( $i - 1 ) .'</span></a> </li>';

                            foreach ( $termchildren as $child ) {
                                $term = get_term_by( 'id', $child, $taxonomy_name );
                                echo '<li class='. $term->slug .' id='. $term->slug .'><a href="javascript:;">' . $term->name . '<span>'. $term->count .'</span></a> </li>';
                            }
                            echo '</ul>';
                        ?> 
                        </div>	

                    <?php } ?>

                <div class="clear"></div>

            </div>	
	
    <?php 
        
        }else{
            echo 'No Faqs available';
        }
		
    }else{
	
        $terms = get_terms( $taxonomy_name, array(
            'orderby'    => 'count',
           'hide_empty' => 0
        ) );

        echo '<div class="faq-page">';
		
            foreach( $terms as $term ) {

                $args = array(
                        'post_type' 	=> $post_type,
                        $taxonomy_name 	=> $term->slug
                );

                $query = new WP_Query( $args ); ?>
					 
                <div class="faq-left">
                    <h2><?php echo $term->name; ?></h2>

                    <ul class="faq-list">

                    <?php 
                        while ($query->have_posts()) : $query->the_post();

                            $allsubcat = get_the_terms( get_the_ID(), $taxonomy_name ); 

                            $i++;
                            $subcatname = "";

                            foreach($allsubcat as $c){
                                if($c->parent != 0)	{
                                    $subcatname = 'id="' . $c->slug . '"';
                                    break;	
                                }
                            }
                            	
                    ?>        	

                            <li <?php echo $subcatname; ?>>
                                <div class="title_content">
                                    <h3 class="faq_title"><?php the_title(); ?></h3>
                                    <div class="faq_content"><?php echo get_the_content(); ?></div>
                                </div>	
                            </li>

                    <?php 
                        endwhile; ?>
                        
                    </ul>
                </div>
        <?php
            } 
	
    }
	
    wp_reset_query();

    return ob_get_clean();
}
add_shortcode( "my_faqs", "my_faqs_shortcode" );

function my_faqs_enqueue_style() {
    wp_enqueue_style( 'my-faq-style', plugins_url( '/css/my-frequently-asked-question-style.css', __FILE__ ), true );
}
add_action( 'wp_enqueue_scripts', 'my_faqs_enqueue_style' );

function my_faqs_enqueue_script() {
    wp_enqueue_script( 'init-faq-script', plugins_url( '/js/my-frequently-asked-question-script.js', __FILE__ ), array( 'jquery' ), true );
}
add_action( 'wp_enqueue_scripts', 'my_faqs_enqueue_script' );

function my_faqs_template_redirect()
{
    if( get_post_type() == 'my-faqs' )
    {
        wp_redirect( home_url() );
        exit();
    }
}
add_action( 'template_redirect', 'my_faqs_template_redirect' );
