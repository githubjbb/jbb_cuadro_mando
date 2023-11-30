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
                                $cumplimiento1 = 0;
                                $cumplimiento2 = 0;
                                $cumplimiento3 = 0;
                                $cumplimiento4 = 0;
                                $programado_indicador = $lista['prog_indi_real_trim_1'] + $lista['prog_indi_real_trim_2'] + $lista['prog_indi_real_trim_3'] + $lista['prog_indi_real_trim_4'];
                                if ($lista['ejec_indi_trim_1'] > 0) {
                                    $cumplimiento1 = ($lista['ejec_indi_trim_1'] / $programado_indicador) * 100;
                                }
                                if ($lista['ejec_indi_trim_2'] > 0) {
                                    $cumplimiento2 = ($lista['ejec_indi_trim_2'] / $programado_indicador) * 100;
                                }
                                if ($lista['ejec_indi_trim_3'] > 0) {
                                    $cumplimiento3 = ($lista['ejec_indi_trim_3'] / $programado_indicador) * 100;
                                }
                                if ($lista['ejec_indi_trim_4'] > 0) {
                                    $cumplimiento4 = ($lista['ejec_indi_trim_4'] / $programado_indicador) * 100;
                                }
                                echo "<tr>";
                                echo "<td><small>" . $lista["numero_proposito"] . " " . $lista["proposito"] . "</small></td>";
                                echo "<td><small>" . $lista["numero_programa"] . " " . $lista["programa"] . "</small></td>";
                                echo "<td><small>" . $lista["numero_proyecto_inversion"] . " " . $lista["nombre_proyecto_inversion"] . "</small></td>";
                                echo "<td><small>" . $lista["numero_meta_pdd"] . " " . $lista["meta_pdd"] . "</small></td>";
                                echo "<td><small>" . $lista["numero_indicador"] . " " . $lista["indicador_sp"] . "</small></td>";
                                echo "<td><small>" . $lista["tipologia"] . "</small></td>";
                                echo "<td><small>" . round($programado_indicador, 2) . "</small></td>";
                                echo "<td><small>" . round($lista['prog_indi_real_trim_1'], 2) . "</small></td>";
                                echo "<td><small>" . round($lista['ejec_indi_trim_1'], 2) . "</small></td>";
                                echo "<td><small>" . round($cumplimiento1, 2) . "%</small></td>";
                                echo "<td><small>" . round($lista['prog_indi_real_trim_2'], 2) . "</small></td>";
                                echo "<td><small>" . round($lista['ejec_indi_trim_2'], 2) . "</small></td>";
                                echo "<td><small>" . round($cumplimiento2, 2) . "%</small></td>";
                                echo "<td><small>" . round($lista['prog_indi_real_trim_3'], 2) . "</small></td>";
                                echo "<td><small>" . round($lista['ejec_indi_trim_3'], 2) . "</small></td>";
                                echo "<td><small>" . round($cumplimiento3, 2) . "%</small></td>";
                                echo "<td><small>" . round($lista['prog_indi_real_trim_4'], 2) . "</small></td>";
                                echo "<td><small>" . round($lista['ejec_indi_trim_4'], 2) . "</small></td>";
                                echo "<td><small>" . round($cumplimiento4, 2) . "%</small></td>";
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