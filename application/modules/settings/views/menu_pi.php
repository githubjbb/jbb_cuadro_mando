<div class="col-lg-3 small">
	<div class="panel panel-info">
		<div class="panel-heading small">
			<i class="fa fa-thumb-tack"></i>
				<strong>Planes Integrados: </strong>
				<br><?php echo $planInstitucional[0]['id_plan_institucional'] . ' ' . $planInstitucional[0]['plan_institucional']; ?>
		</div>
		<div class="panel-body small">
		<?php
			$userRol = $this->session->userdata("role");
			if($userRol == ID_ROL_SUPER_ADMIN || $userRol == ID_ROL_ADMINISTRADOR){
				$vigencia = $this->general_model->get_vigencia();
				$idPlanIntegrado = $planInstitucional[0]['id_plan_integrado'];
				$arrParam = array(
					"idPlanIntegrado" => $idPlanIntegrado,
					'vigencia' => $vigencia['vigencia']
				);
				$listaActividadesPI = $this->general_model->get_actividadesPI($arrParam);
		?>
			<br>
			<button type="button" class="btn btn-info btn-block" data-toggle="modal" data-target="#modal" id="<?php echo $planInstitucional[0]['id_plan_integrado']; ?> " <?php if(!empty($listaActividadesPI)){ ?> disabled <?php } ?> >
					<span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Adicionar Actividad PI
			</button>
		<?php
			}
		?>
		</div>


	</div>

<?php 
	if($listaHistorial)
	{
?>
	<div class="chat-panel panel panel-primary">
		<div class="panel-heading">
			<i class="fa fa-comments fa-fw"></i><b> Historial Trimestre <?php echo $numeroTrimestrePI; ?></b>
		</div>
		<div class="panel-body">
			<ul class="chat">
			<?php 
				foreach ($listaHistorial as $data):		
			?>
				<li class="right clearfix">
					<span class="chat-img pull-right">
						<small class="pull-right text-muted">
							<i class="fa fa-clock-o fa-fw"></i> <?php echo $data['fecha_cambio']; ?>
						</small>
					</span>
					<div class="chat-body clearfix">
						<div class="header">
							<span class="glyphicon glyphicon-user" aria-hidden="true"></span>
							<strong class="primary-font"><?php echo $data['first_name']; ?></strong>
						</div>
						<p>
							<?php echo $data['observacion']; ?>
						</p>
						<?php echo '<p class="text-' . $data['clase'] . '"><strong><i class="fa ' . $data['icono']  . ' fa-fw"></i>' . $data['estado'] . '</strong></p>'; ?>
					</div>
				</li>
			<?php
				endforeach;
			?>
			</ul>		
		</div>
	</div>
<?php
	}
?>


<?php 
	if($listaHistorial1)
	{
?>
	<div class="chat-panel panel panel-primary">
		<div class="panel-heading">
			<i class="fa fa-comments fa-fw"></i> <b>Historial Trimestre I</b>
		</div>
		<div class="panel-body">
			<ul class="chat">
			<?php 
				foreach ($listaHistorial1 as $data):		
			?>
				<li class="right clearfix">
					<span class="chat-img pull-right">
						<small class="pull-right text-muted">
							<i class="fa fa-clock-o fa-fw"></i> <?php echo $data['fecha_cambio']; ?>
						</small>
					</span>
					<div class="chat-body clearfix">
						<div class="header">
							<span class="glyphicon glyphicon-user" aria-hidden="true"></span>
							<strong class="primary-font"><?php echo $data['first_name']; ?></strong>
						</div>
						<p>
							<?php echo $data['observacion']; ?>
						</p>
						<?php echo '<p class="text-' . $data['clase'] . '"><strong><i class="fa ' . $data['icono']  . ' fa-fw"></i>' . $data['estado'] . '</strong></p>'; ?>
					</div>
				</li>
			<?php
				endforeach;
			?>
			</ul>		
		</div>
	</div>
<?php
	}
?>

<?php 
	if($listaHistorial2)
	{
?>
	<div class="chat-panel panel panel-primary">
		<div class="panel-heading">
			<i class="fa fa-comments fa-fw"></i>  <b>Historial Trimestre II </b>
		</div>
		<div class="panel-body">
			<ul class="chat">
			<?php 
				foreach ($listaHistorial2 as $data):		
			?>
				<li class="right clearfix">
					<span class="chat-img pull-right">
						<small class="pull-right text-muted">
							<i class="fa fa-clock-o fa-fw"></i> <?php echo $data['fecha_cambio']; ?>
						</small>
					</span>
					<div class="chat-body clearfix">
						<div class="header">
							<span class="glyphicon glyphicon-user" aria-hidden="true"></span>
							<strong class="primary-font"><?php echo $data['first_name']; ?></strong>
						</div>
						<p>
							<?php echo $data['observacion']; ?>
						</p>
						<?php echo '<p class="text-' . $data['clase'] . '"><strong><i class="fa ' . $data['icono']  . ' fa-fw"></i>' . $data['estado'] . '</strong></p>'; ?>
					</div>
				</li>
			<?php
				endforeach;
			?>
			</ul>		
		</div>
	</div>
<?php
	}
?>

<?php 
	if($listaHistorial3)
	{
?>
	<div class="chat-panel panel panel-primary">
		<div class="panel-heading">
			<i class="fa fa-comments fa-fw"></i>  <b>Historial Trimestre III </b>
		</div>
		<div class="panel-body">
			<ul class="chat">
			<?php 
				foreach ($listaHistorial3 as $data):		
			?>
				<li class="right clearfix">
					<span class="chat-img pull-right">
						<small class="pull-right text-muted">
							<i class="fa fa-clock-o fa-fw"></i> <?php echo $data['fecha_cambio']; ?>
						</small>
					</span>
					<div class="chat-body clearfix">
						<div class="header">
							<span class="glyphicon glyphicon-user" aria-hidden="true"></span>
							<strong class="primary-font"><?php echo $data['first_name']; ?></strong>
						</div>
						<p>
							<?php echo $data['observacion']; ?>
						</p>
						<?php echo '<p class="text-' . $data['clase'] . '"><strong><i class="fa ' . $data['icono']  . ' fa-fw"></i>' . $data['estado'] . '</strong></p>'; ?>
					</div>
				</li>
			<?php
				endforeach;
			?>
			</ul>		
		</div>
	</div>
<?php
	}
?>

<?php 
	if($listaHistorial4)
	{
?>
	<div class="chat-panel panel panel-primary">
		<div class="panel-heading">
			<i class="fa fa-comments fa-fw"></i>  <b>Historial Trimestre IV </b>
		</div>
		<div class="panel-body">
			<ul class="chat">
			<?php 
				foreach ($listaHistorial4 as $data):		
			?>
				<li class="right clearfix">
					<span class="chat-img pull-right">
						<small class="pull-right text-muted">
							<i class="fa fa-clock-o fa-fw"></i> <?php echo $data['fecha_cambio']; ?>
						</small>
					</span>
					<div class="chat-body clearfix">
						<div class="header">
							<span class="glyphicon glyphicon-user" aria-hidden="true"></span>
							<strong class="primary-font"><?php echo $data['first_name']; ?></strong>
						</div>
						<p>
							<?php echo $data['observacion']; ?>
						</p>
						<?php echo '<p class="text-' . $data['clase'] . '"><strong><i class="fa ' . $data['icono']  . ' fa-fw"></i>' . $data['estado'] . '</strong></p>'; ?>
					</div>
				</li>
			<?php
				endforeach;
			?>
			</ul>		
		</div>
	</div>
<?php
	}
?>


</div>	