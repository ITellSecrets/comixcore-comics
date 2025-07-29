<?php
/**
 * Customizer settings and controls for ComixCore Comics plugin.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Register Customizer settings and controls for ComixCore Comics.
 *
 * @param WP_Customize_Manager $wp_customize The Customizer object.
 */
function comixcore_comics_customize_register( $wp_customize ) {

    // --- Add a new section for Comic Navigation Styles ---
    $wp_customize->add_section( 'comixcore_comics_navigation_section', array(
        'title'    => __( 'Comic Navigation Styles', 'comixcore-comics' ),
        'priority' => 160, // A moderate priority to place it below core sections
        'description' => __( 'Customize the background and text colors of the comic page navigation buttons.', 'comixcore-comics' ),
    ) );

    // --- Button Background Color ---
    $wp_customize->add_setting( 'comixcore_nav_button_bg_color', array(
        'default'           => '#333333', // Default light gray (matches previous suggestion)
        'type'              => 'theme_mod', // Stored as a theme mod
        'capability'        => 'edit_theme_options',
        'sanitize_callback' => 'sanitize_hex_color', // Ensures it's a valid hex color
        'transport'         => 'refresh', // 'postMessage' for live preview, 'refresh' for full page refresh
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'comixcore_nav_button_bg_color', array(
        'label'    => __( 'Button Background Color', 'comixcore-comics' ),
        'section'  => 'comixcore_comics_navigation_section',
    ) ) );

    // --- Button Text Color ---
    $wp_customize->add_setting( 'comixcore_nav_button_text_color', array(
        'default'           => '#EEEEEE', // Default dark gray text
        'type'              => 'theme_mod',
        'capability'        => 'edit_theme_options',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'refresh',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'comixcore_nav_button_text_color', array(
        'label'    => __( 'Button Text Color', 'comixcore-comics' ),
        'section'  => 'comixcore_comics_navigation_section',
    ) ) );

    // --- Button Background Hover Color ---
    $wp_customize->add_setting( 'comixcore_nav_button_bg_hover_color', array(
        'default'           => '#DDDDDD', // Default slightly darker gray for hover
        'type'              => 'theme_mod',
        'capability'        => 'edit_theme_options',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'refresh',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'comixcore_nav_button_bg_hover_color', array(
        'label'    => __( 'Button Background Hover Color', 'comixcore-comics' ),
        'section'  => 'comixcore_comics_navigation_section',
    ) ) );

    // --- Button Text Hover Color ---
    $wp_customize->add_setting( 'comixcore_nav_button_text_hover_color', array(
        'default'           => '#000000', // Default black text for hover
        'type'              => 'theme_mod',
        'capability'        => 'edit_theme_options',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'refresh',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'comixcore_nav_button_text_hover_color', array(
        'label'    => __( 'Button Text Hover Color', 'comixcore-comics' ),
        'section'  => 'comixcore_comics_navigation_section',
    ) ) );
}
add_action( 'customize_register', 'comixcore_comics_customize_register' );

/**
 * Output Customizer-generated CSS to the head of the site.
 * This function generates a <style> block with the custom colors.
 */
function comixcore_comics_customizer_css() {
    // Retrieve the colors saved in the Customizer, falling back to defaults
    $button_bg_color       = get_theme_mod( 'comixcore_nav_button_bg_color', '#EFEFEF' );
    $button_text_color     = get_theme_mod( 'comixcore_nav_button_text_color', '#333333' );
    $button_bg_hover_color = get_theme_mod( 'comixcore_nav_button_bg_hover_color', '#DDDDDD' );
    $button_text_hover_color = get_theme_mod( 'comixcore_nav_button_text_hover_color', '#000000' );
    ?>
    <style type="text/css">
        /* Customizer Styles for ComixCore Comic Navigation Buttons */
        .comic-navigation a {
            background-color: <?php echo esc_attr( $button_bg_color ); ?>;
            color: <?php echo esc_attr( $button_text_color ); ?>;
            border-color: <?php echo esc_attr( $button_bg_color ); ?>; /* Border color matches background for sleek look */
        }

        .comic-navigation a:hover,
        .comic-navigation a:focus {
            background-color: <?php echo esc_attr( $button_bg_hover_color ); ?>;
            color: <?php echo esc_attr( $button_text_hover_color ); ?>;
            border-color: <?php echo esc_attr( $button_bg_hover_color ); ?>;
        }
    </style>
    <?php
}
add_action( 'wp_head', 'comixcore_comics_customizer_css' );