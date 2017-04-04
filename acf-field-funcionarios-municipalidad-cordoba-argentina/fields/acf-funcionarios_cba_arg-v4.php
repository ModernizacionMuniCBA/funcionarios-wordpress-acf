<?php

if( ! defined( 'ABSPATH' ) ) exit;

if( !class_exists('acf_field_funcionarios_cba_arg') ) :

class acf_field_funcionarios_cba_arg extends acf_field {

	var $settings,
		$defaults,
		$nonce_busquedas;

	function __construct( $settings )
	{
		$this->name = 'funcionarios_cba_arg';
		$this->label = __('Funcionarios de Córdoba');
		$this->category = __("Choice",'acf');

    	parent::__construct();

		$this->settings = $settings;
	
		add_action('wp_ajax_buscar_funcionarios', array($this, 'buscar_funcionarios')); 
		add_action('wp_ajax_nopriv_funcionarios', array($this, 'buscar_funcionarios'));
	}


	function create_field( $field )
	{
		?>
	<div data-field_type="<?php echo $field['type']; ?>">
	<?php if($field['value']) { ?>
	<p id="funcionario">Funcionario Actual: <?php echo $field['value']['funcionario']['nombrepublico'] . ' (' . $field['value']['cargo']['oficina'] . ')'; ?></p>
	<?php } ?>
	<div class="acf-input-wrap">
		<input id="acf-field-input-funcionarios-cba-arg" type="text" class="text" placeholder="Buscar por nombre del funcionario u oficina..." /><button class="button">Buscar</button>
	</div>
	<div class="acf-field-resultados">
		<p>Seleccione un resultado:</p>
		<select class="acf-field-select-funcionarios-cba-arg select" name="fields[<?php echo $field["key"]; ?>]" id="acf-resultados-funcionarios-cba-arg">
			<?php if($field['value']) { ?>
				<option value="<?php echo $field['value']['id']; ?>"></option>
			<?php } ?>
		</select>
	</div>
	<div class="acf-field-sin-resultados">
		<p>No se encontraron resultados.</p>
	</div>
</div>
		<?php
	}

	function input_admin_enqueue_scripts()
	{
		$url = $this->settings['url'];
		$version = $this->settings['version'];

		wp_register_script('acf-input-funcionarios_cba_arg', "{$url}assets/js/input.js", array('acf-input'), $version);
		wp_enqueue_script('acf-input-funcionarios_cba_arg');
		
		$nonce_busquedas = wp_create_nonce("buscar_funcionarios_nonce");

		wp_localize_script( 
			'acf-input-funcionarios_cba_arg', 
			'buscarFuncionarios', 
			array(
				'url'   => admin_url('admin-ajax.php'),
				'nonce' => $nonce_busquedas
			)
		);

		wp_register_style('acf-input-funcionarios_cba_arg', "{$url}assets/css/input.css", array('acf-input'), $version);
		wp_enqueue_style('acf-input-funcionarios_cba_arg');
	}

	function load_value( $value, $post_id, $field )
	{
		if ($value) {
			$api_response = wp_remote_get('https://gobiernoabierto.cordoba.gob.ar/api/funciones/'.$value);
			$respuesta = json_decode(wp_remote_retrieve_body($api_response), true);
			
			if ($respuesta && !empty($respuesta)) {
				$value = $respuesta;
			} else {
				$value = null;
			}
		}
		return $value;
	}
	
	function format_value_for_api( $value, $post_id, $field )
	{
		if ($value && isset($value['cargo']) && isset($value['cargo']['superioresids'])) {
			if(($key = array_search(1, $value['cargo']['superioresids'])) !== false) {
				unset($value['cargo']['superioresids'][$key]);
			}
			
			foreach($value['cargo']['superioresids'] as $cargo) {
				$api_response = wp_remote_get('https://gobiernoabierto.cordoba.gob.ar/api/funciones/?cargo_id='.$cargo);
				$respuesta = json_decode(wp_remote_retrieve_body($api_response), true);
				
				if ($respuesta['results'] && !empty($respuesta['results'])) {
					$value['jerarquia'][] = $respuesta['results'][0]['cargo']['oficina'];
				}
			};
		}
		
		return $value;
	}
	
	public function buscar_funcionarios()
	{
		check_ajax_referer( 'buscar_funcionarios_nonce', 'nonce' );
		
		$q = $_REQUEST['query'];
		
		if(true) {
			$api_response = wp_remote_get( 'https://gobiernoabierto.cordoba.gob.ar/api/funciones/?q='.$q );
			$respuesta = json_decode(wp_remote_retrieve_body($api_response), true);
			$funcionarios = $respuesta['results'];
		
			wp_send_json_success($funcionarios);
		} else {
			wp_send_json_error(array('error' => print_r($_REQUEST)));
		}
		
		die();
	}
}

new acf_field_funcionarios_cba_arg( $this->settings );

endif;

?>