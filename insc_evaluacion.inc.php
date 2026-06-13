<?php
// Buscar los datos de la solicitud de inscripción
$tramiteData = get_tramite_insc($nroTramite);

$nombre = $tramiteData['nombre'] ?? '';
$apellido = $tramiteData['apellido'] ?? '';
$ci = $tramiteData['ci'] ?? '';
$ru = $tramiteData['ru'] ?? '';
$carrera = $tramiteData['carrera'] ?? '';
$materias = $tramiteData['materias'] ?? [];
$justificacion = $tramiteData['justificacion'] ?? '';
$comprobante_medico = $tramiteData['comprobante_medico'] ?? 'No presentado';
$decision_director = $tramiteData['decision_director'] ?? 'Pendiente';
$observaciones_director = $tramiteData['observaciones_director'] ?? '';
?>

<div class="form-group">
    <p style="color: var(--text-muted); font-size: 14px; margin-bottom: 20px;">
        <strong>Rol: Director de Carrera (Dr. Hugo Gomez)</strong>. Evalúe los justificativos presentados por el estudiante y decida si autoriza la inscripción extemporánea.
    </p>
</div>

<!-- Ficha de la Solicitud -->
<div class="info-table-card">
    <h4 style="margin-top: 0; margin-bottom: 10px; color: #4f46e5; font-family: 'Outfit'; font-size: 15px;">Datos de Solicitud de Inscripción</h4>
    <table style="width: 100%; border: none;">
        <tr>
            <th style="width: 40%;">Estudiante:</th>
            <td><?php echo htmlspecialchars($nombre . ' ' . $apellido); ?></td>
        </tr>
        <tr>
            <th>RU / CI:</th>
            <td><?php echo htmlspecialchars($ru); ?> / <?php echo htmlspecialchars($ci); ?></td>
        </tr>
        <tr>
            <th>Carrera:</th>
            <td><?php echo htmlspecialchars($carrera); ?></td>
        </tr>
        <tr>
            <th style="color: #4f46e5; font-weight: bold;">Materias Solicitadas:</th>
            <td style="color: #4f46e5; font-weight: bold;">
                <?php 
                if (count($materias) > 0) {
                    echo htmlspecialchars(implode(', ', $materias));
                } else {
                    echo "Ninguna materia seleccionada";
                }
                ?>
            </td>
        </tr>
        <tr>
            <th>Documentación Respaldo:</th>
            <td style="font-family: monospace; font-size: 13px;"><?php echo htmlspecialchars($comprobante_medico); ?></td>
        </tr>
        <tr>
            <th style="vertical-align: top;">Justificación del Alumno:</th>
            <td style="white-space: pre-wrap; font-style: italic; color: #475569;"><?php echo htmlspecialchars($justificacion); ?></td>
        </tr>
    </table>
</div>

<div style="display: grid; grid-template-columns: 1fr; gap: 15px; margin-bottom: 15px;">
    <div class="form-group">
        <label for="decision_director">Resolución de Dirección de Carrera:</label>
        <select id="decision_director" name="decision_director" class="form-control" style="background-color: white;" required>
            <option value="Aprobada" <?php echo $decision_director === 'Aprobada' ? 'selected' : ''; ?>>✓ Autorizar Inscripción Extemporánea</option>
            <option value="Rechazada" <?php echo $decision_director === 'Rechazada' ? 'selected' : ''; ?>>✗ Rechazar Solicitud / Sin Justificativo Válido</option>
        </select>
    </div>
</div>

<div class="form-group">
    <label for="observaciones_director">Comentarios de Evaluación / Fundamento Jurídico:</label>
    <textarea id="observaciones_director" name="observaciones_director" class="form-control" style="background-color: white;" placeholder="Ej. El certificado médico de la caja de salud valida la inasistencia. Se autoriza la inscripción extemporánea para las materias solicitadas."><?php echo htmlspecialchars($observaciones_director); ?></textarea>
</div>
