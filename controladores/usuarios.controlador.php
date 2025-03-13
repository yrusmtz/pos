<?php

class ControladorUsuarios{
	/*=============================================
	INGRESO DE USUARIO
	=============================================*/
    static public function ctrIngresoUsuario() {

        if (isset($_POST["ingUsuario"])) {

            if (preg_match('/^[a-zA-Z0-9]+$/', $_POST["ingUsuario"]) &&
                preg_match('/^[a-zA-Z0-9]+$/', $_POST["ingPassword"])) {

                // Encriptar la contraseña ingresada por el usuario utilizando la semilla personalizada
                $encriptar = crypt($_POST["ingPassword"], '$2a$07$asxx54ahjppf45sd87a5auXBm1Vr2M1NV5t/zNQtGHGpS5fFirrbG');

                $tabla = "usuarios";

                $item = "usuario";
                $valor = $_POST["ingUsuario"];

                // Obtener el registro correspondiente al usuario ingresado
                $respuesta = ModeloUsuarios::MdlMostrarUsuarios($tabla, $item, $valor);

                // Validar que el usuario y la contraseña (encriptada) coincidan
                if ($respuesta["usuario"] == $_POST["ingUsuario"] && $respuesta["password"] == $encriptar) {

                    // Verificar que el usuario está activo
                    if ($respuesta["estado"] == 1) {

                        // Iniciar sesión guardando las variables en $_SESSION
                        $_SESSION["iniciarSesion"] = "ok";
                        $_SESSION["id"] = $respuesta["id"];
                        $_SESSION["nombre"] = $respuesta["nombre"];
                        $_SESSION["usuario"] = $respuesta["usuario"];
                        $_SESSION["foto"] = $respuesta["foto"];
                        $_SESSION["perfil"] = $respuesta["perfil"];

                        /*==============================================================
                        REGISTRAR FECHA PARA SABER EL ÚLTIMO LOGIN DEL USUARIO
                        ==============================================================*/

                        date_default_timezone_set('America/Bogota');

                        $fecha = date('Y-m-d');
                        $hora = date('H:i:s');

                        $fechaActual = $fecha . ' ' . $hora;

                        $item1 = "ultimo_login";
                        $valor1 = $fechaActual;

                        $item2 = "id";
                        $valor2 = $respuesta["id"];

                        $ultimoLogin = ModeloUsuarios::mdlActualizarUsuario($tabla, $item1, $valor1, $item2, $valor2);

                        if ($ultimoLogin == "ok") {
                            echo '<script>
                            window.location = "inicio";
                        </script>';
                        }

                    } else {
                        echo '<br>
                        <div class="alert alert-danger">El usuario aún no está activado</div>';
                    }

                } else {
                    echo '<br><div class="alert alert-danger">Error al ingresar, vuelve a intentarlo</div>';
                }

            }

        }

    }

	/*=============================================
	REGISTRO DE USUARIO
	=============================================*/
    static public function ctrCrearUsuario() {

        if (isset($_POST["nuevoUsuario"])) {

            if (preg_match('/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ ]+$/', $_POST["nuevoNombre"]) &&
                preg_match('/^[a-zA-Z0-9]+$/', $_POST["nuevoUsuario"]) &&
                preg_match('/^[a-zA-Z0-9]+$/', $_POST["nuevoPassword"])) {

                /*=============================================
                VALIDAR IMAGEN
                =============================================*/

                // Definir ruta predeterminada
                $ruta = "vistas/img/usuarios/default/anonymous.png";

                // Verificar si se ha subido una imagen nueva
                if (isset($_FILES["nuevaFoto"]) && !empty($_FILES["nuevaFoto"]["tmp_name"])) {

                    // Validar si es una imagen procesable
                    $info = getimagesize($_FILES["nuevaFoto"]["tmp_name"]);
                    if ($info !== false) {

                        list($ancho, $alto) = $info;
                        $nuevoAncho = 500;
                        $nuevoAlto = 500;

                        /* Crear directorio */
                        $directorio = "vistas/img/usuarios/" . $_POST["nuevoUsuario"];
                        if (!file_exists($directorio)) {
                            mkdir($directorio, 0755);
                        }

                        $aleatorio = mt_rand(100, 999);

                        if ($_FILES["nuevaFoto"]["type"] == "image/jpeg") {
                            $ruta = $directorio . "/" . $aleatorio . ".jpg";
                            $origen = imagecreatefromjpeg($_FILES["nuevaFoto"]["tmp_name"]);
                            $destino = imagecreatetruecolor($nuevoAncho, $nuevoAlto);
                            imagecopyresampled($destino, $origen, 0, 0, 0, 0, $nuevoAncho, $nuevoAlto, $ancho, $alto);
                            imagejpeg($destino, $ruta);
                        } elseif ($_FILES["nuevaFoto"]["type"] == "image/png") {
                            $ruta = $directorio . "/" . $aleatorio . ".png";
                            $origen = imagecreatefrompng($_FILES["nuevaFoto"]["tmp_name"]);
                            $destino = imagecreatetruecolor($nuevoAncho, $nuevoAlto);
                            imagecopyresampled($destino, $origen, 0, 0, 0, 0, $nuevoAncho, $nuevoAlto, $ancho, $alto);
                            imagepng($destino, $ruta);
                        } else {
                            echo '<script>
                                swal({
                                    type: "error",
                                    title: "¡Solo se permiten imágenes en formato JPEG o PNG!",
                                    showConfirmButton: true,
                                    confirmButtonText: "Cerrar"
                                }).then(function(result){
                                    if (result.value) {
                                        window.location = "usuarios";
                                    }
                                });
                              </script>';
                            return;
                        }
                    } else {
                        echo '<script>
                            swal({
                                type: "error",
                                title: "¡No se pudo procesar la imagen seleccionada!",
                                showConfirmButton: true,
                                confirmButtonText: "Cerrar"
                            }).then(function(result){
                                if (result.value) {
                                    window.location = "usuarios";
                                }
                            });
                          </script>';
                        return;
                    }
                }

                /*=============================================
                REGISTRAR USUARIO EN LA BASE DE DATOS
                =============================================*/

                $tabla = "usuarios";

                $encriptar = crypt($_POST["nuevoPassword"], '$2a$07$asxx54ahjppf45sd87a5a4dDDGsystemdev$');

                $datos = array(
                    "nombre" => $_POST["nuevoNombre"],
                    "usuario" => $_POST["nuevoUsuario"],
                    "password" => $encriptar,
                    "perfil" => $_POST["nuevoPerfil"],
                    "foto" => $ruta
                );

                $respuesta = ModeloUsuarios::mdlIngresarUsuario($tabla, $datos);

                if ($respuesta == "ok") {
                    echo '<script>
                        swal({
                          type: "success",
                          title: "¡El usuario ha sido guardado correctamente!",
                          showConfirmButton: true,
                          confirmButtonText: "Cerrar"
                        }).then(function(result){
                            if (result.value) {
                                window.location = "usuarios";
                            }
                        });
                      </script>';
                }
            } else {
                echo '<script>
                    swal({
                      type: "error",
                      title: "¡El usuario no puede contener caracteres especiales!",
                      showConfirmButton: true,
                      confirmButtonText: "Cerrar"
                    }).then(function(result){
                        if (result.value) {
                            window.location = "usuarios";
                        }
                    });
                  </script>';
            }
        }
    }

	/*=============================================
	MOSTRAR USUARIO
	=============================================*/

	static public function ctrMostrarUsuarios($item, $valor){

		$tabla = "usuarios";

		$respuesta = ModeloUsuarios::MdlMostrarUsuarios($tabla, $item, $valor);

		return $respuesta;
	}

	/*=============================================
	EDITAR USUARIO
	=============================================*/

	static public function ctrEditarUsuario(){

		if(isset($_POST["editarUsuario"])){

			if(preg_match('/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ ]+$/', $_POST["editarNombre"])){

				/*=============================================
				VALIDAR IMAGEN
				=============================================*/

				$ruta = $_POST["fotoActual"];

				if(isset($_FILES["editarFoto"]["tmp_name"]) && !empty($_FILES["editarFoto"]["tmp_name"])){

					list($ancho, $alto) = getimagesize($_FILES["editarFoto"]["tmp_name"]);

					$nuevoAncho = 500;
					$nuevoAlto = 500;

					/*=============================================
					CREAMOS EL DIRECTORIO DONDE VAMOS A GUARDAR LA FOTO DEL USUARIO
					=============================================*/

					$directorio = "vistas/img/usuarios/".$_POST["editarUsuario"];

					/*=============================================
					PRIMERO PREGUNTAMOS SI EXISTE OTRA IMAGEN EN LA BD
					=============================================*/

					if(!empty($_POST["fotoActual"])){

						unlink($_POST["fotoActual"]);

					}else{

						mkdir($directorio, 0755);

					}	

					/*=============================================
					DE ACUERDO AL TIPO DE IMAGEN APLICAMOS LAS FUNCIONES POR DEFECTO DE PHP
					=============================================*/

					if($_FILES["editarFoto"]["type"] == "image/jpeg"){

						/*=============================================
						GUARDAMOS LA IMAGEN EN EL DIRECTORIO
						=============================================*/

						$aleatorio = mt_rand(100,999);

						$ruta = "vistas/img/usuarios/".$_POST["editarUsuario"]."/".$aleatorio.".jpg";

						$origen = imagecreatefromjpeg($_FILES["editarFoto"]["tmp_name"]);						

						$destino = imagecreatetruecolor($nuevoAncho, $nuevoAlto);

						imagecopyresized($destino, $origen, 0, 0, 0, 0, $nuevoAncho, $nuevoAlto, $ancho, $alto);

						imagejpeg($destino, $ruta);

					}

					if($_FILES["editarFoto"]["type"] == "image/png"){

						/*=============================================
						GUARDAMOS LA IMAGEN EN EL DIRECTORIO
						=============================================*/

						$aleatorio = mt_rand(100,999);

						$ruta = "vistas/img/usuarios/".$_POST["editarUsuario"]."/".$aleatorio.".png";

						$origen = imagecreatefrompng($_FILES["editarFoto"]["tmp_name"]);						

						$destino = imagecreatetruecolor($nuevoAncho, $nuevoAlto);

						imagecopyresized($destino, $origen, 0, 0, 0, 0, $nuevoAncho, $nuevoAlto, $ancho, $alto);

						imagepng($destino, $ruta);

					}

				}

				$tabla = "usuarios";

				if($_POST["editarPassword"] != ""){

					if(preg_match('/^[a-zA-Z0-9]+$/', $_POST["editarPassword"])){

						$encriptar = crypt($_POST["editarPassword"], '$2a$07$asxx54ahjppf45sd87a5a4dDDGsystemdev$');

					}else{

						echo'<script>

								swal({
									  type: "error",
									  title: "¡La contraseña no puede ir vacía o llevar caracteres especiales!",
									  showConfirmButton: true,
									  confirmButtonText: "Cerrar"
									  }).then(function(result){
										if (result.value) {

										window.location = "usuarios";

										}
									})

						  	</script>';

					}

				}else{

					$encriptar = $_POST["passwordActual"];

				}

				$datos = array("nombre" => $_POST["editarNombre"],
							   "usuario" => $_POST["editarUsuario"],
							   "password" => $encriptar,
							   "perfil" => $_POST["editarPerfil"],
							   "foto" => $ruta);

				$respuesta = ModeloUsuarios::mdlEditarUsuario($tabla, $datos);

				if($respuesta == "ok"){

					echo'<script>

					swal({
						  type: "success",
						  title: "El usuario ha sido editado correctamente",
						  showConfirmButton: true,
						  confirmButtonText: "Cerrar"
						  }).then(function(result){
									if (result.value) {

									window.location = "usuarios";

									}
								})

					</script>';

				}


			}else{

				echo'<script>

					swal({
						  type: "error",
						  title: "¡El nombre no puede ir vacío o llevar caracteres especiales!",
						  showConfirmButton: true,
						  confirmButtonText: "Cerrar"
						  }).then(function(result){
							if (result.value) {

							window.location = "usuarios";

							}
						})

			  	</script>';

			}

		}

	}

	/*=============================================
	BORRAR USUARIO
	=============================================*/

	static public function ctrBorrarUsuario(){

		if(isset($_GET["idUsuario"])){

			$tabla ="usuarios";
			$datos = $_GET["idUsuario"];

			if($_GET["fotoUsuario"] != ""){

				unlink($_GET["fotoUsuario"]);
				rmdir('vistas/img/usuarios/'.$_GET["usuario"]);

			}

			$respuesta = ModeloUsuarios::mdlBorrarUsuario($tabla, $datos);

			if($respuesta == "ok"){

				echo'<script>

				swal({
					  type: "success",
					  title: "El usuario ha sido borrado correctamente",
					  showConfirmButton: true,
					  confirmButtonText: "Cerrar"
					  }).then(function(result){
								if (result.value) {

								window.location = "usuarios";

								}
							})

				</script>';

			}		

		}

	}


}
	


