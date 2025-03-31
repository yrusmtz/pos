<?php

class ControladorProductos{

    /*=============================================
    MOSTRAR PRODUCTOS
    =============================================*/

    static public function ctrMostrarProductos($item, $valor, $orden){

        $tabla = "productos";

        $respuesta = ModeloProductos::mdlMostrarProductos($tabla, $item, $valor, $orden);

        // Formatear precios al obtener los productos
        if (is_array($respuesta)) {
            if (isset($respuesta[0])) { // Verifica si es un array de productos
                foreach ($respuesta as &$row) {
                    $row["precio_compra"] = number_format($row["precio_compra"], 0); // Formateado con 0 decimales
                    $row["precio_venta"] = number_format($row["precio_venta"], 0);   // Formateado con 0 decimales
                }
            } else if (is_array($respuesta) || is_object($respuesta)) { // Si solo es un producto (objeto o array)
                $respuesta["precio_compra"] = number_format($respuesta["precio_compra"], 0);
                $respuesta["precio_venta"] = number_format($respuesta["precio_venta"], 0);
            }
        }

        return $respuesta;

    }

    /*=============================================
    CREAR PRODUCTO
    =============================================*/

    static public function ctrCrearProducto(){

        if(isset($_POST["nuevaDescripcion"])){

            if(preg_match('/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ ]+$/', $_POST["nuevaDescripcion"]) &&
                preg_match('/^[0-9]+$/', $_POST["nuevoStock"]) &&
                preg_match('/^[0-9.]+$/', $_POST["nuevoPrecioCompra"]) &&
                preg_match('/^[0-9.]+$/', $_POST["nuevoPrecioVenta"])){

                /*=============================================
            VALIDAR IMAGEN
            =============================================*/

                $ruta = "vistas/img/productos/default/anonymous.png";

                if(isset($_FILES["nuevaImagen"]["tmp_name"]) && $_FILES["nuevaImagen"]["tmp_name"] != ""){  // **AÑADIDO ESTO**

                    list($ancho, $alto) = getimagesize($_FILES["nuevaImagen"]["tmp_name"]);  // LINEA 40

                    $nuevoAncho = 500;
                    $nuevoAlto = 500;

                    /*=============================================
                    CREAMOS EL DIRECTORIO DONDE VAMOS A GUARDAR LA FOTO DEL USUARIO
                    =============================================*/

                    $directorio = "vistas/img/productos/".$_POST["nuevoCodigo"];

                    mkdir($directorio, 0755);

                    /*=============================================
                    DE ACUERDO AL TIPO DE IMAGEN APLICAMOS LAS FUNCIONES POR DEFECTO DE PHP
                    =============================================*/

                    if($_FILES["nuevaImagen"]["type"] == "image/jpeg"){

                        /*=============================================
                        GUARDAMOS LA IMAGEN EN EL DIRECTORIO
                        =============================================*/

                        $aleatorio = mt_rand(100,999);

                        $ruta = "vistas/img/productos/".$_POST["nuevoCodigo"]."/".$aleatorio.".jpg";

                        $origen = imagecreatefromjpeg($_FILES["nuevaImagen"]["tmp_name"]);

                        $destino = imagecreatetruecolor($nuevoAncho, $nuevoAlto);

                        imagecopyresized($destino, $origen, 0, 0, 0, 0, $nuevoAncho, $nuevoAlto, $ancho, $alto);

                        imagejpeg($destino, $ruta);

                    }

                    if($_FILES["nuevaImagen"]["type"] == "image/png"){

                        /*=============================================
                        GUARDAMOS LA IMAGEN EN EL DIRECTORIO
                        =============================================*/

                        $aleatorio = mt_rand(100,999);

                        $ruta = "vistas/img/productos/".$_POST["nuevoCodigo"]."/".$aleatorio.".png";

                        $origen = imagecreatefrompng($_FILES["nuevaImagen"]["tmp_name"]);

                        $destino = imagecreatetruecolor($nuevoAncho, $nuevoAlto);

                        imagecopyresized($destino, $origen, 0, 0, 0, 0, $nuevoAncho, $nuevoAlto, $ancho, $alto);

                        imagepng($destino, $ruta);

                    }

                }  //  **CIERRE DEL IF AÑADIDO**

                $tabla = "productos";
                $descripcion = $_POST["nuevaDescripcion"];

                $item = "descripcion";
                $valor = $descripcion;
                $productoExistente = ModeloProductos::mdlMostrarProductos($tabla, $item, $valor, null);

                if($productoExistente){
                    echo'<script>
                 swal({
                      type: "error",
                      title: "¡Ya existe un producto con ese nombre!",
                      showConfirmButton: true,
                      confirmButtonText: "Cerrar"
                      }).then(function(result){
                       if (result.value) {
                       window.location = "productos";
                       }
                    })
              </script>';
                } else {

                    $datos = array("id_categoria" => $_POST["nuevaCategoria"],
                        "codigo" => $_POST["nuevoCodigo"],
                        "descripcion" => $descripcion,
                        "stock" => $_POST["nuevoStock"],
                        "precio_compra" => $_POST["nuevoPrecioCompra"],
                        "precio_venta" => $_POST["nuevoPrecioVenta"],
                        "imagen" => $ruta);

                    $respuesta = ModeloProductos::mdlIngresarProducto($tabla, $datos);

                    if($respuesta == "ok"){

                        echo'<script>

                    swal({
                         type: "success",
                         title: "El producto ha sido guardado correctamente",
                         showConfirmButton: true,
                         confirmButtonText: "Cerrar"
                         }).then(function(result){
                            if (result.value) {

                            window.location = "productos";

                            }
                         })

                    </script>';

                    }
                }
            }else{

                echo'<script>

             swal({
                  type: "error",
                  title: "¡El producto no puede ir con los campos vacíos o llevar caracteres especiales!",
                  showConfirmButton: true,
                  confirmButtonText: "Cerrar"
                  }).then(function(result){
                   if (result.value) {

                   window.location = "productos";

                   }
                })

          </script>';
            }
        }

    }
    /*=============================================
    EDITAR PRODUCTO
    =============================================*/

    static public function ctrEditarProducto(){

        if(isset($_POST["editarDescripcion"])){

            if(preg_match('/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ ]+$/', $_POST["editarDescripcion"]) &&
                preg_match('/^[0-9]+$/', $_POST["editarStock"]) &&
                preg_match('/^[0-9.]+$/', $_POST["editarPrecioCompra"]) &&
                preg_match('/^[0-9.]+$/', $_POST["editarPrecioVenta"])){

                /*=============================================
            VALIDAR IMAGEN
            =============================================*/

                $ruta = $_POST["imagenActual"];

                if(isset($_FILES["editarImagen"]["tmp_name"]) && !empty($_FILES["editarImagen"]["tmp_name"])){

                    list($ancho, $alto) = getimagesize($_FILES["editarImagen"]["tmp_name"]);

                    $nuevoAncho = 500;
                    $nuevoAlto = 500;

                    /*=============================================
                    CREAMOS EL DIRECTORIO DONDE VAMOS A GUARDAR LA FOTO DEL USUARIO
                    =============================================*/

                    $directorio = "vistas/img/productos/".$_POST["editarCodigo"];

                    /*=============================================
                    PRIMERO PREGUNTAMOS SI EXISTE OTRA IMAGEN EN LA BD
                    =============================================*/

                    if(!empty($_POST["imagenActual"]) && $_POST["imagenActual"] != "vistas/img/productos/default/anonymous.png"){

                        unlink($_POST["imagenActual"]);

                    }else{

                        mkdir($directorio, 0755);

                    }

                    /*=============================================
                    DE ACUERDO AL TIPO DE IMAGEN APLICAMOS LAS FUNCIONES POR DEFECTO DE PHP
                    =============================================*/

                    if($_FILES["editarImagen"]["type"] == "image/jpeg"){

                        /*=============================================
                        GUARDAMOS LA IMAGEN EN EL DIRECTORIO
                        =============================================*/

                        $aleatorio = mt_rand(100,999);

                        $ruta = "vistas/img/productos/".$_POST["editarCodigo"]."/".$aleatorio.".jpg";

                        $origen = imagecreatefromjpeg($_FILES["editarImagen"]["tmp_name"]);

                        $destino = imagecreatetruecolor($nuevoAncho, $nuevoAlto);

                        imagecopyresized($destino, $origen, 0, 0, 0, 0, $nuevoAncho, $nuevoAlto, $ancho, $alto);

                        imagejpeg($destino, $ruta);

                    }

                    if($_FILES["editarImagen"]["type"] == "image/png"){

                        /*=============================================
                        GUARDAMOS LA IMAGEN EN EL DIRECTORIO
                        =============================================*/

                        $aleatorio = mt_rand(100,999);

                        $ruta = "vistas/img/productos/".$_POST["editarCodigo"]."/".$aleatorio.".png";

                        $origen = imagecreatefrompng($_FILES["editarImagen"]["tmp_name"]);

                        $destino = imagecreatetruecolor($nuevoAncho, $nuevoAlto);

                        imagecopyresized($destino, $origen, 0, 0, 0, 0, $nuevoAncho, $nuevoAlto, $ancho, $alto);

                        imagepng($destino, $ruta);

                    }

                }

                $tabla = "productos";

                $datos = array("id_categoria" => $_POST["editarCategoria"],
                    "codigo" => $_POST["editarCodigo"],
                    "descripcion" => $_POST["editarDescripcion"],
                    "stock" => $_POST["editarStock"],
                    "precio_compra" => $_POST["editarPrecioCompra"],
                    "precio_venta" => $_POST["editarPrecioVenta"],
                    "imagen" => $ruta);

                $respuesta = ModeloProductos::mdlEditarProducto($tabla, $datos);

                if($respuesta == "ok"){

                    echo'<script>

                   swal({
                        type: "success",
                        title: "El producto ha sido editado correctamente",
                        showConfirmButton: true,
                        confirmButtonText: "Cerrar"
                        }).then(function(result){
                               if (result.value) {

                               window.location = "productos";

                               }
                            })

                   </script>';

                }


            }else{

                echo'<script>

                swal({
                     type: "error",
                     title: "¡El producto no puede ir con los campos vacíos o llevar caracteres especiales!",
                     showConfirmButton: true,
                     confirmButtonText: "Cerrar"
                     }).then(function(result){
                      if (result.value) {

                      window.location = "productos";

                      }
                   })

             </script>';
            }
        }

    }

    /*=============================================
    BORRAR PRODUCTO
    =============================================*/
    static public function ctrEliminarProducto(){

        if(isset($_GET["idProducto"])){

            $tabla ="productos";
            $datos = $_GET["idProducto"];

            if($_GET["imagen"] != "" && $_GET["imagen"] != "vistas/img/productos/default/anonymous.png"){

                unlink($_GET["imagen"]);
                rmdir('vistas/img/productos/'.$_GET["codigo"]);

            }

            $respuesta = ModeloProductos::mdlEliminarProducto($tabla, $datos);

            if($respuesta == "ok"){

                echo'<script>

             swal({
                  type: "success",
                  title: "El producto ha sido borrado correctamente",
                  showConfirmButton: true,
                  confirmButtonText: "Cerrar"
                  }).then(function(result){
                         if (result.value) {

                         window.location = "productos";

                         }
                      })

             </script>';

            }
        }


    }

    /*=============================================
    MOSTRAR SUMA VENTAS
    =============================================*/

    static public function ctrMostrarSumaVentas(){

        $tabla = "productos";

        $respuesta = ModeloProductos::mdlMostrarSumaVentas($tabla);

        return $respuesta;

    }


}