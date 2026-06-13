<?php
// Guardar o actualizar la solicitud de inscripción extemporánea en JSON
if (isset($_GET["Siguiente"])) {
    $ru = $_GET["ru"] ?? '';
    $materias = $_GET["materias"] ?? []; // Array de materias
    $justificacion = $_GET["justificacion"] ?? '';
    $comprobante_medico = $_GET["comprobante_medico"] ?? '';

    if (!empty($nroTramite)) {
        // Buscar estudiante por usuario activo para registrar correctamente su nombre y CI
        $alumnoData = get_alumno_por_nombre($currentUser);
        $nombre = $alumnoData ? $alumnoData['nombre'] : 'Juan';
        $apellido = $alumnoData ? $alumnoData['apellido'] : 'Perez';
        $ci = $alumnoData ? $alumnoData['ci'] : '8765432';
        $carrera = $alumnoData ? $alumnoData['carrera'] : 'Informática';
        
        $existente = get_tramite_insc($nroTramite);
        
        $registro = [
            "nroTramite" => (int)$nroTramite,
            "ci" => $ci,
            "nombre" => $nombre,
            "apellido" => $apellido,
            "carrera" => $carrera,
            "ru" => $ru,
            "materias" => $materias,
            "justificacion" => $justificacion,
            "comprobante_medico" => $comprobante_medico,
            "decision_director" => $existente['decision_director'] ?? 'Pendiente',
            "observaciones_director" => $existente['observaciones_director'] ?? '',
            "registro_kardex_confirmado" => $existente['registro_kardex_confirmado'] ?? false,
            "estado" => $existente['estado'] ?? 'Pendiente'
        ];
        
        save_tramite_insc($registro);
    }
}
?>
