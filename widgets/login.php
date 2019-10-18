<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * UsersWP forgot password widget.
 *
 * @since 1.0.22
 */

class UWP_Login_Widget extends WP_Super_Duper {

	/**
	 * Register the login widget with WordPress.
	 *
	 */
    public function __construct() {

        $options = array(
            'textdomain'    => 'userswp',
            'block-icon'    => 'admin-site',
            'block-category'=> 'widgets',
            'block-keywords'=> "['userswp','login']",
            'class_name'     => __CLASS__,
            'base_id'       => 'uwp_login',
            'name'          => __('UWP > Login','userswp'),
            'widget_ops'    => array(
                'classname'   => 'uwp-login-class bsui',
                'description' => esc_html__('Displays login form or current logged in user.','userswp'),
            ),
            'arguments'     => array(
                'title'  => array(
                    'title'       => __( 'Widget title', 'userswp' ),
                    'desc'        => __( 'Enter widget title', 'userswp' ),
                    'type'        => 'text',
                    'desc_tip'    => true,
                    'default'     => '',
                    'advanced'    => false
                ),
                'form_title'  => array(
                    'title'       => __( 'Form title', 'userswp' ),
                    'desc'        => __( 'Enter the form title (or "0" for no title)', 'userswp' ),
                    'type'        => 'text',
                    'desc_tip'    => true,
                    'default'     => '',
                    'placeholder' => __('Login','userswp'),
                    'advanced'    => true
                ),
                'form_padding'  => array(
                    'title'       => __( 'Form padding', 'userswp' ),
                    'desc'        => __( 'Enter the px value for the form padding, default is 40, 10 looks better in sidbars.', 'userswp' ),
                    'type'        => 'number',
                    'desc_tip'    => true,
                    'default'     => '',
                    'placeholder' => __('default 40, 10 is better in sidebars','userswp'),
                    'advanced'    => true
                ),
                'logged_in_show'  => array(
                    'title' => __('Logged in show', 'userswp'),
                    'desc' => __('What to show when logged in.', 'userswp'),
                    'type' => 'select',
                    'options'   =>  array(
                        ""        =>  __('User Dashboard (default)', 'userswp'),
                        "simple"        =>  __('Simple username and logout link', 'userswp'),
                        "empty"        =>  __('Nothing', 'userswp'),
                    ),
                    'default'  => '',
                    'desc_tip' => true,
                    'advanced' => true
                ),
                'design_style'  => array(
                    'title' => __('Design Style', 'userswp'),
                    'desc' => __('The design style to use.', 'userswp'),
                    'type' => 'select',
                    'options'   =>  array(
                        ""        =>  __('default', 'userswp'),
                        "bs1"        =>  __('Style 1', 'userswp'),
                    ),
                    'default'  => '',
                    'desc_tip' => true,
                    'advanced' => true
                ),
                'css_class'  => array(
                    'type' => 'text',
                    'title' => __('Extra class:', 'userswp'),
                    'desc' => __('Give the wrapper an extra class so you can style things as you want.', 'userswp'),
                    'placeholder' => '',
                    'default' => '',
                    'desc_tip' => true,
                    'advanced' => true,
                ),
            )

        );

        // GD options
        if(class_exists( 'GeoDirectory' )){
            $options['arguments']['disable_gd'] = array(
                'title' => __("Disable GeoDirectory links from the user dashboard.", 'userswp'),
                'type' => 'checkbox',
                'desc_tip' => true,
                'value'  => '1',
                'default'  => '',
                'advanced' => true,
                'element_require' => '[%logged_in_show%]==""',
            );
        }

        // WPI options
        if(class_exists( 'WPInv_Plugin' )){
            $options['arguments']['disable_wpi'] = array(
                'title' => __("Disable WP Invoicing links from the user dashboard.", 'userswp'),
                'type' => 'checkbox',
                'desc_tip' => true,
                'value'  => '1',
                'default'  => '',
                'advanced' => true,
                'element_require' => '[%logged_in_show%]==""',
            );
        }



        parent::__construct( $options );
    }

	/**
	 * The Super block output function.
	 *
	 * @param array $args
	 * @param array $widget_args
	 * @param string $content
	 *
	 * @return mixed|string
	 */
    public function output( $args = array(), $widget_args = array(), $content = '' ) {

        $defaults = array(
            'form_title'      => __('Login','userswp'),
            'form_padding'     => '',
            'logged_in_show'     => '',
            'css_class'     => 'border-0'
        );

        /**
         * Parse incoming $args into an array and merge it with $defaults
         */
        $args = wp_parse_args( $args, $defaults );

        // if logged in and set to show nothing then bail.
        if(is_user_logged_in() && $args['logged_in_show']=='empty'){
            return '';
        }

        global $uwp_widget_args;
        $uwp_widget_args = $args;

        $design_style = !empty($args['design_style']) ? esc_attr($args['design_style']) : uwp_get_option("design_style",'bootstrap');

        ob_start();
        
        if(is_user_logged_in() && !is_admin() && !$this->is_preview()) {

            if($args['logged_in_show']=='simple'){
                $template = $design_style ? $design_style."/dashboard-simple" : "dashboard-simple";
            }else{

                $user_id = get_current_user_id();
                
                $dashboard_links = array(
                    'placeholder' => array(
                        'url' => '',
                        'text' => __('Select an action','userswp'),
                        'disabled' => true,
                        'selected' => true,
                        'display_none' => true
                    )
                );

                $dashboard_links['uwp_profile'][] = array(
                    'optgroup' => 'open',
                    'text' => esc_attr( __( 'My Profile', 'userswp' ) )
                );
                $dashboard_links['uwp_profile'][] = array(
                    'url' => uwp_build_profile_tab_url( $user_id ),
                    'text' => esc_attr( __( 'View Profile', 'userswp' ) )
                );
                $account_page = uwp_get_page_id('account_page', false);
                $account_link = get_permalink( $account_page );
                if($account_link){
                    $dashboard_links['uwp_profile'][] = array(
                        'url' => $account_link,
                        'text' => esc_attr( __( 'Edit Profile', 'userswp' ) )
                    );
                }

                $dashboard_links['uwp_profile'][] = array(
                    'optgroup' => 'close',
                );
                
                $dashboard_links = apply_filters( 'uwp_dashboard_links',$dashboard_links,$uwp_widget_args);

                $uwp_widget_args['template_args']= array(
                    'dashboard_links' => $dashboard_links
                );
                
                $template = $design_style ? $design_style."/dashboard" : "dashboard";
            }

        } else {
            $template = $design_style ? $design_style."/login" : "login";
        }

        echo '<div class="uwp_page">';

        uwp_locate_template($template);

        echo '</div>';


        $output = ob_get_clean();


        return trim($output);

    }
        
}