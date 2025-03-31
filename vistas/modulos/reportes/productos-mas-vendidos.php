<?php

$item = null;
$valor = null;
$orden = "ventas";

try {
    $productos = ControladorProductos::ctrMostrarProductos($item, $valor, $orden);
    $totalVentas = ControladorProductos::ctrMostrarSumaVentas();

    // Asegurarse de que $productos es un array para evitar errores en foreach
    if (!is_array($productos)) {
        $productos = [];
    }

} catch (Exception $e) {
    error_log("Error al obtener los datos del producto: " . $e->getMessage());
    echo "<p class='error-message'>Error: No se pueden recuperar los datos del producto. Inténtalo de nuevo más tarde.</p>";
    $productos = [];
    $totalVentas = ["total" => 0];
}


$colores = ["red", "green", "yellow", "aqua", "purple"]; // Reducido a los colores que se usan

?>

<div class="box box-default">

    <div class="box-header with-border">

        <h3 class="box-title">Productos más vendidos</h3>

    </div>

    <div class="box-body">

        <div class="row">

            <div class="col-md-7">

                <div class="chart-responsive">

                    <canvas id="pieChart" height="150"></canvas>

                </div>

            </div>

            <div class="col-md-5">

                <ul class="chart-legend clearfix">

                    <?php
                    foreach ($productos as $i => $producto) {
                        if ($i < 5) { // Para mostrar solo los primeros 5
                            echo "<li><i class='fa fa-circle-o text-{$colores[$i % count($colores)]}'></i> " . htmlspecialchars($producto["descripcion"]) . "</li>";
                        }
                    }
                    ?>

                </ul>

            </div>

        </div>

    </div>

    <div class="box-footer no-padding">

        <ul class="nav nav-pills nav-stacked">

            <?php
            foreach ($productos as $i => $producto) {
                if ($i < 5) { // Para mostrar solo los primeros 5
                    echo "<li>
                            <a>
                                <img src='" . htmlspecialchars($producto["imagen"]) . "' class='img-thumbnail' width='60px' style='margin-right:10px'>
                                " . htmlspecialchars($producto["descripcion"]) . "
                                <span class='pull-right text-{$colores[$i % count($colores)]}'>
                                    " . (int)$producto["ventas"] . " vendidos
                                </span>
                            </a>
                        </li>";
                }
            }
            ?>

        </ul>

    </div>

</div>

<script>

    // -------------
    // - PIE CHART -
    // -------------
    var pieChartCanvas = $('#pieChart').get(0).getContext('2d');
    var pieChart = new Chart(pieChartCanvas);
    var PieData = [];

    <?php
    foreach ($productos as $i => $producto) {
        if ($i < 5) {  // Limitar los datos a los primeros 5 productos
            $colorIndex = $i % count($colores); // Calculamos el índice de color correcto
            echo "PieData.push({
                value    : " . (int)$producto["ventas"] . ",
                color    : '" . $colores[$colorIndex] . "',
                highlight: '" . $colores[$colorIndex] . "',
                label    : '" . htmlspecialchars($producto["descripcion"]) . "'
            });";
        }
    }
    ?>

    var pieOptions = {
        segmentShowStroke: true,
        segmentStrokeColor: '#fff',
        segmentStrokeWidth: 1,
        percentageInnerCutout: 50,
        animationSteps: 100,
        animationEasing: 'easeOutBounce',
        animateRotate: true,
        animateScale: false,
        responsive: true,
        maintainAspectRatio: false,
        legendTemplate: '<ul class=\'<%=name.toLowerCase()%>-legend\'><% for (var i=0; i<segments.length; i++){%><li><span style=\'background-color:<%=segments[i].fillColor%>\'></span><%if(segments[i].label){%><%=segments[i].label%><%}%></li><%}%></ul>',
        tooltipTemplate: '<%=value %> <%=label%>'
    };

    if (PieData.length > 0) {
        try {
            pieChart.Doughnut(PieData, pieOptions);
        } catch (error) {
            console.error("Error al crear el gráfico:", error);
        }
    } else {
        console.warn("No hay datos para mostrar en el gráfico circular.");
    }
    // -----------------
    // - END PIE CHART -
    // -----------------

</script>