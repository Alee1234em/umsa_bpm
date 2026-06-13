<?php
// Buscar los datos de la solicitud cargados en el paso 1
$tramiteData = get_tramite_cert($nroTramite);

$ci = $tramiteData['ci'] ?? 'No disponible';
$nombre = $tramiteData['nombre'] ?? '';
$apellido = $tramiteData['apellido'] ?? '';
$carrera = $tramiteData['carrera'] ?? '';
$gestion = $tramiteData['gestion'] ?? '';
$tipo_certificado = $tramiteData['tipo_certificado'] ?? '';
$comprobante_pago = $tramiteData['comprobante_pago'] ?? '';
$estado_pago = $tramiteData['estado_pago'] ?? 'Pendiente';
$observaciones = $tramiteData['observaciones'] ?? '';
?>

<div class="form-group">
    <p style="color: var(--text-muted); font-size: 14px; margin-bottom: 20px;">
        <strong>Rol: Kardex (Lic. Maria Choque)</strong>. Revise la solicitud académica del alumno y verifique si el comprobante de depósito bancario fue conciliado correctamente.
    </p>
</div>

<!-- Resumen del Trámite -->
<div class="info-table-card">
    <h4 style="margin-top: 0; margin-bottom: 10px; color: #1e3a8a; font-family: 'Outfit'; font-size: 15px;">Ficha de Datos de la Solicitud</h4>
    <table style="width: 100%; border: none;">
        <tr>
            <th style="width: 40%;">Estudiante:</th>
            <td><?php echo htmlspecialchars($nombre . ' ' . $apellido); ?></td>
        </tr>
        <tr>
            <th>CI:</th>
            <td><?php echo htmlspecialchars($ci); ?></td>
        </tr>
        <tr>
            <th>Carrera:</th>
            <td><?php echo htmlspecialchars($carrera); ?></td>
        </tr>
        <tr>
            <th>Tipo de Documento:</th>
            <td><?php echo htmlspecialchars($tipo_certificado); ?></td>
        </tr>
        <tr>
            <th>Gestión:</th>
            <td><?php echo htmlspecialchars($gestion); ?></td>
        </tr>
        <tr>
            <th style="color: #1e3a8a; font-weight: bold;">Cód. Comprobante Bancario:</th>
            <td style="color: #1e3a8a; font-weight: bold; font-family: monospace; font-size: 14px;">
                <?php echo htmlspecialchars($comprobante_pago); ?>
            </td>
        </tr>
    </table>
</div>

<div style="display: grid; grid-template-columns: 1fr; gap: 15px; margin-bottom: 15px;">
    <div class="form-group">
        <label for="estado_pago">Resolución de Pago / Estado de Caja:</label>
        <select id="estado_pago" name="estado_pago" class="form-control" style="background-color: white;" required>
            <option value="Aprobado" <?php echo $estado_pago === 'Aprobado' ? 'selected' : ''; ?>>✓ Comprobante Verificado y Pago Aprobado</option>
            <option value="Rechazado" <?php echo $estado_pago === 'Rechazado' ? 'selected' : ''; ?>>✗ Comprobante Inválido / Pago Rechazado</option>
        </select>
    </div>
</div>

<div class="form-group">
    <label for="observaciones">Observaciones o Instrucciones Adicionales:</label>
    <textarea id="observaciones" name="observaciones" class="form-control" style="background-color: white;" placeholder="Ej. Depósito verificado por un monto de 20 Bs. Se aprueba la firma digital del certificado."><?php echo htmlspecialchars($observaciones); ?></textarea>
</div>
