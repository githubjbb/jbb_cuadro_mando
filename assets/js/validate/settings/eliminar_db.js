$( document ).ready( function () {

	$(".btn-danger").click(function () {
			//Activa icono guardando
			Swal.fire({
				title: "Eliminar",
                text: "¿ Esta seguro de eliminar los registros de la Base de Datos ?",
                icon: "warning",
                confirmButtonText: "Confirmar",
                showCancelButton: true,
                cancelButtonColor: "#DD6B55"
			}).then((result) => {
				if (result.isConfirmed) {
					$(".btn-danger").attr('disabled','-1');
					$.ajax ({
						type: 'POST',
						url: base_url + 'settings/eliminar_db',
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
								var url = base_url + "settings/atencion_eliminar";
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
	
	$(".btn-warning").click(function () {
			//Activa icono guardando
			Swal.fire({
				title: "Eliminar",
                text: "¿ Esta seguro de eliminar los registros de la Base de Datos ?",
                icon: "warning",
                confirmButtonText: "Confirmar",
                showCancelButton: true,
                cancelButtonColor: "#DD6B55"
			}).then((result) => {
				if (result.isConfirmed) {
					$(".btn-warning").attr('disabled','-1');
					$.ajax ({
						type: 'POST',
						url: base_url + 'settings/eliminar_metas_objetivos_db',
						cache: false,
						success: function(data){
							if( data.result == "error" )
							{
								alert(data.mensaje);
								$(".btn-warning").removeAttr('disabled');							
								return false;
							} 
							if( data.result )//true
							{	                                                        
								$(".btn-warning").removeAttr('disabled');
								var url = base_url + "settings/atencion_eliminar";
								$(location).attr("href", url);
							}
							else
							{
								alert('Error. Reload the web page.');
								$(".btn-warning").removeAttr('disabled');
							}	
						},
						error: function(result) {
							alert('Error. Reload the web page.');
							$(".btn-warning").removeAttr('disabled');
						}

					});
				}
			});
	});
	
	$(".btn-info").click(function () {
			//Activa icono guardando
			Swal.fire({
				title: "Eliminar",
                text: "¿ Esta seguro de eliminar los registros de la Base de Datos ?",
                icon: "warning",
                confirmButtonText: "Confirmar",
                showCancelButton: true,
                cancelButtonColor: "#DD6B55"
			}).then((result) => {
				if (result.isConfirmed) {
					$(".btn-info").attr('disabled','-1');
					$.ajax ({
						type: 'POST',
						url: base_url + 'settings/eliminar_indicadores_objetivos_db',
						cache: false,
						success: function(data){
							if( data.result == "error" )
							{
								alert(data.mensaje);
								$(".btn-info").removeAttr('disabled');							
								return false;
							} 
							if( data.result )//true
							{	                                                        
								$(".btn-info").removeAttr('disabled');
								var url = base_url + "settings/atencion_eliminar";
								$(location).attr("href", url);
							}
							else
							{
								alert('Error. Reload the web page.');
								$(".btn-info").removeAttr('disabled');
							}	
						},
						error: function(result) {
							alert('Error. Reload the web page.');
							$(".btn-info").removeAttr('disabled');
						}

					});
				}
			});
	});
	
	$(".btn-atencion").click(function () {
			//Activa icono guardando
			Swal.fire({
				title: "Eliminar",
                text: "¿ Esta seguro de eliminar los registros de la Base de Datos ?",
                icon: "warning",
                confirmButtonText: "Confirmar",
                showCancelButton: true,
                cancelButtonColor: "#DD6B55"
			}).then((result) => {
				if (result.isConfirmed) {
					$(".btn-atencion").attr('disabled','-1');
					$.ajax ({
						type: 'POST',
						url: base_url + 'settings/eliminar_resultados_objetivos_db',
						cache: false,
						success: function(data){
							if( data.result == "error" )
							{
								alert(data.mensaje);
								$(".btn-atencion").removeAttr('disabled');							
								return false;
							} 
							if( data.result )//true
							{	                                                        
								$(".btn-atencion").removeAttr('disabled');
								var url = base_url + "settings/atencion_eliminar";
								$(location).attr("href", url);
							}
							else
							{
								alert('Error. Reload the web page.');
								$(".btn-atencion").removeAttr('disabled');
							}	
						},
						error: function(result) {
							alert('Error. Reload the web page.');
							$(".btn-atencion").removeAttr('disabled');
						}
					});
				}
			});
	});
});