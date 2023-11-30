<script type="text/javascript" src="<?php echo base_url("assets/js/validate/settings/tablero_pmr.js"); ?>"></script>
<script>
$(function(){ 
    $(".btn-primary").click(function () {
            $.ajax ({
                type: 'POST',
                url: base_url + 'settings/cargarModalTableroPMR',
                data: {'idPMR': 'x'},
                cache: false,
                success: function (data) {
                    $('#tablaDatos').html(data);
                }
            });
    });
        
    $(".btn-info").click(function () {
            var oID = $(this).attr("id");
            $.ajax ({
                type: 'POST',
                url: base_url + 'settings/cargarModalTableroPMR',
                data: {'idPMR': oID},
                cache: false,
                success: function (data) {
                    $('#tablaDatos').html(data);
                }
            });
    });
});
</script>

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
                        <i class="fa fa-cogs fa-fw"></i> TABLERO PMR <?php echo $vigencia['vigencia']; ?>
                    </h4>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <i class="fa fa-crosshairs"></i> LISTA TABLERO PRM
                    <?php
                        $userRol = $this->session->userdata("role");          
                        if($userRol == ID_ROL_SUPER_ADMIN || $userRol == ID_ROL_ADMINISTRADOR){
                    ?>
                        <div class="pull-right">
                            <div class="btn-group">
                                <button type="button" class="btn btn-primary btn-xs" data-toggle="modal" data-target="#modal" id="x">
                                    <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Adicionar Indicador PMR
                                </button>
                            </div>
                        </div>
                    <?php } ?>
                </div>
                <div class="panel-body small">
                    <?php 
                        if(!$info){
                            echo "<small>No hay definidas las relaciones para esta estretegia.</small>";
                        }else{
                    ?>
                        <table width="100%" class="table table-striped table-bordered table-hover" id="dataTables">
                            <thead>
                                <tr>
                                    <th style='width: 7%'><small>Opciones</small></th>
                                    <th><small>Indicador PMR</small></th>
                                    <th><small>Actividades Relacionadas</small></th>
                                    <th><small>Meta <?php echo $vigencia['vigencia'] ?></small></th>
                                    <th><small>Unidad Medida</small></th>
                                    <th><small>Avance Enero <?php echo $vigencia['vigencia'] ?></small></th>
                                    <th><small>Avance Febrero <?php echo $vigencia['vigencia'] ?></small></th>
                                    <th><small>Avance Marzo <?php echo $vigencia['vigencia'] ?></small></th>
                                    <th><small>Avance Abril <?php echo $vigencia['vigencia'] ?></small></th>
                                    <th><small>Avance Mayo <?php echo $vigencia['vigencia'] ?></small></th>
                                    <th><small>Avance Junio <?php echo $vigencia['vigencia'] ?></small></th>
                                    <th><small>Avance Julio <?php echo $vigencia['vigencia'] ?></small></th>
                                    <th><small>Avance Agosto <?php echo $vigencia['vigencia'] ?></small></th>
                                    <th><small>Avance Septiembre <?php echo $vigencia['vigencia'] ?></small></th>
                                    <th><small>Avance Octubre <?php echo $vigencia['vigencia'] ?></small></th>
                                    <th><small>Avance Noviembre <?php echo $vigencia['vigencia'] ?></small></th>
                                    <th><small>Avance Diciembre <?php echo $vigencia['vigencia'] ?></small></th>
                                    <th><small>% Avance <?php echo $vigencia['vigencia'] ?></small></th>
                                    <th><small>Naturaleza</small></th>
                                    <th><small>Periodicidad</small></th>
                                    <th><small>Elemento PEP</small></th>
                                    <th><small>Producto</small></th>
                                    <th><small>Objetivo</small></th>
                                    <th><small>Proyecto de Inversión</small></th>
                                </tr>
                            </thead>
                            <?php
                            foreach ($info as $lista):
                                $arrParams = array(
                                    'fk_numero_indicador_pmr' => $lista['fk_numero_indicador_pmr'],
                                    'vigencia' => $vigencia['vigencia']
                                );
                                $arrayActividades = $this->settings_model->get_actividades($arrParams);
                                if ($arrayActividades > 0) {
                                    $actividades = $arrayActividades[0]['numero_actividad'];
                                    if (count($arrayActividades) > 1) {
                                        for($i=1; $i<count($arrayActividades); $i++){
                                            $actividades .= ' - ' . $arrayActividades[$i]['numero_actividad'];
                                        }
                                    }
                                    
                                    $arraySumatoriaMetas = $this->settings_model->sumatoria_metas($lista['fk_numero_indicador_pmr']);
                                    $sumatoriaMetas = $arraySumatoriaMetas[0]['meta_plan_operativo_anual'];
                                    if (count($arraySumatoriaMetas) > 1) {
                                        for($i=1; $i<count($arraySumatoriaMetas); $i++){
                                            $sumatoriaMetas .= $arraySumatoriaMetas[$i]['meta_plan_operativo_anual'];
                                        }
                                    }

                                    $sumatoriaTotal = 0;
                                    $arraySumatoriaEnero = $this->settings_model->sumatoria_mes($lista['fk_numero_indicador_pmr'], 1);
                                    $sumatoriaEnero = $arraySumatoriaEnero[0]['ejecutado'];
                                    if (count($arraySumatoriaEnero) > 1) {
                                        for($i=1; $i<count($arraySumatoriaEnero); $i++){
                                            $sumatoriaEnero .= $arraySumatoriaEnero[$i]['ejecutado'];
                                        }
                                    }

                                    $arraySumatoriaFebrero = $this->settings_model->sumatoria_mes($lista['fk_numero_indicador_pmr'], 2);
                                    $sumatoriaFebrero = $arraySumatoriaFebrero[0]['ejecutado'];
                                    if (count($arraySumatoriaFebrero) > 1) {
                                        for($i=1; $i<count($arraySumatoriaFebrero); $i++){
                                            $sumatoriaFebrero .= $arraySumatoriaFebrero[$i]['ejecutado'];
                                        }
                                    }

                                    $arraySumatoriaMarzo = $this->settings_model->sumatoria_mes($lista['fk_numero_indicador_pmr'], 3);
                                    $sumatoriaMarzo = $arraySumatoriaMarzo[0]['ejecutado'];
                                    if (count($arraySumatoriaMarzo) > 1) {
                                        for($i=1; $i<count($arraySumatoriaMarzo); $i++){
                                            $sumatoriaMarzo .= $arraySumatoriaMarzo[$i]['ejecutado'];
                                        }
                                    }

                                    $arraySumatoriaAbril = $this->settings_model->sumatoria_mes($lista['fk_numero_indicador_pmr'], 4);
                                    $sumatoriaAbril = $arraySumatoriaAbril[0]['ejecutado'];
                                    if (count($arraySumatoriaAbril) > 1) {
                                        for($i=1; $i<count($arraySumatoriaAbril); $i++){
                                            $sumatoriaAbril .= $arraySumatoriaAbril[$i]['ejecutado'];
                                        }
                                    }

                                    $arraySumatoriaMayo = $this->settings_model->sumatoria_mes($lista['fk_numero_indicador_pmr'], 5);
                                    $sumatoriaMayo = $arraySumatoriaMayo[0]['ejecutado'];
                                    if (count($arraySumatoriaMayo) > 1) {
                                        for($i=1; $i<count($arraySumatoriaMayo); $i++){
                                            $sumatoriaMayo .= $arraySumatoriaMayo[$i]['ejecutado'];
                                        }
                                    }

                                    $arraySumatoriaJunio = $this->settings_model->sumatoria_mes($lista['fk_numero_indicador_pmr'], 6);
                                    $sumatoriaJunio = $arraySumatoriaJunio[0]['ejecutado'];
                                    if (count($arraySumatoriaJunio) > 1) {
                                        for($i=1; $i<count($arraySumatoriaJunio); $i++){
                                            $sumatoriaJunio .= $arraySumatoriaJunio[$i]['ejecutado'];
                                        }
                                    }

                                    $arraySumatoriaJulio = $this->settings_model->sumatoria_mes($lista['fk_numero_indicador_pmr'], 7);
                                    $sumatoriaJulio = $arraySumatoriaJulio[0]['ejecutado'];
                                    if (count($arraySumatoriaJulio) > 1) {
                                        for($i=1; $i<count($arraySumatoriaJulio); $i++){
                                            $sumatoriaJulio .= $arraySumatoriaJulio[$i]['ejecutado'];
                                        }
                                    }

                                    $arraySumatoriaAgosto = $this->settings_model->sumatoria_mes($lista['fk_numero_indicador_pmr'], 8);
                                    $sumatoriaAgosto = $arraySumatoriaAgosto[0]['ejecutado'];
                                    if (count($arraySumatoriaAgosto) > 1) {
                                        for($i=1; $i<count($arraySumatoriaAgosto); $i++){
                                            $sumatoriaAgosto .= $arraySumatoriaAgosto[$i]['ejecutado'];
                                        }
                                    }

                                    $arraySumatoriaSeptiembre = $this->settings_model->sumatoria_mes($lista['fk_numero_indicador_pmr'], 9);
                                    $sumatoriaSeptiembre = $arraySumatoriaSeptiembre[0]['ejecutado'];
                                    if (count($arraySumatoriaSeptiembre) > 1) {
                                        for($i=1; $i<count($arraySumatoriaSeptiembre); $i++){
                                            $sumatoriaSeptiembre .= $arraySumatoriaSeptiembre[$i]['ejecutado'];
                                        }
                                    }

                                    $arraySumatoriaOctubre = $this->settings_model->sumatoria_mes($lista['fk_numero_indicador_pmr'], 10);
                                    $sumatoriaOctubre = $arraySumatoriaOctubre[0]['ejecutado'];
                                    if (count($arraySumatoriaOctubre) > 1) {
                                        for($i=1; $i<count($arraySumatoriaOctubre); $i++){
                                            $sumatoriaOctubre .= $arraySumatoriaOctubre[$i]['ejecutado'];
                                        }
                                    }

                                    $arraySumatoriaNoviembre = $this->settings_model->sumatoria_mes($lista['fk_numero_indicador_pmr'], 11);
                                    $sumatoriaNoviembre = $arraySumatoriaNoviembre[0]['ejecutado'];
                                    if (count($arraySumatoriaNoviembre) > 1) {
                                        for($i=1; $i<count($arraySumatoriaNoviembre); $i++){
                                            $sumatoriaNoviembre .= $arraySumatoriaNoviembre[$i]['ejecutado'];
                                        }
                                    }

                                    $arraySumatoriaDiciembre = $this->settings_model->sumatoria_mes($lista['fk_numero_indicador_pmr'], 12);
                                    $sumatoriaDiciembre = $arraySumatoriaDiciembre[0]['ejecutado'];
                                    if (count($arraySumatoriaDiciembre) > 1) {
                                        for($i=1; $i<count($arraySumatoriaDiciembre); $i++){
                                            $sumatoriaDiciembre .= $arraySumatoriaDiciembre[$i]['ejecutado'];
                                        }
                                    }

                                    if($sumatoriaDiciembre > 0){
                                        $sumatoriaTotal = ($sumatoriaDiciembre / $sumatoriaMetas) * 100;
                                    } else {
                                        if($sumatoriaNoviembre > 0){
                                            $sumatoriaTotal = ($sumatoriaNoviembre / $sumatoriaMetas) * 100;
                                        } else {
                                            if($sumatoriaOctubre > 0){
                                                $sumatoriaTotal = ($sumatoriaOctubre / $sumatoriaMetas) * 100;
                                            } else {
                                                if($sumatoriaSeptiembre > 0){
                                                    $sumatoriaTotal = ($sumatoriaSeptiembre / $sumatoriaMetas) * 100;
                                                } else {
                                                    if($sumatoriaAgosto > 0){
                                                        $sumatoriaTotal = ($sumatoriaAgosto / $sumatoriaMetas) * 100;
                                                    } else {
                                                        if($sumatoriaJulio > 0){
                                                            $sumatoriaTotal = ($sumatoriaJulio / $sumatoriaMetas) * 100;
                                                        } else {
                                                            if($sumatoriaJunio > 0){
                                                                $sumatoriaTotal = ($sumatoriaJunio / $sumatoriaMetas) * 100;
                                                            } else {
                                                                if($sumatoriaMayo > 0){
                                                                    $sumatoriaTotal = ($sumatoriaMayo / $sumatoriaMetas) * 100;
                                                                } else {
                                                                    if($sumatoriaAbril > 0){
                                                                        $sumatoriaTotal = ($sumatoriaAbril / $sumatoriaMetas) * 100;
                                                                    } else {
                                                                        if($sumatoriaMarzo > 0){
                                                                            $sumatoriaTotal = ($sumatoriaMarzo / $sumatoriaMetas) * 100;
                                                                        } else {
                                                                            if($sumatoriaFebrero > 0){
                                                                                $sumatoriaTotal = ($sumatoriaFebrero / $sumatoriaMetas) * 100;
                                                                            } else {
                                                                                if($sumatoriaEnero > 0){
                                                                                    $sumatoriaTotal = ($sumatoriaEnero / $sumatoriaMetas) * 100;
                                                                                } else {
                                                                                    $sumatoriaTotal = 0;
                                                                                }
                                                                            }
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }

                                    echo "<tr>";
                                    echo "<td class='text-center'>";
                                    ?>
                                    <button title="Editar Tablero PMR" type="button" class="btn btn-info btn-xs" data-toggle="modal" data-target="#modal" id="<?php echo $lista['id_pmr']; ?>" >
                                        <span class="glyphicon glyphicon-edit" aria-hidden="true">
                                    </button>&nbsp;
                                    <button title="Eliminar Tablero PMR" type="button" id="<?php echo $lista['id_pmr']; ?>" class='btn btn-danger btn-xs' title="Eliminar">
                                        <i class="fa fa-trash-o"></i>
                                    </button>
                                    <?php
                                    echo "</td>";
                                    echo "<td><small>" . $lista["fk_numero_indicador_pmr"] . " " . $lista["indicador_pmr"] . "</small></td>";
                                    echo "<td><small>" . $actividades . "</small></td>";
                                    echo "<td><small>" . $sumatoriaMetas . "</small></td>";
                                    echo "<td><small>" . $lista["unidad_medida_pmr"] . "</small></td>";
                                    echo "<td><small>" . round($sumatoriaEnero, 2) . "</small></td>";
                                    echo "<td><small>" . round($sumatoriaFebrero, 2) . "</small></td>";
                                    echo "<td><small>" . round($sumatoriaMarzo, 2) . "</small></td>";
                                    echo "<td><small>" . round($sumatoriaAbril, 2) . "</small></td>";
                                    echo "<td><small>" . round($sumatoriaMayo, 2) . "</small></td>";
                                    echo "<td><small>" . round($sumatoriaJunio, 2) . "</small></td>";
                                    echo "<td><small>" . round($sumatoriaJulio, 2) . "</small></td>";
                                    echo "<td><small>" . round($sumatoriaAgosto, 2) . "</small></td>";
                                    echo "<td><small>" . round($sumatoriaSeptiembre, 2) . "</small></td>";
                                    echo "<td><small>" . round($sumatoriaOctubre, 2) . "</small></td>";
                                    echo "<td><small>" . round($sumatoriaNoviembre, 2) . "</small></td>";
                                    echo "<td><small>" . round($sumatoriaDiciembre, 2) . "</small></td>";
                                    echo "<td><small>" . round($sumatoriaTotal, 2) . "%</small></td>";
                                    echo "<td><small>" . $lista["naturaleza_pmr"] . "</small></td>";
                                    echo "<td><small>" . $lista["periodicidad_pmr"] . "</small></td>";
                                    echo "<td><small>" . $lista["elemento_pep_pmr"] . "</small></td>";
                                    echo "<td><small>" . $lista["fk_numero_producto_pmr"] . " " . $lista["producto_pmr"] . "</small></td>";
                                    echo "<td><small>" . $lista["fk_numero_objetivo_pmr"] . " " . $lista["objetivo_pmr"] . "</small></td>";
                                    echo "<td><small>" . $lista["fk_numero_proyecto_inversion"] . " " . $lista["nombre_proyecto_inversion"] . "</small></td>";
                                    echo "</tr>";
                                }
                            endforeach
                            ?>
                        </table>
                    <?php } ?>
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
<!--FIN Modal  -->
<script>
$(document).ready(function() {
    $('#dataTables').DataTable({
        responsive: true,
        "pageLength": 100,
        paging: false
    });
});
</script>