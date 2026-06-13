<?php
// Buscar si ya existen datos de este trámite en la base de datos de certificados
$tramiteData = get_tramite_cert($nroTramite);

$ci = $tramiteData['ci'] ?? '';
$nombre = $tramiteData['nombre'] ?? '';
$apellido = $tramiteData['apellido'] ?? '';
$carrera = $tramiteData['carrera'] ?? '';
$gestion = $tramiteData['gestion'] ?? date('Y');
$tipo_certificado = $tramiteData['tipo_certificado'] ?? 'Certificado de Calificaciones';
$comprobante_pago = $tramiteData['comprobante_pago'] ?? '';

// Si no existen datos del trámite, pre-cargar con la información del alumno conectado
if (empty($ci)) {
    $alumnoInfo = get_alumno_por_nombre($currentUser);
    if ($alumnoInfo) {
        $ci = $alumnoInfo['ci'];
        $nombre = $alumnoInfo['nombre'];
        $apellido = $alumnoInfo['apellido'];
        $carrera = $alumnoInfo['carrera'];
    }
}
?>
<div class="form-group">
    <p style="color: var(--text-muted); font-size: 14px; margin-bottom: 20px;">
        Por favor, complete los detalles de su solicitud académica y registre el código del comprobante bancario correspondiente al pago de aranceles universitarios.
    </p>
</div>

<!-- Grid para agrupar Datos Personales (Solo Lectura) -->
<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
    <div class="form-group">
        <label>Nombre(s):</label>
        <input type="text" class="form-control" value="<?php echo htmlspecialchars($nombre); ?>" readonly style="background-color: #f1f5f9; cursor: not-allowed;">
    </div>
    <div class="form-group">
        <label>Apellido(s):</label>
        <input type="text" class="form-control" value="<?php echo htmlspecialchars($apellido); ?>" readonly style="background-color: #f1f5f9; cursor: not-allowed;">
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
    <div class="form-group">
        <label for="ci">Carnet de Identidad (CI):</label>
        <input type="text" id="ci" name="ci" class="form-control" value="<?php echo htmlspecialchars($ci); ?>" required>
    </div>
    <div class="form-group">
        <label for="carrera">Carrera Académica:</label>
        <input type="text" id="carrera" name="carrera" class="form-control" value="<?php echo htmlspecialchars($carrera); ?>" required>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
    <div class="form-group">
        <label for="tipo_certificado">Tipo de Certificado:</label>
        <select id="tipo_certificado" name="tipo_certificado" class="form-control">
            <option value="Certificado de Calificaciones" <?php echo $tipo_certificado === 'Certificado de Calificaciones' ? 'selected' : ''; ?>>Certificado de Calificaciones</option>
            <option value="Certificado de Carga Horaria" <?php echo $tipo_certificado === 'Certificado de Carga Horaria' ? 'selected' : ''; ?>>Certificado de Carga Horaria</option>
            <option value="Certificado de Egreso" <?php echo $tipo_certificado === 'Certificado de Egreso' ? 'selected' : ''; ?>>Certificado de Egreso</option>
        </select>
    </div>
    <div class="form-group">
        <label for="gestion">Gestión Solicitada:</label>
        <select id="gestion" name="gestion" class="form-control">
            <option value="2026" <?php echo $gestion === '2026' ? 'selected' : ''; ?>>2026 (Gestión Actual)</option>
            <option value="2025" <?php echo $gestion === '2025' ? 'selected' : ''; ?>>2025</option>
            <option value="2024" <?php echo $gestion === '2024' ? 'selected' : ''; ?>>2024</option>
        </select>
    </div>
</div>

<div class="form-group" style="background-color: #eff6ff; padding: 15px; border-radius: var(--radius-sm); border: 1px solid #bfdbfe;">
    <label for="comprobante_pago" style="color: #1e40af;">Código de Comprobante de Pago Bancario (Arancel):</label>
    <input type="text" id="comprobante_pago" name="comprobante_pago" class="form-control" style="background-color: white; border-color: #3b82f6;" placeholder="Ej. TRANS-98765432" value="<?php echo htmlspecialchars($comprobante_pago); ?>" required>
    <span style="font-size: 11px; color: #1e3a8a; display: block; margin-top: 5px;">* Nota: El arancel de emisión es de 20 Bs. depositados a la cuenta bancaria de la UMSA.</span>
</div>
