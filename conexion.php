<?php

require 'config.php';
require 'fpdf/fpdf.php';

class Conexion extends FPDF
{

    protected $conexion_bd;
    protected $pdf;

    function __construct()
    {
        $this->conexion_bd = new mysqli(SERVIDOR, USUARIO, CONTRASENIA, BD);
        if ($this->conexion_bd->connect_errno) {
            echo 'Se produjo un error en la conexión';
        }
        $this->pdf = new FPDF();
    }



    /* Busca un cliente, y si no existe lo crea y redirige. Si existe, redirige directamente */
    function encontrarcliente()
    {
        $usuario = $_POST['cliente'];

        $sql_consulta = 'SELECT * FROM pedido WHERE cliente = ?';

        if (!$consulta = $this->conexion_bd->prepare($sql_consulta)) {
            echo 'No se pudo elaborar la consulta';
        } else {

            $consulta->bind_param('s', $usuario);
            $consulta->execute();
            $resultado = $consulta->get_result();

            if ($fila = $resultado->fetch_array(MYSQLI_ASSOC)) {
                header("location: subir_img.php? ruta=".$fila['ruta']);
                return $fila['id'];
            } else {
                $this->insertarCliente();
            }
        }
    }


    /* Crea el registro del cliente y finalmente genera su carpeta */
    function insertarCliente()
    {
        $cliente = $_POST['cliente'];

        /* Inserción. Primero Se genera un registro sin ruta */
        $sql_insercion = 'INSERT INTO pedido VALUES (null, "' . $_POST['cliente'] . '", null, now())';

        if (!$insercion = $this->conexion_bd->query($sql_insercion)) {
            echo 'Se ha producido un error y no se pudo registrar su usuario';
        } else {
            $this->actualizarRegistro($this->encontrarcliente());
        }
    }

    /* genera la ruta de la carpeta */
    function actualizarRegistro($id)
    {

        $cliente = $_POST['cliente'];

        $sql_actualizacion = "UPDATE pedido SET ruta = 'img/$cliente$id' WHERE id = '$id'";

        if (!$actualizacion = $this->conexion_bd->query($sql_actualizacion)) {
            echo 'No se ha podido actualizar su registro';
        }

        /* Llamada a la creación de la carpeta del usuario*/
        $this->crearCarpeta();
        /* Conduce a la siguiente página */
        header("location: subir_img.php");

    }

    /* Crea una carpeta */
    function crearCarpeta()
    {
        $cliente = $_POST['cliente'];

        $sql = "SELECT * FROM pedido WHERE cliente = '$cliente'";

        $consulta = $this->conexion_bd->query($sql);
        
        /* Si no existiera la fila, no se crea la carpeta */
        if (!$fila = $consulta->fetch_array(MYSQLI_ASSOC)) {
            echo ' No se ha podido crear una carpeta';
        } else {
            $rutaCarpeta = $fila['ruta'];

            if (file_exists($rutaCarpeta)) {
                echo '';
            } 
            /* Crea una carpeta si no existe */
            else {
                mkdir($rutaCarpeta, 0777, true);
            }
        }
    }
}
