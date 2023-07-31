$( document ).ready( function () {

    $(".btn-danger").click(function () {
            var oID = $(this).attr("id");
            Swal.fire({
                title: "Eliminar",
                text: "¿ Por favor confirmar si desea eliminar la informacion de la tabla indicadores de gestion para la vigencia actual ?",
                icon: "warning",
                confirmButtonText: "Confirmar",
                showCancelButton: true,
                cancelButtonColor: "#DD6B55"
            }).then((result) => {
                if (result.isConfirmed) {
                    $(".btn-danger").attr('disabled','-1');
                    $.ajax ({
                        type: 'POST',
                        url: base_url + 'settings/delete_indicadores_gestion',
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
                                var url = base_url + "settings/indicadores_gestion";
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