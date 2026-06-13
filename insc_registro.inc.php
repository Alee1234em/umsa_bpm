<?php
// Buscar los datos de la solicitud de inscripción
$tramiteData = get_tramite_insc($nroTramite);

$nombre = $tramiteData['nombre'] ?? '';
$apellido = $tramiteData['apellido'] ?? '';
$ci = $tramiteData['ci'] ?? '';
$ru = $tramiteData['ru'] ?? '';
$materias = $tramiteData['materias'] ?? [];
$observaciones_director = $tramiteData['observaciones_director'] ?? '';
$registro_kardex_confirmado = $tramiteData['registro_kardex_confirmado'] ?? false;
?>

<div class="form-group">
    <p style="color: var(--text-muted); font-size: 14px; margin-bottom: 20px;">
        <strong>Rol: Kardex (Lic. Maria Choque)</strong>. Proceda al registro formal de las siglas autorizadas en el sistema académico institucional de la UMSA y marque la confirmación.
    </p>
</div>

<!-- Ficha de Datos con Aval del Director -->
<div class="info-table-card" style="border-left: 5px solid var(--success);">
    <h4 style="margin-top: 0; margin-bottom: 10px; color: var(--success); font-family: 'Outfit'; font-size: 15px;">Autorización de Dirección de Carrera</h4>
    <table style="width: 100%; border: none;">
        <tr>
            <th style="width: 40%;">Estudiante:</th>
            <td><?php echo htmlspecialchars($nombre . ' ' . $apellido); ?> (RU: <?php echo htmlspecialchars($ru); ?>)</td>
        </tr>
        <tr>
            <th style="color: var(--success); font-weight: bold;">Materias Autorizadas:</th>
            <td style="color: var(--success); font-weight: bold;">
                <?php 
                if (count($materias) > 0) {
                    echo htmlspecialchars(implode(', ', $materias));
                } else {
                    echo "Ninguna materia";
                }
                ?>
            </td>
        </tr>
        <tr>
            <th style="vertical-align: top;">Aval del Director:</th>
            <td style="color: #334155; font-style: italic;">
                "<?php echo htmlspecialchars($observaciones_director); ?>"
            </td>
        </tr>
    </table>
</div>

<div class="form-group" style="background-color: #f0fdf4; padding: 20px; border-radius: var(--radius-sm); border: 1px solid #bbf7d0; margin-top: 20px;">
    <label style="display: flex; align-items: center; gap: 12px; cursor: pointer; font-size: 14px; color: #166534;">
        <input type="checkbox" name="registro_kardex_confirmado" value="1" <?php echo $registro_kardex_confirmado ? 'checked' : ''; ?> style="width: 18px; height: 18px;" required>
        <span><strong>Confirmar Matriculación y Carga en Sistema (SIA-UMSA)</strong></span>
    </label>
    <span style="font-size: 11px; color: #166534; display: block; margin-top: 8px; padding-left: 30px;">
        * Al marcar esta casilla, usted certifica que las materias e históricos del alumno Juan Perez ya fueron modificados en la intranet oficial de la facultad.
    </span>
</div>
