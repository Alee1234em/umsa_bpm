<?php
// Buscar si ya existen datos cargados para este trámite en tramite_insc.json
$tramiteData = get_tramite_insc($nroTramite);

$ci = $tramiteData['ci'] ?? '';
$nombre = $tramiteData['nombre'] ?? '';
$apellido = $tramiteData['apellido'] ?? '';
$carrera = $tramiteData['carrera'] ?? '';
$ru = $tramiteData['ru'] ?? '';
$materias_seleccionadas = $tramiteData['materias'] ?? [];
$justificacion = $tramiteData['justificacion'] ?? '';
$comprobante_medico = $tramiteData['comprobante_medico'] ?? '';

// Si no existen datos del trámite, pre-cargar con la información del alumno conectado
if (empty($ci)) {
    $alumnoInfo = get_alumno_por_nombre($currentUser);
    if ($alumnoInfo) {
        $ci = $alumnoInfo['ci'];
        $nombre = $alumnoInfo['nombre'];
        $apellido = $alumnoInfo['apellido'];
        $carrera = $alumnoInfo['carrera'];
        $ru = $alumnoInfo['ru'] ?? '1734921';
    }
}
?>

<div class="form-group">
    <p style="color: var(--text-muted); font-size: 14px; margin-bottom: 20px;">
        <strong>Solicitud de Inscripción Extemporánea:</strong> Registre el Registro Universitario (RU), seleccione las siglas de las materias que desea inscribir y justifique el motivo de su solicitud fuera del calendario regular.
    </p>
</div>

<!-- Datos del Estudiante (Solo Lectura) -->
<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
    <div class="form-group">
        <label>Estudiante:</label>
        <input type="text" class="form-control" value="<?php echo htmlspecialchars($nombre . ' ' . $apellido); ?>" readonly style="background-color: #f1f5f9; cursor: not-allowed;">
    </div>
    <div class="form-group">
        <label for="ru">Registro Universitario (RU):</label>
        <input type="text" id="ru" name="ru" class="form-control" value="<?php echo htmlspecialchars($ru); ?>" required>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
    <div class="form-group">
        <label>CI:</label>
        <input type="text" class="form-control" value="<?php echo htmlspecialchars($ci); ?>" readonly style="background-color: #f1f5f9; cursor: not-allowed;">
    </div>
    <div class="form-group">
        <label>Carrera:</label>
        <input type="text" class="form-control" value="<?php echo htmlspecialchars($carrera); ?>" readonly style="background-color: #f1f5f9; cursor: not-allowed;">
    </div>
</div>

<!-- Materias a Inscribir -->
<div class="form-group">
    <label style="margin-bottom: 10px;">Seleccione las Materias a Inscribir (Máx. 2 materias):</label>
    <div style="display: flex; flex-direction: column; gap: 10px; background: #f8fafc; border: 1px solid var(--border); padding: 15px; border-radius: var(--radius-sm);">
        <label style="display: flex; align-items: center; gap: 10px; font-weight: normal; cursor: pointer;">
            <input type="checkbox" name="materias[]" value="INF-131" <?php echo in_array('INF-131', $materias_seleccionadas) ? 'checked' : ''; ?>>
            <span><strong>INF-131</strong> - Estructuras de Datos y Algoritmos (Grupo A)</span>
        </label>
        <label style="display: flex; align-items: center; gap: 10px; font-weight: normal; cursor: pointer;">
            <input type="checkbox" name="materias[]" value="INF-143" <?php echo in_array('INF-143', $materias_seleccionadas) ? 'checked' : ''; ?>>
            <span><strong>INF-143</strong> - Base de Datos I (Grupo B)</span>
        </label>
        <label style="display: flex; align-items: center; gap: 10px; font-weight: normal; cursor: pointer;">
            <input type="checkbox" name="materias[]" value="INF-161" <?php echo in_array('INF-161', $materias_seleccionadas) ? 'checked' : ''; ?>>
            <span><strong>INF-161</strong> - Programación Web (Grupo A)</span>
        </label>
    </div>
</div>

<div class="form-group">
    <label for="justificacion">Justificación Detallada (Motivo de retraso):</label>
    <textarea id="justificacion" name="justificacion" class="form-control" style="background-color: white;" placeholder="Describa el motivo médico, laboral o administrativo que imposibilitó su inscripción..." required><?php echo htmlspecialchars($justificacion); ?></textarea>
</div>

<div class="form-group">
    <label for="comprobante_medico">Código de Comprobante o Documento de Respaldo (Opcional):</label>
    <input type="text" id="comprobante_medico" name="comprobante_medico" class="form-control" style="background-color: white;" placeholder="Ej. CERT-MED-2026-981 o CERT-TRAB-304" value="<?php echo htmlspecialchars($comprobante_medico); ?>">
    <span style="font-size: 11px; color: var(--text-muted); display: block; margin-top: 5px;">* Nota: Adjuntar respaldo físico o digital facilita la validación por parte del Director de Carrera.</span>
</div>
