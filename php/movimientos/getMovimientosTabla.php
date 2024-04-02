<?php
session_start();
include "../funtions.php";

//CONEXION A DB
$mysqli = connect_mysqli();

$categoria = $_POST['categoria'];
$productos_id = $_POST['productos_id'];
$fechai = $_POST['fechai'];
$fechaf = $_POST['fechaf'];

//CONSULTA LOS DATOS DE LA ENTIDAD CORPORACION
$consulta = "SELECT p.nombre AS 'producto', p.concentracion AS 'concentracion', me.nombre AS 'medida', m.cantidad_entrada AS 'entrada', m.cantidad_salida AS 'salida', m.saldo AS 'saldo', m.fecha_registro AS 'fecha_registro', m.comentario AS 'comentario', CONCAT(sf.prefijo,'',LPAD(f.number, sf.relleno, 0)) AS 'numero'
	FROM movimientos AS m
	INNER JOIN productos AS p
	ON m.productos_id = p.productos_id
	INNER JOIN medida AS me
	ON p.medida_id = me.medida_id
	LEFT JOIN facturas_detalle AS fd
	ON fd.productos_id = m.productos_id
	LEFT JOIN facturas AS f 
	ON f.facturas_id = fd.facturas_id
	LEFT JOIN secuencia_facturacion AS sf
	ON f.secuencia_facturacion_id = sf.secuencia_facturacion_id
	WHERE p.categoria_producto_id = '$categoria'";

// Agregar la condición del producto_id si está definido
if (!empty($productos_id)) {
    $consulta .= " AND m.productos_id = '$productos_id'";
} else {
    // Agregar la condición del rango de fechas si el productos_id no está definido
    $consulta .= " AND CAST(m.fecha_registro AS DATE) BETWEEN '$fechai' AND '$fechaf'";
}

// Ordenar los resultados de manera descendente según la fecha de registro
$consulta .= " ORDER BY m.fecha_registro DESC";

$result = $mysqli->query($consulta);

$arreglo = array();

while($data = $result->fetch_assoc()){
	$arreglo["data"][] = $data;
}

echo json_encode($arreglo);

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN