$( document ).ready( function () {

	$(".btn-danger").click(function () {				
			//Activa icono guardando
			Swal.fire({
				title: "Cerrar Trimestre",
                text: "¿ Por favor confirmar que desea cerrar el Trimestre ? \n Se notificará al Supervisor para que realice la revisión del Trimestre.",
                icon: "warning",
                confirmButtonText: "Confirmar",
                showCancelButton: true,
                cancelButtonColor: "#DD6B55"
			}).then((result) => {
				if (result.isConfirmed) {
					$(".btn-danger").attr('disabled','-1');
					var numeroTrimestre = $(this).attr("id");
					var idPlanIntegrado = $('#idPlanIntegrado').val();
				    var numeroActividadPI = $('#numeroActividadPI').val();
				    var avancePOA = $('#avancePOA').val();
					if(numeroTrimestre == 1){
						var cumplimientoTrimestre = $('#cumplimiento1').val();
					}else if(numeroTrimestre == 2){
						var cumplimientoTrimestre = $('#cumplimiento2').val();
					}else if(numeroTrimestre == 3){
						var cumplimientoTrimestre = $('#cumplimiento3').val();
					}else if(numeroTrimestre == 4){
						var cumplimientoTrimestre = $('#cumplimiento4').val();
					}

					$.ajax ({
						type: 'POST',
						url: base_url + 'settings/update_trimestrePI',
						data: {'numeroTrimestre': numeroTrimestre,'idPlanIntegrado': idPlanIntegrado,'numeroActividadPI': numeroActividadPI,'cumplimientoTrimestre': cumplimientoTrimestre,'avancePOA': avancePOA },
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
								var url = base_url + "settings/actividadesPI/" + data.idPlanIntegrado + "/" + data.numeroActividadPI;
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
	
});