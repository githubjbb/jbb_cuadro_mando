$( document ).ready( function () {

	$(".btn-danger").click(function () {				
			//Activa icono guardando
			Swal.fire({
				title: "Eliminar",
                text: "¿ Esta seguro de eliminar la actividad ? Se borrará toda la información relacionada con la actividad.",
                icon: "warning",
                confirmButtonText: "Confirmar",
                showCancelButton: true,
                cancelButtonColor: "#DD6B55"
			}).then((result) => {
				if (result.isConfirmed) {
					$(".btn-danger").attr('disabled','-1');
					var idActividad = $(this).attr("id");
					$.ajax ({
						type: 'POST',
						url: base_url + 'settings/delete_actividadPI',
						data: {'idActividad': idActividad},
						cache: false,
						success: function(data){
												
							if( data.result == "error" )
							{
								alert(data.mensaje);
								$(".btn-danger").removeAttr('disabled');							
								return false;
							} 
											
							if( data.result )//true
							{	                                                        
								$(".btn-danger").removeAttr('disabled');
								var url = base_url + "settings/actividadesPI/" + data.idPlanIntegrado;
								$(location).attr("href", url);
							}
							else
							{
								alert('Error. Reload the web page.');
								$(".btn-danger").removeAttr('disabled');
							}	
						},
						error: function(result) {
							alert('Error. Reload the web page.');
							$(".btn-danger").removeAttr('disabled');
						}

					});
				}
			});
	});
		
	$( "#formEstado" ).validate( {
		rules: {
			observacion:			{ required: true }
		},
		errorElement: "em",
		errorPlacement: function ( error, element ) {
			// Add the `help-block` class to the error element
			error.addClass( "help-block" );
			error.insertAfter( element );

		},
		highlight: function ( element, errorClass, validClass ) {
			$( element ).parents( ".col-sm-8" ).addClass( "has-error" ).removeClass( "has-success" );
		},
		unhighlight: function (element, errorClass, validClass) {
			$( element ).parents( ".col-sm-8" ).addClass( "has-success" ).removeClass( "has-error" );
		},
		submitHandler: function (form) {
			return true;
		}
	});

	$("#btnEstado").click(function(){		
	
		if ($("#formEstado").valid() == true){
		
				//Activa icono guardando
				$('#btnEstado').attr('disabled','-1');
			
				$.ajax({
					type: "POST",	
					url: base_url + "settings/save_estado_actividadPI",	
					data: $("#formEstado").serialize(),
					dataType: "json",
					contentType: "application/x-www-form-urlencoded;charset=UTF-8",
					cache: false,
					
					success: function(data){
                                            
						if( data.result == "error" )
						{
							//alert(data.mensaje);
							$("#div_cargando").css("display", "none");
							$('#btnEstado').removeAttr('disabled');							
							
							$("#span_msj").html(data.mensaje);
							$("#div_msj").css("display", "inline");
							return false;
						
						} 

						if( data.result )//true
						{	                                                        
							$("#div_cargando").css("display", "none");
							$("#div_guardado").css("display", "inline");
							$('#btnEstado').removeAttr('disabled');

							var url = base_url + "settings/actividadesPI/" + data.record;
							$(location).attr("href", url);
						}
						else
						{
							alert('Error. Reload the web page.');
							$("#div_cargando").css("display", "none");
							$("#div_error").css("display", "inline");
							$('#btnEstado').removeAttr('disabled');
						}	
					},
					error: function(result) {
						alert('Error. Reload the web page.');
						$("#div_cargando").css("display", "none");
						$("#div_error").css("display", "inline");
						$('#btnEstado').removeAttr('disabled');
					}
					
		
				});	
		
		}//if			
	});

});