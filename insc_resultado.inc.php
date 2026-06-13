<?php
// Obtener los datos del trámite
$tramiteData = get_tramite_insc($nroTramite);

$nombre = $tramiteData['nombre'] ?? '';
$apellido = $tramiteData['apellido'] ?? '';
$ru = $tramiteData['ru'] ?? '';
$materias = $tramiteData['materias'] ?? [];
$decision_director = $tramiteData['decision_director'] ?? 'Pendiente';
$observaciones_director = !empty($tramiteData['observaciones_director']) ? $tramiteData['observaciones_director'] : 'Sin observaciones.';
$registro_kardex_confirmado = $tramiteData['registro_kardex_confirmado'] ?? false;
$estado = $tramiteData['estado'] ?? 'Pendiente';
?>

<div class="form-group" style="text-align: center; padding: 10px 0;">
    
    <?php if ($decision_director === 'Aprobada' && $registro_kardex_confirmado): ?>
        
        <!-- Caso Aprobado y Registrado -->
        <div style="background-color: var(--success-bg); color: #065f46; border: 1px solid #a7f3d0; padding: 20px; border-radius: var(--radius-md); font-weight: bold; font-size: 18px; margin-bottom: 25px; display: inline-flex; align-items: center; gap: 10px;">
            <svg width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
            </svg>
            ¡Inscripción Extemporánea Procesada con Éxito!
        </div>

        <div class="card" style="border: 2px solid #10b981; background-color: #f8fafc; text-align: left; padding: 25px;">
            <div style="border-bottom: 1px solid var(--border); padding-bottom: 15px; margin-bottom: 15px; display: flex; justify-content: space-between; align-items: center;">
                <h4 style="margin: 0; color: #1e293b; font-family: 'Outfit'; font-size: 16px;">Boleta Oficial de Inscripción</h4>
                <span class="badge badge-green">Matriculado</span>
            </div>
            
            <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 15px;">
                Se procedió a inscribir al estudiante <strong><?php echo htmlspecialchars($nombre . ' ' . $apellido); ?></strong> con RU <strong><?php echo htmlspecialchars($ru); ?></strong> en las siguientes siglas universitarias:
            </p>

            <!-- Lista de Materias Registradas -->
            <div style="background: white; border: 1px solid var(--border); border-radius: var(--radius-sm); padding: 15px; margin-bottom: 20px;">
                <ul style="margin: 0; padding-left: 20px; font-size: 14px; font-weight: 600; color: #1e3a8a;">
                    <?php foreach ($materias as $m): ?>
                        <li style="margin-bottom: 5px;">
                            <?php 
                            if ($m === 'INF-131') echo "INF-131 - Estructuras de Datos y Algoritmos (Grupo A)";
                            elseif ($m === 'INF-143') echo "INF-143 - Base de Datos I (Grupo B)";
                            elseif ($m === 'INF-161') echo "INF-161 - Programación Web (Grupo A)";
                            else echo htmlspecialchars($m);
                            ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div style="font-size: 11px; color: var(--text-muted); border-top: 1px solid var(--border); padding-top: 10px;">
                * Aval del Director de Carrera: <em>"<?php echo htmlspecialchars($observaciones_director); ?>"</em>
            </div>
        </div>

        <div style="margin-top: 20px;">
            <a href="#" onclick="alert('Imprimiendo boleta de inscripción extemporánea...'); return false;" class="btn btn-success" style="padding: 12px 24px; font-size: 14px; font-family: 'Outfit'; box-shadow: 0 4px 10px rgba(16, 185, 129, 0.2);">
                Imprimir Boleta de Inscripción (PDF)
            </a>
        </div>

    <?php elseif ($decision_director === 'Rechazada'): ?>
        
        <!-- Caso Rechazado por Director -->
        <div style="background-color: var(--danger-bg); color: #991b1b; border: 1px solid #fecaca; padding: 20px; border-radius: var(--radius-md); font-weight: bold; font-size: 18px; margin-bottom: 25px; display: inline-flex; align-items: center; gap: 10px;">
            <svg width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
            </svg>
            Su Solicitud de Inscripción fue Rechazada
        </div>

        <div class="card" style="border-left: 5px solid var(--danger); background-color: #fafafa; text-align: left; padding: 20px;">
            <h4 style="margin-top: 0; margin-bottom: 10px; color: var(--danger); font-family: 'Outfit';">Fundamento del Rechazo (Director de Carrera):</h4>
            <p style="font-size: 14px; color: #334155; line-height: 1.5; font-style: italic;">
                "<?php echo nl2br(htmlspecialchars($observaciones_director)); ?>"
            </p>
            <p style="font-size: 13px; color: var(--text-muted); margin-top: 20px; border-top: 1px solid var(--border); padding-top: 15px;">
                <strong>Sugerencia:</strong> Si considera que existe un error en la valoración de sus justificativos, comuníquese directamente con la Dirección de la Carrera de Informática aportando documentación complementaria.
            </p>
        </div>

    <?php else: ?>
        
        <!-- Caso Aprobado por Director pero Pendiente de Registro en Kardex -->
        <div style="background-color: var(--warning-bg); color: #92400e; border: 1px solid #fde68a; padding: 20px; border-radius: var(--radius-md); font-weight: bold; font-size: 18px; margin-bottom: 25px; display: inline-flex; align-items: center; gap: 10px;">
            <svg width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 14h-2v-2h2v2zm0-4h-2V7h2v5z"/>
            </svg>
            Solicitud Aprobada - En Espera de Registro
        </div>

        <div class="card" style="text-align: left; padding: 20px; background: #fffbeb; border: 1px solid #fde68a;">
            <p style="font-size: 14px; color: #78350f; margin: 0;">
                El Director de Carrera <strong>aprobó</strong> su solicitud extemporánea. El trámite se encuentra actualmente en la bandeja de **Kardex (Lic. Maria Choque)** para el registro de las materias en la base de datos central.
            </p>
        </div>

    <?php endif; ?>

    <p style="color: var(--text-muted); font-size: 13px; margin-top: 30px;">
        Haga clic en <strong>Finalizar Trámite</strong> para completar y archivar formalmente este proceso.
    </p>
</div>
