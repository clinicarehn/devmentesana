<?php 
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli(); 

$estado = $_POST['estado'];

//CONSULTA LOS DATOS DE LA ENTIDAD CORPORACION
$consulta = "SELECT
	p.productos_id,
	p.nombre AS 'producto',
	(COALESCE(mov.entradas, 0) - COALESCE(mov.salidas, 0)) AS 'cantidad',
	p.concentracion AS 'concentracion',
	me.nombre AS 'medida',
	cp.nombre AS 'categoria',
	a.nombre AS 'almacen',
	p.precio_compra AS 'precio_compra',
	p.precio_venta AS 'precio_venta',
	(CASE WHEN p.isv = '1' THEN 'Sí' ELSE 'No' END) AS 'isv',
	p.descripcion AS 'descripcion'
	FROM
	productos AS p
	INNER JOIN
	medida AS me ON p.medida_id = me.medida_id
	INNER JOIN
	categoria_producto AS cp ON p.categoria_producto_id = cp.categoria_producto_id
	INNER JOIN
	almacen AS a ON p.almacen_id = a.almacen_id
	LEFT JOIN (
	SELECT
		productos_id,
		SUM(CASE WHEN cantidad_entrada IS NOT NULL THEN cantidad_entrada ELSE 0 END) AS entradas,
		SUM(CASE WHEN cantidad_salida IS NOT NULL THEN cantidad_salida ELSE 0 END) AS salidas
	FROM
		movimientos
	GROUP BY
		productos_id
	) AS mov ON p.productos_id = mov.productos_id
	WHERE
	p.estado = $estado;";

$result = $mysqli->query($consulta);	

$arreglo = array();

while($data = $result->fetch_assoc()){				
	$arreglo["data"][] = $data;		
}

echo json_encode($arreglo);

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN