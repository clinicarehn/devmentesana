<?php
session_start();
include "../funtions.php";

//CONEXION A DB
$mysqli = connect_mysqli();

//OBTENEMOS EL DESCUENTO A APLICAR SEGUN LO ESTABLECIDO POR EL PROFESIONAL
$query = "SELECT p.pacientes_id, CONCAT(p.nombre,' ',p.apellido) AS 'cliente'
	FROM facturas AS f
	INNER JOIN pacientes AS p
	ON f.pacientes_id = p.pacientes_id
	WHERE p.estado = 1
	GROUP BY p.pacientes_id";
$result = $mysqli->query($query) or die($mysqli->error);

if($result->num_rows>0){
	while($consulta2 = $result->fetch_assoc()){
		echo '<option value="'.$consulta2['pacientes_id'].'">'.$consulta2['cliente'].'</option>';
	}
}else{
		echo '<option value="">Seleccione</option>';
}

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN
?>
