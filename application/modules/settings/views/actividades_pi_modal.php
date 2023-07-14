<script type="text/javascript" src="<?php echo base_url("assets/js/validate/settings/actividades_pi.js"); ?>"></script>

<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	<h4 class="modal-title" id="exampleModalLabel">Actividad PI
	<br><small>Adicionar/Editar Actividad PI</small>
	</h4>
</div>

<div class="modal-body">
	<form name="form" id="form" role="form" method="post" >
		<input type="hidden" id="hddIdPlanIntegrado" name="hddIdPlanIntegrado" value="<?php echo $idPlanIntegrado; ?>"/>
		<input type="hidden" id="hddId" name="hddId" value="<?php echo $information?$information[0]["id_actividad_pi"]:""; ?>"/>

		<div class="row">
			<div class="col-sm-4">
				<div class="form-group text-left">
					<label class="control-label" for="numero">No. Actividad: *</label>
					<input type="number" id="numero_actividad" name="numero_actividad" class="form-control" value="<?php echo $information?$information[0]["numero_actividad_pi"]:""; ?>" placeholder="No. Actividad" required >
				</div>
			</div>
			<div class="col-sm-4"></div>
			<div class="col-sm-4">		
				<div class="form-group text-left">
					<label class="control-label" for="id_dependencia">Dependencia: *</label>
					<select name="id_dependencia" id="id_dependencia" class="form-control" required >
						<option value="">Seleccione...</option>
						<?php for ($i = 0; $i < count($listaDependencia); $i++) { ?>
							<option value="<?php echo $listaDependencia[$i]["id_dependencia"]; ?>" <?php if($information && $information[0]["fk_id_dependencia"] == $listaDependencia[$i]["id_dependencia"]) { echo "selected"; }  ?>><?php echo $listaDependencia[$i]["dependencia"]; ?></option>	
						<?php } ?>
					</select>
				</div>
			</div>
		</div>

		<div class="row">				
			<div class="col-sm-12">		
				<div class="form-group text-left">
					<label class="control-label" for="descripcion">Actividad: *</label>
					<textarea id="descripcion" name="descripcion" placeholder="Descripción" class="form-control" rows="2" required><?php echo $information?$information[0]["descripcion_actividad_pi"]:""; ?></textarea>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-sm-4">
				<div class="form-group text-left">
					<label class="control-label" for="meta_plan">Meta Anual: *</label>
					<input type="number" id="meta_plan" name="meta_plan" class="form-control" value="<?php echo $information?$information[0]["meta_plan_operativo_anual_pi"]:""; ?>" placeholder="Meta Plan Operativo Anual" required >
				</div>
			</div>
			
			<div class="col-sm-4">
				<div class="form-group text-left">
					<label class="control-label" for="unidad_medida">Unidad de Medida: *</label>
					<input type="text" id="unidad_medida" name="unidad_medida" class="form-control" value="<?php echo $information?$information[0]["unidad_medida_pi"]:""; ?>" placeholder="Unidad de Medida" required >
				</div>
			</div>

			<div class="col-sm-4">
				<div class="form-group text-left">
					<label class="control-label" for="ponderacion">Ponderación: *</label>
					<input type="number" id="ponderacion" name="ponderacion" class="form-control" value="<?php echo $information?$information[0]["ponderacion_pi"]:""; ?>" placeholder="Ponderación" required >
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-sm-4">
				<div class="form-group text-left">
					<label class="control-label" for="id_responsable">Responsable: *</label>
					<select name="id_responsable" id="id_responsable" class="form-control" required >
						<option value="">Seleccione...</option>
						<?php for ($i = 0; $i < count($listaAreaResponsable); $i++) { ?>
							<option value="<?php echo $listaAreaResponsable[$i]["id_area_responsable"]; ?>" <?php if($information && $information[0]["fk_id_area_responsable"] == $listaAreaResponsable[$i]["id_area_responsable"]) { echo "selected"; }  ?>><?php echo $listaAreaResponsable[$i]["area_responsable"]; ?></option>
						<?php } ?>
					</select>
				</div>
			</div>

			<div class="col-sm-4">
				<div class="form-group text-left">
					<label class="control-label" for="fecha_inicial">Fecha Inicial: *</label>
					<select name="fecha_inicial" id="fecha_inicial" class="form-control" required >
						<option value="">Seleccione...</option>
						<?php for ($i = 0; $i < count($listaMeses); $i++) { ?>
							<option value="<?php echo $listaMeses[$i]["id_mes"]; ?>" <?php if($information && $information[0]["fecha_inicial_pi"] == $listaMeses[$i]["id_mes"]) { echo "selected"; }  ?>><?php echo $listaMeses[$i]["mes"]; ?></option>		
						<?php } ?>
					</select>
				</div>
			</div>
			
			<div class="col-sm-4">		
				<div class="form-group text-left">
					<label class="control-label" for="fecha_final">Fecha Final: *</label>
					<select name="fecha_final" id="fecha_final" class="form-control" required >
						<option value="">Seleccione...</option>
						<?php for ($i = 0; $i < count($listaMeses); $i++) { ?>
							<option value="<?php echo $listaMeses[$i]["id_mes"]; ?>" <?php if($information && $information[0]["fecha_final_pi"] == $listaMeses[$i]["id_mes"]) { echo "selected"; }  ?>><?php echo $listaMeses[$i]["mes"]; ?></option>		
						<?php } ?>
					</select>
				</div>
			</div>
		</div>

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
					<button type="button" id="btnSubmit" name="btnSubmit" class="btn btn-primary" >
						Guardar <span class="glyphicon glyphicon-floppy-disk" aria-hidden="true">
					</button> 
				</div>
			</div>
		</div>
			
	</form>
</div>