<div id="page-wrapper">
	<br>
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-primary">
				<div class="panel-heading">
					<h4 class="list-group-item-heading">
					<i class="fa fa-gear fa-fw"></i> RESUMEN - PLANES INSTITUCIONALES <?php echo $vigencia ?>
					</h4>
				</div>
			</div>
		</div>			
	</div>
	
	<!-- /.row -->
	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<i class="fa fa-crosshairs"></i> RESUMEN PLANES INSTITUCIONALES <?php echo $vigencia ?>
				</div>
				<div class="panel-body">

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
					<table width="100%" class="table table-hover">
						<thead>
							<tr>
								<th width='45%'>Plan Institucional</th>
								<th width='10%' class="text-center">Dependencia</th>
								<th width='10%' class="text-center">Actividad</th>
								<th width='45%' class="text-center">Promedio de Cumplimiento</th>
							</tr>
						</thead>
						<?php
						if ($planInstitucional) {
							foreach ($planInstitucional as $lista):
							?>
							<tbody>
							<?php
		                            $arrParam = array(
		                            	'idPlanIntegrado' => $lista['id_plan_integrado'],
		                            	'vigencia' => $lista['vigencia']
		                            );
									$cumplimiento = $this->general_model->get_sumCumplimiento($arrParam);
		                            $promedioCumplimiento = number_format($cumplimiento[0]["cumplimiento"],2);
		                                         
		                            if(!$promedioCumplimiento){
		                                $promedioCumplimiento = 0;
		                                $estilos = "bg-warning";
		                            }else{
		                                if($promedioCumplimiento > 70){
		                                    $estilos = "progress-bar-success";
		                                }elseif($promedioCumplimiento > 40 && $promedioCumplimiento <= 70){
		                                    $estilos = "progress-bar-warning";
		                                }else{
		                                    $estilos = "progress-bar-danger";
		                                }
		                            }
									echo "<tr>";
									echo "<td>" . $lista['plan_institucional'] ."</td>";
									echo "<td>" . $lista['dependencia'] ."</td>";
									echo "<td class='text-center'><small>" . $lista['numero_actividad_pi'] . ' - ' . $lista['descripcion_actividad_pi'] . "</small></td>";
		                            echo "<td class='text-center'>";
		                            echo "<b>" . $promedioCumplimiento ."%</b>";
		                            echo '<div class="progress progress-striped">
		                                      <div class="progress-bar ' . $estilos . '" role="progressbar" style="width: '. $promedioCumplimiento .'%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">' . $promedioCumplimiento . '%</div>
		                                    </div>';
		                            echo "</td>";
		                            echo "</tr>";
							?>
							</tbody>
							<?php
							endforeach;
						}
						?>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Tables -->
<script>
$(document).ready(function() {
	$('#dataTables').DataTable({
		responsive: true,
		"order": [[ 1, "asc" ]],
		"pageLength": 100
	});
});
</script>