<div id="page-wrapper">
    <br>
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h4 class="list-group-item-heading">
                        <i class="fa fa-cogs fa-fw"></i> METAS SECTORIALES <?php echo $vigencia['vigencia']; ?>
                    </h4>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <i class="fa fa-crosshairs"></i> LISTA METAS SECTORIALES
                </div>
                <div class="panel-body small">
                    <?php 
                        if(!$info){
                            echo "<small>No hay registros.</small>";
                        } else {
                    ?>
                        <table width="100%" class="table table-striped table-bordered table-hover" id="dataTables">
                            <thead>
                                <tr>
                                    <th><small>Proposito PDD</small></th>
                                    <th><small>Programa General PDD</small></th>
                                    <th><small>Proyecto de Inversión</small></th>
                                    <th><small>Gerencia Responsable</small></th>
                                    <th><small>Meta Sectorial</small></th>
                                    <th><small>Indicador Sectorial</small></th>
                                    <th><small>Tipología del Indicador</small></th>
                                    <th><small>Programación Indicador Sectorial <?php echo $vigencia['vigencia'] ?></small></th>
                                    <th><small>Programación Indicador Sectorial Trimestre I</small></th>
                                    <th><small>Magnitud Ejecutada Trimestre I</small></th>
                                    <th><small>% de Avance Trimestre I</small></th>
                                    <th><small>Programación Indicador Sectorial Trimestre II</small></th>
                                    <th><small>Magnitud Ejecutada Trimestre II</small></th>
                                    <th><small>% de Avance Trimestre II</small></th>
                                    <th><small>Programación Indicador Sectorial Trimestre III</small></th>
                                    <th><small>Magnitud Ejecutada Trimestre III</small></th>
                                    <th><small>% de Avance Trimestre III</small></th>
                                    <th><small>Programación Indicador Sectorial Trimestre IV</small></th>
                                    <th><small>Magnitud Ejecutada Trimestre IV</small></th>
                                    <th><small>% de Avance Trimestre IV</small></th>
                                </tr>
                            </thead>
                            <?php
                            foreach ($info as $lista):
                                $valorProgramadoTrimestre1 = 0;
                                $valorProgramadoTrimestre2 = 0;
                                $valorProgramadoTrimestre3 = 0;
                                $valorProgramadoTrimestre4 = 0;
                                $sumaEjecutadoTrimestre1 = 0;
                                $sumaEjecutadoTrimestre2 = 0;
                                $sumaEjecutadoTrimestre3 = 0;
                                $sumaEjecutadoTrimestre4 = 0;
                                $cumplimiento1 = 0;
                                $cumplimiento2 = 0;
                                $cumplimiento3 = 0;
                                $cumplimiento4 = 0;

                                $arrParam = array("numeroActividad" => $lista["numero_actividad"]);
                                $estadoActividad = $this->general_model->get_estados_actividades($arrParam);

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

                                $valorProgramadoTrimestre1 = $sumaProgramadoTrimestre1['programado'];
                                $valorProgramadoTrimestre2 = $sumaProgramadoTrimestre2['programado'];
                                $valorProgramadoTrimestre3 = $sumaProgramadoTrimestre3['programado'];
                                $valorProgramadoTrimestre4 = $sumaProgramadoTrimestre4['programado'];
                                
                                if ($estadoActividad[0]['estado_trimestre_1'] != 0){
                                    if($sumaProgramadoTrimestre1['programado'] > 0) {
                                        $cumplimiento1 = round($sumaEjecutadoTrimestre1['ejecutado'] / $sumaProgramadoTrimestre1['programado'] * 100,3);
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
                                        $cumplimiento2 = round($sumaEjecutadoTrimestre2['ejecutado'] / $sumaProgramadoTrimestre2['programado'] * 100,3);
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
                                        $cumplimiento3 = round($sumaEjecutadoTrimestre3['ejecutado'] / $sumaProgramadoTrimestre3['programado'] * 100,3);
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
                                        $cumplimiento4 = round($sumaEjecutadoTrimestre4['ejecutado'] / $sumaProgramadoTrimestre4['programado'] * 100,3);
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

                                echo "<tr>";
                                echo "<td><small>" . $lista["numero_proposito"] . " " . $lista["proposito"] . "</small></td>";
                                echo "<td><small>" . $lista["numero_programa"] . " " . $lista["programa"] . "</small></td>";
                                echo "<td><small>" . $lista["numero_proyecto_inversion"] . " " . $lista["nombre_proyecto_inversion"] . "</small></td>";
                                echo "<td><small>" . $lista["dependencia"] . "</small></td>";
                                echo "<td><small>" . $lista["numero_meta_pdd"] . " " . $lista["meta_pdd"] . "</small></td>";
                                echo "<td><small>" . $lista["numero_indicador"] . " " . $lista["indicador_sp"] . "</small></td>";
                                echo "<td><small>" . $lista["tipologia"] . "</small></td>";
                                echo "<td><small>" . round($lista["meta_plan_operativo_anual"], 3) . "</small></td>";
                                echo "<td><small>" . round($valorProgramadoTrimestre1, 3) . "</small></td>";
                                echo "<td><small>" . round($sumaEjecutadoTrimestre1['ejecutado'], 3) . "</small></td>";
                                echo "<td><small>" . round($cumplimiento1, 3) . "</small></td>";
                                echo "<td><small>" . round($valorProgramadoTrimestre2, 3) . "</small></td>";
                                echo "<td><small>" . round($sumaEjecutadoTrimestre2['ejecutado'], 3) . "</small></td>";
                                echo "<td><small>" . round($cumplimiento2, 3) . "</small></td>";
                                echo "<td><small>" . round($valorProgramadoTrimestre3, 3) . "</small></td>";
                                echo "<td><small>" . round($sumaEjecutadoTrimestre3['ejecutado'], 3) . "</small></td>";
                                echo "<td><small>" . round($cumplimiento3, 3) . "</small></td>";
                                echo "<td><small>" . round($valorProgramadoTrimestre4, 3) . "</small></td>";
                                echo "<td><small>" . round($sumaEjecutadoTrimestre4['ejecutado'], 3) . "</small></td>";
                                echo "<td><small>" . round($cumplimiento4, 3) . "</small></td>";
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