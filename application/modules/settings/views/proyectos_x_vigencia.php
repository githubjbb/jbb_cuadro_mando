<div id="page-wrapper">
	<br>
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-primary">
				<div class="panel-heading">
					<h4 class="list-group-item-heading">
					<i class="fa fa-gear fa-fw"></i> SEGPLAN - PROYECTOS DE INVERSIÓN POR VIGENCIA
					</h4>
				</div>
			</div>
		</div>				
	</div>

	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<i class="fa fa-thumb-tack"></i> LISTA PROYECTOS DE INVERSIÓN POR VIGENCIA
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
				<ul class="nav nav-tabs">
					<li <?php if($vigencia == 2020){ echo "class='active'";} ?>><a href="<?php echo base_url("settings/proyectos_x_vigencia/2020"); ?>"><b>2020</b></a>
					</li>
					<li <?php if($vigencia == 2021){ echo "class='active'";} ?>><a href="<?php echo base_url("settings/proyectos_x_vigencia/2021"); ?>"><b>2021</b></a>
					</li>
					<li <?php if($vigencia == 2022){ echo "class='active'";} ?>><a href="<?php echo base_url("settings/proyectos_x_vigencia/2022"); ?>"><b>2022</b></a>
					</li>
					<li <?php if($vigencia == 2023){ echo "class='active'";} ?>><a href="<?php echo base_url("settings/proyectos_x_vigencia/2023"); ?>"><b>2023</b></a>
					</li>
					<li <?php if($vigencia == 2024){ echo "class='active'";} ?>><a href="<?php echo base_url("settings/proyectos_x_vigencia/2024"); ?>"><b>2024</b></a>
					</li>
				</ul>
				<br>
				<?php
					if($info){
				?>				
					<table width="100%" class="table table-striped table-bordered table-hover" id="dataTables">
						<thead>
							<tr>
								<th width="5%" class="text-center">No.</th>
								<th width="25%" class="text-center">Proyecto Inversión</th>
								<th width="10%" class="text-center">Vigencia</th>
								<th width="20%" class="text-center">Recurso programado</th>
								<th width="20%" class="text-center">Recurso ejecutado</th>
								<th width="20%" class="text-center">Porcentaje cumplimiento</th>
							</tr>
						</thead>
						<tbody>							
						<?php
							foreach ($info as $lista):
									echo "<tr>";
									echo "<td class='text-center'>" . $lista['numero_proyecto_inversion'] . "</td>";
									echo "<td>" . $lista['nombre_proyecto_inversion'] . "</td>";
									echo "<td class='text-center'>" . $lista['vigencia_proyecto'] . "</td>";
									echo "<td class='text-right'>$ " . number_format($lista['recurso_programado_proyecto']) . "</td>";
									echo "<td class='text-right'>$ " . number_format($lista['recurso_ejecutado_proyecto']) . "</td>";
									echo "<td class='text-right'>" . round($lista['porcentaje_cumplimiento_proyecto'],2) . " %</td>";
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

<!-- Tables -->
<script>
$(document).ready(function() {
	$('#dataTables').DataTable({
		responsive: true,
		"pageLength": 100,
		paging: false
	});
});
</script>