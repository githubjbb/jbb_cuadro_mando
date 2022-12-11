<script type="text/javascript" src="<?php echo base_url("assets/js/validate/resumen/evaluacion_objetivos.js"); ?>"></script>

<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	<h4 class="modal-title" id="exampleModalLabel">Evaluación Objetivos Estratégicos
	<br><b>Objetivo Estratégico No.: </b> <?php echo $numeroObjetivoEstrategico; ?>
	</h4>
</div>

<div class="modal-body small">
	<div class="row">
		<div class="col-sm-12">
			<div class="form-group text-left">
				<small>
					<label class="control-label" for="Estrategia">Estratégia: </label>
					<?php echo $infoSupervisores[0]["id_estrategia"] . ' ' . $infoSupervisores[0]["estrategia"]; ?>
					<br>
					<label class="control-label" for="descripcion">Descrición Estratégia: </label>
					<?php echo $infoSupervisores[0]["descripcion_estrategia"]; ?>
					<br>
					<label class="control-label" for="objetivo_estrategico">Objetivo Estrategico: </label>
					<?php echo $infoSupervisores[0]["numero_objetivo_estrategico"] . ' ' . $infoSupervisores[0]["objetivo_estrategico"]; ?>
				</small>
			</div>
		</div>
	</div>

	<hr>
<?php
	if(!$infoEvaluacion){
?>
	<p class="text-danger text-left">No hay registros.</p>
	<hr>
<?php 
	} else {
?>
		<table class='table table-hover'>
			<thead>
				<tr class="headings">
					<th><small>Fecha</small></th>
					<th><small>Usuario</small></th>
					<th class="text-right"><small>Cumplimiento POA</small></th>
					<th><small>Observación</small></th>
					<th><small>Comentario</small></th>
					<th class="text-right"><small>Cumplimiento</small></th>
					<th><small>Estado</small></th>
				</tr>
			</thead>

			<tbody>
                <?php
                    foreach ($infoEvaluacion as $data):
                    	$usuario = $this->general_model->get_usuarios($data['fk_id_usuario']);
                ?>
                    <tr>
                        <td class="text-left"><small><?php echo $data['fecha_cambio']; ?></small></td>
                        <td class="text-left"><small><?php echo $usuario['first_name'] . ' ' . $usuario['last_name']; ?></small></td>
                        <td class="text-right"><small><?php echo $data['cumplimiento_poa']; ?></small></td>
                        <td class="text-left"><small><?php echo $data['observacion']; ?></small></td>
                        <td class="text-left"><small><?php echo $data['comentario']; ?></small></td>
                        <td class="text-right"><small><?php echo $data['calificacion']; ?></small></td>
                        <td class="text-left"><small><?php if ($data['estado'] == 1) { echo 'Enviada'; } else if ($data['estado'] == 2) { echo 'Aprobada'; } else if ($data['estado'] == 3) { echo 'Rechazada'; } else if ($data['estado'] == 4) { echo 'Devuelta'; } ?></small></td>
                    </tr>
                <?php
                    endforeach;
                ?>
			</tbody>
		</table>
		<hr>
<?php } ?>

	<form name="form" id="form" role="form" method="post" >
		<?php
			$arrParam = array(
                "numeroObjetivoEstrategico" => $infoSupervisores[0]["numero_objetivo_estrategico"],
                "vigencia" => date("Y")
            );
            $nroActividades = $this->general_model->countActividades($arrParam);
			$cumplimiento = $this->general_model->sumCumplimiento($arrParam);
			$calificacion = $this->general_model->get_evaluacion_calificacion($arrParam);
			$calificacion_0 = isset($calificacion[0]['calificacion']);
			$calificacion_1 = isset($calificacion[1]['calificacion']);
            $promedioCumplimiento = 0;
            if($nroActividades){
                $promedioCumplimiento = number_format($cumplimiento["cumplimiento"]/$nroActividades,2);
            }
            if ($calificacion_0){
            	if ($promedioCumplimiento > $calificacion[0]['calificacion']) {
            		$promedioCumplimiento = number_format($cumplimiento["cumplimiento"]/$nroActividades,2);
                } else {
                	if ($calificacion[0]['estado'] == 2) {
                        $promedioCumplimiento = $calificacion[0]['calificacion'];
                	}
                	if ($calificacion[0]['estado'] == 1 || $calificacion[0]['estado'] == 3) {
                		if ($calificacion_1) {
                			if ($calificacion[1]['estado'] == 2) {
                				$promedioCumplimiento = $calificacion[1]['calificacion'];
                			}
                		} else {
                            $promedioCumplimiento = number_format($cumplimiento["cumplimiento"]/$nroActividades,2);
                		}
                	}
                	if ($calificacion[0]['estado'] == 4) {
                        $promedioCumplimiento = number_format($cumplimiento["cumplimiento"]/$nroActividades,2);
                	}
                }
            } else {
            	$promedioCumplimiento = number_format($cumplimiento["cumplimiento"]/$nroActividades,2);
            }
		?>
		<input type="hidden" id="hddId" name="hddId" value="<?php echo $infoEvaluacion?$infoEvaluacion[0]["id_evaluacion_objetivo_estrategico"]:""; ?>"/>
		<input type="hidden" id="hddNumero" name="hddNumero" value="<?php echo $infoSupervisores?$infoSupervisores[0]["numero_objetivo_estrategico"]:""; ?>"/>
		<input type="hidden" id="hddCumplimientoPOA" name="hddCumplimientoPOA" value="<?php echo $promedioCumplimiento?$promedioCumplimiento:""; ?>"/>

		<div class="row">
			<div class="col-sm-4">
				<div class="form-group text-left">
					<label class="control-label" for="calificacion">Calificación: *</label>
					<input type="number" id="calificacion" name="calificacion" class="form-control" value="<?php if ($infoEvaluacion && $infoEvaluacion[0]["estado"] == 1) { echo $infoEvaluacion[0]["calificacion"]; } else { echo ""; } ?>" placeholder="Calificación" required >
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-sm-12">
				<div class="form-group text-left">
					<label class="control-label" for="observacion">Observación: *</label>
					<textarea id="observacion" name="observacion" class="form-control" rows="3" placeholder="Observación" required><?php if ($infoEvaluacion && $infoEvaluacion[0]["estado"] == 1) { echo $infoEvaluacion[0]["observacion"]; } else { echo ""; } ?></textarea>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-sm-12">
				<div class="form-group text-left">
					<label class="control-label" for="comentario">Comentario: *</label>
					<textarea id="comentario" name="comentario" class="form-control" rows="3" placeholder="Comentario" required><?php if ($infoEvaluacion && $infoEvaluacion[0]["estado"] == 1) { echo $infoEvaluacion[0]["comentario"]; } else { echo ""; } ?></textarea>
				</div>
			</div>
		</div>
		<hr>

		<div class="row">
			<div class="col-sm-12">
				<div class="form-group text-left">
					<?php
						if ($infoComentario) { 
					?>
					<label class="control-label" for="supervisores">Comentario de Supervisores Ultima Calificación:</label><br>
					<table class='table table-hover'>
						<thead>
							<tr class="headings">
								<th width='10%'><small>Fecha</small></th>
								<th width='30%'><small>Supervisor</small></th>
								<th width='60%'><small>Comentario</small></th>
							</tr>
						</thead>

						<tbody>
			                <?php
			                    foreach ($infoComentario as $data):
			                ?>
			                    <tr>
			                        <td class="text-left"><small><?php echo $data['fecha_comentario']; ?></small></td>
			                        <td class="text-left"><small><?php echo $data['first_name'] . ' ' . $data['last_name']; ?></small></td>
			                        <td class="text-left"><small><?php echo $data['comentario_supervisor']; ?></small></td>
			                    </tr>
			                <?php
			                    endforeach;
			                ?>
						</tbody>
					</table>
				<?php } else { ?>
					<p class="text-danger text-left">No hay comentarios de supervisores.</p>
				<?php } ?>
				</div>
			</div>
		</div>
		<hr>
				
		<div class="form-group">
			<div id="div_load" style="display:none">		
				<div class="progress progress-striped active">
					<div class="progress-bar" role="progressbar" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100" style="width: 45%">
						<span class="sr-only">45% completado</span>
					</div>
				</div>
			</div>
			<div id="div_error" style="display:none">			
				<div class="alert alert-danger"><span class="glyphicon glyphicon-remove" id="span_msj">&nbsp;</span></div>
			</div>	
		</div>
		
		<div class="form-group">
			<div class="row" align="center">
				<div style="width:50%;" align="center">
					<?php
					$arrParam = array(
                        "numeroObjetivoEstrategico" => $infoSupervisores[0]["numero_objetivo_estrategico"],
                        "vigencia" => date("Y")
                    );
                    $calificacion = $this->general_model->get_evaluacion_calificacion($arrParam);
                    $comentarios = $this->general_model->get_comentarios_supervisores($arrParam);
                    $calificacion_0 = isset($calificacion[0]['estado']);
                    $habilitar_1 = '';
                    $habilitar_2 = ' disabled';
                    /*$contador = 0;*/
                    if ($calificacion_0) {
	                    if ($calificacion[0]['estado'] == 1) {
	                    	for ($i=0; $i<count($infoSupervisores); $i++) {
	                    		if ($comentarios[$i]["comentario_supervisor"] != NULL) {
	                    			$habilitar_1 = ' disabled';
	                    			$habilitar_2 = '';
	                    			/*$contador += 1;*/
	                    		}
	                    	}
	                    	/*if ($contador == count($infoSupervisores)) {
	                    		$habilitar = '';
	                    	}*/
	                    }
	                }
					?>
					<button type="button" id="btnSubmit" name="btnSubmit" class="btn btn-primary" <?php echo $habilitar_1; ?>>
						Enviar <span class="glyphicon glyphicon-send" aria-hidden="true">
					</button>
					<button type="button" id="btnAprobar" name="btnAprobar" class="btn btn-success" <?php echo $habilitar_2; ?>>
						Aprobar <span class="glyphicon glyphicon-ok" aria-hidden="true">
					</button>
					<button type="button" id="btnRechazar" name="btnRechazar" class="btn btn-warning" <?php echo $habilitar_2; ?>>
						Rechazar <span class="glyphicon glyphicon-ban-circle" aria-hidden="true">
					</button>
					<button type="button" id="btnDevolver" name="btnDevolver" class="btn btn-danger" <?php if ((!$calificacion_0) || ($calificacion[0]['estado'] == 4) || ($calificacion[0]['estado'] == 2 && $promedioCumplimiento > $calificacion[0]['calificacion'])) { ?> disabled <?php } ?>>
						Devolver <span class="glyphicon glyphicon-repeat" aria-hidden="true">
					</button>
				</div>
			</div>
		</div>
	</form>
</div>