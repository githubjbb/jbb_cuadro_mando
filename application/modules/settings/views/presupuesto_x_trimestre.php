<div id="page-wrapper">
	<br>
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-primary">
				<div class="panel-heading">
					<h4 class="list-group-item-heading">
					<i class="fa fa-gear fa-fw"></i> SEGPLAN - PRESUPUESTO <?php echo $vigencia['vigencia']; ?>
					</h4>
				</div>
			</div>
		</div>				
	</div>
	
	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<i class="fa fa-thumb-tack"></i> LISTA PRESUPUESTO
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
					if($info){
				?>				
					<table width="100%" class="table table-striped table-bordered table-hover" id="dataTables">
						<thead>
							<tr>
								<th><small>No.</small></th>
								<th><small>Proyecto Inversión</small></th>
								<th><small>Apropiación <?php echo $vigencia['vigencia']; ?> </small></th>
								<th><small>Compromisos acumulados trimestre 1</small></th>
								<th><small>% Ejec. compromisos trimestre 1</small></th>
								<th><small>Giros acumulados trimestre 1</small></th>
								<th><small>% Ejec. giros trimestre 1</small></th>
								<th><small>Compromisos acumulados trimestre 2</small></th>
								<th><small>% Ejec. compromisos trimestre 2</small></th>
								<th><small>Giros acumulados trimestre 2</small></th>
								<th><small>% Ejec. giros trimestre 2</small></th>
								<th><small>Compromisos acumulados trimestre 3</small></th>
								<th><small>% Ejec. compromisos trimestre 3</small></th>
								<th><small>Giros acumulados trimestre 3</small></th>
								<th><small>% Ejec. giros trimestre 3</small></th>
								<th><small>Compromisos acumulados trimestre 4</small></th>
								<th><small>% Ejec. compromisos trimestre 4</small></th>
								<th><small>Giros acumulados trimestre 4</small></th>
								<th><small>% Ejec. giros trimestre 4</small></th>
							</tr>
						</thead>
						<tbody>							
						<?php
							foreach ($info as $lista):
									echo "<tr>";
									echo "<td><small>" . $lista['numero_proyecto_inversion'] . "</small></td>";
									echo "<td><small>" . $lista['nombre_proyecto_inversion'] . "</small></td>";
									echo "<td><small>" . number_format($lista['apropiacion']) . "</small></td>";
									echo "<td><small>" . number_format($lista['comp_acum_trim_1']) . "</small></td>";
									echo "<td><small>" . round($lista['porc_ejec_trim_1'],2) . " %</small></td>";
									echo "<td><small>" . number_format($lista['giros_acum_trim_1']) . "</small></td>";
									echo "<td><small>" . round($lista['porc_giro_trim_1'],2) . " %</small></td>";
									echo "<td><small>" . number_format($lista['comp_acum_trim_2']) . "</small></td>";
									echo "<td><small>" . round($lista['porc_ejec_trim_2'],2) . " %</small></td>";
									echo "<td><small>" . number_format($lista['giros_acum_trim_2']) . "</small></td>";
									echo "<td><small>" . round($lista['porc_giro_trim_2'],2) . " %</small></td>";
									echo "<td><small>" . number_format($lista['comp_acum_trim_3']) . "</small></td>";
									echo "<td><small>" . round($lista['porc_ejec_trim_3'],2) . " %</small></td>";
									echo "<td><small>" . number_format($lista['giros_acum_trim_3']) . "</small></td>";
									echo "<td><small>" . round($lista['porc_giro_trim_3'],2) . " %</small></td>";
									echo "<td><small>" . number_format($lista['comp_acum_trim_4']) . "</small></td>";
									echo "<td><small>" . round($lista['porc_ejec_trim_4'],2) . " %</small></td>";
									echo "<td><small>" . number_format($lista['giros_acum_trim_4']) . "</small></td>";
									echo "<td><small>" . round($lista['porc_giro_trim_4'],2) . " %</small></td>";
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