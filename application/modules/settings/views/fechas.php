<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<script>
$(function(){ 
	$(".btn-success").click(function () {	
		var oID = $(this).attr("id");
        $.ajax ({
            type: 'POST',
			url: base_url + 'settings/cargarModalFechas',
            data: {'idFecha': oID},
            cache: false,
            success: function (data) {
                $('#tablaDatos').html(data);
            }
        });
	});

	$(".btn-primary").click(function () {

		var vigencia = $('#vigencia').val();
		$("#div_error").css("display", "none");
		$("#div_load").css("display", "inline");
		
        $.ajax ({
            type: 'POST',
			url: base_url + 'settings/cambiarVigencia',
            data: {'vigencia': vigencia},
            cache: false,
            success: function(data){
				if(data.result == "error") {
					alert('Error. Reload the web page.');
					$("#div_load").css("display", "none");
					$("#div_error").css("display", "inline");
					return false;
				} 
				if(data.result) {
					$("#div_load").css("display", "none");
					var url = base_url + "settings/fechas_limite";
					$(location).attr("href", url);
				}
				else {
					alert('Error. Reload the web page.');
					$("#div_load").css("display", "none");
					$("#div_error").css("display", "inline");
				}	
			},
			error: function(result) {
				alert('Error. Reload the web page.');
				$("#div_load").css("display", "none");
				$("#div_error").css("display", "inline");
			}
        });
	});
});
</script>

<div id="page-wrapper">
	<br>
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-primary">
				<div class="panel-heading">
					<h4 class="list-group-item-heading">
					<i class="fa fa-gear fa-fw"></i> CONFIGURACIÓN - VIGENCIA Y FECHAS LIMITE 
					</h4>
				</div>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-info">
<?php
	$retornoExito2 = $this->session->flashdata('retornoExito2');
	if ($retornoExito2) {
?>
		<div class="alert alert-success ">
			<span class="glyphicon glyphicon-ok" aria-hidden="true"></span>
			<?php echo $retornoExito2 ?>		
		</div>
<?php
	}
	$retornoError2 = $this->session->flashdata('retornoError2');
	if ($retornoError2) {
?>
		<div class="alert alert-danger ">
			<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
			<?php echo $retornoError2 ?>
		</div>
<?php
	}
?> 
				<div class="panel-heading text-center">
					<div class="row">
						<div class="col-md-5"></div>
						<div class="col-md-2">
							<label class="control-label" for="vigencia">Vigencia</label>
							<select class="form-control" name="vigencia" id="vigencia">
								<option value="2022" <?php if($vigencia['vigencia'] == 2022) { echo "selected"; } ?>>2022</option>
								<option value="2023" <?php if($vigencia['vigencia'] == 2023) { echo "selected"; } ?>>2023</option>
							</select><br>
							<button class="btn btn-primary btn-sm">Guardar <span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	
	<!-- /.row -->
	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<i class="fa fa-crosshairs"></i> LISTA FECHAS LIMITE POR TRIMESTRE PARA REGISTRAR LA EJECUCIÓN
				</div>
				<div class="panel-body small">

<?php
	$retornoExito = $this->session->flashdata('retornoExito');
	if ($retornoExito) {
?>
		<div class="alert alert-success ">
			<span class="glyphicon glyphicon-ok" aria-hidden="true"></span>
			<?php echo $retornoExito ?>		
		</div>
<?php
	}
	$retornoError = $this->session->flashdata('retornoError');
	if ($retornoError) {
?>
		<div class="alert alert-danger ">
			<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
			<?php echo $retornoError ?>
		</div>
<?php
	}
?> 

				<?php
					if($info){
				?>				
					<table width="100%" class="table table-striped table-bordered table-hover" id="dataTables">
						<thead>
							<tr>
								<th>Número Trimestre</th>
								<th>Fecha Limite</th>
								<th class="text-center">Editar</th>
							</tr>
						</thead>
						<tbody>							
						<?php
							foreach ($info as $lista):
									echo "<tr>";
									echo "<td>Trimestre" . $lista['numero_trimestre'] . "</td>";
									echo "<td>" . $lista['fecha'] . "</td>";
									echo "<td class='text-center'>";
						?>
									<button type="button" class="btn btn-success btn-xs" data-toggle="modal" data-target="#modal" id="<?php echo $lista['id_fecha']; ?>" >
										Editar <span class="glyphicon glyphicon-edit" aria-hidden="true">
									</button>
						<?php
									echo "</td>";
							endforeach;
						?>
						</tbody>
					</table>
				<?php } ?>
				</div>
			</div>
		</div>
	</div>
</div>

<!--INICIO Modal -->
<div class="modal fade text-center" id="modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">    
	<div class="modal-dialog" role="document">
		<div class="modal-content" id="tablaDatos">

		</div>
	</div>
</div>                       
<!--FIN Modal  -->