<?php
// Guardar el registro de Kardex
if (isset($_GET["Siguiente"])) {
    $registro_kardex_confirmado = isset($_GET["registro_kardex_confirmado"]) ? true : false;

    if (!empty($nroTramite)) {
        $tramiteData = get_tramite_insc($nroTramite);
        if ($tramiteData) {
            $tramiteData['registro_kardex_confirmado'] = $registro_kardex_confirmado;
            $tramiteData['estado'] = $registro_kardex_confirmado ? 'Completado' : 'Pendiente Registro';
            
            save_tramite_insc($tramiteData);
        }
    }
}
?>
