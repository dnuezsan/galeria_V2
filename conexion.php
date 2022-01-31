<?php

require 'config.php';
require 'fpdf/fpdf.php';

class Conexion extends FPDF{

    protected $conexion_bd;
    protected $pdf;

    function __construct(){
         $this->conexion_bd = new mysqli(SERVIDOR, USUARIO, CONTRASENIA, BD);
         if ($this->conexion_bd->connect_errno) {
         echo 'Se produjo un error en la conexión';
        }
        $this->pdf = new FPDF();
    }


    /* Crea el registro del cliente y finalmente genera su carpeta */
    function insertarCliente(){

        /* Inserción. Primero Se genera un registro sin */
        $sql_insercion = 'INSERT INTO pedido VALUES (null, "'.$_POST['cliente'].'", null, null, now())';

        if (!$insercion = $this->conexion_bd->query($sql_insercion)) {
            echo 'Se ha producido un error y no se pudo registrar su usuario';
        }

        $insercion->close();

        /* Selección. Esto se hace para extraer el id del cliente y usarlo para renombrar su carpeta o álbum personal */
        $sql_consuta = 'SELECT * FROM pedido WHERE cliente = '.$_POST['cliente'];

        $consulta = $this->conexion_bd->query($sql_consuta);

        if (!$fila = $consulta->fetch_array(MYSQLI_ASSOC)) {
            echo 'No se pudo finalizar su registro';
            
        }

        $id = $fila['id'];

        /* Actualización */

        $sql_actualizacion = 'UPDATE pedido SET ruta = img/'.$_POST['cliente'] + $id.' WHERE id ='.$id;

        if (!$actualizacion = $this->conexion_bd->query($sql_actualizacion)) {
           echo 'No se ha podido actualizar su registro';
        }

        /* Cierre de consultas */
        $consulta->close();
        $actualizacion->close();

        /* Llamada a la creación de la carpeta del usuario*/
        if (!$this->conexion_bd->Conexion::crearCarpeta($id)) {
            echo 'No se pudo crear su álbum';
        }

    }


    function mostrar_carpeta(){
        $sql = 'SELECT ruta FROM pedido WHERE cliente = '.$_POST['cliente'];
        
        $consulta = $this->conexion_bd->query($sql);

        while ($fila = $consulta->fetch_array(MYSQLI_ASSOC)) {
            echo '<a href="ver_imagenes.php?">'.$fila['ruta'].'</a>    ';
            echo $fila['num_imagenes'].'    ';
            echo $fila['fecha'].'<br />';
        }
    }

    function crearCarpeta($id){

        $sql = 'SELECT * FROM pedido WHERE id = '.$id;

        $consulta = $this->conexion_bd->query($sql);

        if (!$fila = $consulta->fetch_array(MYSQLI_ASSOC) ) {
            echo ' No se ha podido crear una carpeta';
        }

        $rutaCarpeta = $fila['ruta'];
        
        mkdir($rutaCarpeta);

    }

}
?>