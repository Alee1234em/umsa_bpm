<?php
// Guardar la resolución de Kardex en JSON
if (isset($_GET["Siguiente"])) {
    $estado_pago = $_GET["estado_pago"] ?? 'Pendiente';
    $observaciones = $_GET["observaciones"] ?? '';

    if (!empty($nroTramite)) {
        $tramiteData = get_tramite_cert($nroTramite);
        if ($tramiteData) {
            $tramiteData['estado_pago'] = $estado_pago;
            $tramiteData['observaciones'] = $observaciones;
            $tramiteData['estado'] = ($estado_pago === 'Aprobado') ? 'Aprobada' : 'Rechazada';
            $tramiteData['firmado'] = ($estado_pago === 'Aprobado');
            
            save_tramite_cert($tramiteData);
        }
    }
}
?>
