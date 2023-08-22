$( document ).ready( function () {

	$("#numero_meta_proyecto").bloquearTexto().maxlength(4);
	$("#presupuesto_meta").bloquearTexto().maxlength(15);
	$("#valor_meta").bloquearTexto().maxlength(10);
	
	$( "#form" ).validate( {
		rules: {
			numero_meta_proyecto:		{ required: true, minlength: 1, maxlength: 4 },
			vigencia:					{ required: true },
			numeroProyecto:				{ required: true },
			meta_proyecto:				{ required: true },
			presupuesto_meta:			{ required: true, minlength: 1, maxlength: 15 },
			programado_meta_proyecto:	{ required: true, minlength: 1, maxlength:10 },
			unidad_meta:				{ required: true, minlength: 2, maxlength: 30 },
			id_tipologia:				{ required: true }
		},
		errorElement: "em",
		errorPlacement: function ( error, element ) {
			// Add the `help-block` class to the error element
			error.addClass( "help-block" );
			error.insertAfter( element );
		},
		highlight: function ( element, errorClass, validClass ) {
			$( element ).parents( ".col-sm-6" ).addClass( "has-error" ).removeClass( "has-success" );
			$( element ).parents( ".col-sm-12" ).addClass( "has-error" ).removeClass( "has-success" );
		},
		unhighlight: function (element, errorClass, validClass) {
			$( element ).parents( ".col-sm-6" ).addClass( "has-success" ).removeClass( "has-error" );
			$( element ).parents( ".col-sm-12" ).addClass( "has-success" ).removeClass( "has-error" );
		},
		submitHandler: function (form) {
			return true;
		}
	});

	$(".btn-warning").click(function () {
            var oID = $(this).attr("id");
            var vigencia = $('#vigencia').val();
            Swal.fire({
                title: "Eliminar",
                text: "¿ Por favor confirmar si desea eliminar la informacion de la tabla meta proyectos de inversión para la vigencia actual ?",
                icon: "warning",
                confirmButtonText: "Confirmar",
                showCancelButton: true,
                cancelButtonColor: "#DD6B55"
            }).then((result) => {
                if (result.isConfirmed) {
                    $(".btn-danger").attr('disabled','-1');
                    $.ajax ({
                        type: 'POST',
                        url: base_url + 'settings/delete_meta_proyectos_inversion/' + vigencia,
                        data: {'identificador': oID},
                        cache: false,
                        success: function(data){
                            if( data.result == "error" )
                            {
                                alert(data.mensaje);
                                $(".btn-danger").removeAttr('disabled');                            
                                return false;
                            } 
                            if( data.result )
                            {                                                           
                                $(".btn-danger").removeAttr('disabled');
                                var url = base_url + "settings/metas_proyectos";
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

	$(".btn-danger").click(function () {	
			var oID = $(this).attr("id");
			//Activa icono guardando
			Swal.fire({
				title: "Eliminar",
                text: "¿ Por favor confirmar si desea eliminar la Meta Proyecto de Inversion ?",
                icon: "warning",
                confirmButtonText: "Confirmar",
                showCancelButton: true,
                cancelButtonColor: "#DD6B55"
			}).then((result) => {
				if (result.isConfirmed) {
					$(".btn-danger").attr('disabled','-1');
					$.ajax ({
						type: 'POST',
						url: base_url + 'settings/delete_meta_proyecto',
						data: {'identificador': oID},
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
								var url = base_url + "settings/metas_proyectos";
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
	
	$("#btnSubmit").click(function(){
		if ($("#form").valid() == true){
				//Activa icono guardando
				$('#btnSubmit').attr('disabled','-1');
				$("#div_error").css("display", "none");
				$("#div_load").css("display", "inline");
				$.ajax({
					type: "POST",	
					url: base_url + "settings/save_metas_proyectos",	
					data: $("#form").serialize(),
					dataType: "json",
					contentType: "application/x-www-form-urlencoded;charset=UTF-8",
					cache: false,
					success: function(data){
						if( data.result == "error" )
						{
							$("#div_load").css("display", "none");
							$('#btnSubmit').removeAttr('disabled');							
							return false;
						}
						if( data.result )//true
						{	                                                        
							$("#div_load").css("display", "none");
							$('#btnSubmit').removeAttr('disabled');
							var url = base_url + "settings/metas_proyectos";
							$(location).attr("href", url);
						}
						else
						{
							alert('Error. Reload the web page.');
							$("#div_load").css("display", "none");
							$("#div_error").css("display", "inline");
							$('#btnSubmit').removeAttr('disabled');
						}	
					},
					error: function(result) {
						alert('Error. Reload the web page.');
						$("#div_load").css("display", "none");
						$("#div_error").css("display", "inline");
						$('#btnSubmit').removeAttr('disabled');
					}
				});
		}
	});
});