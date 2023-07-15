<?php
/**
 * Plugin Name: Seren - Simulateur de crédit immobilier
 * Plugin URI: https://github.com/cedricmillet
 * Description: Un plugin écrit sur mesure pour simuler le cout d'un crédit
 * Version: 1.0.0
 * Author: Cédric
 * Author URI: https://cedricmillet.fr
 * License: GPL
 */

if (!defined("ABSPATH")) exit; 

// DEV ONLY
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

class SerenLoanCalculator
{
	function __construct( $args = array() )
	{
        //  Register .css files
		$this->init_css();

        if(is_admin()) {
            //  Init admin menu
            add_action('admin_menu', array($this,'init_admin_menu'));
            //  Init database fields
            add_action('admin_init', array( $this, 'setup_sections' ) );
        }

        require_once( __DIR__.'/includes/calculator.shortcode.php' );
	}

    //-----------------------------------------
    // Register CSS files
    //-----------------------------------------
	private function init_css() {
		wp_enqueue_style( strtolower(__CLASS__).'-global-style', plugin_dir_url(__FILE__) . 'assets/css/style.global.css' );
        if(is_admin())
            wp_enqueue_style( strtolower(__CLASS__).'-admin-style', plugin_dir_url(__FILE__) . 'assets/css/style.admin.css' );
	}

    //-----------------------------------------
    // Init admin menu
    //-----------------------------------------
	public function init_admin_menu() {
        $page_title = 'Simulateur de crédit';
        $menu_title = 'Simulateur de crédit';
        $capability = 'manage_options';
        $slug = 'seren-loan-calculator';
        $callback = array($this,'seren_loan_admin_menu_content');
        $icon = 'dashicons-media-spreadsheet';
		add_menu_page($page_title, $menu_title, $capability, $slug, $callback, $icon);
        
	}

    //-----------------------------------------
    // Load admin page
    //-----------------------------------------
    public function seren_loan_admin_menu_content() {
        
        
        require_once( __DIR__.'/admin/index.php' );
    }

    public function setup_sections() {
        add_settings_section( strtolower(__CLASS__).'general', 'Paramètres par défaut', array( $this, 'section_callback' ), 'smashing_fields' );
        //add_settings_section( strtolower(__CLASS__).'our_second_section', 'My Second Section Title', array( $this, 'section_callback' ), 'smashing_fields' );
        //add_settings_section( strtolower(__CLASS__).'our_third_section', 'My Third Section Title', array( $this, 'section_callback' ), 'smashing_fields' );
        
        $fields = array(
            array(
                'uid' => 'frais_notaire_neuf',
                'label' => 'Frais de notaire dans le neuf',
                'section' => strtolower(__CLASS__).'general',
                'type' => 'number',
                'options' => false,
                'placeholder' => 'Un nombre entre 0 et 100.',
                'helper' => '(en % de la valeur du bien)',
                'supplemental' => '',
                'default' => '3'
            ),
            array(
                'uid' => 'apport_recommande',
                'label' => 'Apport recommandé',
                'section' => strtolower(__CLASS__).'general',
                'type' => 'number',
                'options' => false,
                'placeholder' => 'Un nombre entre 0 et 100.',
                'helper' => '(en % de la valeur du bien)',
                'supplemental' => '',
                'default' => '30'
            ),
            array(
                'uid' => 'taux_par_default',
                'label' => 'Taux',
                'section' => strtolower(__CLASS__).'general',
                'type' => 'number',
                'options' => false,
                'placeholder' => 'Un nombre entre 0 et 100.',
                'helper' => '(en %, assurance comprise)',
                'supplemental' => '',
                'default' => '4.04'
            ),
            array(
                'uid' => 'duree_par_default',
                'label' => 'Durée de l\'emprunt',
                'section' => strtolower(__CLASS__).'general',
                'type' => 'number',
                'options' => false,
                'placeholder' => 'Par exemple 7.',
                'helper' => '(en années)',
                'supplemental' => '',
                'default' => '20'
            ),
        );
        foreach( $fields as $field ){
            add_settings_field( $field['uid'], $field['label'], 'field_callback', 'smashing_fields', $field['section'], $field );
            register_setting( 'smashing_fields', $field['uid'] );
        }
    }

    public function section_callback( $arguments ) {
        switch( $arguments['id'] ){
            case 'our_first_section':
                echo 'This is the first description here!';
                break;
            case 'our_second_section':
                echo 'This one is number two';
                break;
            case 'our_third_section':
                echo 'Third time is the charm!';
                break;
        }
    }


}

$instance = new SerenLoanCalculator();


