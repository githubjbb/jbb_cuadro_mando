<div id="page-wrapper">
	<br>
	<div class="row">
		<!-- Start of menu -->
		<?php
			$userRol = $this->session->userdata("role");
			$this->load->view('menu');
		?>
		<!-- End of menu -->
		<div class="col-lg-9">
			<div class="panel panel-info small">
				<div class="panel-heading">
					<i class="fa fa-thumb-tack"></i> <strong>ACTIVIDADES </strong>
					<div class="pull-right">
						<div class="btn-group">
							<?php
								if($numeroActividad != 'x' ){
							?>
									<a class="btn btn-primary btn-xs" href=" <?php echo base_url($urlBotonRegresar); ?> "><span class="glyphicon glyphicon glyphicon-chevron-left" aria-hidden="true"></span> Regresar</a> 
							<?php
								}
							?>
						</div>
					</div>
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

<?php 										
	if(!$listaActividades){ 
		echo '<div class="col-lg-12">
				<p class="text-danger"><span class="glyphicon glyphicon-alert" aria-hidden="true"></span> No hay actividades en el sistema.</p>
			</div>';
	}else{
		foreach ($listaActividades as $lista):
            //buscar SUPERVISORES
			$arrParam = array(
				"idDependencia" => $lista['fk_id_dependencia'],
				"idRole" => ID_ROL_SUPERVISOR,
				"filtroState" => TRUE
			);
            $supervisores = $this->general_model->get_user($arrParam);
            //buscar ENLACES
            $arrParam["idRole"] =  ID_ROL_ENLACE;
            $enlaces = $this->general_model->get_user($arrParam);
?>
					<div class="alert alert-info ">
						<div class="row">
							<div class="col-lg-8">
								<h4><b>No. Actividad: <?php echo $lista['numero_actividad']; ?></b></h4>
								<b>Actividad:</b><br> <?php echo $lista['descripcion_actividad']; ?>	
							</div>
							<div class="col-lg-4">
								<div class="pull-right">
									<h4><b><?php echo $lista['dependencia']; ?></b></h4>
									<b>Supervisor:</b><br> 
									<?php
										if($supervisores){
	                                        foreach ($supervisores as $datos):
	                                            echo "<li>" . $datos["first_name"] . " " . $datos["last_name"] . "</li>";
	                                        endforeach;										
										} 
                                    ?>
									<br><b>Enlace:</b><br>
									<?php
										if($enlaces){
	                                        foreach ($enlaces as $datos):
	                                            echo "<li>" . $datos["first_name"] . " " . $datos["last_name"] . "</li>";
	                                        endforeach;
	                                    }
                                    ?>
								</div>
							</div>
						</div>
					</div>
					<table class="table table-hover">
						<thead>
							<tr>
								<th>Meta Anual</th>
								<th class="text-center">Unidad Medida</th>
								<th>Nombre Indicador</th>
								<th>Tipo Indicador</th>
								<th>Ponderación</th>
								<th>Fechas</th>
								<th>Responsable</th>
								<th class="text-center" style="width: 10%">Enlaces</th>
							</tr>
						</thead>
						<tbody>							
						<?php
								$unidadMedida = $lista['unidad_medida'];
								$clase = "text-danger";

								switch ($lista['tipo_indicador']) {
									case 1:
										$valor2 = 'Suma';
										$clase2 = "text-success";
										break;
									case 2:
										$valor2 = 'Constante';
										$clase2 = "text-danger";
										break;
									case 3:
										$valor2 = 'Creciente';
										$clase2 = "text-primary";
										break;
								}
								$ponderacion = $lista['ponderacion'];
								echo "<tr>";
								echo "<td>" . $lista['meta_plan_operativo_anual'] . "</td>";
								echo "<td class='text-center'>";
								echo '<p class="' . $clase . '"><strong>' . $unidadMedida . '</strong></p>';
								echo "</td>";
								echo "<td>" . $lista['nombre_indicador'] . "</td>";
								echo "<td class='text-center'>";
								echo '<p class="' . $clase2 . '"><strong>' . $valor2 . '</strong></p>';
								echo "</td>";
								echo "<td class='text-right'>" . round($lista['ponderacion'],2) . "%</td>";
								echo "<td class='text-center'>";
								echo $lista['mes_inicial'] . '-' . $lista['mes_final'];
								echo "</td>";
								echo "<td>" . $lista['responsable'] . "</td>";
								echo "<td class='text-center'>";
        
							if($numeroActividad == 'x') {
								echo "<a class='btn btn-primary btn-xs' href='" . base_url('dashboard/actividades/' . $lista["fk_id_cuadro_base"] .  '/' . $lista["numero_actividad"]) . "' title='Ver Detalle Actividad'> <span class='fa fa-eye' aria-hidden='true'></a>";
							}
								echo "</td>";
								echo "</tr>";
								echo "</tbody></table>";

								$arrParam = array("numeroActividad" => $lista["numero_actividad"]);
								$estadoActividad = $this->general_model->get_estados_actividades($arrParam);
								$sumaProgramado = $this->general_model->sumarProgramado($arrParam);

								$arrParam['numeroTrimestre'] = 1;
								$sumaProgramadoTrimestre1 = $this->general_model->sumarProgramado($arrParam);
								$sumaEjecutadoTrimestre1 = $this->general_model->sumarEjecutado($arrParam);
								$arrParam['numeroTrimestre'] = 2;
								$sumaProgramadoTrimestre2 = $this->general_model->sumarProgramado($arrParam);
								$sumaEjecutadoTrimestre2 = $this->general_model->sumarEjecutado($arrParam);
								$arrParam['numeroTrimestre'] = 3;
								$sumaProgramadoTrimestre3 = $this->general_model->sumarProgramado($arrParam);
								$sumaEjecutadoTrimestre3 = $this->general_model->sumarEjecutado($arrParam);
								$arrParam['numeroTrimestre'] = 4;
								$sumaProgramadoTrimestre4 = $this->general_model->sumarProgramado($arrParam);
								$sumaEjecutadoTrimestre4 = $this->general_model->sumarEjecutado($arrParam);
								
								$sumaEjecutado['ejecutado'] = 0;
								if ($lista["tipo_indicador"] == 3) {
									if ($estadoActividad[0]['estado_trimestre_4'] == 5){
										$sumaEjecutado['ejecutado'] += $sumaEjecutadoTrimestre4['ejecutado'];
									} else {
										if ($estadoActividad[0]['estado_trimestre_3'] == 5){
											$sumaEjecutado['ejecutado'] += $sumaEjecutadoTrimestre3['ejecutado'];
										} else {
											if ($estadoActividad[0]['estado_trimestre_2'] == 5){
												$sumaEjecutado['ejecutado'] += $sumaEjecutadoTrimestre2['ejecutado'];
											} else {
												if ($estadoActividad[0]['estado_trimestre_1'] == 5){
													$sumaEjecutado['ejecutado'] += $sumaEjecutadoTrimestre1['ejecutado'];
												} else {
													$sumaEjecutado['ejecutado'] = 0;
												}
											}
										}
									}
								} else {
									if ($estadoActividad[0]['estado_trimestre_1'] == 5){
										$sumaEjecutado['ejecutado'] += $sumaEjecutadoTrimestre1['ejecutado'];
									}
									if ($estadoActividad[0]['estado_trimestre_2'] == 5){
										$sumaEjecutado['ejecutado'] += $sumaEjecutadoTrimestre2['ejecutado'];
									}
									if ($estadoActividad[0]['estado_trimestre_3'] == 5){
										$sumaEjecutado['ejecutado'] += $sumaEjecutadoTrimestre3['ejecutado'];
									}
									if ($estadoActividad[0]['estado_trimestre_4'] == 5){
										$sumaEjecutado['ejecutado'] += $sumaEjecutadoTrimestre4['ejecutado'];
									}
								}
								
								$valorProgramadoTotal = round($sumaProgramado['programado'],2);
								$valorProgramadoTrimestre1 = round($sumaProgramadoTrimestre1['programado'],2);
								$valorProgramadoTrimestre2 = round($sumaProgramadoTrimestre2['programado'],2);
								$valorProgramadoTrimestre3 = round($sumaProgramadoTrimestre3['programado'],2);
								$valorProgramadoTrimestre4 = round($sumaProgramadoTrimestre4['programado'],2);

								$cumplimiento1 = 0;
								$cumplimiento2 = 0;
								$cumplimiento3 = 0;
								$cumplimiento4 = 0;

								$avancePOA = 0;
								if($sumaProgramado['programado'] > 0){
									$avancePOA = round(($sumaEjecutado['ejecutado']/$sumaProgramado['programado']) * $ponderacion,2);
								}
								if ($estadoActividad[0]['estado_trimestre_1'] != 0){
									if($sumaProgramadoTrimestre1['programado'] > 0) {
										$cumplimiento1 = round($sumaEjecutadoTrimestre1['ejecutado'] / $sumaProgramadoTrimestre1['programado'] * 100,2);
									} else {
										if($sumaEjecutadoTrimestre1['ejecutado'] > 0) {
											$cumplimiento1 = 100;
										} else {
											$cumplimiento1 = 0;
										}
									}
								} else {
									$cumplimiento1 = 0;
								}
								if ($estadoActividad[0]['estado_trimestre_2'] != 0){
									if($sumaProgramadoTrimestre2['programado'] > 0) {
										$cumplimiento2 = round($sumaEjecutadoTrimestre2['ejecutado'] / $sumaProgramadoTrimestre2['programado'] * 100,2);
									} else {
										if($sumaEjecutadoTrimestre2['ejecutado'] > 0) {
											$cumplimiento2 = 100;
										} else {
											$cumplimiento2 = 0;
										}
									}
								} else {
									$cumplimiento2 = 0;
								}
								if ($estadoActividad[0]['estado_trimestre_3'] != 0){
									if($sumaProgramadoTrimestre3['programado'] > 0) {
										$cumplimiento3 = round($sumaEjecutadoTrimestre3['ejecutado'] / $sumaProgramadoTrimestre3['programado'] * 100,2);
									} else {
										if($sumaEjecutadoTrimestre3['ejecutado'] > 0) {
											$cumplimiento3 = 100;
										} else {
											$cumplimiento3 = 0;
										}
									}
								} else {
									$cumplimiento3 = 0;
								}
								if ($estadoActividad[0]['estado_trimestre_4'] != 0){
									if($sumaProgramadoTrimestre4['programado'] > 0) {
										$cumplimiento4 = round($sumaEjecutadoTrimestre4['ejecutado'] / $sumaProgramadoTrimestre4['programado'] * 100,2);
									} else {
										if($sumaEjecutadoTrimestre4['ejecutado'] > 0) {
											$cumplimiento4 = 100;
										} else {
											$cumplimiento4 = 0;
										}
									}
								} else {
									$cumplimiento4 = 0;
								}
?>
							<table class='table table-hover'>
								<thead>
									<tr>
										<th>
											Plan Institucional
										</th>
									</tr>
								</thead>
									<tr>
										<td>
											<?php if($lista['plan_archivos'] == 1 ){ ?>
											<span class="glyphicon glyphicon-ok"></span> Plan Institucional de Archivos de la Entidad <br>
											<?php } ?>
											<?php if($lista['plan_adquisiciones'] == 1 ){ ?>
											<span class="glyphicon glyphicon-ok"></span> Plan Anual de Adquisiciones <br>
											<?php } ?>
											<?php if($lista['plan_vacantes'] == 1 ){ ?>
											<span class="glyphicon glyphicon-ok"></span> Plan Anual de Vacantes <br>
											<?php } ?>
											<?php if($lista['plan_recursos'] == 1 ){ ?>
											<span class="glyphicon glyphicon-ok"></span> Plan de Previsión de Recursos Humanos <br>
											<?php } ?>
											<?php if($lista['plan_talento'] == 1 ){ ?>
											<span class="glyphicon glyphicon-ok"></span> Plan Estratégico de Talento Humano <br>
											<?php } ?>
											<?php if($lista['plan_capacitacion'] == 1 ){ ?>
											<span class="glyphicon glyphicon-ok"></span> Plan Institucional de Capacitación <br>
											<?php } ?>
											<?php if($lista['plan_incentivos'] == 1 ){ ?>
											<span class="glyphicon glyphicon-ok"></span> Plan de Incentivos Institucionales <br>
											<?php } ?>
											<?php if($lista['plan_trabajo'] == 1 ){ ?>
											<span class="glyphicon glyphicon-ok"></span> Plan de Trabajo Anual en Seguridad y Salud en el Trabajo <br>
											<?php } ?>
											<?php if($lista['plan_anticorrupcion'] == 1 ){ ?>
											<span class="glyphicon glyphicon-ok"></span> Plan Anticorrupción y de Atención al Ciudadano <br>
											<?php } ?>
											<?php if($lista['plan_tecnologia'] == 1 ){ ?>
											<span class="glyphicon glyphicon-ok"></span> Plan Estratégico de Tecnologías de la Información y las Comunicaciones <br>
											<?php } ?>
											<?php if($lista['plan_riesgos'] == 1 ){ ?>
											<span class="glyphicon glyphicon-ok"></span> Plan de Tratamiento de Riesgos de Seguridad y Privacidad de la Información <br>
											<?php } ?>
											<?php if($lista['plan_informacion'] == 1 ){ ?>
											<span class="glyphicon glyphicon-ok"></span> Plan de Seguridad y Privacidad de la Información <br>
											<?php } ?>
										</tr>
									</tr>
							</table>

							<table class='table table-hover'>
								<thead>
									<tr class="text-primary">
										<td>
											<h2>Programado Año: <?php echo $valorProgramadoTotal; ?></h2>
											<small>(Suma Programado)</small>
										</td>
										<td class="text-right">
											<h2>Avance: <?php echo $avancePOA . '%'; ?></h2>
											<small>(Suma Ejecutado /Suma Programado * Ponderación)</small>
										</td>
									</tr>
								</thead>
							</table>						
							<table class='table table-hover info'>
								<thead>
									<tr class="headings default">
										<th class="column-title">
											<p>Programado Trimestre I: <?php echo $valorProgramadoTrimestre1; ?></p>
										</th>
										<th class="column-title">
											<p>Cumplimiento Trimestre I: <?php echo $cumplimiento1 . '%'; ?></p>
										</th>
										<th class="column-title small text-center <?php if($estadoActividad){ echo $estadoActividad[0]['primer_clase']; } ?>">
											<?php if($estadoActividad){ ?>
											<p class="<?php echo 'text-' . $estadoActividad[0]['primer_clase']; ?>"><b><?php echo $estadoActividad[0]['primer_estado']; ?></b></p>
											<?php } ?>
										</th>
									</tr>
									<tr class="headings">
										<th class="column-title">
											<p>Programado Trimestre II: <?php echo $valorProgramadoTrimestre2; ?></p>
										</th>
										<th class="column-title">
											<p>Cumplimiento Trimestre II: <?php echo $cumplimiento2 . '%'; ?></p>
										</th>
										<th class="column-title small text-center <?php if($estadoActividad){ echo $estadoActividad[0]['segundo_clase']; } ?>">
											<?php if($estadoActividad){ ?>
											<p class="<?php echo 'text-' . $estadoActividad[0]['segundo_clase']; ?>"><b><?php echo $estadoActividad[0]['segundo_estado']; ?></b></p>
											<?php } ?>
										</th>	
									</tr>
									<tr class="headings">
										<th class="column-title">
											<p>Programado Trimestre III: <?php echo $valorProgramadoTrimestre3; ?></p>
										</th>
										<th class="column-title">
											<p>Cumplimiento Trimestre III: <?php echo $cumplimiento3 . '%'; ?></p>
										</th>
										<th class="column-title small text-center <?php if($estadoActividad){ echo $estadoActividad[0]['tercer_clase']; } ?>">
											<?php if($estadoActividad){ ?>
											<p class="<?php echo 'text-' . $estadoActividad[0]['tercer_clase']; ?>"><b><?php echo $estadoActividad[0]['tercer_estado']; ?></b></p>
											<?php } ?>
										</th>
									</tr>
									<tr class="headings">
										<th class="column-title">
											<p>Programado Trimestre IV: <?php echo $valorProgramadoTrimestre4; ?></p>
										</th>
										<th class="column-title">
											<p>Cumplimiento Trimestre IV: <?php echo $cumplimiento4 . '%'; ?></p>
										</th>
										<th class="column-title small text-center <?php if($estadoActividad){ echo $estadoActividad[0]['cuarta_clase']; } ?>">
											<?php if($estadoActividad){ ?>
											<p class="<?php echo 'text-' . $estadoActividad[0]['cuarta_clase']; ?>"><b><?php echo $estadoActividad[0]['cuarta_estado']; ?></b></p>
											<?php } ?>
										</th>
									</tr>
									<tr class="headings default">
										<td width="28%"><small>Suma Programado Trimestre</small></td>
										<td width="29%"><small>Suma Ejecutado Trimestre /Suma Programado Trimestre * 100</small></td>
										<td width="20%"></td>
									</tr>
								</thead>
				
					</table>
						<?php
							endforeach;
						?>
				<?php } ?>

<!-- INICIO HISTORICO -->
		<?php
			if($infoEjecucion){
		?>
					<div class="table-responsive">
						<table class='table table-hover'>
							<thead>
								<tr class="text-primary">
									<th colspan="4">
										<h2>-- Ejecución Actividad --</h2>
										<?php
											$deshabilidarCampos = "disabled";
											if($numeroTrimestre){
												echo '<p class="text-primary">Trimestre ' . $numeroTrimestre. '</p>'; 
											}
										?>
									</th>
								</tr>
								
								<tr class="headings">
									<th class="column-title" style="width: 7%">Mes</th>
									<th class="column-title" style="width: 10%">Programado (<?php echo $unidadMedida; ?>)</th>
									<th class="column-title" style="width: 13%">Ejecutado (<?php echo $unidadMedida; ?>)</th>
									<th class="column-title" style="width: 70%">Descripción / Evidencias</th>
								</tr>
							</thead>

							<tbody>
							<?php
								foreach ($infoEjecucion as $data):						
							?>		
									<tr>
										<td><?php echo $data['mes']; ?></td>
										<td><?php echo $data['programado']; ?></td>
										<td><?php echo $data['ejecutado']; ?></td>
										<td>
											<small>
											<?php 
												if($data['descripcion_actividades']!=""){
													echo "<b>Descripción: </b><br>". $data['descripcion_actividades'] . "<br>";
												}
												if($data['evidencias']!=""){
													echo "<b>Evidencias: </b><br>". $data['evidencias'] . "<br>";
												}
											?>
											</small>
										</td>
									</tr>
							<?php
								endforeach;
							?>
							</tbody>
						</table>
					</div>	
		<?php
			}
		?>
<!-- FIN HISTORICO -->
				</div>
			</div>
		</div>
	</div>
</div>
		
<!--INICIO Modal -->
<div class="modal fade text-center" id="modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">    
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content" id="tablaDatos">

		</div>
	</div>
</div>                       
<!--FIN Modal -->

<!--INICIO Modal -->
<div class="modal fade text-center" id="modalEjecucion" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">    
	<div class="modal-dialog" role="document">
		<div class="modal-content" id="tablaDatosEjecucion">

		</div>
	</div>
</div>                       
<!--FIN Modal -->

<!-- Tables -->
<script>
$(document).ready(function() {
    $('#dataTables').DataTable({
        responsive: true,
		 "ordering": false,
		 paging: false,
		"searching": false,
		"info": false
    });
});
</script>