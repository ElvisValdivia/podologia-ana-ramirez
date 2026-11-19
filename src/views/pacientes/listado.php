<?php
$tituloPagina = "Directorio de Pacientes";
require_once dirname(__DIR__) . '/layout/header.php';
$filtroActual = $filtro ?? 'activo';
?>

<!-- ALERTAS DE SESIÓN -->
<?php if (!empty($mensajeExito)): ?>
    <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-start space-x-3 shadow-sm animate-fade-in">
        <i class="fa-solid fa-circle-check text-xl text-emerald-600 mt-0.5"></i>
        <div class="text-sm font-medium"><?= $mensajeExito ?></div>
    </div>
<?php endif; ?>

<?php if (!empty($mensajeError)): ?>
    <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 flex items-start space-x-3 shadow-sm animate-fade-in">
        <i class="fa-solid fa-triangle-exclamation text-xl text-rose-600 mt-0.5"></i>
        <div class="text-sm font-medium"><?= $mensajeError ?></div>
    </div>
<?php endif; ?>

<!-- ENCABEZADO Y ACCIONES -->
<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
        <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-slate-900">
            Directorio de Pacientes
        </h1>
        <p class="text-xs text-slate-500 mt-1">
            Expedientes clínicos, trazabilidad, alertas podológicas y gestión de registros.
        </p>
    </div>
    <div class="flex flex-wrap items-center gap-2">
        <a href="index.php?accion=ficha_blanco" target="_blank" class="touch-target inline-flex items-center justify-center px-4 py-3 rounded-xl border border-slate-300 bg-white text-slate-700 font-bold text-xs hover:bg-slate-50 active:scale-95 transition shadow-sm">
            <i class="fa-solid fa-file-lines mr-2 text-[#005B94]"></i> Ver Ficha en Blanco
        </a>

        <a href="index.php?accion=registro" class="touch-target inline-flex items-center justify-center px-5 py-3 rounded-xl bg-[#005B94] text-white font-bold text-xs hover:bg-[#0A2540] active:scale-95 transition shadow-sm">
            <i class="fa-solid fa-user-plus mr-2 text-[#2EC4B6]"></i> Registrar Nuevo Paciente
        </a>
    </div>
</div>

<!-- PESTAÑAS DE ESTADO: ACTIVOS VS PAPELERA / RETIRADOS -->
<div class="mb-6 flex items-center space-x-2 border-b border-slate-200 pb-2">
    <a href="index.php?accion=listado&filtro=activo" 
       class="inline-flex items-center px-4 py-2 text-xs font-bold rounded-xl transition <?= ($filtroActual === 'activo') ? 'bg-[#005B94] text-white shadow' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' ?>">
        <i class="fa-solid fa-users mr-2 <?= ($filtroActual === 'activo') ? 'text-[#2EC4B6]' : 'text-slate-400' ?>"></i>
        Expedientes Activos
        <span class="ml-2 px-2 py-0.5 rounded-full text-[10px] <?= ($filtroActual === 'activo') ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600' ?>">
            <?= $totalActivos ?? 0 ?>
        </span>
    </a>

    <?php if (puedeEliminarPaciente()): ?>
        <a href="index.php?accion=listado&filtro=inactivo" 
           class="inline-flex items-center px-4 py-2 text-xs font-bold rounded-xl transition <?= ($filtroActual === 'inactivo') ? 'bg-rose-700 text-white shadow' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' ?>">
            <i class="fa-solid fa-trash-can mr-2 <?= ($filtroActual === 'inactivo') ? 'text-rose-200' : 'text-rose-400' ?>"></i>
            Papelera / Retirados
            <span class="ml-2 px-2 py-0.5 rounded-full text-[10px] <?= ($filtroActual === 'inactivo') ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600' ?>">
                <?= $totalInactivos ?? 0 ?>
            </span>
        </a>
    <?php endif; ?>
</div>

<!-- BARRA DE BÚSQUEDA RÁPIDA -->
<div class="mb-6 bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm">
    <form action="index.php" method="GET" class="flex flex-col sm:flex-row gap-3">
        <input type="hidden" name="accion" value="listado">
        <input type="hidden" name="filtro" value="<?= e($filtroActual) ?>">
        <div class="relative flex-1">
            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                <i class="fa-solid fa-magnifying-glass"></i>
            </span>
            <input type="text" name="q" value="<?= e($_GET['q'] ?? '') ?>" 
                   placeholder="Buscar por DNI, nombres, apellidos o celular..."
                   class="touch-target w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-300 text-slate-800 placeholder-slate-400 focus:border-[#005B94] focus:ring-2 focus:ring-[#005B94]/20 transition text-xs">
        </div>
        <div class="flex items-center space-x-2">
            <button type="submit" class="touch-target px-5 py-2.5 bg-[#005B94] text-white font-bold text-xs rounded-xl hover:bg-[#0A2540] transition">
                Buscar
            </button>
            <?php if (!empty($_GET['q'])): ?>
                <a href="index.php?accion=listado&filtro=<?= e($filtroActual) ?>" class="touch-target px-3.5 py-2.5 bg-slate-100 text-slate-600 font-bold text-xs rounded-xl hover:bg-slate-200 transition">
                    Limpiar
                </a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- LISTADO EN TABLA RESPONSIVE -->
<div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
    <?php if (empty($pacientes)): ?>
        <div class="p-12 text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-100 text-slate-400 mb-4">
                <i class="fa-solid fa-user-slash text-2xl"></i>
            </div>
            <h3 class="text-sm font-bold text-slate-800">No se encontraron expedientes</h3>
            <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                <?= !empty($_GET['q']) ? 'No hay resultados que coincidan con su búsqueda.' : ($filtroActual === 'inactivo' ? 'La papelera está vacía. No hay pacientes retirados.' : 'No hay pacientes activos registrados en el sistema.') ?>
            </p>
        </div>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200 text-[10px] font-bold uppercase tracking-wider text-slate-500">
                        <th class="py-3.5 px-4 sm:px-6">Paciente / Identificación</th>
                        <th class="py-3.5 px-4 hidden sm:table-cell">Contacto</th>
                        <th class="py-3.5 px-4">Alertas Clínicas</th>
                        <th class="py-3.5 px-4 hidden lg:table-cell">Trazabilidad de Creación</th>
                        <th class="py-3.5 px-4 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    <?php foreach ($pacientes as $p): ?>
                        <tr class="hover:bg-slate-50/70 transition <?= $p['estado'] === 'inactivo' ? 'bg-rose-50/30' : '' ?>">
                            <!-- Nombre y Documento -->
                            <td class="py-4 px-4 sm:px-6">
                                <a href="index.php?accion=detalle&id=<?= $p['id'] ?>" class="font-black text-slate-900 text-sm hover:text-[#005B94] transition">
                                    <?= e($p['apellido']) ?>, <?= e($p['nombre']) ?>
                                </a>
                                <div class="text-[11px] text-slate-500 mt-0.5 flex items-center space-x-2">
                                    <span class="inline-block px-2 py-0.5 rounded bg-slate-100 font-mono font-bold text-slate-700 border border-slate-200">
                                        <?= e($p['tipo_documento']) ?>: <?= e($p['documento_identidad']) ?>
                                    </span>
                                    <span>• <?= e($p['genero']) ?></span>
                                    <?php if ($p['estado'] === 'inactivo'): ?>
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800 border border-rose-200">
                                            Retirado
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </td>

                            <!-- Teléfono y Contacto -->
                            <td class="py-4 px-4 hidden sm:table-cell">
                                <div class="font-bold text-slate-800 flex items-center">
                                    <i class="fa-solid fa-phone text-[#005B94] mr-1.5 text-[11px]"></i> <?= e($p['telefono']) ?>
                                </div>
                                <?php if (!empty($p['email'])): ?>
                                    <div class="text-[10px] text-slate-400 mt-0.5 truncate max-w-[150px]">
                                        <?= e($p['email']) ?>
                                    </div>
                                <?php endif; ?>
                            </td>

                            <!-- Alertas de Riesgo Podológico -->
                            <td class="py-4 px-4">
                                <div class="flex flex-wrap gap-1">
                                    <?php if (!empty($p['es_diabetico'])): ?>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800 border border-rose-200">
                                            <i class="fa-solid fa-triangle-exclamation mr-1 text-rose-600"></i> Diabético
                                        </span>
                                    <?php endif; ?>

                                    <?php if (!empty($p['usa_anticoagulantes'])): ?>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                            <i class="fa-solid fa-droplet mr-1 text-amber-600"></i> Anticoagulado
                                        </span>
                                    <?php endif; ?>

                                    <?php if (!empty($p['problemas_circulatorios'])): ?>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-cyan-100 text-cyan-800 border border-cyan-200">
                                            Circulación
                                        </span>
                                    <?php endif; ?>

                                    <?php if (empty($p['es_diabetico']) && empty($p['usa_anticoagulantes']) && empty($p['problemas_circulatorios'])): ?>
                                        <span class="text-[11px] text-slate-400 italic">Sin alertas</span>
                                    <?php endif; ?>
                                </div>
                            </td>

                            <!-- Creado Por (Trazabilidad) -->
                            <td class="py-4 px-4 hidden lg:table-cell text-[11px] text-slate-600">
                                <div class="font-semibold text-slate-800">
                                    <?= e($p['creado_por_nombre'] ?? 'Personal de Clínica') ?>
                                </div>
                                <div class="text-[10px] text-slate-400 font-mono">
                                    <?= e($p['fecha_creacion'] ?? '') ?> <?= e($p['hora_creacion'] ?? '') ?>
                                </div>
                            </td>

                            <!-- Acciones -->
                            <td class="py-4 px-4 text-right">
                                <div class="inline-flex items-center space-x-1.5">
                                    <!-- Ver Ficha -->
                                    <a href="index.php?accion=detalle&id=<?= $p['id'] ?>" 
                                       class="touch-target inline-flex items-center justify-center px-3 py-1.5 rounded-xl bg-[#005B94]/10 text-[#005B94] hover:bg-[#005B94] hover:text-white font-bold text-xs transition">
                                        <i class="fa-solid fa-folder-open mr-1.5"></i> Ficha
                                    </a>

                                    <?php if ($filtroActual === 'activo'): ?>
                                        <!-- Editar datos secundarios (Admin / SuperAdmin) -->
                                        <?php if (puedeEditarPaciente()): ?>
                                            <a href="index.php?accion=editar&id=<?= $p['id'] ?>" 
                                               class="touch-target inline-flex items-center justify-center w-8 h-8 rounded-xl bg-slate-100 hover:bg-[#005B94] text-slate-600 hover:text-white transition" title="Editar Datos Secundarios">
                                                <i class="fa-solid fa-pen text-[10px]"></i>
                                            </a>
                                        <?php endif; ?>

                                        <!-- Retirar / Borrado Lógico (Admin / SuperAdmin) -->
                                        <?php if (puedeEliminarPaciente()): ?>
                                            <a href="index.php?accion=eliminar&id=<?= $p['id'] ?>" 
                                               onclick="return confirm('¿Está seguro de retirar este paciente del directorio activo? Pasará a la Papelera preservando la auditoría.');"
                                               class="touch-target inline-flex items-center justify-center w-8 h-8 rounded-xl bg-rose-50 hover:bg-rose-600 text-rose-600 hover:text-white transition" 
                                               title="Retirar Paciente">
                                                <i class="fa-solid fa-trash-can text-[10px]"></i>
                                            </a>
                                        <?php endif; ?>

                                    <?php else: ?>
                                        <!-- En Papelera: Restaurar -->
                                        <?php if (puedeEliminarPaciente()): ?>
                                            <a href="index.php?accion=restaurar&id=<?= $p['id'] ?>" 
                                               onclick="return confirm('¿Desea restaurar a este paciente al directorio activo?');"
                                               class="touch-target inline-flex items-center justify-center px-2.5 py-1.5 rounded-xl bg-emerald-50 hover:bg-emerald-600 text-emerald-700 hover:text-white font-bold text-xs transition border border-emerald-200" 
                                               title="Restaurar al directorio activo">
                                                <i class="fa-solid fa-rotate-left mr-1"></i> Restaurar
                                            </a>
                                        <?php endif; ?>

                                        <!-- En Papelera: Purga Física Permanente (Exclusivo SuperAdmin) -->
                                        <?php if (esSuperAdmin()): ?>
                                            <a href="index.php?accion=purgar&id=<?= $p['id'] ?>" 
                                               onclick="return confirm('¡ADVERTENCIA CRÍTICA DE SUPERADMIN!\n\n¿Eliminar definitivamente este paciente y todas sus historias clínicas de la Base de Datos?\nEsta acción es irreversible y borrará el registro por completo.');"
                                               class="touch-target inline-flex items-center justify-center px-2.5 py-1.5 rounded-xl bg-rose-700 hover:bg-rose-900 text-white font-bold text-xs transition shadow-sm" 
                                               title="Purga Física de Base de Datos">
                                                <i class="fa-solid fa-skull-crossbones mr-1"></i> Purgar
                                            </a>
                                        <?php endif; ?>

                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php require_once dirname(__DIR__) . '/layout/footer.php'; ?>
