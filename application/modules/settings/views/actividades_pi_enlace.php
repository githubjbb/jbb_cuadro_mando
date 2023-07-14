<script type="text/javascript" src="<?php echo base_url("assets/js/validate/settings/actividades_enlace_pi.js"); ?>"></script>
<div id="page-wrapper">
	<br>
	<div class="row">
		<?php
			$this->load->view('menu_pi');
		?>
		<div class="col-lg-9">
			<div class="panel panel-primary small">
				<div class="panel-heading">
					<i class="fa fa-thumb-tack"></i> <strong>ACTIVIDADES <?php 	if($infoEjecucion){ echo ' - REGISTRO EJECUCIÓN'; } ?></strong>
					<div class="pull-right">
						<div class="btn-group">
							<?php
								if($numeroActividadPI != 'x'){
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
	if(!$listaActividadesPI){ 
		echo '<div class="col-lg-12">
				<p class="text-danger"><span class="glyphicon glyphicon-alert" aria-hidden="true"></span> No hay actividades en el sistema.</p>
			</div>';
	}else{
		foreach ($listaActividadesPI as $lista):
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
								<h4><b>No. Actividad: <?php echo $lista['numero_actividad_pi']; ?></b></h4>
								<b>Actividad:</b><br> <?php echo $lista['descripcion_actividad_pi']; ?>	
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
								<th class="text-center">Ponderación</th>
								<th class="text-center">Fechas</th>
								<th>Responsable</th>
								<th class="text-center" style="width: 10%">Enlaces</th>
							</tr>
						</thead>
						<tbody>							
						<?php
								$unidadMedida = $lista['unidad_medida_pi'];
								$clase = "text-danger";
								$ponderacion = $lista['ponderacion_pi'];
								echo "<tr>";
								echo "<td>" . $lista['meta_plan_operativo_anual_pi'] . "</td>";
								echo "<td class='text-center'>";
								echo '<p class="' . $clase . '"><strong>' . $unidadMedida . '</strong></p>';
								echo "</td>";
								echo "<td class='text-right'>" . $lista['ponderacion_pi'] . "%</td>";
								echo "<td class='text-center'>";
								echo $lista['mes_inicial'] . '-' . $lista['mes_final'];
								echo "</td>";
								echo "<td>" . $lista['responsable'] . "</td>";
								echo "<td class='text-center'>";
								if(!$infoEjecucion){
									echo "<a class='btn btn-primary btn-xs' href='" . base_url('settings/actividadesPI/' . $lista["fk_id_plan_integrado"] .  '/' . $lista["numero_actividad_pi"]) . "'> <span class='fa fa-eye' aria-hidden='true'></a>";
								}else{
									echo "---";
								}
								echo "</td>";
								echo "</tr>";
								echo "</tbody></table>";

								$arrParam = array("numeroActividadPI" => $lista["numero_actividad_pi"]);
								$estadoActividad = $this->general_model->get_estados_actividadesPI($arrParam);
								$sumaProgramado = $this->general_model->sumarProgramadoPI($arrParam);

								$arrParam['numeroTrimestre'] = 1;
								$sumaProgramadoTrimestre1 = $this->general_model->sumarProgramadoPI($arrParam);
								$sumaEjecutadoTrimestre1 = $this->general_model->sumarEjecutadoPI($arrParam);
								$arrParam['numeroTrimestre'] = 2;
								$sumaProgramadoTrimestre2 = $this->general_model->sumarProgramadoPI($arrParam);
								$sumaEjecutadoTrimestre2 = $this->general_model->sumarEjecutadoPI($arrParam);
								$arrParam['numeroTrimestre'] = 3;
								$sumaProgramadoTrimestre3 = $this->general_model->sumarProgramadoPI($arrParam);
								$sumaEjecutadoTrimestre3 = $this->general_model->sumarEjecutadoPI($arrParam);
								$arrParam['numeroTrimestre'] = 4;
								$sumaProgramadoTrimestre4 = $this->general_model->sumarProgramadoPI($arrParam);
								$sumaEjecutadoTrimestre4 = $this->general_model->sumarEjecutadoPI($arrParam);
								
								$sumaEjecutado['ejecutado'] = 0;
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

								$cumplimiento1 = 0;
								$cumplimiento2 = 0;
								$cumplimiento3 = 0;
								$cumplimiento4 = 0;

								$avancePOA = 0;
								if($sumaProgramado['programado'] > 0){
									$avancePOA = round(($sumaEjecutado['ejecutado']/$sumaProgramado['programado']) * $ponderacion,2);
								}
								if ($estadoActividad[0]['estado_trimestre_1'] == 5){
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
								if ($estadoActividad[0]['estado_trimestre_2'] == 5){
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
								if ($estadoActividad[0]['estado_trimestre_3'] == 5){
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
								if ($estadoActividad[0]['estado_trimestre_4'] == 5){
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
							<tr class="text-primary">
								<td>
									<h2>Programado Año: <?php echo $sumaProgramado['programado']; ?></h2>
									<small>(Suma Programado)</small>
								</td>
								<td class="text-right">
									<h2>Avance PAI: <?php echo $avancePOA . '%'; ?></h2>
									<small>(Suma Ejecutado /Suma Programado * Ponderación)</small>
								</td>
							</tr>
						</thead>
					</table>

					<form name="form" id="form" role="form" method="post" >
						<input type="hidden" id="idPlanIntegrado" name="idPlanIntegrado" value="<?php echo $idPlanIntegrado; ?>"/>
						<input type="hidden" id="idActividad" name="idActividad" value="<?php echo $lista["id_actividad_pi"]; ?>"/>
						<input type="hidden" id="numeroActividadPI" name="numeroActividadPI" value="<?php echo $lista["numero_actividad_pi"]; ?>"/>
						<input type="hidden" id="cumplimiento1" name="cumplimiento1" value="<?php echo $cumplimiento1; ?>"/>
						<input type="hidden" id="cumplimiento2" name="cumplimiento2" value="<?php echo $cumplimiento2; ?>"/>
						<input type="hidden" id="cumplimiento3" name="cumplimiento3" value="<?php echo $cumplimiento3; ?>"/>
						<input type="hidden" id="cumplimiento4" name="cumplimiento4" value="<?php echo $cumplimiento4; ?>"/>
						<input type="hidden" id="avancePOA" name="avancePOA" value="<?php echo $avancePOA; ?>"/>

						<table class='table table-hover info'>
							<thead>

								<!-- INFO TRIMESTRE I -->

								<tr class="headings default">
									<th class="column-title">
										<p>Programado Trimestre I: <?php echo $sumaProgramadoTrimestre1['programado']; ?></p>
									</th>
									<th class="column-title">
										<p>Cumplimiento Trimestre I: <?php echo $cumplimiento1 . '%'; ?></p>
									</th>
									<th class="column-title small text-center <?php if($estadoActividad){ echo $estadoActividad[0]['primer_clase']; } ?>">
										<?php if($estadoActividad){ ?>
										<p class="<?php echo 'text-' . $estadoActividad[0]['primer_clase']; ?>"><b><?php echo $estadoActividad[0]['primer_estado']; ?></b></p>
										<?php } ?>
									</th>
									<th class="column-title text-center">
<?php if($estadoActividad){ ?>
										<!-- si esta CERRADO o APROBADO POR EL SUPERVISOR o APROBADO POR PLANEACION o con OBSERVACION DE CONTROL INTERNO, no muestre el boton -->
										<?php if($estadoActividad[0]['estado_trimestre_1'] != 2 && $estadoActividad[0]['estado_trimestre_1'] != 3 && $estadoActividad[0]['estado_trimestre_1'] != 5 && $estadoActividad[0]['estado_trimestre_1'] != 7 && $estadoActividad[0]['estado_trimestre_1'] != 8){ ?>
											<p><a class='btn btn-primary btn-xs' href='<?php echo base_url('settings/actividadesPI/' . $idPlanIntegrado . '/' . $lista['numero_actividad_pi'] . '/1') ?>' title="Registrar Ejecución I">
													Registrar Ejecución I <span class="fa fa-edit" aria-hidden="true"> </span>
											</a></p>
										<?php } ?>

										<?php if($estadoActividad[0]['estado_trimestre_1'] == 1){ ?>
											<p>
												<button type="button" id="1" class='btn btn-danger btn-xs' title="Cerrar Trimestre I">
														Cerrar Trimestre I  <i class="fa fa-arrow-right"></i>
												</button>
											</p>
										<?php } ?>
<?php } ?>										
									</th>
<!--
									<th class="column-title text-right">
										<p>Avance POA I: <?php echo $avancePOA1; ?></p>
									</th>
-->
								</tr>

								<!-- INFO TRIMESTRE II -->

								<tr class="headings">
									<th class="column-title">
										<p>Programado Trimestre II: <?php echo $sumaProgramadoTrimestre2['programado']; ?></p>
									</th>
									<th class="column-title">
										<p>Cumplimiento Trimestre II: <?php echo $cumplimiento2 . '%'; ?></p>
									</th>
									<th class="column-title small text-center <?php if($estadoActividad){ echo $estadoActividad[0]['segundo_clase']; } ?>">
										<?php if($estadoActividad){ ?>
										<p class="<?php echo 'text-' . $estadoActividad[0]['segundo_clase']; ?>"><b><?php echo $estadoActividad[0]['segundo_estado']; ?></b></p>
										<?php } ?>
									</th>
									<th class="column-title text-center">
<?php if($estadoActividad){ ?>
										<!-- si esta CERRADO o APROBALDO POR EL SUPERVISOR o APROBADO POR PLANEACION, no muestre el boton -->
										<?php if($estadoActividad[0]['estado_trimestre_2'] != 2 && $estadoActividad[0]['estado_trimestre_2'] != 3 && $estadoActividad[0]['estado_trimestre_2'] != 5 && $estadoActividad[0]['estado_trimestre_2'] != 8 && $estadoActividad[0]['estado_trimestre_2'] != 8){ ?>
											<p><a class='btn btn-primary btn-xs' href='<?php echo base_url('settings/actividadesPI/' . $idPlanIntegrado . '/' . $lista['numero_actividad_pi'] . '/2') ?>' title="Registrar Ejecución II">
													Registrar Ejecución II <span class="fa fa-edit" aria-hidden="true"> </span>
											</a></p>
										<?php } ?>

										<?php if($estadoActividad[0]['estado_trimestre_2'] == 1 || $estadoActividad[0]['estado_trimestre_2'] == 4){ ?>
											<p>
												<button type="button" id="2" class='btn btn-danger btn-xs' title="Cerrar Trimestre II">
														Cerrar Trimestre II  <i class="fa fa-arrow-right"></i>
												</button>
											</p>
										<?php } ?>
<?php } ?>										
									</th>
<!--
									<th class="column-title text-right">
										<p>Avance POA II: <?php echo $avancePOA2; ?></p>
									</th>
-->
								</tr>

								<!-- INFO TRIMESTRE III -->

								<tr class="headings">
									<th class="column-title">
										<p>Programado Trimestre III: <?php echo $sumaProgramadoTrimestre3['programado']; ?></p>
									</th>
									<th class="column-title">
										<p>Cumplimiento Trimestre III: <?php echo $cumplimiento3 . '%'; ?></p>
									</th>
									<th class="column-title small text-center <?php if($estadoActividad){ echo $estadoActividad[0]['tercer_clase']; } ?>">
										<?php if($estadoActividad){ ?>
										<p class="<?php echo 'text-' . $estadoActividad[0]['tercer_clase']; ?>"><b><?php echo $estadoActividad[0]['tercer_estado']; ?></b></p>
										<?php } ?>
									</th>
									<th class="column-title text-center">
<?php if($estadoActividad){ ?>
										<!-- si esta CERRADO o APROBALDO POR EL SUPERVISOR o APROBADO POR PLANEACION, no muestre el boton -->
										<?php if($estadoActividad[0]['estado_trimestre_3'] != 2 && $estadoActividad[0]['estado_trimestre_3'] != 3 && $estadoActividad[0]['estado_trimestre_3'] != 5 && $estadoActividad[0]['estado_trimestre_3'] != 8 && $estadoActividad[0]['estado_trimestre_3'] != 8){ ?>
											<p><a class='btn btn-primary btn-xs' href='<?php echo base_url('settings/actividadesPI/' . $idPlanIntegrado . '/' . $lista['numero_actividad_pi'] . '/3') ?>' title="Registrar Ejecución III">
													Registrar Ejecución III <span class="fa fa-edit" aria-hidden="true"> </span>
											</a></p>
										<?php } ?>

										<?php if($estadoActividad[0]['estado_trimestre_3'] == 1 || $estadoActividad[0]['estado_trimestre_3'] == 4){ ?>
											<p>
												<button type="button" id="3" class='btn btn-danger btn-xs' title="Cerrar Trimestre III">
														Cerrar Trimestre III  <i class="fa fa-arrow-right"></i>
												</button>
											</p>
										<?php } ?>
<?php } ?>											
									</th>
<!--
									<th class="column-title text-right">
										<p>Avance POA III: <?php echo $avancePOA3; ?></p>
									</th>
-->
								</tr>

								<!-- INFO TRIMESTRE IV -->

								<tr class="headings">
									<th class="column-title">
										<p>Programado Trimestre IV: <?php echo $sumaProgramadoTrimestre4['programado']; ?></p>
									</th>
									<th class="column-title">
										<p>Cumplimiento Trimestre IV: <?php echo $cumplimiento4 . '%'; ?></p>
									</th>
									<th class="column-title small text-center <?php if($estadoActividad){ echo $estadoActividad[0]['cuarta_clase']; } ?>">
										<?php if($estadoActividad){ ?>
										<p class="<?php echo 'text-' . $estadoActividad[0]['cuarta_clase']; ?>"><b><?php echo $estadoActividad[0]['cuarta_estado']; ?></b></p>
										<?php } ?>
									</th>
									<th class="column-title text-center">
<?php if($estadoActividad){ ?>
										<!-- si esta CERRADO o APROBALDO POR EL SUPERVISOR o APROBADO POR PLANEACION, no muestre el boton -->
										<?php if($estadoActividad[0]['estado_trimestre_4'] != 2 && $estadoActividad[0]['estado_trimestre_4'] != 3 && $estadoActividad[0]['estado_trimestre_4'] != 5 && $estadoActividad[0]['estado_trimestre_4'] != 8 && $estadoActividad[0]['estado_trimestre_4'] != 8){ ?>
											<p><a class='btn btn-primary btn-xs' href='<?php echo base_url('settings/actividadesPI/' . $idPlanIntegrado . '/' . $lista['numero_actividad_pi'] . '/4') ?>' title="Registrar Ejecución IV">
													Registrar Ejecución IV <span class="fa fa-edit" aria-hidden="true"> </span>
											</a></p>
										<?php } ?>

										<?php if($estadoActividad[0]['estado_trimestre_4'] == 1 || $estadoActividad[0]['estado_trimestre_4'] == 4){ ?>
											<p>
												<button type="button" id="4" class='btn btn-danger btn-xs' title="Cerrar Trimestre IV">
														Cerrar Trimestre IV  <i class="fa fa-arrow-right"></i>
												</button>
											</p>
										<?php } ?>
<?php } ?>										
									</th>
<!--
									<th class="column-title text-right">
										<p>Avance POA IV: <?php echo $avancePOA4; ?></p>
									</th>
-->
								</tr>
									<tr class="headings default">
										<td width="28%"><small>Suma Programado Trimestre</small></td>
										<td width="29%"><small>Suma Ejecutado Trimestre /Suma Programado Trimestre * 100</small></td>
										<td width="20%"></td>
										<td width="23%"></td>
<!--
										<td width="20%" class="text-right"><small>Suma Ejecutado Trimestre /Suma Programado Trimestre * Ponderación</small></td>
-->
									</tr>
							</thead>				
						</table>
					</form>
						<?php
							endforeach;
						?>
				<?php } ?>

<!-- INICIO HISTORICO -->
		<?php
			if($infoEjecucion){
		?>
					<div class="table-responsive">
						<form  name="ejecucion" id="ejecucion" method="post" action="<?php echo base_url("settings/update_ejecucion_PI"); ?>">

							<input type="hidden" id="hddNumeroActividad" name="hddNumeroActividad" value="<?php echo $numeroActividadPI; ?>"/>
							<input type="hidden" id="hddIdPlanIntegrado" name="hddIdPlanIntegrado" value="<?php echo $idPlanIntegrado; ?>"/>	
							<h2 class="text-primary">-- Ejecución Actividad --</h2>

							<table id="dataTablesWorker" class="table table-striped jambo_table bulk_action" cellspacing="0" width="100%">
								<thead>
							<?php
								$deshabilidar = '';
								if($numeroTrimestrePI){
									$variable = 'estado_trimestre_' . $numeroTrimestrePI;
									
									if($estadoActividad){
										$estado = $estadoActividad[0][$variable];
										//si esta cerrado o aprobado por el supervisor o aprobado por planeacion, debe bloquear la edicion
										if($estado == 2 || $estado == 3 || $estado == 5 ){
											$deshabilidar = 'disabled';
										}
									}
							?>
									<tr class="text-primary">
										<th colspan="4">
											<h4>Registrar la informacion para el <b>TRIMESTRE <?php echo $numeroTrimestrePI; ?></b>.
											<?php
												$fechaActual = date("Y-m-d");

												$fecha1= new DateTime($fechaActual);
												$fecha2= new DateTime($infoFechaLimite[0]["fecha"]);
												$diff = $fecha1->diff($fecha2);

												if($infoFechaLimite[0]["fecha"] >= $fechaActual){
													echo "Fecha limite de registro de información: <b>" . $infoFechaLimite[0]["fecha"] . "</b>.";
													echo "<br>" . $diff->days . " días para la entrega.";
												}else{
													echo "No es posible registrar la ejecución de la actividad, la fecha limite era <b>" . $infoFechaLimite[0]["fecha"] . "</b>.";
												}
											?>
											</h4>
										</th>
									</tr>

							<?php
								$fechaActual = date("Y-m-d");
								if($infoFechaLimite[0]["fecha"] >= $fechaActual){
							?>
									<tr class="info">
										<input type="hidden" id="hddNumeroTrimestre" name="hddNumeroTrimestre" value="<?php echo $numeroTrimestrePI; ?>"/>	
										<th class="column-title text-right" colspan="4">
											<textarea id="observacion" name="observacion" class="form-control" rows="2" placeholder="Observación" required ></textarea><br>
											<button type="submit" class="btn btn-primary" id="btnSubmit2" name="btnSubmit2" <?php echo $deshabilidar; ?> >
												Guardar <span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span>
											</button>
										</th>
									</tr>
							<?php
								}
							?>
							<?php
								}else{
									$deshabilidar = 'disabled';
								}
							?>	
									<tr class="headings">
										<th class="column-title" style="width: 10%">Mes</th>
										<th class="column-title" style="width: 10%">Programado (<?php echo $unidadMedida; ?>)</th>
										<th class="column-title" style="width: 15%">Ejecutado (<?php echo $unidadMedida; ?>)</th>
										<th class="column-title" style="width: 65%">Descripción / Evidencias</th>
									</tr>
								</thead>

								<tbody>
								<?php
									foreach ($infoEjecucion as $data):
										$variable = 'estado_trimestre_' . $data['numero_trimestre'];
										
										if($estadoActividad){
											$estado = $estadoActividad[0][$variable];
											//si esta cerrado o aprobado por el supervisor o aprobado por planeacion, debe bloquear la edicion
											if($estado == 2 || $estado == 3 || $estado == 5 ){
												$deshabilidar = 'disabled';
											}
										}						
										$idRecord = $data['id_ejecucion_actividad_pi'];
										$numeroActividad = $data['fk_numero_actividad_pi'];
								?>		
										<input type="hidden" name="form[id][]" value="<?php echo $idRecord; ?>"/>
										<tr>
											<td ><?php echo $data['mes']; ?></td>
											<td ><?php echo $data['programado']; ?></td>
											<?php 
												if($deshabilidar){
											?>
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
											<?php }else{ ?>
												<td>
													<input type="number" step="any" min="0" max="500000" name="form[ejecutado][]" class="form-control" placeholder="Ejecutado" value="<?php echo $data['ejecutado']; ?>"  <?php echo $deshabilidar; ?> >
												</td>
												<td>
													<textarea name="form[descripcion][]" placeholder="Descripción" class="form-control" rows="3" <?php echo $deshabilidar; ?>><?php echo $data['descripcion_actividades']; ?></textarea>
													<br>
													<textarea name="form[evidencia][]" placeholder="Evidencia" class="form-control" rows="3" <?php echo $deshabilidar; ?>><?php echo $data['evidencias']; ?></textarea>
												</td>
											<?php } ?>
										</tr>

								<?php
									endforeach;
								?>
								</tbody>
							</table>
						</form>
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