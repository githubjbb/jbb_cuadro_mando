$( document ).ready( function () {
	
	$( "#form" ).validate( {
		rules: {
			plan_institucional:		{ required: true, maxlength: 500 }
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

	$(".btn-success").click(function () {
			var oID = $(this).attr("id");

				$(".btn-success").attr('disabled','-1');
				$.ajax ({
					type: 'POST',
					url: base_url + 'settings/inactivar_planInstitucional',
					data: {'identificador': oID},
					cache: false,
					success: function(data){
						if( data.result == "error" )
						{
							alert(data.mensaje);
							$(".btn-success").removeAttr('disabled');							
							return false;
						} 
						if( data.result )//true
						{	                                                        
							$(".btn-success").removeAttr('disabled');
							var url = base_url + "settings/planesInstitucionales";
							$(location).attr("href", url);
						}
						else
						{
							alert('Error. Reload the web page.');
							$(".btn-success").removeAttr('disabled');
						}	
					},
					error: function(result) {
						alert('Error. Reload the web page.');
						$(".btn-success").removeAttr('disabled');
					}
				});
			
	});

	$(".btn-danger").click(function () {
			var oID = $(this).attr("id");
			
				$(".btn-danger").attr('disabled','-1');
				$.ajax ({
					type: 'POST',
					url: base_url + 'settings/activar_planInstitucional',
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
							var url = base_url + "settings/planesInstitucionales";
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
			
	});
	
	$("#btnSubmit").click(function(){
		if ($("#form").valid() == true){
				//Activa icono guardando
				$('#btnSubmit').attr('disabled','-1');
				$("#div_error").css("display", "none");
				$("#div_load").css("display", "inline");
				$.ajax({
					type: "POST",	
					url: base_url + "settings/save_planesInstitucionales",	
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
							var url = base_url + "settings/planesInstitucionales";
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
		}//if
	});
});