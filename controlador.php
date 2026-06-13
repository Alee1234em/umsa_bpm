<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include_once "db_helper.php";
$currentUser = $_SESSION["usuario"] ?? 'Juan';

$codFlujo = $_GET["codFlujo"] ?? '';
$codProceso = $_GET["codProceso"] ?? '';
$codProcesoSiguiente = $_GET["codProcesoSiguiente"] ?? '';
$archivo = $_GET["archivo"] ?? '';
$nroTramite = $_GET["nroTramite"] ?? '';

// 1. Ejecutar el controlador específico de la pantalla si existe
$controladorEspecifico = "controlador" . $archivo;
if (!empty($archivo) && file_exists(__DIR__ . '/' . $controladorEspecifico)) {
    include __DIR__ . '/' . $controladorEspecifico;
}

$codProcesoEnviar = $codProceso;

// 2. Si presionó ANTERIOR (Retroceder de pantalla)
if (isset($_GET["Anterior"])) {
    // Buscamos cuál era el paso previo (el que apunta a este como siguiente)
    $procesos = get_procesos_flujo($codFlujo);
    $prevProceso = null;
    foreach ($procesos as $p) {
        if ($p['codProcesoSiguiente'] === $codProceso) {
            $prevProceso = $p;
            break;
        }
    }
    
    if ($prevProceso) {
        $codProcesoEnviar = $prevProceso['codProceso'];
        
        if (!empty($nroTramite)) {
            // Eliminar la entrada del seguimiento para el paso actual (que se descarta al volver atrás)
            delete_seguimiento($nroTramite, $codFlujo, $codProceso);
            
            // Reabrir el paso anterior en seguimiento poniendo fechafin = null
            $segPrev = get_seguimiento($nroTramite, $codFlujo, $codProcesoEnviar);
            if ($segPrev) {
                $segPrev['fechafin'] = null;
                save_seguimiento($segPrev);
            }
        }
    }
}

// 3. Si presionó SIGUIENTE (Avanzar de pantalla)
if (isset($_GET["Siguiente"])) {
    // Buscar la configuración del siguiente paso
    $sigProcesoInfo = get_proceso($codFlujo, $codProcesoSiguiente);
    
    if ($sigProcesoInfo) {
        $codProcesoEnviar = $sigProcesoInfo['codProceso'];
        $siguienteRol = $sigProcesoInfo['codRol'];
        
        // Determinar qué usuario atiende el siguiente paso según su rol
        // E = Estudiante (Juan), K = Kardex (Maria), D = Director (Hugo)
        if ($siguienteRol === 'K') {
            $siguienteUsuario = 'Maria';
        } elseif ($siguienteRol === 'D') {
            $siguienteUsuario = 'Hugo';
        } else {
            $siguienteUsuario = 'Juan'; // Por defecto, Estudiante Juan
        }
        
        if (!empty($nroTramite)) {
            // Finalizar el paso actual (poner fechafin = hoy)
            $segActual = get_seguimiento($nroTramite, $codFlujo, $codProceso);
            if ($segActual) {
                $segActual['fechafin'] = date('Y-m-d');
                save_seguimiento($segActual);
            }
            
            // Crear o actualizar la entrada para el siguiente paso
            $segSiguiente = get_seguimiento($nroTramite, $codFlujo, $codProcesoEnviar);
            if ($segSiguiente) {
                // Si ya existía (ej: regresó por Anterior y volvió a avanzar), reactivarlo
                $segSiguiente['fechafin'] = null;
                $segSiguiente['codUsuario'] = $siguienteUsuario;
            } else {
                // Nuevo paso en el historial
                $segSiguiente = [
                    "nroTramite" => (int)$nroTramite,
                    "codFlujo" => $codFlujo,
                    "codProceso" => $codProcesoEnviar,
                    "codUsuario" => $siguienteUsuario,
                    "fechaini" => date('Y-m-d'),
                    "fechafin" => null
                ];
            }
            save_seguimiento($segSiguiente);
        }
    } else {
        // No hay paso siguiente (el trámite ha finalizado por completo)
        if (!empty($nroTramite)) {
            $segActual = get_seguimiento($nroTramite, $codFlujo, $codProceso);
            if ($segActual) {
                $segActual['fechafin'] = date('Y-m-d');
                save_seguimiento($segActual);
            }
        }
        header("Location: bentrada.php");
        exit();
    }
}

// Redirigir de regreso al motor de flujo con los parámetros actualizados
$urlRedireccion = "motor.php?codFlujo=$codFlujo&codProceso=$codProcesoEnviar&nroTramite=$nroTramite";
header("Location: " . $urlRedireccion);
exit();
?>
