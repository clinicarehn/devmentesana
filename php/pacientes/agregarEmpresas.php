<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli();

$expediente = 0;
$nombre = cleanStringStrtolower($_POST['empresa']);
$identidad = $_POST['rtn'];
$telefono1 = $_POST['telefono1'];
$telefono2 = $_POST['telefono2'];
$pais_id = $_POST['pais_id'] ?? 0;
$departamento_id = $_POST['departamento_id'] ?? 0;
$municipio_id = $_POST['municipio_id'] ?? 0;
$localidad = cleanStringStrtolower($_POST['direccion']);
$correo = strtolower(cleanString($_POST['correo']));
$referido_id = $_POST['referido_id'] ?? 0;
$responsable_id = $_POST['responsable_id'] ?? 0;
$profesional = cleanStringStrtolower($_POST['profesional'] ?? 0);
$fecha = date("Y-m-d");
$usuario = $_SESSION['colaborador_id'];
$estado = 1; //1. Activo 2. Inactivo
$fecha_registro = date("Y-m-d H:i:s");
$apellido = "";
$genero = "";

//CONSULTAR IDENTIDAD DEL USUARIO
if($identidad == 0){
	$flag_identidad = true;
	while($flag_identidad){
	   $d=rand(1,99999999);
	   $query_identidadRand = "SELECT pacientes_id 
	       FROM pacientes 
		   WHERE identidad = '$d'";
	   $result_identidad = $mysqli->query($query_identidadRand);
	   if($result_identidad->num_rows==0){
		  $identidad = $d;
		  $flag_identidad = false;
	   }else{
		  $flag_identidad = true;
	   }		
	}
}

//EVALUAR SI EXISTE EL PACIENTE
$select = "SELECT pacientes_id
	FROM pacientes
	WHERE identidad = '$identidad' AND nombre = '$nombre'";
$result = $mysqli->query($select) or die($mysqli->error);

if($result->num_rows==0){
	$pacientes_id = correlativo('pacientes_id ', 'pacientes');
	$expediente = correlativo('expediente ', 'pacientes');

	$insert = "INSERT INTO pacientes (
		pacientes_id,
		expediente,
		nombre, 
		apellido,
		genero,
		fecha_nacimiento,
		identidad,
		telefono1, 
		telefono2, 
		pais_id, 
		departamento_id, 
		municipio_id, 
		localidad, 
		email, 
		referido_id, 
		responsable_id, 
		profesional, 
		fecha, 
		usuario, 
		estado, 
		fecha_registro,
		religion_id,
		profesion_id,
		estado_civil,
		responsable
	)
	VALUES (
		$pacientes_id,
		$expediente, 
		'$nombre',
		'$apellido', 
		'$genero',
		'$fecha',
		'$identidad', 
		'$telefono1', 
		'$telefono2', 
		$pais_id, 
		$departamento_id, 
		$municipio_id, 
		'$localidad', 
		'$correo', 
		$referido_id, 
		$responsable_id, 
		'$profesional', 
		'$fecha', 
		$usuario, 
		$estado, 
		'$fecha_registro',
		'0', -- campo religion_id asignado como vacío
		'0', -- campo profesion_id asignado como vacío
		'0', -- campo estado_civil asignado como vacío
		''  -- campo responsable asignado como vacío
	)";

	$query = $mysqli->query($insert);

	if($query){
		$datos = array(
			0 => "Almacenado", 
			1 => "Registro Almacenado Correctamente", 
			2 => "success",
			3 => "btn-primary",
			4 => "formulario_pacientes",
			5 => "Registro",
			6 => "formPacientes",
			7 => "modal_pacientes",
		);
	}else{
		$datos = array(
			0 => "Error", 
			1 => "No se puedo almacenar este registro, los datos son incorrectos por favor corregir", 
			2 => "error",
			3 => "btn-danger",
			4 => "",
			5 => "",			
		);
	}
}else{
	$datos = array(
		0 => "Error", 
		1 => "Lo sentimos este registro ya existe no se puede almacenar", 
		2 => "error",
		3 => "btn-danger",
		4 => "",
		5 => "",		
	);
}

echo json_encode($datos);