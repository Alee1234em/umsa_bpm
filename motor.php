<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include_once "db_helper.php";
$currentUser = $_SESSION["usuario"] ?? 'Juan';

$codFlujo = $_GET["codFlujo"] ?? '';
$codProceso = $_GET["codProceso"] ?? '';
$nroTramite = $_GET["nroTramite"] ?? '';

// Obtener información del proceso actual
$procesoActual = get_proceso($codFlujo, $codProceso);

if (!$procesoActual) {
    die("Error: No se encontró la definición para el proceso $codProceso del flujo $codFlujo en el archivo de procesos.");
}

$codProcesoSiguiente = $procesoActual['codProcesoSiguiente'];
$archivo = $procesoActual['pantalla'];
$nombreProcesoActual = $procesoActual['nombreProceso'];

// Obtener todos los procesos del flujo para renderizar el timeline
$todosProcesos = get_procesos_flujo($codFlujo);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Motor de Flujo - UMSA BPM</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container" style="max-width: 760px;">
        
        <!-- Botón Volver a la Bandeja -->
        <div style="margin-bottom: 20px;">
            <a href="bentrada.php" class="btn btn-secondary" style="padding: 6px 12px; font-size: 13px; display: inline-flex; align-items: center; gap: 6px; background-color: #475569;">
                ← Volver a la Bandeja
            </a>
        </div>

        <!-- Ficha de Información del Trámite -->
        <div class="card" style="padding: 20px; background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); color: white; border: none;">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                <div>
                    <span style="font-size: 11px; text-transform: uppercase; letter-spacing: 1px; color: #94a3b8;">Trámite Académico</span>
                    <h2 style="color: white; margin: 2px 0 0 0; font-size: 22px; font-family: 'Outfit';">
                        #<?php echo htmlspecialchars($nroTramite); ?> 
                        <span style="font-size: 14px; font-weight: normal; color: #cbd5e1; margin-left: 10px;">
                            (<?php echo $codFlujo === 'CERT' ? 'Certificado de Notas' : 'Inscripción Extemporánea'; ?>)
                        </span>
                    </h2>
                </div>
                <div>
                    <span class="badge badge-blue" style="font-size: 12px; padding: 6px 12px; background: #2563eb; color: white;">
                        Paso Activo: <?php echo htmlspecialchars($codProceso); ?>
                    </span>
                </div>
            </div>
        </div>

        <!-- LÍNEA DE TIEMPO DINÁMICA (BPM Progress Timeline) -->
        <div class="card" style="padding: 20px 10px;">
            <div class="stepper-timeline">
                <?php 
                $isCurrentFound = false;
                foreach ($todosProcesos as $index => $p): 
                    $stepClass = '';
                    if ($p['codProceso'] === $codProceso) {
                        $stepClass = 'active';
                        $isCurrentFound = true;
                    } elseif (!$isCurrentFound) {
                        $stepClass = 'completed';
                    }
                ?>
                    <div class="step-node <?php echo $stepClass; ?>">
                        <div class="step-circle">
                            <?php if ($stepClass === 'completed'): ?>
                                ✓
                            <?php else: ?>
                                <?php echo ($index + 1); ?>
                            <?php endif; ?>
                        </div>
                        <div class="step-label">
                            <?php echo htmlspecialchars($p['nombreProceso']); ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Tarjeta de Contenido / Formulario -->
        <div class="card">
            <h3 class="card-title">
                <span><?php echo htmlspecialchars($nombreProcesoActual); ?></span>
                <span style="font-size: 12px; font-weight: normal; color: var(--text-muted);">
                    Responsable: 
                    <strong>
                        <?php 
                        $rol = $procesoActual['codRol'];
                        if ($rol === 'E') echo "Estudiante";
                        elseif ($rol === 'K') echo "Kardex";
                        elseif ($rol === 'D') echo "Director";
                        else echo $rol;
                        ?>
                    </strong>
                </span>
            </h3>

            <!-- Formulario dinámico del paso -->
            <form action="controlador.php" method="GET">
                
                <div style="margin-bottom: 25px;">
                    <?php
                    // Incluir dinámicamente la interfaz correspondiente
                    if (!empty($archivo) && file_exists($archivo)) {
                        include $archivo;
                    } else {
                        echo "<div class='empty-state'>Error: No se encontró la interfaz física '$archivo'.</div>";
                    }
                    ?>
                </div>

                <!-- Propagación de datos de control del Workflow -->
                <input type="hidden" name="codFlujo" value="<?php echo htmlspecialchars($codFlujo); ?>">
                <input type="hidden" name="codProceso" value="<?php echo htmlspecialchars($codProceso); ?>">
                <input type="hidden" name="codProcesoSiguiente" value="<?php echo htmlspecialchars($codProcesoSiguiente); ?>">
                <input type="hidden" name="nroTramite" value="<?php echo htmlspecialchars($nroTramite); ?>">
                <input type="hidden" name="archivo" value="<?php echo htmlspecialchars($archivo); ?>">

                <!-- Botones de Navegación -->
                <div class="buttons-row" style="display: flex; justify-content: space-between; border-top: 1px solid var(--border); padding-top: 20px;">
                    
                    <!-- Botón Anterior: Ocultar en el primer paso -->
                    <?php if ($todosProcesos[0]['codProceso'] !== $codProceso): ?>
                        <input type="submit" name="Anterior" value="← Anterior" class="btn btn-secondary" style="padding: 10px 24px; font-family: 'Outfit';">
                    <?php else: ?>
                        <div></div> <!-- Espaciador si no hay botón anterior -->
                    <?php endif; ?>

                    <input type="submit" name="Siguiente" value="<?php echo empty($codProcesoSiguiente) ? 'Finalizar Trámite ✓' : 'Siguiente →'; ?>" class="btn btn-primary" style="padding: 10px 28px; font-family: 'Outfit';">
                </div>

            </form>
        </div>

    </div>
</body>
</html>
