<?php

require 'conexion.php';

class Metodo extends Conexion
{

    protected $controlador;

    function __construct()
    {
        /* Llama al constructor de la clase padre */
        parent::__construct();
    }


    /* Sube los archivos en la ruta que recibe */
    function subidaMasivaArchivos($ruta)
    {

        foreach ($_FILES['archivo']['tmp_name'] as $nombre_archivo => $tmp_name) {
            /* Comprueba que haya un archivo */
            if ($_FILES['archivo']['name'][$nombre_archivo]) {

                $nombre = $_FILES['archivo']['name'][$nombre_archivo];
                $origen = $_FILES['archivo']['tmp_name'][$nombre_archivo];
                $tipo_img = $_FILES['archivo']['type'][$nombre_archivo];
                $tamanio = $_FILES['archivo']['size'][$nombre_archivo];

                /* recoge la ruta */
                $carpeta = $ruta;

                /* Comprueba que el tamaño no sea superior a 5Mb */
                if ($tamanio < 5000000) {
                    /* Comprueba que el formato sea uno de los requeridos */
                    if ($tipo_img == 'image/jpg' || $tipo_img == 'image/png' || $tipo_img == 'image/jpeg') {
                        $dir = opendir($carpeta);
                        $destino = $carpeta . '/' . $nombre;

                        /* De esta manera muestra un mensaje si se trasladan imágenes */
                        if (move_uploaded_file($origen, $destino)) {
                            $booleano = true;
                        }
                        if ($booleano) {
                            echo '<h3>Imagenes subidas</h3>';
                        }

                        closedir($dir);
                        
                    } else {
                        echo '<h3>El archivo debe ser jpg, jpeg o png</h3>';
                    }
                } else {
                    echo '<h3>la imagen no debe exceder los 5Mb</h3>';
                }
            }
        }
    }
}
