<?php

$item = null;
$valor = null;
$orden = "id";

$productos = ControladorProductos::ctrMostrarProductos($item, $valor, $orden);

?>

<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Productos Agregados Recientemente</h3>
        <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse">
                <i class="fa fa-minus"></i>
            </button>
            <button type="button" class="btn btn-box-tool" data-widget="remove">
                <i class="fa fa-times"></i>
            </button>
        </div>
    </div>
    <div class="box-body">
        <ul class="products-list product-list-in-box">
            <?php
            if (!empty($productos)) { // Verifica si $productos tiene datos
                $count = min(10, count($productos)); // Limita a 10 productos o el total si es menor
                for ($i = 0; $i < $count; $i++) {
                    echo '<li class="item">
                  <div class="product-img">
                    <img src="' . $productos[$i]["imagen"] . '" alt="Product Image">
                  </div>
                  <div class="product-info">
                    <a href="" class="product-title">
                      ' . $productos[$i]["descripcion"] . '
                      <span class="label label-warning pull-right">L.' . $productos[$i]["precio_venta"] . '</span>
                    </a>
                  </div>
                </li>';
                }
            } else {
                echo '<li class="item">No hay productos agregados recientemente.</li>'; // Mensaje si no hay productos
            }
            ?>
        </ul>
    </div>
    <div class="box-footer text-center">
        <a href="productos" class="uppercase">Ver todos los productos</a>
    </div>
</div>