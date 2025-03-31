<?php

class ControladorClientes{

    /*=============================================
    CREAR CLIENTES
    =============================================*/

    static public function ctrCrearCliente(){

        if(isset($_POST["nuevoCliente"])){

            if(preg_match('/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ ]+$/', $_POST["nuevoCliente"]) &&
                preg_match('/^\d{4}-\d{4}-\d{5}$/', $_POST["nuevoDocumentoId"]) &&
                preg_match('/^[^0-9][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[@][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[.][a-zA-Z]{2,4}$/', $_POST["nuevoEmail"]) &&
                preg_match('/^\d{4}-\d{4}$/', $_POST["nuevoTelefono"]) &&
                preg_match('/^[#\.\-a-zA-Z0-9 ]+$/', $_POST["nuevaDireccion"])){

                $tabla = "clientes";

                // ******** NUEVA VALIDACIÓN DE DUPLICADO ********
                $item = "documento";
                $valor = $_POST["nuevoDocumentoId"];

                $clienteExistente = ModeloClientes::mdlMostrarClientes($tabla, $item, $valor);

                if($clienteExistente){

                    echo '<script>

                       swal({
                            type: "error",
                            title: "¡Ya existe un cliente con este número de Documento!",
                            showConfirmButton: true,
                            confirmButtonText: "Cerrar"
                            }).then(function(result){
                               if (result.value) {

                               window.location = "clientes";

                               }
                            })

                       </script>';

                } else {

                    // ******** FIN NUEVA VALIDACIÓN ********

                    $datos = array(
                        "nombre"=>$_POST["nuevoCliente"],
                        "documento"=>$_POST["nuevoDocumentoId"],
                        "email"=>$_POST["nuevoEmail"],
                        "telefono"=>$_POST["nuevoTelefono"],
                        "direccion"=>$_POST["nuevaDireccion"],
                        "fecha_nacimiento"=>$_POST["nuevaFechaNacimiento"]
                    );

                    $respuesta = ModeloClientes::mdlIngresarCliente($tabla, $datos);

                    if($respuesta == "ok"){

                        echo'<script>

                     swal({
                          type: "success",
                          title: "El cliente ha sido guardado correctamente",
                          showConfirmButton: true,
                          confirmButtonText: "Cerrar"
                          }).then(function(result){
                             if (result.value) {

                             window.location = "clientes";

                             }
                          })

                     </script>';

                    }

                }

            } else {

                // Depuración: Imprime los valores y los errores
                error_log("Validación de datos fallida al crear cliente.");
                error_log("Datos del cliente: " . print_r($_POST, true));

                echo'<script>

                   swal({
                        type: "error",
                        title: "¡El cliente no puede ir vacío o llevar caracteres especiales o los formatos son incorrectos!",
                        showConfirmButton: true,
                        confirmButtonText: "Cerrar"
                        }).then(function(result){
                         if (result.value) {

                         window.location = "clientes";

                         }
                      })

                </script>';
            }

        }

    }

    /*=============================================
    MOSTRAR CLIENTES
    =============================================*/

    static public function ctrMostrarClientes($item, $valor){

        $tabla = "clientes";

        $respuesta = ModeloClientes::mdlMostrarClientes($tabla, $item, $valor);

        return $respuesta;

    }

    /*=============================================
    EDITAR CLIENTE
    =============================================*/

    static public function ctrEditarCliente(){

        if(isset($_POST["editarCliente"])){

            if(preg_match('/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ ]+$/', $_POST["editarCliente"]) &&
                preg_match('/^\d{4}-\d{4}-\d{5}$/', $_POST["editarDocumentoId"]) &&
                preg_match('/^[^0-9][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[@][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[.][a-zA-Z]{2,4}$/', $_POST["editarEmail"]) &&
                preg_match('/^\d{4}-\d{4}$/', $_POST["editarTelefono"]) &&
                preg_match('/^[#\.\-a-zA-Z0-9 ]+$/', $_POST["editarDireccion"])){

                $tabla = "clientes";

                $datos = array(
                    "id"=>$_POST["idCliente"],
                    "nombre"=>$_POST["editarCliente"],
                    "documento"=>$_POST["editarDocumentoId"],
                    "email"=>$_POST["editarEmail"],
                    "telefono"=>$_POST["editarTelefono"],
                    "direccion"=>$_POST["editarDireccion"],
                    "fecha_nacimiento"=>$_POST["editarFechaNacimiento"]
                );

                $respuesta = ModeloClientes::mdlEditarCliente($tabla, $datos);

                if($respuesta == "ok"){

                    echo'<script>

                 swal({
                      type: "success",
                      title: "El cliente ha sido cambiado correctamente",
                      showConfirmButton: true,
                      confirmButtonText: "Cerrar"
                      }).then(function(result){
                             if (result.value) {

                             window.location = "clientes";

                             }
                          })

                 </script>';

                }

            }else{

                echo'<script>

                 swal({
                      type: "error",
                      title: "¡El cliente no puede ir vacío o llevar caracteres especiales o los formatos son incorrectos!",
                      showConfirmButton: true,
                      confirmButtonText: "Cerrar"
                      }).then(function(result){
                       if (result.value) {

                       window.location = "clientes";

                       }
                    })

              </script>';
            }

        }

    }

    /*=============================================
    ELIMINAR CLIENTE
    =============================================*/

    static public function ctrEliminarCliente(){

        if(isset($_GET["idCliente"])){

            $tabla ="clientes";
            $datos = $_GET["idCliente"];

            $respuesta = ModeloClientes::mdlEliminarCliente($tabla, $datos);

            if($respuesta == "ok"){

                echo'<script>

             swal({
                  type: "success",
                  title: "El cliente ha sido borrado correctamente",
                  showConfirmButton: true,
                  confirmButtonText: "Cerrar",
                  closeOnConfirm: false
                  }).then(function(result){
                         if (result.value) {

                         window.location = "clientes";

                         }
                      })

             </script>';

            }

        }

    }

}