<?php

require_once "../controladores/productos.controlador.php";
require_once "../modelos/productos.modelo.php";

require_once "../controladores/categorias.controlador.php";
require_once "../modelos/categorias.modelo.php";

class AjaxProductos{

    /*=============================================
      GENERAR CÓDIGO A PARTIR DE ID CATEGORIA
      =============================================*/
    public $idCategoria;

    public function ajaxCrearCodigoProducto(){

        $item = "id_categoria";
        $valor = $this->idCategoria;
        $orden = "id DESC";

        $respuesta = ControladorProductos::ctrMostrarProductos($item, $valor, $orden);

        // *********** OBTENER EL NOMBRE DE LA CATEGORÍA **********
        $tablaCategorias = "categorias";
        $itemCategoria = "id";
        $valorCategoria = $this->idCategoria;
        $ordenCategoria = "id";

        $categoria = ModeloCategorias::mdlMostrarCategorias($tablaCategorias, $itemCategoria, $valorCategoria);

        if (!$categoria) {
            $prefijoCategoria = "MISC";  // Default si no se encuentra
        } else {
            $prefijoCategoria = strtoupper(substr($categoria["categoria"], 0, 4)); // Prefijo de 4 letras
        }
        // *********** FIN OBTENER NOMBRE CATEGORIA **********

        if (!$respuesta || !isset($respuesta["codigo"])) {
            echo json_encode(array("codigo" => $prefijoCategoria . "-001"));
        } else {
            $ultimoCodigo = $respuesta["codigo"];
            if (preg_match('/^[A-Z0-9]+-\d{3}$/', $ultimoCodigo)) {
                $numero = intval(substr($ultimoCodigo, -3)) + 1;
                $nuevoCodigo = substr($ultimoCodigo, 0, strlen($ultimoCodigo) - 3) . str_pad($numero, 3, "0", STR_PAD_LEFT);
                echo json_encode(array("codigo" => $nuevoCodigo));
            } elseif (preg_match('/^\d{3}$/', $ultimoCodigo)) {
                $numero = intval($ultimoCodigo) + 1;
                $nuevoCodigo = $prefijoCategoria . "-" . str_pad($numero, 3, "0", STR_PAD_LEFT);
                echo json_encode(array("codigo" => $nuevoCodigo));
            } else {
                echo json_encode(array("codigo" => $prefijoCategoria . "-001"));
            }
        }
    }


    /*=============================================
      EDITAR PRODUCTO
      =============================================*/

    public $idProducto;
    public $traerProductos;
    public $nombreProducto;

    public function ajaxEditarProducto(){

        if ($this->traerProductos == "ok") {

            $item = null;
            $valor = null;
            $orden = "id";

            $respuesta = ControladorProductos::ctrMostrarProductos($item, $valor, $orden);

            echo json_encode($respuesta);


        } else if ($this->nombreProducto != "") {

            $item = "descripcion";
            $valor = $this->nombreProducto;
            $orden = "id";

            $respuesta = ControladorProductos::ctrMostrarProductos($item, $valor, $orden);

            echo json_encode($respuesta);

        } else {

            $item = "id";
            $valor = $this->idProducto;
            $orden = "id";

            $respuesta = ControladorProductos::ctrMostrarProductos($item, $valor, $orden);

            echo json_encode($respuesta);

        }

    }

}


/*=============================================
  GENERAR CÓDIGO A PARTIR DE ID CATEGORIA
  =============================================*/

if (isset($_POST["idCategoria"])) {

    $codigoProducto = new AjaxProductos();
    $codigoProducto->idCategoria = $_POST["idCategoria"];
    $codigoProducto->ajaxCrearCodigoProducto();

}
/*=============================================
  EDITAR PRODUCTO
  =============================================*/

if (isset($_POST["idProducto"])) {

    $editarProducto = new AjaxProductos();
    $editarProducto->idProducto = $_POST["idProducto"];
    $editarProducto->ajaxEditarProducto();

}

/*=============================================
  TRAER PRODUCTO
  =============================================*/

if (isset($_POST["traerProductos"])) {

    $traerProductos = new AjaxProductos();
    $traerProductos->traerProductos = $_POST["traerProductos"];
    $traerProductos->ajaxEditarProducto();

}

/*=============================================
  TRAER PRODUCTO
  =============================================*/

if (isset($_POST["nombreProducto"])) {

    $traerProductos = new AjaxProductos();
    $traerProductos->nombreProducto = $_POST["nombreProducto"];
    $traerProductos->ajaxEditarProducto();

}