<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include_once "db_helper.php";

// Cambiar de usuario/rol si se solicita
if (isset($_GET['switch_user'])) {
    $_SESSION["usuario"] = $_GET['switch_user'];
}

// Usuario por defecto
if (!isset($_SESSION["usuario"])) {
    $_SESSION["usuario"] = "Juan";
}

$currentUser = $_SESSION["usuario"];

// Obtener detalles del usuario si es alumno
$alumno = get_alumno_por_nombre($currentUser);

// LÓGICA: Iniciar nuevo trámite
if (isset($_GET['action']) && $_GET['action'] == 'nuevo' && isset($_GET['flujo'])) {
    $flujo_nuevo = $_GET['flujo'];
    
    // Obtener el primer paso del flujo
    $procesos = get_procesos_flujo($flujo_nuevo);
    $first_proc = isset($procesos[0]) ? $procesos[0]['codProceso'] : 'P1';
    
    // Generar un nuevo nroTramite correlativo único
    $nuevo_nro = get_max_nro_tramite() + 1;
    
    // Configurar registro inicial en seguimiento
    $nuevo_registro = [
        "nroTramite" => $nuevo_nro,
        "codFlujo" => $flujo_nuevo,
        "codProceso" => $first_proc,
        "codUsuario" => $currentUser,
        "fechaini" => date('Y-m-d'),
        "fechafin" => null
    ];
    
    if (save_seguimiento($nuevo_registro)) {
        header("Location: motor.php?codFlujo=$flujo_nuevo&codProceso=$first_proc&nroTramite=$nuevo_nro");
        exit();
    }
}

// Filtro de Flujos en la bandeja
$filtro_flujo = $_GET['filtro_flujo'] ?? 'Todos';

// Obtener tareas pendientes del usuario activo
$pendientes = get_seguimiento_usuario($currentUser, $filtro_flujo);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bandeja de Entrada - UMSA BPM Engine</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container">
        
        <!-- Banner Principal -->
        <div class="header-banner">
            <h1>Plataforma de Trámites Digitales</h1>
            <p>Universidad Mayor de San Andrés (UMSA) | Facultad de Ciencias Puras y Naturales</p>
        </div>

        <!-- Panel de Control de Sesión/Roles -->
        <div class="user-panel">
            <div>
                <div class="active-label">Usuario Conectado:</div>
                <div class="active-user-name">
                    <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24" style="vertical-align: middle;">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z"/>
                    </svg>
                    <?php 
                    if ($currentUser === 'Juan') echo "Juan Perez (Estudiante)";
                    elseif ($currentUser === 'Maria') echo "Lic. Maria Choque (Kardex)";
                    elseif ($currentUser === 'Hugo') echo "Dr. Hugo Gomez (Director)";
                    else echo htmlspecialchars($currentUser);
                    ?>
                </div>
            </div>
            <div>
                <div class="active-label" style="text-align: right; margin-bottom: 5px;">Cambiar Rol de Simulación:</div>
                <div class="role-buttons">
                    <a href="bentrada.php?switch_user=Juan" class="btn-role <?php echo $currentUser == 'Juan' ? 'active' : ''; ?>">
                        Estudiante (Juan)
                    </a>
                    <a href="bentrada.php?switch_user=Maria" class="btn-role <?php echo $currentUser == 'Maria' ? 'active' : ''; ?>">
                        Kardex (Maria)
                    </a>
                    <a href="bentrada.php?switch_user=Hugo" class="btn-role <?php echo $currentUser == 'Hugo' ? 'active' : ''; ?>">
                        Director (Hugo)
                    </a>
                </div>
            </div>
        </div>

        <!-- Cuerpo del Dashboard -->
        <div class="dashboard-grid">
            
            <!-- Panel Izquierdo: Bandeja de Pendientes -->
            <div>
                <div class="card" style="min-height: 280px;">
                    <div class="card-title">
                        <span>Trámites Pendientes por Atender</span>
                        <span class="badge badge-blue"><?php echo count($pendientes); ?> tareas</span>
                    </div>

                    <!-- Filtros Rápidos -->
                    <div style="display: flex; gap: 8px; margin-bottom: 20px; flex-wrap: wrap;">
                        <a href="bentrada.php?filtro_flujo=Todos" class="btn-role <?php echo $filtro_flujo == 'Todos' ? 'active' : ''; ?>" style="font-size:11px; padding: 4px 12px;">Todos</a>
                        <a href="bentrada.php?filtro_flujo=CERT" class="btn-role <?php echo $filtro_flujo == 'CERT' ? 'active' : ''; ?>" style="font-size:11px; padding: 4px 12px;">Certificados (CERT)</a>
                        <a href="bentrada.php?filtro_flujo=INSC" class="btn-role <?php echo $filtro_flujo == 'INSC' ? 'active' : ''; ?>" style="font-size:11px; padding: 4px 12px;">Inscripciones (INSC)</a>
                    </div>

                    <?php if (count($pendientes) > 0): ?>
                        <table style="width:100%">
                            <thead>
                                <tr>
                                    <th>Trámite</th>
                                    <th>Tipo de Flujo</th>
                                    <th>Paso</th>
                                    <th>Fecha Inicio</th>
                                    <th style="text-align: right;">Operación</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pendientes as $p): ?>
                                    <?php 
                                    $proc_info = get_proceso($p['codFlujo'], $p['codProceso']);
                                    $nombre_proc = $proc_info ? $proc_info['nombreProceso'] : $p['codProceso'];
                                    ?>
                                    <tr>
                                        <td style="font-weight: 700; color: #1e293b;">#<?php echo $p['nroTramite']; ?></td>
                                        <td>
                                            <span class="badge <?php echo $p['codFlujo'] == 'CERT' ? 'badge-blue' : 'badge-purple'; ?>">
                                                <?php echo $p['codFlujo'] == 'CERT' ? 'Certificado de Notas' : 'Inscripción Extemp.'; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div style="font-size: 13px; font-weight: 500;"><?php echo $nombre_proc; ?></div>
                                            <span style="font-size: 11px; color: var(--text-muted);"><?php echo $p['codProceso']; ?></span>
                                        </td>
                                        <td style="color: var(--text-muted); font-size:13px;"><?php echo $p['fechaini']; ?></td>
                                        <td style="text-align: right;">
                                            <a href="motor.php?codFlujo=<?php echo urlencode($p['codFlujo']); ?>&codProceso=<?php echo urlencode($p['codProceso']); ?>&nroTramite=<?php echo urlencode($p['nroTramite']); ?>" class="btn btn-primary" style="padding: 6px 12px; font-size: 12px;">
                                                Procesar
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <div class="empty-state">
                            No tienes trámites asignados que requieran tu atención en este momento.
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Panel Derecho: Iniciar Procesos (Solo Estudiantes) -->
            <div>
                <div class="card">
                    <div class="card-title">Iniciar Trámite</div>
                    
                    <?php if ($currentUser === 'Juan'): ?>
                        <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 20px;">
                            Selecciona uno de los siguientes procesos autorizados para iniciar una solicitud como estudiante:
                        </p>
                        <div class="flow-initiators">
                            <a href="bentrada.php?action=nuevo&flujo=CERT" class="btn-start-flow">
                                <div>
                                    <strong>Certificado de Notas</strong>
                                    <div style="font-weight: normal; font-size: 11px; margin-top:2px; color: var(--text-muted);">Emisión de calificaciones oficiales</div>
                                </div>
                                <span>Iniciar →</span>
                            </a>
                            <a href="bentrada.php?action=nuevo&flujo=INSC" class="btn-start-flow">
                                <div>
                                    <strong>Inscripción Extemporánea</strong>
                                    <div style="font-weight: normal; font-size: 11px; margin-top:2px; color: var(--text-muted);">Inscripción fuera de término</div>
                                </div>
                                <span>Iniciar →</span>
                            </a>
                        </div>
                    <?php else: ?>
                        <div style="text-align: center; padding: 20px 10px; color: var(--text-muted); font-size: 13px;">
                            <svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="margin: 0 auto 10px auto; display: block; opacity: 0.6;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m0 0v2m0-2h2m-2 0H8m13 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Los nuevos trámites deben ser iniciados por un <strong>Estudiante</strong>. Cambia tu rol para iniciar solicitudes.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
        </div>

        <!-- SECCIÓN: Visualizador interactivo de Modelos BPM -->
        <div class="bpm-diagram-container">
            <h2 style="color: white; margin-top: 0; font-size: 20px; font-family: 'Outfit';">Visualizador de Modelos BPM del Sistema</h2>
            <p style="color: #94a3b8; font-size: 13px; margin-bottom: 25px;">
                Selecciona una pestaña para ver el modelado completo del proceso, incluyendo entradas, validaciones, roles asignados y resultados.
            </p>

            <div class="bpm-tab-nav">
                <button class="bpm-tab-btn active" onclick="switchBpmTab('cert-bpm-diagram', this)">
                    Emisión de Certificado (CERT)
                </button>
                <button class="bpm-tab-btn" onclick="switchBpmTab('insc-bpm-diagram', this)">
                    Inscripción Extemporánea (INSC)
                </button>
            </div>

            <!-- Flujograma CERT -->
            <div id="cert-bpm-diagram" class="bpm-tab-content">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; align-items: start;">
                    
                    <!-- Lado Izquierdo: Flujo visual -->
                    <!-- Lado Izquierdo: Flujo visual SVG -->
                    <div class="bpm-flowchart-svg" style="background: #0f172a; padding: 15px; border-radius: 8px; border: 1px solid #1e293b; overflow-x: auto; display: flex; justify-content: center;">
                        <svg width="100%" height="340" viewBox="0 0 650 340" xmlns="http://www.w3.org/2000/svg" style="display: block; min-width: 600px;">
                            <defs>
                                <marker id="arrow-blue" viewBox="0 0 10 10" refX="6" refY="5" markerWidth="6" markerHeight="6" orient="auto-start-reverse">
                                    <path d="M 0 1.5 L 8 5 L 0 8.5 z" fill="#3b82f6" />
                                </marker>
                            </defs>

                            <!-- Conexiones/Líneas de Flujo -->
                            <path d="M 390 210 L 370 210" stroke="#475569" stroke-width="1.5" fill="none" marker-end="url(#arrow-blue)" />
                            <path d="M 270 180 L 270 100" stroke="#475569" stroke-width="1.5" fill="none" marker-end="url(#arrow-blue)" />
                            <path d="M 210 100 L 210 125 L 95 125 L 95 210 L 170 210" stroke="#475569" stroke-width="1.5" fill="none" marker-end="url(#arrow-blue)" />
                            <path d="M 400 70 L 555 70 L 555 180" stroke="#475569" stroke-width="1.5" fill="none" marker-end="url(#arrow-blue)" />
                            <path d="M 555 240 L 555 285" stroke="#475569" stroke-width="1.5" fill="none" marker-end="url(#arrow-blue)" />

                            <!-- Nodo P2: Validación de Pago y Emisión (Kardex) -->
                            <rect x="170" y="40" width="230" height="60" fill="#1e293b" stroke="#334155" stroke-width="1.5" rx="6" />
                            <text x="285" y="62" fill="#f8fafc" font-family="Outfit, sans-serif" font-size="12" font-weight="bold" text-anchor="middle">P2: Validación de Pago y Emisión</text>
                            <text x="285" y="82" fill="#94a3b8" font-family="Inter, sans-serif" font-size="11" text-anchor="middle">- Kardex -</text>

                            <!-- Nodo P1: Solicitud de Certificado (Estudiante) -->
                            <rect x="170" y="180" width="200" height="60" fill="#1e293b" stroke="#334155" stroke-width="1.5" rx="6" />
                            <text x="270" y="202" fill="#f8fafc" font-family="Outfit, sans-serif" font-size="12" font-weight="bold" text-anchor="middle">P1: Solicitud de Certificado</text>
                            <text x="270" y="222" fill="#94a3b8" font-family="Inter, sans-serif" font-size="11" text-anchor="middle">- Estudiante -</text>

                            <!-- Nodo P3: Descarga y Cierre (Estudiante) -->
                            <rect x="470" y="180" width="170" height="60" fill="#1e293b" stroke="#334155" stroke-width="1.5" rx="6" />
                            <text x="555" y="202" fill="#f8fafc" font-family="Outfit, sans-serif" font-size="12" font-weight="bold" text-anchor="middle">P3: Descarga y Cierre</text>
                            <text x="555" y="222" fill="#94a3b8" font-family="Inter, sans-serif" font-size="11" text-anchor="middle">- Estudiante -</text>

                            <!-- Nodo Inicio -->
                            <rect x="390" y="190" width="70" height="40" fill="#0f172a" stroke="#475569" stroke-width="1.5" rx="20" />
                            <text x="425" y="214" fill="#f8fafc" font-family="Outfit, sans-serif" font-size="12" font-weight="bold" text-anchor="middle">Inicio</text>

                            <!-- Nodo Fin -->
                            <rect x="520" y="285" width="70" height="40" fill="#0f172a" stroke="#475569" stroke-width="1.5" rx="20" />
                            <text x="555" y="309" fill="#f8fafc" font-family="Outfit, sans-serif" font-size="12" font-weight="bold" text-anchor="middle">Fin</text>

                            <!-- Transiciones/Etiquetas -->
                            <rect x="180" y="125" width="180" height="22" fill="#1e293b" stroke="#334155" stroke-width="1" rx="4" />
                            <text x="270" y="139" fill="#cbd5e1" font-family="Inter, sans-serif" font-size="9" text-anchor="middle">Ingreso de datos y comprobante</text>

                            <rect x="15" y="113" width="140" height="22" fill="#1e293b" stroke="#334155" stroke-width="1" rx="4" />
                            <text x="85" y="127" fill="#cbd5e1" font-family="Inter, sans-serif" font-size="9" text-anchor="middle">Rechazado: Observaciones</text>

                            <rect x="475" y="110" width="160" height="22" fill="#1e293b" stroke="#334155" stroke-width="1" rx="4" />
                            <text x="555" y="124" fill="#cbd5e1" font-family="Inter, sans-serif" font-size="9" text-anchor="middle">Aprobado: Firma y Generación</text>
                        </svg>
                    </div>

                    <!-- Lado Derecho: Metadatos del Negocio -->
                    <div style="background: #1e293b; padding: 20px; border-radius: var(--radius-sm); border: 1px solid #334155;">
                        <h3 style="color: white; margin-top:0; font-size:16px;">Detalles Técnicos y de Negocio (BPM)</h3>
                        <table style="width: 100%; font-size: 13px; color: #cbd5e1;">
                            <tr>
                                <td style="font-weight:bold; border-bottom: 1px solid #334155; padding: 8px 0; color: #f8fafc; width: 35%;">Código Flujo:</td>
                                <td style="border-bottom: 1px solid #334155; padding: 8px 0;">CERT</td>
                            </tr>
                            <tr>
                                <td style="font-weight:bold; border-bottom: 1px solid #334155; padding: 8px 0; color: #f8fafc;">Regla de Negocio:</td>
                                <td style="border-bottom: 1px solid #334155; padding: 8px 0;">Todo certificado requiere validación de caja en Kardex antes de emitir firma digital.</td>
                            </tr>
                            <tr>
                                <td style="font-weight:bold; border-bottom: 1px solid #334155; padding: 8px 0; color: #f8fafc;">Validación Clave:</td>
                                <td style="border-bottom: 1px solid #334155; padding: 8px 0;">El estudiante debe existir en el padrón (`alumno.json`) con su CI correspondiente.</td>
                            </tr>
                            <tr>
                                <td style="font-weight:bold; border-bottom: 1px solid #334155; padding: 8px 0; color: #f8fafc;">Persistencia JSON:</td>
                                <td style="border-bottom: 1px solid #334155; padding: 8px 0;">`tramite_cert.json`</td>
                            </tr>
                            <tr>
                                <td style="font-weight:bold; padding: 8px 0; color: #f8fafc;">Actores:</td>
                                <td style="padding: 8px 0;">Estudiante (Juan) y Kardex (Maria).</td>
                            </tr>
                        </table>
                    </div>

                </div>
            </div>

            <!-- Flujograma INSC -->
            <div id="insc-bpm-diagram" class="bpm-tab-content" style="display: none;">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; align-items: start;">
                    
                    <!-- Lado Izquierdo: Flujo visual -->
                    <!-- Lado Izquierdo: Flujo visual SVG -->
                    <div class="bpm-flowchart-svg" style="background: #0f172a; padding: 15px; border-radius: 8px; border: 1px solid #1e293b; overflow-x: auto; display: flex; justify-content: center;">
                        <svg width="100%" height="700" viewBox="0 0 650 700" xmlns="http://www.w3.org/2000/svg" style="display: block; min-width: 600px;">
                            <defs>
                                <marker id="arrow-blue2" viewBox="0 0 10 10" refX="6" refY="5" markerWidth="6" markerHeight="6" orient="auto-start-reverse">
                                    <path d="M 0 1.5 L 8 5 L 0 8.5 z" fill="#3b82f6" />
                                </marker>
                            </defs>

                            <!-- Conexiones/Líneas de Flujo -->
                            <path d="M 300 60 L 300 100" stroke="#475569" stroke-width="1.5" fill="none" marker-end="url(#arrow-blue2)" />
                            <path d="M 300 160 L 300 250" stroke="#475569" stroke-width="1.5" fill="none" marker-end="url(#arrow-blue2)" />
                            <path d="M 420 280 L 460 280 L 460 400" stroke="#475569" stroke-width="1.5" fill="none" marker-end="url(#arrow-blue2)" />
                            <path d="M 180 280 L 115 280 L 115 570 L 190 570" stroke="#475569" stroke-width="1.5" fill="none" marker-end="url(#arrow-blue2)" />
                            <path d="M 460 460 L 460 570 L 410 570" stroke="#475569" stroke-width="1.5" fill="none" marker-end="url(#arrow-blue2)" />
                            <path d="M 300 610 L 300 645" stroke="#475569" stroke-width="1.5" fill="none" marker-end="url(#arrow-blue2)" />

                            <!-- Nodo P1: Solicitud de Inscripción (Estudiante) -->
                            <rect x="180" y="100" width="240" height="60" fill="#1e293b" stroke="#334155" stroke-width="1.5" rx="6" />
                            <text x="300" y="122" fill="#f8fafc" font-family="Outfit, sans-serif" font-size="12" font-weight="bold" text-anchor="middle">P1: Solicitud de Inscripción</text>
                            <text x="300" y="142" fill="#94a3b8" font-family="Inter, sans-serif" font-size="11" text-anchor="middle">- Estudiante -</text>

                            <!-- Nodo P2: Evaluación Académica (Director de Carrera) -->
                            <rect x="180" y="250" width="240" height="60" fill="#1e293b" stroke="#334155" stroke-width="1.5" rx="6" />
                            <text x="300" y="272" fill="#f8fafc" font-family="Outfit, sans-serif" font-size="12" font-weight="bold" text-anchor="middle">P2: Evaluación Académica</text>
                            <text x="300" y="292" fill="#94a3b8" font-family="Inter, sans-serif" font-size="11" text-anchor="middle">- Director de Carrera -</text>

                            <!-- Nodo P3: Registro en Sistema (Kardex) -->
                            <rect x="340" y="400" width="240" height="60" fill="#1e293b" stroke="#334155" stroke-width="1.5" rx="6" />
                            <text x="460" y="422" fill="#f8fafc" font-family="Outfit, sans-serif" font-size="12" font-weight="bold" text-anchor="middle">P3: Registro en Sistema</text>
                            <text x="460" y="442" fill="#94a3b8" font-family="Inter, sans-serif" font-size="11" text-anchor="middle">- Kardex -</text>

                            <!-- Nodo P4: Notificación y Cierre (Estudiante) -->
                            <rect x="190" y="550" width="220" height="60" fill="#1e293b" stroke="#334155" stroke-width="1.5" rx="6" />
                            <text x="300" y="572" fill="#f8fafc" font-family="Outfit, sans-serif" font-size="12" font-weight="bold" text-anchor="middle">P4: Notificación y Cierre</text>
                            <text x="300" y="592" fill="#94a3b8" font-family="Inter, sans-serif" font-size="11" text-anchor="middle">- Estudiante -</text>

                            <!-- Nodo Inicio -->
                            <rect x="265" y="20" width="70" height="40" fill="#0f172a" stroke="#475569" stroke-width="1.5" rx="20" />
                            <text x="300" y="44" fill="#f8fafc" font-family="Outfit, sans-serif" font-size="12" font-weight="bold" text-anchor="middle">Inicio</text>

                            <!-- Nodo Fin -->
                            <rect x="265" y="645" width="70" height="40" fill="#0f172a" stroke="#475569" stroke-width="1.5" rx="20" />
                            <text x="300" y="669" fill="#f8fafc" font-family="Outfit, sans-serif" font-size="12" font-weight="bold" text-anchor="middle">Fin</text>

                            <!-- Transiciones/Etiquetas -->
                            <rect x="210" y="195" width="180" height="22" fill="#1e293b" stroke="#334155" stroke-width="1" rx="4" />
                            <text x="300" y="209" fill="#cbd5e1" font-family="Inter, sans-serif" font-size="9" text-anchor="middle">Materias y Justificación</text>

                            <rect x="385" y="325" width="150" height="22" fill="#1e293b" stroke="#334155" stroke-width="1" rx="4" />
                            <text x="460" y="339" fill="#cbd5e1" font-family="Inter, sans-serif" font-size="9" text-anchor="middle">Aprobado: Autorizar</text>

                            <rect x="20" y="415" width="190" height="22" fill="#1e293b" stroke="#334155" stroke-width="1" rx="4" />
                            <text x="115" y="429" fill="#cbd5e1" font-family="Inter, sans-serif" font-size="9" text-anchor="middle">Rechazado: Fin con observaciones</text>

                            <rect x="395" y="505" width="130" height="22" fill="#1e293b" stroke="#334155" stroke-width="1" rx="4" />
                            <text x="460" y="519" fill="#cbd5e1" font-family="Inter, sans-serif" font-size="9" text-anchor="middle">Materia Registrada</text>
                        </svg>
                    </div>

                    <!-- Lado Derecho: Metadatos del Negocio -->
                    <div style="background: #1e293b; padding: 20px; border-radius: var(--radius-sm); border: 1px solid #334155;">
                        <h3 style="color: white; margin-top:0; font-size:16px;">Detalles Técnicos y de Negocio (BPM)</h3>
                        <table style="width: 100%; font-size: 13px; color: #cbd5e1;">
                            <tr>
                                <td style="font-weight:bold; border-bottom: 1px solid #334155; padding: 8px 0; color: #f8fafc; width: 35%;">Código Flujo:</td>
                                <td style="border-bottom: 1px solid #334155; padding: 8px 0;">INSC</td>
                            </tr>
                            <tr>
                                <td style="font-weight:bold; border-bottom: 1px solid #334155; padding: 8px 0; color: #f8fafc;">Regla de Negocio:</td>
                                <td style="border-bottom: 1px solid #334155; padding: 8px 0;">Solo el Director de Carrera puede autorizar admisiones extemporáneas. Kardex no puede inscribir sin aval formal del Director.</td>
                            </tr>
                            <tr>
                                <td style="font-weight:bold; border-bottom: 1px solid #334155; padding: 8px 0; color: #f8fafc;">Validación Clave:</td>
                                <td style="border-bottom: 1px solid #334155; padding: 8px 0;">Se comprueba disponibilidad de cupos académicos y coherencia de prerrequisitos de materias.</td>
                            </tr>
                            <tr>
                                <td style="font-weight:bold; border-bottom: 1px solid #334155; padding: 8px 0; color: #f8fafc;">Persistencia JSON:</td>
                                <td style="border-bottom: 1px solid #334155; padding: 8px 0;">`tramite_insc.json`</td>
                            </tr>
                            <tr>
                                <td style="font-weight:bold; padding: 8px 0; color: #f8fafc;">Actores:</td>
                                <td style="padding: 8px 0;">Estudiante (Juan), Director (Hugo) y Kardex (Maria).</td>
                            </tr>
                        </table>
                    </div>

                </div>
            </div>

        </div>

    </div>

    <script>
        function switchBpmTab(tabId, btnElement) {
            // Ocultar todos los contenidos de pestaña
            document.querySelectorAll('.bpm-tab-content').forEach(function(content) {
                content.style.display = 'none';
            });
            // Quitar clase activa de todos los botones
            document.querySelectorAll('.bpm-tab-btn').forEach(function(btn) {
                btn.classList.remove('active');
            });
            // Mostrar pestaña seleccionada y activar botón
            document.getElementById(tabId).style.display = 'block';
            btnElement.classList.add('active');
        }
    </script>
</body>
</html>
