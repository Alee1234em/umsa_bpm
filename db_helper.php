<?php
/**
 * DB Helper - Manejo de persistencia de datos en archivos JSON
 * UMSA BPM Engine
 */

define('DATA_DIR', __DIR__ . '/data/');

function read_json($file) {
    $path = DATA_DIR . $file . '.json';
    if (!file_exists($path)) {
        return [];
    }
    $content = file_get_contents($path);
    return json_decode($content, true) ?: [];
}

function write_json($file, $data) {
    $path = DATA_DIR . $file . '.json';
    $content = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    return file_put_contents($path, $content) !== false;
}

// --- Consultas sobre PROCESO ---
function get_proceso($codFlujo, $codProceso) {
    $procesos = read_json('proceso');
    foreach ($procesos as $p) {
        if ($p['codFlujo'] === $codFlujo && $p['codProceso'] === $codProceso) {
            return $p;
        }
    }
    return null;
}

function get_procesos_flujo($codFlujo) {
    $procesos = read_json('proceso');
    $res = [];
    foreach ($procesos as $p) {
        if ($p['codFlujo'] === $codFlujo) {
            $res[] = $p;
        }
    }
    // Ordenar por codProceso
    usort($res, function($a, $b) {
        return strcmp($a['codProceso'], $b['codProceso']);
    });
    return $res;
}

// --- Consultas sobre SEGUIMIENTO ---
function get_seguimiento($nroTramite, $codFlujo, $codProceso) {
    $seg = read_json('seguimiento');
    foreach ($seg as $s) {
        if ((int)$s['nroTramite'] === (int)$nroTramite && $s['codFlujo'] === $codFlujo && $s['codProceso'] === $codProceso) {
            return $s;
        }
    }
    return null;
}

function get_seguimiento_usuario($usuario, $filtro_flujo = 'Todos') {
    $seg = read_json('seguimiento');
    $res = [];
    foreach ($seg as $s) {
        if ($s['codUsuario'] === $usuario && $s['fechafin'] === null) {
            if ($filtro_flujo === 'Todos' || $s['codFlujo'] === $filtro_flujo) {
                $res[] = $s;
            }
        }
    }
    return $res;
}

function get_historial_tramite($nroTramite) {
    $seg = read_json('seguimiento');
    $res = [];
    foreach ($seg as $s) {
        if ((int)$s['nroTramite'] === (int)$nroTramite) {
            $res[] = $s;
        }
    }
    // Ordenar por fecha o secuencia
    return $res;
}

function get_max_nro_tramite() {
    $seg = read_json('seguimiento');
    $max = 99; // Comenzar en 100
    foreach ($seg as $s) {
        if ((int)$s['nroTramite'] > $max) {
            $max = (int)$s['nroTramite'];
        }
    }
    return $max;
}

function save_seguimiento($record) {
    $seg = read_json('seguimiento');
    $found = false;
    foreach ($seg as $i => $s) {
        if ((int)$s['nroTramite'] === (int)$record['nroTramite'] && 
            $s['codFlujo'] === $record['codFlujo'] && 
            $s['codProceso'] === $record['codProceso']) {
            $seg[$i] = array_merge($s, $record);
            $found = true;
            break;
        }
    }
    if (!$found) {
        $seg[] = $record;
    }
    return write_json('seguimiento', $seg);
}

function delete_seguimiento($nroTramite, $codFlujo, $codProceso) {
    $seg = read_json('seguimiento');
    $newSeg = [];
    foreach ($seg as $s) {
        if ((int)$s['nroTramite'] === (int)$nroTramite && 
            $s['codFlujo'] === $codFlujo && 
            $s['codProceso'] === $codProceso) {
            continue; // Se elimina
        }
        $newSeg[] = $s;
    }
    return write_json('seguimiento', $newSeg);
}

// --- Consultas sobre ALUMNO ---
function get_alumno_por_ci($ci) {
    $alumnos = read_json('alumno');
    foreach ($alumnos as $a) {
        if ($a['ci'] === $ci) {
            return $a;
        }
    }
    return null;
}

function get_alumno_por_nombre($nombre) {
    $alumnos = read_json('alumno');
    foreach ($alumnos as $a) {
        if (strcasecmp($a['nombre'], $nombre) === 0) {
            return $a;
        }
    }
    return null;
}

// --- Consultas sobre TRAMITE CERT (Certificado de Notas) ---
function get_tramite_cert($nroTramite) {
    $tramites = read_json('tramite_cert');
    foreach ($tramites as $t) {
        if ((int)$t['nroTramite'] === (int)$nroTramite) {
            return $t;
        }
    }
    return null;
}

function save_tramite_cert($record) {
    $tramites = read_json('tramite_cert');
    $found = false;
    foreach ($tramites as $i => $t) {
        if ((int)$t['nroTramite'] === (int)$record['nroTramite']) {
            $tramites[$i] = array_merge($t, $record);
            $found = true;
            break;
        }
    }
    if (!$found) {
        $tramites[] = $record;
    }
    return write_json('tramite_cert', $tramites);
}

// --- Consultas sobre TRAMITE INSC (Inscripción Extemporánea) ---
function get_tramite_insc($nroTramite) {
    $tramites = read_json('tramite_insc');
    foreach ($tramites as $t) {
        if ((int)$t['nroTramite'] === (int)$nroTramite) {
            return $t;
        }
    }
    return null;
}

function save_tramite_insc($record) {
    $tramites = read_json('tramite_insc');
    $found = false;
    foreach ($tramites as $i => $t) {
        if ((int)$t['nroTramite'] === (int)$record['nroTramite']) {
            $tramites[$i] = array_merge($t, $record);
            $found = true;
            break;
        }
    }
    if (!$found) {
        $tramites[] = $record;
    }
    return write_json('tramite_insc', $tramites);
}
?>
