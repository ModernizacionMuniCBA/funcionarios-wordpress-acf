(function($){
	function initialize_field($el) {
		var $inputBusqueda = $el.find('#acf-field-input-funcionarios-cba-arg');
		var $botonBusqueda = $el.find('button');
		var $selectResultados = $el.find('select');
		var $sinResultados = $el.find('.acf-field-sin-resultados');

		$botonBusqueda.click(function(e){
			e.preventDefault();
			e.stopPropagation();
			var value = $inputBusqueda.val();

			if (value !== '') {
				$botonBusqueda.html('...');	

				$.ajax({
					type: "POST",
					dataType: "JSON",
					url: buscarFuncionarios.url,
					data: {
						action: 'buscar_funcionarios',
						nonce: buscarFuncionarios.nonce,
						query: value
					},
					success: function(response) {
						if (response.data && response.data.length > 0) {
							let texto = '';
							response.data.forEach(function(f){
								texto += '<option value="' + f.id + '">' + f.cargo.oficina + ': '+ f.funcionario.nombrepublico + '</option>';
							});
							$selectResultados.html('');
							$selectResultados.append(texto);
							$sinResultados.hide();
							$selectResultados.parent().show();
						} else {
							$selectResultados.parent().hide();
							$sinResultados.show();
						}
						$botonBusqueda.html('Buscar');
					},
					error: function() {
						$botonBusqueda.html('Buscar');
					}
				});
			}
		});

		$inputBusqueda.keyup(function(e){
			if(e.keyCode == 13)	{
				$botonBusqueda.click();
			}
		});
	}
	if( typeof acf.add_action !== 'undefined' ) {
		acf.add_action('ready append', function( $el ){
			acf.get_fields({ type : 'funcionarios_cba_arg'}, $el).each(function(){
				initialize_field( $(this) );
			});
		});
	} else {
		$(document).on('acf/setup_fields', function(e, postbox){
			$(postbox).find('.field[data-field_type="funcionarios_cba_arg"]').each(function(){
				initialize_field( $(this) );
			});
		});
	}
})(jQuery);
