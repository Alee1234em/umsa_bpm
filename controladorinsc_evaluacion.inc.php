<?php
// Guardar la evaluación de dirección de carrera
if (isset($_GET["Siguiente"])) {
    $decision_director = $_GET["decision_director"] ?? 'Pendiente';
    $observaciones_director = $_GET["observaciones_director"] ?? '';

    if (!empty($nroTramite)) {
        $tramiteData = get_tramite_insc($nroTramite);
        if ($tramiteData) {
            $tramiteData['decision_director'] = $decision_director;
            $tramiteData['observaciones_director'] = $observaciones_director;
            $tramiteData['estado'] = ($decision_director === 'Aprobada') ? 'Aprobada por Director' : 'Rechazada por Director';
            
            save_tramite_insc($tramiteData);
            
            // COMPONENTE BPM: Enrutamiento Condicional
            // Si el Director de Carrera rechaza, saltamos Kardex (P3) y vamos directamente al resultado estudiantil (P4)
            if ($decision_director === 'Rechazada') {
                $codProcesoSiguiente = 'P4';
            }
        }
    }
}
?>
