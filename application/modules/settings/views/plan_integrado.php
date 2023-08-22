<script type="text/javascript" src="<?php echo base_url("assets/js/validate/settings/plan_integrado.js"); ?>"></script>
<script>
$(function(){
	$(".btn-primary").click(function () {	
			var oID = $(this).attr("id");
            $.ajax ({
                type: 'POST',
				url: base_url + 'settings/cargarModalPlanIntegrado',
                data: {'idPlanIntegrado': oID},
                cache: false,
                success: function (data) {
                    $('#tablaDatos').html(data);
                }
            });
	});

	$(".btn-success").click(function () {	
			var oID = $(this).attr("id");
            $.ajax ({
                type: 'POST',
				url: base_url + 'settings/cargarModalPlanIntegrado',
                data: {'idPlanIntegrado': oID},
                cache: false,
                success: function (data) {
                    $('#tablaDatos').html(data);
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
						<i class="fa fa-gear fa-fw"></i> PLAN INTEGRADO - PLANES INTEGRADOS <?php echo $vigencia['vigencia']; ?>
					</h4>
				</div>
			</div>
		</div>
	</div>
	
	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-info">
				<div class="panel-heading">
					<i class="fa fa-crosshairs"></i> LISTA PLANES INTEGRADOS
					<div class="pull-right">
						<div class="btn-group">
							<?php
								if($userRol == ID_ROL_SUPER_ADMIN || $userRol == ID_ROL_ADMINISTRADOR || $userRol == ID_ROL_PLANEACION){
							?>
								<button type="button" class="btn btn-primary btn-xs" data-toggle="modal" data-target="#modal" id="x">
										<span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Adicionar Plan Integrado
								</button>
							<?php } ?>
						</div>
					</div>
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
								<th>ID</th>
								<th>Plan Institucional</th>
								<th>Dependencia</th>
								<th>Actividad</th>
								<th>Avance PAI</th>
								<th class="text-center">Opciones</th>
							</tr>
						</thead>
						<tbody>
						<?php
							foreach ($info as $lista):
									echo "<tr>";
									echo "<td>" . $lista['id_plan_integrado'] . "</td>";
									echo "<td>" . $lista['plan_institucional'] . "</td>";
									echo "<td>" . $lista['dependencia'] . "</td>";
									echo "<td>" . $lista['descripcion_actividad_pi'] . "</td>";
									if (!empty($lista['avance_poa'])) {
										echo "<td>" . $lista['avance_poa'] . "%</td>";
									} else {
										echo "<td>" . $lista['avance_poa'] . "</td>";
									}
									echo "<td class='text-center'>";
									echo "<p><a title='Ver Actividad' class='btn btn-info btn-xs' href='" . base_url('settings/actividadesPI/' . $lista["id_plan_integrado"]) . "'> Ver Actividades <span class='glyphicon glyphicon-eye-open' aria-hidden='true'></a></p>";
									if($userRol != ID_ROL_ENLACE && $userRol != ID_ROL_SUPERVISOR && $userRol != ID_ROL_PLANEACION) {
						?>
									<button type="button" class="btn btn-success btn-xs" data-toggle="modal" data-target="#modal" id="<?php echo $lista['id_plan_integrado']; ?>" >
										Editar <span class="glyphicon glyphicon-edit" aria-hidden="true"></span>
									</button>
									<button type="button" class='btn btn-danger btn-xs' id="<?php echo $lista['id_plan_integrado']; ?>">
										Eliminar <i class="fa fa-trash-o"></i>
									</button>
						<?php
									}
									echo "</td>";
									echo "</tr>";
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

<!-- Tables -->
<script>
$(document).ready(function() {
	$('#dataTables').DataTable({
		responsive: true,
		"pageLength": 100
	});
});
</script>