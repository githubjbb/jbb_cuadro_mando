<script type="text/javascript" src="<?php echo base_url("assets/js/validate/settings/form_estado_actividad_pi.js"); ?>"></script>

<script>
$(function(){ 
	$(".btn-info").click(function () {	
			var oID = $(this).attr("id");
            $.ajax ({
                type: 'POST',
				url: base_url + 'settings/cargarModalActividadPI',
				data: {'idPlanIntegrado': oID, 'idActividadPI': 'x'},
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
				url: base_url + 'settings/cargarModalActividadPI',
				data: {'idPlanIntegrado': '', 'idActividadPI': oID},
                cache: false,
                success: function (data) {
                    $('#tablaDatos').html(data);
                }
            });
	});

	$(".btn-warning").click(function () {	
			var oID = $(this).attr("id");
            $.ajax ({
                type: 'POST',
				url: base_url + 'settings/cargarModalProgramarActividadPI',
				data: {'idActividadPI': oID},
                cache: false,
                success: function (data) {
                    $('#tablaDatosEjecucion').html(data);
                }
            });
	});

});
</script>

<div id="page-wrapper">
	<br>
	<div class="row">
		<!-- Start of menu -->
		<?php
			$userRol = $this->session->userdata("role");
			$this->load->view('menu_pi');
		?>
		<!-- End of menu -->
		<div class="col-lg-9">
			<div class="panel panel-info small">
				<div class="panel-heading">
					<i class="fa fa-thumb-tack"></i> <strong>ACTIVIDADES PI</strong>
					<div class="pull-right">
						<div class="btn-group">
							<?php
								if($numeroActividadPI != 'x' ){
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
								echo "<td class='text-center'>" . $lista['ponderacion_pi'] . "%</td>";
								echo "<td class='text-center'>";
								echo $lista['mes_inicial'] . '-' . $lista['mes_final'];
								echo "</td>";
								echo "<td>" . $lista['responsable'] . "</td>";
								echo "<td class='text-center'>";
        
    							if($userRol == ID_ROL_SUPER_ADMIN || $userRol == ID_ROL_ADMINISTRADOR){
						?>
									<button type="button" class="btn btn-success btn-xs" data-toggle="modal" data-target="#modal" id="<?php echo $lista['id_actividad_pi']; ?>" title="Editar Actividad">
										<span class="fa fa-pencil" aria-hidden="true">
									</button>
									<?php
										if($numeroActividadPI != 'x') {
									?>
											<button type="button" class="btn btn-warning btn-xs" data-toggle="modal" data-target="#modalEjecucion" id="<?php echo $lista['id_actividad_pi']; ?>" title="Adicionar Fecha a la Actividad">
													<i class="fa fa-plus"></i>
											</button>

											<button type="button" id="<?php echo $lista["id_actividad_pi"]; ?>" class='btn btn-danger btn-xs' title="Eliminar Actividad">
													<span class="fa fa-trash-o" aria-hidden="true"> </span>
											</button>

											<!--<button type="button" class="btn btn-default btn-xs" data-toggle="modal" data-target="#modalAuditoria" id="<?php echo $lista['numero_actividad_pi']; ?>" title="Auditoría Cambios Programación/Ejecución Actividad">
													<i class="fa fa-exclamation-circle"></i>
											</button>-->
						<?php
										}
								}

							if($numeroActividadPI == 'x') {
								echo "<a class='btn btn-primary btn-xs' href='" . base_url('settings/actividadesPI/' . $lista["fk_id_plan_integrado"] .  '/' . $lista["numero_actividad_pi"]) . "' title='Ver Detalle Actividad'> <span class='fa fa-eye' aria-hidden='true'></a>";
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
											<h2>Programado Año: <?php echo $valorProgramadoTotal; ?></h2>
											<small>(Suma Programado)</small>
										</td>
										<td class="text-right">
											<h2>Avance PAI: <?php echo $avancePOA . '%'; ?></h2>
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
										<th class="column-title text-center">
											<p><a class='btn btn-primary btn-xs' href='<?php echo base_url('settings/actividadesPI/' . $idPlanIntegrado . '/' . $lista['numero_actividad_pi'] . '/1') ?>' title="Seguimiento I">
													Seguimiento I <span class="fa fa-tag" aria-hidden="true"> </span>
											</a></p>											
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
										<th class="column-title text-center">
											<p><a class='btn btn-primary btn-xs' href='<?php echo base_url('settings/actividadesPI/' . $idPlanIntegrado . '/' . $lista['numero_actividad_pi'] . '/2') ?>' title="Seguimiento II">
													Seguimiento II <span class="fa fa-tag" aria-hidden="true"> </span>
											</a></p>										
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
										<th class="column-title text-center">
											<p><a class='btn btn-primary btn-xs' href='<?php echo base_url('settings/actividadesPI/' . $idPlanIntegrado . '/' . $lista['numero_actividad_pi'] . '/3') ?>' title="Seguimiento III">
													Seguimiento III <span class="fa fa-tag" aria-hidden="true"> </span>
											</a></p>											
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
										<th class="column-title text-center">
											<p><a class='btn btn-primary btn-xs' href='<?php echo base_url('settings/actividadesPI/' . $idPlanIntegrado . '/' . $lista['numero_actividad_pi'] . '/4') ?>' title="Seguimiento IV">
													Seguimiento IV <span class="fa fa-tag" aria-hidden="true"> </span>
											</a></p>											
										</th>
									</tr>
									<tr class="headings default">
										<td width="28%"><small>Suma Programado Trimestre</small></td>
										<td width="29%"><small>Suma Ejecutado Trimestre /Suma Programado Trimestre * 100</small></td>
										<td width="20%"></td>
										<td width="23%"></td>
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
				if($numeroTrimestrePI){
		?>
<!--INICIO ADDITIONAL INFORMATION -->
	<div class="row">
		<div class="col-lg-6">				
			<div class="panel panel-primary">
				<div class="panel-heading">
					<span class="fa fa-tag" aria-hidden="true"> </span> <b>SEGUIMIENTO EJECUCIÓN TRIMESTRE <?php echo $numeroTrimestrePI; ?></b>
				</div>
				<div class="panel-body">
					<div class="col-lg-12">	
						<form name="formEstado" id="formEstado" class="form-horizontal" method="post">
							<input type="hidden" id="hddIdPlanIntegrado" name="hddIdPlanIntegrado" value="<?php echo $idPlanIntegrado; ?>"/>
							<input type="hidden" id="hddNumeroActividadPI" name="hddNumeroActividadPI" value="<?php echo $lista['numero_actividad_pi']; ?>"/>
							<input type="hidden" id="hddNumeroTrimestre" name="hddNumeroTrimestre" value="<?php echo $numeroTrimestrePI; ?>"/>

							<div class="form-group">
								<label class="col-sm-4 control-label" for="estado">Estado:</label>
								<div class="col-sm-8">
									<select name="estado" id="estado" class="form-control" >
										<option value="">Seleccione...</option>
										<option value=5 >Aprobada</option>
										<option value=6 >Rechazada</option>
										<option value=7 >Incumplida</option>
									</select>
								</div>
							</div>

							<div class="form-group">
								<label class="col-sm-4 control-label" for="information">Observación:</label>
								<div class="col-sm-8">
								<textarea id="observacion" name="observacion" class="form-control" rows="3" placeholder="Observación" ></textarea>
								</div>
							</div>
							
							<div class="form-group">
								<div class="row" align="center">
									<div style="width:100%;" align="center">
										<button type="button" id="btnEstado" name="btnEstado" class="btn btn-primary" >
											Guardar Seguimiento <span class="glyphicon glyphicon-floppy-disk" aria-hidden="true" />
										</button> 
										
									</div>
								</div>
							</div>							
						</form>
					</div>
				</div>
			</div>
		</div>		
	</div>
<!--FIN ADDITIONAL INFORMATION -->
		<?php
				}
		?>
					<div class="table-responsive">
						<form  name="ejecucion" id="ejecucion" method="post" action="<?php echo base_url("settings/update_programacion_PI"); ?>">

							<input type="hidden" id="hddNumeroActividadPI" name="hddNumeroActividadPI" value="<?php echo $numeroActividadPI; ?>"/>
							<input type="hidden" id="hddIdPlanIntegrado" name="hddIdPlanIntegrado" value="<?php echo $idPlanIntegrado; ?>"/>		

							<table class='table table-hover'>
								<thead>
									<tr class="text-primary">
										<th colspan="3">
											<h2>-- Ejecución Actividad --</h2>
											<?php
												if($numeroTrimestrePI){
													echo '<p class="text-primary">Trimestre ' . $numeroTrimestrePI. '</p>'; 
												}
											?>
										</th>
									<?php
										$deshabilidarCampos = false;
										if($userRol == ID_ROL_SUPER_ADMIN || $userRol == ID_ROL_ADMINISTRADOR){
									?>
										<th class="column-title text-right" colspan="2">
											<button type="submit" class="btn btn-primary" id="btnSubmit2" name="btnSubmit2" >
												Guardar Programación/Ejecución <span class="glyphicon glyphicon-floppy-disk" aria-hidden="true">
											</button>
										</th>
									<?php
										}else{
											$deshabilidarCampos = "disabled";
										}
									?>
									</tr>
									
									<tr class="headings">
										<th class="column-title" style="width: 7%">Mes</th>
										<th class="column-title" style="width: 15%">Programado (<?php echo $unidadMedida; ?>)</th>
										<th class="column-title" style="width: 13%">Ejecutado (<?php echo $unidadMedida; ?>)</th>
										<th class="column-title" style="width: 58%">Descripción / Evidencias</th>
										<th class="column-title text-center" style="width: 7%">Enlaces</th>
									</tr>
								</thead>

								<tbody>
								<?php
									foreach ($infoEjecucion as $data):
										echo "<tr>";
										echo "<td >$data[mes]</td>";							
										$idRecord = $data['id_ejecucion_actividad_pi'];
										$idActividad = $data['fk_numero_actividad_pi'];
								?>		
										<input type="hidden" name="form[id][]" value="<?php echo $idRecord; ?>"/>
										<td>
											<input type="number" step="any" min="0" name="form[programado][]" class="form-control" placeholder="Programado" value="<?php echo $data['programado']; ?>" max="500000" required <?php echo $deshabilidarCampos; ?> >
										</td>
										<td>
											<input type="number" step="any" min="0" max="500000" name="form[ejecutado][]" class="form-control" placeholder="Ejecutado" value="<?php echo $data['ejecutado']; ?>"  <?php echo $deshabilidarCampos; ?> >
										</td>
										<td>
											<textarea name="form[descripcion][]" placeholder="Descripción" class="form-control" rows="3" <?php echo $deshabilidarCampos; ?>><?php echo $data['descripcion_actividades']; ?></textarea>
												<br>
											<textarea name="form[evidencia][]" placeholder="Evidencia" class="form-control" rows="3" <?php echo $deshabilidarCampos; ?>><?php echo $data['evidencias']; ?></textarea>
										</td>
										<td class='text-center'>
									<?php
										if($userRol == ID_ROL_SUPER_ADMIN || $userRol == ID_ROL_ADMINISTRADOR){
									?>
											<a class='btn btn-violeta btn-xs' href='<?php echo base_url('settings/deleteEjecucionPI/' . $idPlanIntegrado . '/' . $idActividad . '/' . $idRecord) ?>' id="btn-delete" title="Eliminar Fecha" >
													<span class="fa fa-trash-o" aria-hidden="true"> </span>
											</a>
									<?php
										}
									?>
										</td>
								<?php
										echo "</tr>";
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

<!--INICIO Modal -->
<!--<div class="modal fade text-center" id="modalAuditoria" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">    
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content" id="tablaDatosAuditoria">

		</div>
	</div>
</div>-->
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