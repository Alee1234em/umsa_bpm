
<?php
// Obtener los datos del trámite
$tramiteData = get_tramite_cert($nroTramite);

$estado = $tramiteData['estado'] ?? 'Pendiente';
$observaciones = !empty($tramiteData['observaciones']) ? $tramiteData['observaciones'] : 'Sin observaciones registradas.';
$nombre = $tramiteData['nombre'] ?? '';
$apellido = $tramiteData['apellido'] ?? '';
$carrera = $tramiteData['carrera'] ?? '';
$gestion = $tramiteData['gestion'] ?? '';
$tipo_certificado = $tramiteData['tipo_certificado'] ?? '';
$ci = $tramiteData['ci'] ?? '';
?>

<div class="form-group" style="text-align: center; padding: 10px 0;">
    
    <?php if ($estado === 'Aprobada'): ?>
        
        <!-- Banner de Éxito -->
        <div style="background-color: var(--success-bg); color: #065f46; border: 1px solid #a7f3d0; padding: 20px; border-radius: var(--radius-md); font-weight: bold; font-size: 18px; margin-bottom: 25px; display: inline-flex; align-items: center; gap: 10px;">
            <svg width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
            </svg>
            ¡Su Solicitud de Certificado ha sido Aprobada y Emitida!
        </div>

        <!-- Previsualización del Certificado con Estética Premium -->
        <div class="card" style="border: 2px solid #3b82f6; background-color: #fafaf9; text-align: left; padding: 30px; box-shadow: var(--shadow-lg); font-family: 'Georgia', serif; position: relative;">
            
            <!-- Marca de agua UMSA -->
            <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); font-size: 80px; color: rgba(29, 78, 216, 0.04); font-weight: bold; pointer-events: none; text-align: center; width: 100%;">
                UMSA
            </div>
            
            <div style="text-align: center; border-bottom: 2px double #1e3a8a; padding-bottom: 15px; margin-bottom: 20px;">
                <h3 style="font-family: 'Outfit', sans-serif; color: #1e3a8a; margin: 0 0 5px 0; font-size: 18px; font-weight: bold;">UNIVERSIDAD MAYOR DE SAN ANDRÉS</h3>
                <span style="font-family: 'Inter', sans-serif; font-size: 11px; text-transform: uppercase; letter-spacing: 1px; color: var(--text-muted);">Facultad de Ciencias Puras y Naturales</span>
            </div>

            <h4 style="text-align: center; font-family: 'Outfit', sans-serif; color: #0f172a; font-size: 16px; font-weight: bold; margin-bottom: 25px;">
                <?php echo strtoupper(htmlspecialchars($tipo_certificado)); ?>
            </h4>

            <p style="font-size: 13px; line-height: 1.6; color: #334155; text-align: justify; margin-bottom: 20px;">
                La División de Kardex Académico de la Carrera de <strong><?php echo htmlspecialchars($carrera); ?></strong> de la Universidad Mayor de San Andrés, certifica que el estudiante:
            </p>

            <p style="text-align: center; font-size: 18px; font-weight: bold; color: #1e3a8a; margin: 15px 0; font-family: 'Outfit', sans-serif;">
                <?php echo htmlspecialchars($nombre . ' ' . $apellido); ?>
            </p>

            <p style="font-size: 13px; line-height: 1.6; color: #334155; text-align: justify; margin-bottom: 30px;">
                Con Cédula de Identidad <strong><?php echo htmlspecialchars($ci); ?></strong>, se encuentra debidamente registrado en los libros de actas de calificaciones de la gestión <strong><?php echo htmlspecialchars($gestion); ?></strong>, habiendo cumplido con todos los requisitos académicos aprobatorios correspondientes.
            </p>

            <!-- Sección de Firma Digital y QR -->
            <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-top: 40px; border-top: 1px solid #e2e8f0; padding-top: 20px;">
                <div>
                    <!-- QR Code Mock -->
                    <div style="display: flex; gap: 10px; align-items: center;">
                        <div style="width: 60px; height: 60px; background: #e2e8f0; border: 1px solid #cbd5e1; display: flex; align-items: center; justify-content: center; font-size: 9px; color: #475569; font-weight: bold;">
                            [QR VERIFY]
                        </div>
                        <div style="font-family: 'Inter', sans-serif; font-size: 10px; color: var(--text-muted);">
                            Documento firmado digitalmente.<br>
                            Cód. Verif: <strong style="font-family: monospace;"><?php echo md5($nroTramite . $ci); ?></strong>
                        </div>
                    </div>
                </div>
                
                <div style="text-align: center; width: 200px;">
                    <div style="font-family: 'Courier New', monospace; font-size: 11px; color: #059669; font-weight: bold; border: 2px dashed #059669; padding: 4px; border-radius: 4px; margin-bottom: 8px;">
                        FIRMADO DIGITALMENTE<br>
                        Por: Lic. Maria Choque
                    </div>
                    <span style="font-family: 'Inter', sans-serif; font-size: 11px; color: var(--text-muted); border-top: 1px solid #cbd5e1; display: block; padding-top: 4px; width: 100%;">División de Kardex</span>
                </div>
            </div>
        </div>

        <div style="margin-top: 20px;">
            <a href="#" onclick="alert('Descargando archivo PDF del Certificado de Notas...'); return false;" class="btn btn-success" style="padding: 12px 24px; font-size: 14px; font-family: 'Outfit'; box-shadow: 0 4px 10px rgba(16, 185, 129, 0.2);">
                Descargar Documento Oficial (PDF)
            </a>
        </div>

    <?php elseif ($estado === 'Rechazada'): ?>
        
        <!-- Banner de Rechazo -->
        <div style="background-color: var(--danger-bg); color: #991b1b; border: 1px solid #fecaca; padding: 20px; border-radius: var(--radius-md); font-weight: bold; font-size: 18px; margin-bottom: 25px; display: inline-flex; align-items: center; gap: 10px;">
            <svg width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
            </svg>
            Su Solicitud de Certificado fue Observada
        </div>

        <div class="card" style="border-left: 5px solid var(--danger); background-color: #fafafa; text-align: left; padding: 20px;">
            <h4 style="margin-top: 0; margin-bottom: 10px; color: var(--danger); font-family: 'Outfit';">Detalle de la Observación:</h4>
            <p style="font-size: 14px; color: #334155; line-height: 1.5; font-style: italic;">
                "<?php echo nl2br(htmlspecialchars($observaciones)); ?>"
            </p>
            <p style="font-size: 13px; color: var(--text-muted); margin-top: 20px; border-top: 1px solid var(--border); padding-top: 15px;">
                <strong>Instrucciones:</strong> El código del comprobante bancario registrado no es válido o no corresponde al monto establecido. Por favor, finalice este trámite e inicie una nueva solicitud con un comprobante bancario válido.
            </p>
        </div>

    <?php else: ?>
        <div class="empty-state">
            Su solicitud aún está en proceso de revisión por Kardex.
        </div>
    <?php endif; ?>

    <p style="color: var(--text-muted); font-size: 13px; margin-top: 30px;">
        Haga clic en <strong>Finalizar Trámite</strong> para archivar esta solicitud en su historial de trámites.
    </p>
</div>
