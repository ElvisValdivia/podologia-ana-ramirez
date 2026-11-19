<?php
$tituloPagina = "Modificar Ficha del Paciente - " . e($paciente['nombre']) . " " . e($paciente['apellido']);
require_once dirname(__DIR__) . '/layout/header.php';
?>

<!-- BANNER INFORMATIVO: MODO DE EDICIÓN MANUAL (SIN API RENIEC) -->
<div class="mb-6 p-4 rounded-2xl bg-sky-50 border border-sky-200 text-sky-950 flex items-start space-x-3.5 shadow-sm">
    <div class="w-9 h-9 rounded-xl bg-sky-100 text-[#005B94] flex items-center justify-center shrink-0 mt-0.5">
        <i class="fa-solid fa-id-card-clip text-lg"></i>
    </div>
    <div class="flex-1">
        <div class="flex flex-wrap items-center gap-2 mb-1">
            <h4 class="font-bold text-sm text-slate-800">Modificación Habilitada • Modo Manual (Sin Conexión API RENIEC)</h4>
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-[#005B94] text-white uppercase tracking-wider">
                Auditoría Activa
            </span>
        </div>
        <p class="text-xs text-slate-600 leading-relaxed">
            Al no contar actualmente con un servicio automatizado de consulta a la API de RENIEC, la Administración tiene habilitada la 
            <strong>edición y corrección de datos filiatorios</strong> (DNI/Documento, nombres, apellidos, fecha de nacimiento y género) para subsanar errores humanos de digitación. 
            Cualquier modificación será registrada automáticamente en el <strong>historial inmutable de auditoría</strong> con fecha, hora, responsable y justificación.
        </p>
    </div>
</div>

<?php if (!empty($mensajeError)): ?>
    <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs flex items-start space-x-3">
        <i class="fa-solid fa-circle-exclamation text-base text-rose-600 mt-0.5"></i>
        <div><?= $mensajeError ?></div>
    </div>
<?php endif; ?>

<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
    <div>
        <h1 class="text-2xl font-black text-slate-900">Modificar Ficha del Paciente</h1>
        <p class="text-xs text-slate-500 mt-0.5">Expediente #<?= $paciente['id'] ?> • Acción autorizada para Administración</p>
    </div>
    <a href="index.php?accion=detalle&id=<?= $paciente['id'] ?>" class="touch-target inline-flex items-center px-4 py-2 rounded-xl border border-slate-300 bg-white text-xs font-bold text-slate-700 hover:bg-slate-50 transition shadow-sm">
        <i class="fa-solid fa-arrow-left mr-2"></i> Cancelar
    </a>
</div>

<form action="index.php?accion=actualizar" method="POST" class="space-y-6">
    <input type="hidden" name="id" value="<?= $paciente['id'] ?>">

    <!-- BLOQUE 1: DATOS PERSONALES Y FILIACIÓN (EDITABLES) -->
    <div class="bg-white rounded-2xl border border-slate-200 p-6 space-y-5 shadow-sm">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <div class="flex items-center space-x-2.5">
                <div class="w-7 h-7 rounded-lg bg-[#005B94]/10 text-[#005B94] flex items-center justify-center font-bold text-xs">
                    1
                </div>
                <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider text-[#005B94]">
                    Datos Personales y Filiación
                </h2>
            </div>
            <span class="text-[11px] font-semibold text-amber-700 bg-amber-50 px-2 py-0.5 rounded-md border border-amber-200">
                <i class="fa-solid fa-pen mr-1"></i> Editable por Admin
            </span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <!-- Tipo de Documento -->
            <div>
                <label for="tipo_documento" class="block text-xs font-bold text-slate-600 mb-1">Tipo de Documento *</label>
                <select name="tipo_documento" id="tipo_documento" required class="touch-target w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs text-slate-800 focus:border-[#005B94] focus:ring-2 focus:ring-[#005B94]/20 transition bg-white">
                    <option value="DNI" <?= ($paciente['tipo_documento'] ?? '') === 'DNI' ? 'selected' : '' ?>>DNI (Documento Nacional de Identidad)</option>
                    <option value="Carné de Extranjería" <?= ($paciente['tipo_documento'] ?? '') === 'Carné de Extranjería' ? 'selected' : '' ?>>Carné de Extranjería (CE)</option>
                    <option value="Pasaporte" <?= ($paciente['tipo_documento'] ?? '') === 'Pasaporte' ? 'selected' : '' ?>>Pasaporte</option>
                    <option value="Otro" <?= ($paciente['tipo_documento'] ?? '') === 'Otro' ? 'selected' : '' ?>>Otro</option>
                </select>
            </div>

            <!-- Número de Documento -->
            <div>
                <label for="documento_identidad" class="block text-xs font-bold text-slate-600 mb-1">Número de Documento *</label>
                <input type="text" name="documento_identidad" id="documento_identidad" required value="<?= e($paciente['documento_identidad']) ?>"
                       class="touch-target w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs text-slate-800 font-mono font-bold focus:border-[#005B94] focus:ring-2 focus:ring-[#005B94]/20 transition bg-white">
            </div>

            <!-- Género -->
            <div>
                <label for="genero" class="block text-xs font-bold text-slate-600 mb-1">Género *</label>
                <select name="genero" id="genero" required class="touch-target w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs text-slate-800 focus:border-[#005B94] focus:ring-2 focus:ring-[#005B94]/20 transition bg-white">
                    <option value="Femenino" <?= ($paciente['genero'] ?? '') === 'Femenino' ? 'selected' : '' ?>>Femenino</option>
                    <option value="Masculino" <?= ($paciente['genero'] ?? '') === 'Masculino' ? 'selected' : '' ?>>Masculino</option>
                    <option value="Otro" <?= ($paciente['genero'] ?? '') === 'Otro' ? 'selected' : '' ?>>Otro</option>
                </select>
            </div>

            <!-- Nombres -->
            <div>
                <label for="nombre" class="block text-xs font-bold text-slate-600 mb-1">Nombres del Paciente *</label>
                <input type="text" name="nombre" id="nombre" required value="<?= e($paciente['nombre']) ?>"
                       class="touch-target w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs text-slate-800 focus:border-[#005B94] focus:ring-2 focus:ring-[#005B94]/20 transition">
            </div>

            <!-- Apellidos -->
            <div>
                <label for="apellido" class="block text-xs font-bold text-slate-600 mb-1">Apellidos del Paciente *</label>
                <input type="text" name="apellido" id="apellido" required value="<?= e($paciente['apellido']) ?>"
                       class="touch-target w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs text-slate-800 focus:border-[#005B94] focus:ring-2 focus:ring-[#005B94]/20 transition">
            </div>

            <!-- Fecha de Nacimiento -->
            <div>
                <label for="fecha_nacimiento" class="block text-xs font-bold text-slate-600 mb-1">Fecha de Nacimiento *</label>
                <input type="date" name="fecha_nacimiento" id="fecha_nacimiento" required value="<?= e($paciente['fecha_nacimiento']) ?>"
                       class="touch-target w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs text-slate-800 focus:border-[#005B94] focus:ring-2 focus:ring-[#005B94]/20 transition">
            </div>
        </div>
    </div>

    <!-- BLOQUE 2: DATOS DE CONTACTO Y DOMICILIO -->
    <div class="bg-white rounded-2xl border border-slate-200 p-6 space-y-5 shadow-sm">
        <div class="flex items-center space-x-2.5 border-b border-slate-100 pb-3">
            <div class="w-7 h-7 rounded-lg bg-[#005B94]/10 text-[#005B94] flex items-center justify-center font-bold text-xs">
                2
            </div>
            <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider text-[#005B94]">
                Datos de Contacto y Domicilio
            </h2>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <div>
                <label for="telefono" class="block text-xs font-bold text-slate-600 mb-1">Teléfono Principal / Celular *</label>
                <input type="tel" name="telefono" id="telefono" required value="<?= e($paciente['telefono']) ?>" class="touch-target w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs text-slate-800 focus:border-[#005B94] focus:ring-2 focus:ring-[#005B94]/20 transition">
            </div>
            <div>
                <label for="telefono_emergencia" class="block text-xs font-bold text-slate-600 mb-1">Teléfono de Emergencia</label>
                <input type="tel" name="telefono_emergencia" id="telefono_emergencia" value="<?= e($paciente['telefono_emergencia'] ?? '') ?>" class="touch-target w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs text-slate-800 focus:border-[#005B94] focus:ring-2 focus:ring-[#005B94]/20 transition">
            </div>
            <div>
                <label for="contacto_emergencia" class="block text-xs font-bold text-slate-600 mb-1">Contacto Emergencia (Parentesco)</label>
                <input type="text" name="contacto_emergencia" id="contacto_emergencia" value="<?= e($paciente['contacto_emergencia'] ?? '') ?>" class="touch-target w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs text-slate-800 focus:border-[#005B94] focus:ring-2 focus:ring-[#005B94]/20 transition">
            </div>
            <div>
                <label for="email" class="block text-xs font-bold text-slate-600 mb-1">Correo Electrónico</label>
                <input type="email" name="email" id="email" value="<?= e($paciente['email'] ?? '') ?>" class="touch-target w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs text-slate-800 focus:border-[#005B94] focus:ring-2 focus:ring-[#005B94]/20 transition">
            </div>
            <div>
                <label for="ocupacion" class="block text-xs font-bold text-slate-600 mb-1">Ocupación / Actividad</label>
                <input type="text" name="ocupacion" id="ocupacion" value="<?= e($paciente['ocupacion'] ?? '') ?>" class="touch-target w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs text-slate-800 focus:border-[#005B94] focus:ring-2 focus:ring-[#005B94]/20 transition">
            </div>
            <div>
                <label for="direccion" class="block text-xs font-bold text-slate-600 mb-1">Dirección Domiciliaria</label>
                <input type="text" name="direccion" id="direccion" value="<?= e($paciente['direccion'] ?? '') ?>" class="touch-target w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs text-slate-800 focus:border-[#005B94] focus:ring-2 focus:ring-[#005B94]/20 transition">
            </div>
        </div>
    </div>

    <!-- BLOQUE 3: ANTECEDENTES, PATOLOGÍAS Y BIOMECÁNICA -->
    <div class="bg-white rounded-2xl border border-slate-200 p-6 space-y-5 shadow-sm">
        <div class="flex items-center space-x-2.5 border-b border-slate-100 pb-3">
            <div class="w-7 h-7 rounded-lg bg-[#005B94]/10 text-[#005B94] flex items-center justify-center font-bold text-xs">
                3
            </div>
            <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider text-[#005B94]">
                Patologías, Antecedentes y Calzado
            </h2>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
            <label class="flex items-center p-3 rounded-xl border border-slate-200 bg-slate-50 hover:bg-slate-100 transition cursor-pointer">
                <input type="checkbox" name="es_diabetico" value="1" <?= !empty($paciente['es_diabetico']) ? 'checked' : '' ?> class="w-5 h-5 text-rose-600 rounded border-slate-300">
                <span class="ml-2.5 text-xs font-bold text-slate-800">Diabetes Mellitus</span>
            </label>
            <label class="flex items-center p-3 rounded-xl border border-slate-200 bg-slate-50 hover:bg-slate-100 transition cursor-pointer">
                <input type="checkbox" name="usa_anticoagulantes" value="1" <?= !empty($paciente['usa_anticoagulantes']) ? 'checked' : '' ?> class="w-5 h-5 text-amber-600 rounded border-slate-300">
                <span class="ml-2.5 text-xs font-bold text-slate-800">Usa Anticoagulantes</span>
            </label>
            <label class="flex items-center p-3 rounded-xl border border-slate-200 bg-slate-50 hover:bg-slate-100 transition cursor-pointer">
                <input type="checkbox" name="problemas_circulatorios" value="1" <?= !empty($paciente['problemas_circulatorios']) ? 'checked' : '' ?> class="w-5 h-5 text-cyan-600 rounded border-slate-300">
                <span class="ml-2.5 text-xs font-bold text-slate-800">Problemas Circulatorios</span>
            </label>
            <label class="flex items-center p-3 rounded-xl border border-slate-200 bg-slate-50 hover:bg-slate-100 transition cursor-pointer">
                <input type="checkbox" name="tiene_hipertension" value="1" <?= !empty($paciente['tiene_hipertension']) ? 'checked' : '' ?> class="w-5 h-5 text-clinic-600 rounded border-slate-300">
                <span class="ml-2.5 text-xs font-bold text-slate-800">Hipertensión Arterial</span>
            </label>
            <label class="flex items-center p-3 rounded-xl border border-slate-200 bg-slate-50 hover:bg-slate-100 transition cursor-pointer">
                <input type="checkbox" name="tiene_marcapasos" value="1" <?= !empty($paciente['tiene_marcapasos']) ? 'checked' : '' ?> class="w-5 h-5 text-purple-600 rounded border-slate-300">
                <span class="ml-2.5 text-xs font-bold text-slate-800">Tiene Marcapasos</span>
            </label>
            <label class="flex items-center p-3 rounded-xl border border-slate-200 bg-slate-50 hover:bg-slate-100 transition cursor-pointer">
                <input type="checkbox" name="enfermedades_renales" value="1" <?= !empty($paciente['enfermedades_renales']) ? 'checked' : '' ?> class="w-5 h-5 text-indigo-600 rounded border-slate-300">
                <span class="ml-2.5 text-xs font-bold text-slate-800">Enfermedades Renales</span>
            </label>
            <label class="flex items-center p-3 rounded-xl border border-slate-200 bg-slate-50 hover:bg-slate-100 transition cursor-pointer">
                <input type="checkbox" name="usa_plantillas" value="1" <?= !empty($paciente['usa_plantillas']) ? 'checked' : '' ?> class="w-5 h-5 text-[#005B94] rounded border-slate-300">
                <span class="ml-2.5 text-xs font-bold text-slate-800">Usa Plantillas Previas</span>
            </label>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 pt-2">
            <div>
                <label for="tipo_diabetes" class="block text-xs font-bold text-slate-600 mb-1">Tipo de Diabetes (si aplica)</label>
                <input type="text" name="tipo_diabetes" id="tipo_diabetes" placeholder="Ej: Tipo 1 / Tipo 2 / Controlada" value="<?= e($paciente['tipo_diabetes'] ?? '') ?>" class="touch-target w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs text-slate-800 focus:border-[#005B94] focus:ring-2 focus:ring-[#005B94]/20 transition">
            </div>
            <div>
                <label for="alergias_medicamentos" class="block text-xs font-bold text-slate-600 mb-1">Alergias a Medicamentos</label>
                <input type="text" name="alergias_medicamentos" id="alergias_medicamentos" placeholder="Ej: Penicilina, AINEs" value="<?= e($paciente['alergias_medicamentos'] ?? '') ?>" class="touch-target w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs text-slate-800 focus:border-[#005B94] focus:ring-2 focus:ring-[#005B94]/20 transition">
            </div>
            <div>
                <label for="alergias_contacto" class="block text-xs font-bold text-slate-600 mb-1">Alergias de Contacto</label>
                <input type="text" name="alergias_contacto" id="alergias_contacto" placeholder="Ej: Látex, Yodo" value="<?= e($paciente['alergias_contacto'] ?? '') ?>" class="touch-target w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs text-slate-800 focus:border-[#005B94] focus:ring-2 focus:ring-[#005B94]/20 transition">
            </div>
            <div>
                <label for="tipo_calzado_habitual" class="block text-xs font-bold text-slate-600 mb-1">Tipo de Calzado Habitual</label>
                <input type="text" name="tipo_calzado_habitual" id="tipo_calzado_habitual" placeholder="Ej: Zapatillas deportivas, Mocasines" value="<?= e($paciente['tipo_calzado_habitual'] ?? '') ?>" class="touch-target w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs text-slate-800 focus:border-[#005B94] focus:ring-2 focus:ring-[#005B94]/20 transition">
            </div>
            <div>
                <label for="talla_calzado" class="block text-xs font-bold text-slate-600 mb-1">Talla de Calzado</label>
                <input type="text" name="talla_calzado" id="talla_calzado" placeholder="Ej: 38, 41 EUR" value="<?= e($paciente['talla_calzado'] ?? '') ?>" class="touch-target w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs text-slate-800 focus:border-[#005B94] focus:ring-2 focus:ring-[#005B94]/20 transition">
            </div>
            <div>
                <label for="actividad_fisica" class="block text-xs font-bold text-slate-600 mb-1">Nivel de Actividad Física</label>
                <input type="text" name="actividad_fisica" id="actividad_fisica" placeholder="Ej: Sedentario, Caminata diaria, Running" value="<?= e($paciente['actividad_fisica'] ?? '') ?>" class="touch-target w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs text-slate-800 focus:border-[#005B94] focus:ring-2 focus:ring-[#005B94]/20 transition">
            </div>
            <div class="sm:col-span-2 lg:col-span-3">
                <label for="otras_patologias" class="block text-xs font-bold text-slate-600 mb-1">Otras Patologías Relevantes</label>
                <input type="text" name="otras_patologias" id="otras_patologias" placeholder="Detalle cualquier otro antecedente médico relevante..." value="<?= e($paciente['otras_patologias'] ?? '') ?>" class="touch-target w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs text-slate-800 focus:border-[#005B94] focus:ring-2 focus:ring-[#005B94]/20 transition">
            </div>
        </div>
    </div>

    <!-- BLOQUE 4: JUSTIFICACIÓN OBLIGATORIA DEL CAMBIO (AUDITORÍA) -->
    <div class="bg-amber-50/70 rounded-2xl border border-amber-200 p-6 space-y-2">
        <label for="motivo_cambio" class="block text-xs font-bold uppercase tracking-wider text-amber-950 flex items-center space-x-1.5">
            <i class="fa-solid fa-stamp text-amber-700"></i>
            <span>Justificación Administrativa de la Modificación (Audit Log) *</span>
        </label>
        <p class="text-[11px] text-amber-900 leading-relaxed">
            Explique el motivo por el cual se actualizan o corrigen los datos del paciente (ej: <em>"Corrección de error de digitación en apellido paterno / Actualización de teléfono por cambio de operador"</em>). 
            Este motivo quedará asentado permanentemente en el historial forense de la ficha clínica.
        </p>
        <input type="text" name="motivo_cambio" id="motivo_cambio" required placeholder="Ingrese el motivo de la corrección o actualización..."
               class="touch-target w-full rounded-xl border border-amber-300 bg-white px-3.5 py-2.5 text-xs text-slate-800 focus:border-[#005B94] focus:ring-2 focus:ring-[#005B94]/20 transition">
    </div>

    <!-- BOTONES DE ACCIÓN -->
    <div class="flex items-center justify-end space-x-3 pt-2">
        <a href="index.php?accion=detalle&id=<?= $paciente['id'] ?>" class="touch-target px-6 py-3 rounded-xl border border-slate-300 bg-white text-xs font-bold text-slate-700 hover:bg-slate-50 transition">
            Cancelar
        </a>
        <button type="submit" class="touch-target px-6 py-3 rounded-xl bg-[#005B94] hover:bg-[#0A2540] text-white text-xs font-bold shadow-md hover:shadow-lg transition flex items-center space-x-2">
            <i class="fa-solid fa-floppy-disk text-[#2EC4B6]"></i>
            <span>Guardar Cambios y Registrar Auditoría</span>
        </button>
    </div>
</form>

<?php require_once dirname(__DIR__) . '/layout/footer.php'; ?>

