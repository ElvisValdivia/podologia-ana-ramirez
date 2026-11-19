<?php
// Vista de Impresión Oficial y Ficha Clínica Digital (Eco-Friendly / Cero Papel)
// Podología Ana Ramírez • Software desarrollado por KilaCode
$esFichaEnBlanco = !empty($esFichaEnBlanco) || empty($paciente['id']);

$telPaciente = preg_replace('/[^0-9]/', '', $paciente['telefono'] ?? '');
if (strlen($telPaciente) === 9 && !str_starts_with($telPaciente, '51')) {
    $telPaciente = '51' . $telPaciente;
}
$expedienteCod = $esFichaEnBlanco ? '#000000 (MODELO OFICIAL)' : ('#' . str_pad((string)$paciente['id'], 6, '0', STR_PAD_LEFT));
$mensajeWa = urlencode("Estimado(a) " . ($paciente['nombre'] ?? '') . " " . ($paciente['apellido'] ?? '') . ", le saludamos cordialmente de Podología Ana Ramírez.\n\nLe compartimos su Ficha Clínica Oficial (" . $expedienteCod . "). Con nuestro compromiso ecológico 'Cero Papel', mantenemos su expediente disponible en formato digital para su consulta y cuidado podológico.\n\n¡Gracias por confiar en nosotros!");
$emailAsunto = urlencode("Ficha Clínica Podológica " . $expedienteCod . " - " . ($paciente['nombre'] ?? '') . " " . ($paciente['apellido'] ?? ''));
$emailCuerpo = urlencode("Estimado(a) " . ($paciente['nombre'] ?? '') . ",\n\nAdjuntamos la referencia de su expediente clínico en Podología Ana Ramírez (" . $expedienteCod . ").\n\nCuidamos su salud podológica con tecnología y compromiso ambiental.\n\nPodología Ana Ramírez • Software por KilaCode");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $esFichaEnBlanco ? 'Modelo_Ficha_Clinica_Oficial' : ('Ficha_Clinica_' . e($paciente['documento_identidad']) . '_' . e($paciente['apellido'])) ?></title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background: white !important;
                color: black !important;
                font-size: 10pt;
                padding: 0 !important;
                margin: 0 !important;
            }
            .page-container {
                box-shadow: none !important;
                border: none !important;
                padding: 6mm 10mm !important;
                max-width: 100% !important;
                width: 100% !important;
            }
            @page {
                size: A4 portrait;
                margin: 8mm 10mm 10mm 10mm;
            }
            .watermark-bg {
                opacity: 0.04 !important;
            }
            .page-break-avoid {
                page-break-inside: avoid;
            }
        }
        .dotted-line {
            border-bottom: 1.5px dotted #94a3b8;
            height: 20px;
            width: 100%;
        }
    </style>
</head>
<body class="bg-slate-100 font-sans text-slate-800 antialiased p-3 sm:p-6">

    <!-- BARRA FLOTANTE DE ACCIONES ECOLÓGICAS Y DE IMPRESIÓN (NO SE IMPRIME) -->
    <div class="no-print max-w-4xl mx-auto mb-6 bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-lg flex flex-col md:flex-row items-center justify-between gap-4">
        <div class="flex items-center space-x-3.5">
            <div class="w-11 h-11 rounded-2xl bg-[#005B94] text-white flex items-center justify-center text-xl shadow-md">
                <i class="fa-solid fa-file-invoice text-[#2EC4B6]"></i>
            </div>
            <div>
                <div class="flex items-center space-x-2">
                    <h3 class="text-sm font-black text-slate-900">
                        <?= $esFichaEnBlanco ? 'Modelo de Ficha Clínica en Blanco (Formato Oficial)' : 'Ficha Clínica Digital & Flujo Ecológico' ?>
                    </h3>
                    <span class="px-2 py-0.5 text-[10px] font-bold rounded-full <?= $esFichaEnBlanco ? 'bg-blue-100 text-[#005B94]' : 'bg-emerald-100 text-emerald-800' ?>">
                        <?= $esFichaEnBlanco ? 'Plantilla Oficial' : 'Cero Papel 🌱' ?>
                    </span>
                </div>
                <p class="text-xs text-slate-500 mt-0.5">
                    <?= $esFichaEnBlanco ? 'Visualice el formato original sin rellenar o imprima hojas físicas de exploración podológica.' : 'Envíe la ficha directamente por WhatsApp o Correo para cuidar el medio ambiente, o guárdela en PDF.' ?>
                </p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <a href="<?= $esFichaEnBlanco ? 'index.php?accion=listado' : ('index.php?accion=detalle&id=' . $paciente['id']) ?>" 
               class="px-3.5 py-2 text-xs font-bold rounded-xl border border-slate-300 bg-white text-slate-700 hover:bg-slate-50 transition shadow-sm">
                <i class="fa-solid fa-arrow-left mr-1.5"></i> Volver
            </a>

            <?php if (!$esFichaEnBlanco && !empty($telPaciente)): ?>
                <a href="https://wa.me/<?= $telPaciente ?>?text=<?= $mensajeWa ?>" target="_blank" 
                   class="px-3.5 py-2 text-xs font-bold rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white shadow-sm transition flex items-center">
                    <i class="fa-brands fa-whatsapp mr-1.5 text-sm"></i> WhatsApp
                </a>
            <?php endif; ?>

            <?php if (!$esFichaEnBlanco && !empty($paciente['email'])): ?>
                <a href="mailto:<?= e($paciente['email']) ?>?subject=<?= $emailAsunto ?>&body=<?= $emailCuerpo ?>" 
                   class="px-3.5 py-2 text-xs font-bold rounded-xl bg-sky-600 hover:bg-sky-700 text-white shadow-sm transition flex items-center">
                    <i class="fa-regular fa-envelope mr-1.5 text-sm"></i> Correo
                </a>
            <?php endif; ?>

            <button onclick="window.print()" class="px-4 py-2 text-xs font-bold rounded-xl bg-[#005B94] hover:bg-[#0A2540] text-white shadow-md transition flex items-center">
                <i class="fa-solid fa-print mr-1.5 text-[#2EC4B6]"></i> <?= $esFichaEnBlanco ? 'Imprimir Ficha en Blanco' : 'Imprimir / PDF' ?>
            </button>
        </div>
    </div>

    <!-- DOCUMENTO MÉDICO FORMAL (HOJA A4) -->
    <div class="page-container relative max-w-4xl mx-auto bg-white p-6 sm:p-10 rounded-3xl border border-slate-200 shadow-xl overflow-hidden space-y-4">
        
        <!-- WATERMARK CENTRAL (SILUETA TENUE DEL PIE ANATÓMICO) -->
        <div class="watermark-bg pointer-events-none absolute inset-0 flex items-center justify-center opacity-[0.045] z-0 select-none">
            <svg class="w-[420px] h-[640px] text-slate-900 fill-current" viewBox="0 0 200 320">
                <!-- Gran silueta anatómica de pie para marca de agua -->
                <path d="M95 15 C85 15 78 22 78 35 C78 48 84 55 95 55 C106 55 112 48 112 35 C112 22 105 15 95 15 Z" />
                <path d="M120 25 C113 25 108 30 108 40 C108 50 113 56 120 56 C127 56 132 50 132 40 C132 30 127 25 120 25 Z" />
                <path d="M140 38 C135 38 130 42 130 50 C130 58 134 63 140 63 C146 63 150 58 150 50 C150 42 146 38 140 38 Z" />
                <path d="M158 55 C154 55 150 58 150 65 C150 72 153 76 158 76 C163 76 166 72 166 65 C166 58 163 55 158 55 Z" />
                <path d="M172 75 C169 75 166 78 166 84 C166 90 169 94 172 94 C175 94 178 90 178 84 C178 78 175 75 172 75 Z" />
                <path d="M85 65 C60 85 50 115 50 150 C50 190 62 230 65 260 C68 285 75 305 95 305 C115 305 125 285 128 260 C132 225 145 190 155 150 C165 110 155 80 135 65 C118 55 102 55 85 65 Z" />
            </svg>
        </div>

        <div class="relative z-10 space-y-4">

            <!-- ENCABEZADO OFICIAL -->
            <div class="border-b-2 border-[#005B94] pb-3 flex items-start justify-between">
                <div class="flex items-center space-x-3.5">
                    <div class="w-16 h-16 rounded-full border-2 border-[#2EC4B6] p-0.5 bg-[#0A2540] flex items-center justify-center shadow-sm">
                        <img src="assets/img/logo-badge.png" alt="Logo" class="w-full h-full object-contain">
                    </div>
                    <div>
                        <h1 class="text-xl font-black text-[#0A2540] tracking-tight">PODOLOGÍA ANA RAMÍREZ</h1>
                        <p class="text-[11px] font-bold text-[#005B94] uppercase tracking-wider">Centro Especializado de Salud Podológica & Biomecánica</p>
                        <p class="text-[10px] text-slate-500 mt-0.5">
                            Dra. Ana Ramírez • Registro Profesional: <span class="font-mono font-bold">POD-09823</span> • RUC: 10432854731<br>
                            Quiropodia Avanzada, Ortopodología, Tratamiento Láser y Pie de Riesgo
                        </p>
                    </div>
                </div>

                <div class="text-right">
                    <div class="inline-block bg-slate-50 border border-slate-300 px-3 py-1 rounded-xl text-center">
                        <div class="text-[9px] font-bold text-slate-500 uppercase tracking-widest">FICHA CLÍNICA N°</div>
                        <div class="text-base font-black font-mono text-[#005B94]"><?= $expedienteCod ?></div>
                    </div>
                    <div class="text-[9px] text-slate-400 mt-1 font-mono">
                        Emisión: <?= date('d/m/Y H:i') ?>
                    </div>
                </div>
            </div>

            <!-- TÍTULO DE LA FICHA -->
            <div class="text-center py-1">
                <h2 class="text-sm font-black tracking-widest uppercase text-slate-800 border-b-2 border-slate-900 inline-block px-4 pb-0.5">
                    MODELO DE FICHA CLÍNICA PODOLÓGICA
                </h2>
            </div>

            <!-- 1. DATOS FILIATORIOS Y LABORALES -->
            <div class="bg-slate-50/70 p-3 rounded-xl border border-slate-200 text-xs space-y-2">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                    <div class="sm:col-span-2">
                        <span class="font-bold text-slate-600">Nombre y Apellido:</span>
                        <span class="font-bold text-slate-900 ml-1 border-b border-slate-300 pb-0.5 inline-block w-[calc(100%-140px)]"><?= $esFichaEnBlanco ? '&nbsp;' : (e($paciente['apellido']) . ', ' . e($paciente['nombre'])) ?></span>
                    </div>
                    <div>
                        <span class="font-bold text-slate-600">Tel / Celular:</span>
                        <span class="font-mono font-bold text-slate-900 ml-1 border-b border-slate-300 pb-0.5 inline-block w-[calc(100%-90px)]"><?= $esFichaEnBlanco ? '&nbsp;' : e($paciente['telefono']) ?></span>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                    <div class="sm:col-span-2">
                        <span class="font-bold text-slate-600">Domicilio:</span>
                        <span class="text-slate-800 ml-1 border-b border-slate-300 pb-0.5 inline-block w-[calc(100%-80px)]"><?= $esFichaEnBlanco ? '&nbsp;' : (e($paciente['direccion'] ?: 'No registrado')) ?></span>
                    </div>
                    <div>
                        <span class="font-bold text-slate-600">Doc. Identidad:</span>
                        <span class="font-mono font-bold text-slate-900 ml-1 border-b border-slate-300 pb-0.5 inline-block w-[calc(100%-100px)]"><?= $esFichaEnBlanco ? '&nbsp;' : (e($paciente['tipo_documento']) . ' ' . e($paciente['documento_identidad'])) ?></span>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                    <div>
                        <span class="font-bold text-slate-600">Fecha de nacimiento:</span>
                        <span class="text-slate-800 font-mono ml-1 border-b border-slate-300 pb-0.5 inline-block w-[calc(100%-130px)]"><?= $esFichaEnBlanco ? '&nbsp;' : e($paciente['fecha_nacimiento']) ?></span>
                    </div>
                    <div>
                        <span class="font-bold text-slate-600">Ocupación habitual:</span>
                        <span class="text-slate-800 ml-1 border-b border-slate-300 pb-0.5 inline-block w-[calc(100%-120px)]"><?= $esFichaEnBlanco ? '&nbsp;' : (e($paciente['ocupacion'] ?: 'No especificada')) ?></span>
                    </div>
                    <div>
                        <span class="font-bold text-slate-600">Usa calzado de seguridad:</span>
                        <span class="font-bold ml-1 text-slate-700">
                            <?= $esFichaEnBlanco ? '<span class="text-slate-500 font-bold">SI  /  NO</span>' : ((stripos($paciente['tipo_calzado_habitual'] ?? '', 'seguridad') !== false || stripos($paciente['tipo_calzado_habitual'] ?? '', 'trabajo') !== false) ? 'SÍ' : 'NO') ?>
                        </span>
                    </div>
                </div>
            </div>

            <!-- 2. ANTECEDENTES PATOLÓGICOS Y CHECKLIST CLÍNICO -->
            <div class="text-xs">
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                    
                    <!-- Diabetes -->
                    <div class="p-2 rounded-lg border flex items-center justify-between <?= (!$esFichaEnBlanco && !empty($paciente['es_diabetico'])) ? 'bg-rose-50 border-rose-300 font-bold text-rose-900' : 'bg-slate-50 border-slate-200' ?>">
                        <span class="text-slate-700 font-bold">Diabetes:</span>
                        <span>
                            <?php if ($esFichaEnBlanco): ?>
                                <span class="text-slate-500 font-bold">SI  /  NO</span>
                            <?php else: ?>
                                <span class="font-bold <?= !empty($paciente['es_diabetico']) ? 'text-rose-700' : 'text-slate-400' ?>">
                                    <?= !empty($paciente['es_diabetico']) ? 'SI ('.e($paciente['tipo_diabetes'] ?: 'DM').')' : 'NO' ?>
                                </span>
                            <?php endif; ?>
                        </span>
                    </div>

                    <!-- Alergias -->
                    <div class="p-2 rounded-lg border flex items-center justify-between <?= (!$esFichaEnBlanco && (!empty($paciente['alergias_medicamentos']) || !empty($paciente['alergias_contacto']))) ? 'bg-amber-50 border-amber-300 font-bold text-amber-900' : 'bg-slate-50 border-slate-200' ?>">
                        <span class="text-slate-700 font-bold">Alergias:</span>
                        <span>
                            <?php if ($esFichaEnBlanco): ?>
                                <span class="text-slate-500 font-bold">SI  /  NO</span>
                            <?php else: ?>
                                <span class="font-bold <?= (!empty($paciente['alergias_medicamentos']) || !empty($paciente['alergias_contacto'])) ? 'text-amber-700' : 'text-slate-400' ?>">
                                    <?= (!empty($paciente['alergias_medicamentos']) || !empty($paciente['alergias_contacto'])) ? 'SI' : 'NO' ?>
                                </span>
                            <?php endif; ?>
                        </span>
                    </div>

                    <!-- Artritis -->
                    <div class="p-2 rounded-lg border flex items-center justify-between <?= (!$esFichaEnBlanco && stripos($paciente['otras_patologias'] ?? '', 'artritis') !== false) ? 'bg-blue-50 border-blue-300 font-bold text-blue-900' : 'bg-slate-50 border-slate-200' ?>">
                        <span class="text-slate-700 font-bold">Artritis:</span>
                        <span>
                            <?php if ($esFichaEnBlanco): ?>
                                <span class="text-slate-500 font-bold">SI  /  NO</span>
                            <?php else: ?>
                                <span class="font-bold <?= stripos($paciente['otras_patologias'] ?? '', 'artritis') !== false ? 'text-blue-700' : 'text-slate-400' ?>">
                                    <?= stripos($paciente['otras_patologias'] ?? '', 'artritis') !== false ? 'SI' : 'NO' ?>
                                </span>
                            <?php endif; ?>
                        </span>
                    </div>

                    <!-- H.I.V -->
                    <div class="p-2 rounded-lg border flex items-center justify-between <?= (!$esFichaEnBlanco && (stripos($paciente['otras_patologias'] ?? '', 'hiv') !== false || stripos($paciente['otras_patologias'] ?? '', 'vih') !== false)) ? 'bg-purple-50 border-purple-300 font-bold text-purple-900' : 'bg-slate-50 border-slate-200' ?>">
                        <span class="text-slate-700 font-bold">H.I.V:</span>
                        <span>
                            <?php if ($esFichaEnBlanco): ?>
                                <span class="text-slate-500 font-bold">SI  /  NO</span>
                            <?php else: ?>
                                <span class="font-bold <?= (stripos($paciente['otras_patologias'] ?? '', 'hiv') !== false || stripos($paciente['otras_patologias'] ?? '', 'vih') !== false) ? 'text-purple-700' : 'text-slate-400' ?>">
                                    <?= (stripos($paciente['otras_patologias'] ?? '', 'hiv') !== false || stripos($paciente['otras_patologias'] ?? '', 'vih') !== false) ? 'SI' : 'NO' ?>
                                </span>
                            <?php endif; ?>
                        </span>
                    </div>

                    <!-- Hipertenso -->
                    <div class="p-2 rounded-lg border flex items-center justify-between <?= (!$esFichaEnBlanco && !empty($paciente['tiene_hipertension'])) ? 'bg-cyan-50 border-cyan-300 font-bold text-cyan-900' : 'bg-slate-50 border-slate-200' ?>">
                        <span class="text-slate-700 font-bold">Hipertenso:</span>
                        <span>
                            <?php if ($esFichaEnBlanco): ?>
                                <span class="text-slate-500 font-bold">SI  /  NO</span>
                            <?php else: ?>
                                <span class="font-bold <?= !empty($paciente['tiene_hipertension']) ? 'text-cyan-700' : 'text-slate-400' ?>">
                                    <?= !empty($paciente['tiene_hipertension']) ? 'SI' : 'NO' ?>
                                </span>
                            <?php endif; ?>
                        </span>
                    </div>

                    <!-- Epilepsia -->
                    <div class="p-2 rounded-lg border flex items-center justify-between <?= (!$esFichaEnBlanco && stripos($paciente['otras_patologias'] ?? '', 'epilepsia') !== false) ? 'bg-indigo-50 border-indigo-300 font-bold text-indigo-900' : 'bg-slate-50 border-slate-200' ?>">
                        <span class="text-slate-700 font-bold">Epilepsia:</span>
                        <span>
                            <?php if ($esFichaEnBlanco): ?>
                                <span class="text-slate-500 font-bold">SI  /  NO</span>
                            <?php else: ?>
                                <span class="font-bold <?= stripos($paciente['otras_patologias'] ?? '', 'epilepsia') !== false ? 'text-indigo-700' : 'text-slate-400' ?>">
                                    <?= stripos($paciente['otras_patologias'] ?? '', 'epilepsia') !== false ? 'SI' : 'NO' ?>
                                </span>
                            <?php endif; ?>
                        </span>
                    </div>

                    <!-- Aspirinas -->
                    <div class="p-2 rounded-lg border flex items-center justify-between <?= (!$esFichaEnBlanco && (stripos($paciente['otras_patologias'] ?? '', 'aspirina') !== false || !empty($paciente['usa_anticoagulantes']))) ? 'bg-rose-50 border-rose-300 font-bold text-rose-900' : 'bg-slate-50 border-slate-200' ?>">
                        <span class="text-slate-700 font-bold">¿Aspirinas?:</span>
                        <span>
                            <?php if ($esFichaEnBlanco): ?>
                                <span class="text-slate-500 font-bold">SI  /  NO</span>
                            <?php else: ?>
                                <span class="font-bold <?= (stripos($paciente['otras_patologias'] ?? '', 'aspirina') !== false || !empty($paciente['usa_anticoagulantes'])) ? 'text-rose-700' : 'text-slate-400' ?>">
                                    <?= (stripos($paciente['otras_patologias'] ?? '', 'aspirina') !== false || !empty($paciente['usa_anticoagulantes'])) ? 'SI' : 'NO' ?>
                                </span>
                            <?php endif; ?>
                        </span>
                    </div>

                    <!-- Anticoagulantes -->
                    <div class="p-2 rounded-lg border flex items-center justify-between <?= (!$esFichaEnBlanco && !empty($paciente['usa_anticoagulantes'])) ? 'bg-rose-50 border-rose-300 font-bold text-rose-900' : 'bg-slate-50 border-slate-200' ?>">
                        <span class="text-slate-700 font-bold">¿Anticoagulantes?:</span>
                        <span>
                            <?php if ($esFichaEnBlanco): ?>
                                <span class="text-slate-500 font-bold">SI  /  NO</span>
                            <?php else: ?>
                                <span class="font-bold <?= !empty($paciente['usa_anticoagulantes']) ? 'text-rose-700' : 'text-slate-400' ?>">
                                    <?= !empty($paciente['usa_anticoagulantes']) ? 'SI' : 'NO' ?>
                                </span>
                            <?php endif; ?>
                        </span>
                    </div>

                </div>

                <!-- Medicamentos y Atención Médica -->
                <div class="mt-2.5 p-2.5 bg-slate-50 rounded-xl border border-slate-200 grid grid-cols-1 sm:grid-cols-2 gap-2 text-[11px]">
                    <div>
                        <span class="font-bold text-slate-700">¿Está bajo atención médica?:</span>
                        <span class="ml-1">
                            <?= $esFichaEnBlanco ? '<span class="text-slate-500 font-bold">SI  /  NO</span>' : '<span class="font-bold text-slate-900">' . ((!empty($paciente['es_diabetico']) || !empty($paciente['tiene_hipertension']) || !empty($paciente['enfermedades_renales'])) ? 'SÍ' : 'NO REFERIDO') . '</span>' ?>
                        </span>
                    </div>
                    <div>
                        <span class="font-bold text-slate-700">¿Toma medicamentos?:</span>
                        <span class="ml-1">
                            <?php if ($esFichaEnBlanco): ?>
                                <span class="text-slate-500 font-bold">SI  /  NO</span> &nbsp;&nbsp; <span class="text-slate-600 font-bold">¿Cuáles?:</span> <span class="border-b border-slate-300 inline-block w-36">&nbsp;</span>
                            <?php else: ?>
                                <span class="text-slate-900"><?= !empty($paciente['otras_patologias']) ? ('SÍ (' . e($paciente['otras_patologias']) . ')') : 'No manifiesta' ?></span>
                            <?php endif; ?>
                        </span>
                    </div>
                    <?php if (!$esFichaEnBlanco && (!empty($paciente['alergias_medicamentos']) || !empty($paciente['alergias_contacto']))): ?>
                        <div class="sm:col-span-2 text-rose-800 font-semibold">
                            <i class="fa-solid fa-triangle-exclamation mr-1 text-rose-600"></i>
                            Alergias específicas: <?= e(trim($paciente['alergias_medicamentos'] . ' ' . $paciente['alergias_contacto'])) ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- 3. OBSERVACIONES Y DIAGNÓSTICO CLÍNICO -->
            <div class="text-xs space-y-1">
                <span class="font-bold text-slate-700 block">Observaciones Clínicas / Diagnóstico:</span>
                
                <?php if (!$esFichaEnBlanco && !empty($historiasClinicas)): ?>
                    <div class="p-2.5 bg-slate-50 rounded-lg border border-slate-200 space-y-1">
                        <?php foreach (array_slice($historiasClinicas, 0, 2) as $hc): ?>
                            <div class="text-[11px]">
                                <span class="font-mono font-bold text-[#005B94]"><?= substr($hc['fecha_atencion'], 0, 10) ?>:</span>
                                <strong class="text-slate-800"><?= e($hc['diagnostico']) ?></strong> — <span class="text-slate-600"><?= e($hc['procedimiento_realizado']) ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="space-y-2 pt-1">
                        <div class="dotted-line"></div>
                        <div class="dotted-line"></div>
                        <?php if ($esFichaEnBlanco): ?>
                            <div class="dotted-line"></div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- 4. EL PODOGRAMA ANATÓMICO (ESQUEMA DE 4 PIES: VISTA DORSAL Y PLANTAR) -->
            <div class="page-break-avoid pt-2">
                <div class="border border-slate-300 rounded-2xl p-3 bg-white shadow-sm">
                    
                    <div class="flex items-center justify-between border-b border-slate-200 pb-2 mb-3">
                        <div class="flex items-center space-x-2">
                            <i class="fa-solid fa-socks text-[#005B94] text-base"></i>
                            <h3 class="text-xs font-black uppercase tracking-wider text-slate-900">
                                ESQUEMA ANATÓMICO PODOLÓGICO (PODOGRAMA DE EXPLORACIÓN)
                            </h3>
                        </div>
                        <span class="text-[10px] text-slate-400 uppercase tracking-widest font-mono">MAPA TOPOGRÁFICO DE LESIONES</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-center">
                        
                        <!-- PAR 1: VISTA DORSAL (EMPEINE Y UÑAS) -->
                        <div class="border border-slate-200 rounded-xl p-3 bg-slate-50/50 text-center">
                            <div class="text-[11px] font-black uppercase text-[#005B94] tracking-wide mb-1">
                                VISTA DORSAL (Empeine y Láminas Ungueales)
                            </div>
                            <p class="text-[9px] text-slate-500 mb-2">Para evaluar onicocriptosis, onicomicosis, distrofias y afecciones de dedos.</p>

                            <!-- SVG DORSAL: Pie Derecho y Pie Izquierdo -->
                            <div class="flex items-center justify-center space-x-6 py-1">
                                
                                <!-- Pie Derecho Dorsal -->
                                <div class="flex flex-col items-center">
                                    <svg class="w-24 h-40" viewBox="0 0 100 160" fill="none" stroke="#334155" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                        <!-- Silueta Pie Derecho Dorsal -->
                                        <path d="M 50 155 C 38 155 32 145 30 130 C 28 115 25 95 24 75 C 23 55 24 40 28 32 C 30 28 36 28 38 34 C 40 25 46 25 48 33 C 50 25 56 25 58 35 C 60 28 66 28 68 38 C 70 30 80 30 82 45 C 84 65 82 85 80 110 C 78 135 72 155 50 155 Z" fill="#ffffff" />
                                        
                                        <!-- Dedos y Uñas Pie Derecho -->
                                        <!-- Dedo 1 (Hallux / Pulgar en borde interno derecho) -->
                                        <path d="M 68 38 Q 75 35 82 45" />
                                        <rect x="71" y="38" width="9" height="7" rx="2" fill="#e2e8f0" stroke="#0f172a" stroke-width="1.2" />
                                        
                                        <!-- Dedo 2 -->
                                        <path d="M 58 35 Q 63 32 68 38" />
                                        <rect x="60" y="31" width="7" height="6" rx="1.5" fill="#e2e8f0" stroke="#0f172a" stroke-width="1.2" />
                                        
                                        <!-- Dedo 3 -->
                                        <path d="M 48 33 Q 53 30 58 35" />
                                        <rect x="50" y="28" width="6" height="5.5" rx="1.5" fill="#e2e8f0" stroke="#0f172a" stroke-width="1.2" />
                                        
                                        <!-- Dedo 4 -->
                                        <path d="M 38 34 Q 43 31 48 33" />
                                        <rect x="40" y="29" width="5.5" height="5" rx="1" fill="#e2e8f0" stroke="#0f172a" stroke-width="1.2" />
                                        
                                        <!-- Dedo 5 (Meñique en borde externo izquierdo) -->
                                        <path d="M 28 32 Q 33 30 38 34" />
                                        <rect x="29" y="33" width="5" height="4.5" rx="1" fill="#e2e8f0" stroke="#0f172a" stroke-width="1.2" />

                                        <!-- Líneas anatómicas del empeine -->
                                        <path d="M 40 75 Q 52 80 65 75" stroke="#94a3b8" stroke-width="1" stroke-dasharray="2 2" />
                                        <path d="M 42 105 Q 52 110 63 105" stroke="#94a3b8" stroke-width="1" stroke-dasharray="2 2" />
                                    </svg>
                                    <span class="text-[10px] font-bold text-slate-700 mt-1">Derecho</span>
                                </div>

                                <!-- Pie Izquierdo Dorsal -->
                                <div class="flex flex-col items-center">
                                    <svg class="w-24 h-40" viewBox="0 0 100 160" fill="none" stroke="#334155" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                        <!-- Silueta Pie Izquierdo Dorsal (Espejo) -->
                                        <path d="M 50 155 C 72 155 78 145 80 130 C 82 115 85 95 86 75 C 87 55 86 40 82 32 C 80 28 74 28 72 34 C 70 25 64 25 62 33 C 60 25 54 25 52 35 C 50 28 44 28 42 38 C 40 30 30 30 28 45 C 26 65 28 85 30 110 C 32 135 38 155 50 155 Z" fill="#ffffff" />
                                        
                                        <!-- Dedos y Uñas Pie Izquierdo -->
                                        <!-- Dedo 1 (Hallux / Pulgar en borde interno izquierdo) -->
                                        <path d="M 42 38 Q 35 35 28 45" />
                                        <rect x="30" y="38" width="9" height="7" rx="2" fill="#e2e8f0" stroke="#0f172a" stroke-width="1.2" />
                                        
                                        <!-- Dedo 2 -->
                                        <path d="M 52 35 Q 47 32 42 38" />
                                        <rect x="43" y="31" width="7" height="6" rx="1.5" fill="#e2e8f0" stroke="#0f172a" stroke-width="1.2" />
                                        
                                        <!-- Dedo 3 -->
                                        <path d="M 62 33 Q 57 30 52 35" />
                                        <rect x="54" y="28" width="6" height="5.5" rx="1.5" fill="#e2e8f0" stroke="#0f172a" stroke-width="1.2" />
                                        
                                        <!-- Dedo 4 -->
                                        <path d="M 72 34 Q 67 31 62 33" />
                                        <rect x="64" y="29" width="5.5" height="5" rx="1" fill="#e2e8f0" stroke="#0f172a" stroke-width="1.2" />
                                        
                                        <!-- Dedo 5 (Meñique en borde externo derecho) -->
                                        <path d="M 82 32 Q 77 30 72 34" />
                                        <rect x="76" y="33" width="5" height="4.5" rx="1" fill="#e2e8f0" stroke="#0f172a" stroke-width="1.2" />

                                        <!-- Líneas anatómicas del empeine -->
                                        <path d="M 45 75 Q 58 80 70 75" stroke="#94a3b8" stroke-width="1" stroke-dasharray="2 2" />
                                        <path d="M 47 105 Q 58 110 68 105" stroke="#94a3b8" stroke-width="1" stroke-dasharray="2 2" />
                                    </svg>
                                    <span class="text-[10px] font-bold text-slate-700 mt-1">Izquierdo</span>
                                </div>

                            </div>
                        </div>

                        <!-- PAR 2: VISTA PLANTAR (PLANTA, ARCO Y TALÓN) -->
                        <div class="border border-slate-200 rounded-xl p-3 bg-slate-50/50 text-center">
                            <div class="text-[11px] font-black uppercase text-[#005B94] tracking-wide mb-1">
                                VISTA PLANTAR (Planta, Metatarso y Talón)
                            </div>
                            <p class="text-[9px] text-slate-500 mb-2">Para registrar helomas (callos), hiperqueratosis, verrugas y puntos de presión.</p>

                            <!-- SVG PLANTAR: Der. e Izq. -->
                            <div class="flex items-center justify-center space-x-6 py-1">
                                
                                <!-- Pie Derecho Plantar -->
                                <div class="flex flex-col items-center">
                                    <svg class="w-24 h-40" viewBox="0 0 100 160" fill="none" stroke="#334155" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                        <!-- Silueta Planta Derecha con curvatura de arco interno -->
                                        <path d="M 52 154 C 36 154 34 142 34 126 C 34 105 38 85 36 62 C 34 50 36 44 42 42 C 48 40 58 40 66 42 C 78 45 84 55 82 70 C 80 82 66 94 66 112 C 66 128 66 154 52 154 Z" fill="#ffffff" />
                                        
                                        <!-- Almohadillas de los dedos plantares -->
                                        <ellipse cx="74" cy="28" rx="7" ry="9" fill="#f8fafc" stroke="#334155" stroke-width="1.3" />
                                        <ellipse cx="61" cy="24" rx="5" ry="7" fill="#f8fafc" stroke="#334155" stroke-width="1.3" />
                                        <ellipse cx="50" cy="24" rx="4.5" ry="6.5" fill="#f8fafc" stroke="#334155" stroke-width="1.3" />
                                        <ellipse cx="40" cy="26" rx="4" ry="6" fill="#f8fafc" stroke="#334155" stroke-width="1.3" />
                                        <ellipse cx="31" cy="30" rx="3.5" ry="5.5" fill="#f8fafc" stroke="#334155" stroke-width="1.3" />

                                        <!-- Zonas de Metatarso y Talón -->
                                        <path d="M 37 68 Q 55 75 79 68" stroke="#cbd5e1" stroke-width="1.2" />
                                        <path d="M 40 120 Q 50 125 64 120" stroke="#cbd5e1" stroke-width="1.2" />
                                        <circle cx="50" cy="138" r="8" stroke="#e2e8f0" stroke-width="1" stroke-dasharray="2 2" />
                                    </svg>
                                    <span class="text-[10px] font-bold text-slate-700 mt-1">Der.</span>
                                </div>

                                <!-- Pie Izquierdo Plantar -->
                                <div class="flex flex-col items-center">
                                    <svg class="w-24 h-40" viewBox="0 0 100 160" fill="none" stroke="#334155" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                        <!-- Silueta Planta Izquierda (Espejo) -->
                                        <path d="M 48 154 C 64 154 66 142 66 126 C 66 105 62 85 64 62 C 66 50 64 44 58 42 C 52 40 42 40 34 42 C 22 45 16 55 18 70 C 20 82 34 94 34 112 C 34 128 34 154 48 154 Z" fill="#ffffff" />
                                        
                                        <!-- Almohadillas de los dedos plantares -->
                                        <ellipse cx="26" cy="28" rx="7" ry="9" fill="#f8fafc" stroke="#334155" stroke-width="1.3" />
                                        <ellipse cx="39" cy="24" rx="5" ry="7" fill="#f8fafc" stroke="#334155" stroke-width="1.3" />
                                        <ellipse cx="50" cy="24" rx="4.5" ry="6.5" fill="#f8fafc" stroke="#334155" stroke-width="1.3" />
                                        <ellipse cx="60" cy="26" rx="4" ry="6" fill="#f8fafc" stroke="#334155" stroke-width="1.3" />
                                        <ellipse cx="69" cy="30" rx="3.5" ry="5.5" fill="#f8fafc" stroke="#334155" stroke-width="1.3" />

                                        <!-- Zonas de Metatarso y Talón -->
                                        <path d="M 21 68 Q 45 75 63 68" stroke="#cbd5e1" stroke-width="1.2" />
                                        <path d="M 36 120 Q 50 125 60 120" stroke="#cbd5e1" stroke-width="1.2" />
                                        <circle cx="50" cy="138" r="8" stroke="#e2e8f0" stroke-width="1" stroke-dasharray="2 2" />
                                    </svg>
                                    <span class="text-[10px] font-bold text-slate-700 mt-1">Izq.</span>
                                </div>

                            </div>
                        </div>

                    </div>

                    <!-- LEYENDA CLÍNICA RÁPIDA -->
                    <div class="mt-2.5 pt-2 border-t border-slate-200 flex flex-wrap items-center justify-between text-[9px] text-slate-500 font-mono">
                        <span><strong>(O)</strong> Onicocriptosis / Micosis</span>
                        <span><strong>(H)</strong> Heloma / Callosidad</span>
                        <span><strong>(HQ)</strong> Hiperqueratosis</span>
                        <span><strong>(V)</strong> Verruga Plantar</span>
                        <span><strong>(U)</strong> Úlcera / Flictena</span>
                        <span><strong>(*)</strong> Dolor / Presión</span>
                    </div>

                </div>
            </div>

            <!-- 5. FIRMAS Y SELLOS LEGALES -->
            <div class="pt-4 page-break-avoid">
                <div class="grid grid-cols-2 gap-10 text-center text-xs">
                    <div>
                        <div class="border-b border-slate-400 h-12 mb-1.5 flex items-end justify-center">
                            <!-- Simulación de firma digital profesional -->
                            <span class="font-serif italic text-base text-[#005B94]/80 tracking-widest select-none">Ana Ramírez M.</span>
                        </div>
                        <span class="font-bold text-slate-900 block text-[11px]">Dra. Ana Ramírez</span>
                        <span class="text-[9px] text-slate-500 block">Podóloga Especialista • Reg. POD-09823</span>
                        <span class="text-[8px] text-slate-400">Firma y Sello Facultativo</span>
                    </div>

                    <div>
                        <div class="border-b border-slate-400 h-12 mb-1.5"></div>
                        <span class="font-bold text-slate-900 block text-[11px]"><?= $esFichaEnBlanco ? 'Firma del Paciente / Tutor' : (e($paciente['nombre']) . ' ' . e($paciente['apellido'])) ?></span>
                        <span class="text-[9px] text-slate-500 block">DNI / Doc: <?= $esFichaEnBlanco ? '....................................' : e($paciente['documento_identidad']) ?></span>
                        <span class="text-[8px] text-slate-400">Consentimiento Informado</span>
                    </div>
                </div>

                <!-- PIE LEGAL Y DERECHOS RESERVADOS A KILACODE -->
                <div class="mt-4 text-center text-[9px] text-slate-400 border-t border-slate-200 pt-2 space-y-0.5">
                    <div>
                        Documento clínico oficial confidencial amparado por el secreto médico profesional. Válido para control y seguimiento podológico.
                    </div>
                    <div class="font-medium text-slate-500">
                        © <?= date('Y') ?> <strong>Podología Ana Ramírez</strong> • Software de Gestión Clínica desarrollado por <strong>KilaCode</strong>. Todos los derechos reservados a KilaCode.
                    </div>
                </div>
            </div>

        </div>

    </div>

</body>
</html>
