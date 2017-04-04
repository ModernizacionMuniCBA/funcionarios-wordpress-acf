<?php
/*
Plugin Name: Advanced Custom Fields: Funcionarios de Córdoba
Plugin URI: PLUGIN_URL
Description: Extensi&oacute;n para el plugin Advanced Custom Fields, genera un campo para buscar y seleccionar un funcionario de la Ciudad de Córdoba.
Version: 1.0.0
Author: Florencia Peretti
Author URI: https://github.com/florenperetti/
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

if( ! defined( 'ABSPATH' ) ) exit;

if( !class_exists('acf_plugin_funcionarios_cba_arg') ) :

class acf_plugin_funcionarios_cba_arg {
	function __construct() {
		$this->settings = array(
			'version'	=> '1.0.0',
			'url'		=> plugin_dir_url( __FILE__ ),
			'path'		=> plugin_dir_path( __FILE__ )
		);

		load_plugin_textdomain( 'acf-funcionarios_cba_arg', false, plugin_basename( dirname( __FILE__ ) ) . '/lang' ); 

		add_action('acf/include_field_types', 	array($this, 'include_field_types')); // v5
		add_action('acf/register_fields', 		array($this, 'include_field_types')); // v4
	}
	
	function include_field_types( $version = false ) {
		if( !$version ) $version = 4;
		include_once('fields/acf-funcionarios_cba_arg-v' . $version . '.php');
	}
}

new acf_plugin_funcionarios_cba_arg();

endif;
?>