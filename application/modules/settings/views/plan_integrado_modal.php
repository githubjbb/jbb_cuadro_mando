<script type="text/javascript" src="<?php echo base_url("assets/js/validate/settings/plan_integrado.js"); ?>"></script>
<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	<h4 class="modal-title" id="exampleModalLabel">Formulario Planes Integrados
	<br><small>Adicionar/Editar Planes Integrados.</small>
	</h4>
</div>

<div class="modal-body">
	<p class="text-danger text-left">Los campos con * son obligatorios.</p>
	<form name="form" id="form" role="form" method="post" >
		<input type="hidden" id="hddId" name="hddId" value="<?php echo $information?$information[0]["id_plan_integrado"]:""; ?>"/>
		<input type="hidden" id="hddVigencia" name="hddVigencia" value="<?php echo $vigencia['vigencia']; ?>"/>
		<div class="row">
			<div class="col-sm-12">
				<div class="form-group text-left">
                    <label class="control-label" for="plan_institucional">Plan Institucional:</label>
                    <select name="plan_institucional" id="plan_institucional" class="form-control" >
                        <option value="">Seleccione...</option>
                        <?php for ($i = 0; $i < count($listaPlanesInstitucionales); $i++) { ?>
                            <option value="<?php echo $listaPlanesInstitucionales[$i]["id_plan_institucional"]; ?>" <?php if($information && $information[0]["fk_id_plan_institucional"] == $listaPlanesInstitucionales[$i]["id_plan_institucional"]) { echo "selected"; } ?>><?php echo $listaPlanesInstitucionales[$i]["plan_institucional"]; ?></option>
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