<script type="text/javascript" src="<?php echo base_url("assets/js/validate/settings/indicadores_gestion.js"); ?>"></script>

<div id="page-wrapper">
    <br>
    <?php
    $retornoExito = $this->session->flashdata('retornoExito');
    if ($retornoExito) {
        ?>
    	<div class="row">
    		<div class="col-lg-12">	
    			<div class="alert alert-success ">
    				<span class="glyphicon glyphicon-ok" aria-hidden="true"></span>
    				<strong><?php echo $this->session->userdata("firstname"); ?></strong> <?php echo $retornoExito ?>		
    			</div>
    		</div>
    	</div>
        <?php
    }
    $retornoError = $this->session->flashdata('retornoError');
    if ($retornoError) {
        ?>
    	<div class="row">
    		<div class="col-lg-12">	
    			<div class="alert alert-danger ">
    				<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
    				<?php echo $retornoError ?>
    			</div>
    		</div>
    	</div>
        <?php
    }
    ?> 
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h4 class="list-group-item-heading">
                        <i class="fa fa-cogs fa-fw"></i> INDICADORES DE GESTIÓN <?php echo $vigencia['vigencia']; ?>
                    </h4>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <i class="fa fa-crosshairs"></i> LISTA INDICADORES DE GESTIÓN
                    <?php
                        $userRol = $this->session->userdata("role");          
                        if($userRol == ID_ROL_SUPER_ADMIN || $userRol == ID_ROL_ADMINISTRADOR){
                    ?>
                        <div class="pull-right">
                            <div class="btn-group">
                                <?php if(!$info){ ?>
                                    <a class="btn btn-primary btn-xs" href=" <?php echo base_url(). 'settings/subir_archivo/cargar_indicadores_gestion/'.$vigencia['vigencia']; ?> ">
                                        Subir Indicadores de Gestión <?php echo $vigencia['vigencia']; ?> <span class="glyphicon glyphicon-upload" aria-hidden="true"></span>
                                    </a>
                                <?php } else { ?>
                                    <button class="btn btn-danger btn-xs">Eliminar Indicadores de Gestión <?php echo $vigencia['vigencia']; ?> <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
                                    </button>
                                <?php } ?>
                            </div>
                        </div>
                    <?php } ?>
                </div>
                <div class="panel-body small">
                    <?php 
                        if(!$info){
                            echo "No hay registros cargados en la base de datos.";
                        } else {
                    ?>
                        <table width="100%" class="table table-striped table-bordered table-hover" id="dataTables">
                            <thead>
                                <tr>
                                    <th><small>Mas</small></th>
                                    <th><small>Codigo</small></th>
                                    <th><small>Nombre</small></th>
                                    <th><small>Estado</small></th>
                                    <th><small>Objetivo Indicador</small></th>
                                    <th><small>Dependencia</small></th>
                                    <th><small>Proceso</small></th>
                                    <th><small>Objetivo Proceso</small></th>
                                    <th><small>Responsable</small></th>
                                    <th><small>Tipo Indicador</small></th>
                                    <th><small>Tendencia</small></th>
                                    <th><small>Periodicidad</small></th>
                                    <th><small>Fuente Información</small></th>
                                    <th><small>Línea Base</small></th>
                                    <th><small>Unidad de Medida</small></th>
                                    <th><small>Fecha Creación</small></th>
                                    <th><small>Fórmula</small></th>
                                    <th><small>Enero</small></th>
                                    <th><small>Febrero</small></th>
                                    <th><small>Marzo</small></th>
                                    <th><small>Abril</small></th>
                                    <th><small>Mayo</small></th>
                                    <th><small>Junio</small></th>
                                    <th><small>Julio</small></th>
                                    <th><small>Agosto</small></th>
                                    <th><small>Septiembre</small></th>
                                    <th><small>Octubre</small></th>
                                    <th><small>Noviembre</small></th>
                                    <th><small>Diciembre</small></th>
                                    <th><small>Ejecución Acumulada</small></th>
                                </tr>
                            </thead>
                            <?php
                            foreach ($info as $lista):
                                echo "<tr>";
                                echo "<td class='text-center'></td>";
                                echo "<td><small>" . $lista["codigo"] . "</small></td>";
                                echo "<td><small>" . $lista["nombre"] . "</small></td>";
                                echo "<td><small>" . $lista["estado"] . "</small></td>";
                                echo "<td><small>" . $lista["objetivo_indicador"] . "</small></td>";
                                echo "<td><small>" . $lista["dependencia"] . "</small></td>";
                                echo "<td><small>" . $lista["proceso"] . "</small></td>";
                                echo "<td><small>" . $lista["objetivo_proceso"] . "</small></td>";
                                echo "<td><small>" . $lista["responsable"] . "</small></td>";
                                echo "<td><small>" . $lista["tipo_indicador"] . "</small></td>";
                                echo "<td><small>" . $lista["tendencia"] . "</small></td>";
                                echo "<td><small>" . $lista["periodicidad"] . "</small></td>";
                                echo "<td><small>" . $lista["fuente_informacion"] . "</small></td>";
                                echo "<td><small>" . $lista["linea_base"] . "</small></td>";
                                echo "<td><small>" . $lista["unidad_medida"] . "</small></td>";
                                echo "<td><small>" . $lista["fecha_creacion"] . "</small></td>";
                                echo "<td><small>" . $lista["formula"] . "</small></td>";
                                echo "<td><small>" . $lista["enero"] . "<br><strong>Seguimiento: </strong>" . $lista["seguimiento_ene"] . "<br><strong>Seguimiento OAP: </strong>" . $lista["seguimiento_oap_ene"] . "</small></td>";
                                echo "<td><small>" . $lista["febrero"] . "<br><strong>Seguimiento: </strong>" . $lista["seguimiento_feb"] . "<br><strong>Seguimiento OAP: </strong>" . $lista["seguimiento_oap_feb"] . "</small></td>";
                                echo "<td><small>" . $lista["marzo"] . "<br><strong>Seguimiento: </strong>" . $lista["seguimiento_mar"] . "<br><strong>Seguimiento OAP: </strong>" . $lista["seguimiento_oap_mar"] . "</small></td>";
                                echo "<td><small>" . $lista["abril"] . "<br><strong>Seguimiento: </strong>" . $lista["seguimiento_abr"] . "<br><strong>Seguimiento OAP: </strong>" . $lista["seguimiento_oap_abr"] . "</small></td>";
                                echo "<td><small>" . $lista["mayo"] . "<br><strong>Seguimiento: </strong>" . $lista["seguimiento_may"] . "<br><strong>Seguimiento OAP: </strong>" . $lista["seguimiento_oap_may"] . "</small></td>";
                                echo "<td><small>" . $lista["junio"] . "<br><strong>Seguimiento: </strong>" . $lista["seguimiento_jun"] . "<br><strong>Seguimiento OAP: </strong>" . $lista["seguimiento_oap_jun"] . "</small></td>";
                                echo "<td><small>" . $lista["julio"] . "<br><strong>Seguimiento: </strong>" . $lista["seguimiento_jul"] . "<br><strong>Seguimiento OAP: </strong>" . $lista["seguimiento_oap_jul"] . "</small></td>";
                                echo "<td><small>" . $lista["agosto"] . "<br><strong>Seguimiento: </strong>" . $lista["seguimiento_ago"] . "<br><strong>Seguimiento OAP: </strong>" . $lista["seguimiento_oap_ago"] . "</small></td>";
                                echo "<td><small>" . $lista["septiembre"] . "<br><strong>Seguimiento: </strong>" . $lista["seguimiento_sep"] . "<br><strong>Seguimiento OAP: </strong>" . $lista["seguimiento_oap_sep"] . "</small></td>";
                                echo "<td><small>" . $lista["octubre"] . "<br><strong>Seguimiento: </strong>" . $lista["seguimiento_oct"] . "<br><strong>Seguimiento OAP: </strong>" . $lista["seguimiento_oap_oct"] . "</small></td>";
                                echo "<td><small>" . $lista["noviembre"] . "<br><strong>Seguimiento: </strong>" . $lista["seguimiento_nov"] . "<br><strong>Seguimiento OAP: </strong>" . $lista["seguimiento_oap_nov"] . "</small></td>";
                                echo "<td><small>" . $lista["diciembre"] . "<br><strong>Seguimiento: </strong>" . $lista["seguimiento_dic"] . "<br><strong>Seguimiento OAP: </strong>" . $lista["seguimiento_oap_dic"] . "</small></td>";
                                echo "<td><small>" . $lista["ejecucion_acumulada"] . "</small></td>";
                                echo "</tr>";
                            endforeach
                            ?>
                        </table>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#dataTables').DataTable({
        responsive: true,
        "pageLength": 100,
        paging: false
    });
});
</script>