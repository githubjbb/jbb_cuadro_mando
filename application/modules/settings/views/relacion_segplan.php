<div id="page-wrapper">
    <br>
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h4 class="list-group-item-heading">
                        <i class="fa fa-gear fa-fw"></i> SEGPLAN - RELACIÓN SEGPLAN <?php echo $vigencia['vigencia']; ?>
                    </h4>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <i class="fa fa-crosshairs"></i> LISTA RELACIÓN SEGPLAN
                </div>
                <div class="panel-body small">
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
                    if($info){
                ?>              
                    <table width="100%" class="table table-striped table-bordered table-hover" id="dataTables">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Objetivo Estratégico</th>
                                <th>Propósito</th>
                                <th>Programa</th>
                                <th>Meta Sectorial</th>
                                <th>Indicador 1</th>
                                <th>Indicador 2</th>
                                <th>Proyecto de Inversión</th>
                                <th>Meta Proyecto de Inversión</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                            $i = 1;
                            foreach ($info as $lista):
                                    
                                    echo "<tr>";
                                    echo "<td>" . $i . "</td>";
                                    echo "<td>" . $lista['fk_numero_objetivo_estrategico'] . "</td>";
                                    echo "<td>" . $lista['fk_numero_proposito'] . "</td>";
                                    echo "<td>" . $lista['fk_numero_programa'] . "</td>";
                                    echo "<td>" . $lista['fk_numero_meta_pdd'] . "</td>";
                                    echo "<td>" . $lista['indicador_1'] . "</td>";
                                    echo "<td>" . $lista['indicador_2'] . "</td>";
                                    echo "<td>" . $lista['fk_numero_proyecto_inversion'] . "</td>";
                                    echo "<td>" . $lista['numero_meta_proyecto'] . "</td>";
                                    echo "</tr>";
                                    $i++;
                            endforeach;
                        ?>
                        </tbody>
                    </table>
                <?php } ?>
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
        "pageLength": 100
    });
});
</script>