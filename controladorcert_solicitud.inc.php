<?php

// Guardar o actualizar la solicitud de certificado en JSON
if (isset($_GET["Siguiente"])) {
    $ci = $_GET["ci"] ?? '';
    $carrera = $_GET["carrera"] ?? '';
    $tipo_certificado = $_GET["tipo_certificado"] ?? '';
    $gestion = $_GET["gestion"] ?? '';
    $comprobante_pago = $_GET["comprobante_pago"] ?? '';

    if (!empty($nroTramite)) {
        // Buscar si ya existía un registro de trámite
        $existente = get_tramite_cert($nroTramite);
        
        // Obtener nombre del alumno por CI
        $nombre = 'Estudiante';
        $apellido = 'No Identificado';
        $alumnoData = get_alumno_por_ci($ci);
        
        if ($alumnoData) {
            $nombre = $alumnoData['nombre'];
            $apellido = $alumnoData['apellido'];
        } else {
            // Intento buscar por usuario activo si no coincide el CI
            $alumnoData2 = get_alumno_por_nombre($currentUser);
            if ($alumnoData2) {
                $nombre = $alumnoData2['nombre'];
                $apellido = $alumnoData2['apellido'];
            }
        }
        
        $registro = [
            "nroTramite" => (int)$nroTramite,
            "ci" => $ci,
            "nombre" => $nombre,
            "apellido" => $apellido,
            "carrera" => $carrera,
            "gestion" => $gestion,
            "tipo_certificado" => $tipo_certificado,
            "comprobante_pago" => $comprobante_pago,
            "fecha_pago" => date('Y-m-d'),
            "estado_pago" => "Pendiente",
            "firmado" => false,
            "observaciones" => $existente['observaciones'] ?? '',
            "estado" => "Pendiente"
        ];
        
        save_tramite_cert($registro);
    }
}
?>
