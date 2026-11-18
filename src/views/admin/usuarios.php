<?php
$tituloPagina = "Gestión de Personal & Usuarios";
require_once dirname(__DIR__) . '/layout/header.php';
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
    <div class="flex items-center space-x-3">
        <div class="w-12 h-12 rounded-2xl bg-[#005B94]/10 text-[#005B94] flex items-center justify-center text-xl shadow-sm">
            <i class="fa-solid fa-users-gear text-2xl text-[#005B94]"></i>
        </div>
        <div>
            <div class="flex items-center space-x-2">
                <h1 class="text-2xl font-black text-slate-900">Personal & Cuentas de Acceso</h1>
                <span class="px-2.5 py-0.5 rounded-full bg-slate-200 text-slate-700 font-bold text-[10px] uppercase">
                    Control RBAC
                </span>
            </div>
            <p class="text-xs text-slate-500 mt-0.5">
                Administración de podólogos, recepcionistas y credenciales de acceso por DNI.
            </p>
        </div>
    </div>
    <div>
        <button type="button" onclick="document.getElementById('modalNuevoUsuario').classList.remove('hidden')" 
                class="touch-target inline-flex items-center justify-center px-5 py-3 rounded-xl bg-[#005B94] text-white font-bold text-xs hover:bg-[#0A2540] active:scale-95 transition shadow-sm">
            <i class="fa-solid fa-user-plus mr-2 text-[#2EC4B6]"></i> Registrar Nuevo Usuario
        </button>
    </div>
</div>

<!-- TARJETA INFORMATIVA: POLÍTICA DE CONTRASEÑA INICIAL -->
<div class="mb-6 p-4 rounded-2xl bg-sky-50 border border-sky-200 text-sky-900 text-xs flex items-start space-x-3 shadow-sm">
    <i class="fa-solid fa-id-card text-sky-600 text-xl mt-0.5"></i>
    <div>
        <strong>Política de Seguridad para Nuevo Personal:</strong>
        Al dar de alta a un trabajador, su <strong>contraseña inicial será automáticamente su número de DNI</strong>. 
        Al ingresar por primera vez, el sistema bloqueará su navegación y le solicitará de forma obligatoria crear su contraseña personal privada.
    </div>
</div>

<!-- TABLA DE USUARIOS DEL SISTEMA -->
<div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden mb-8">
    <div class="bg-slate-50/80 px-6 py-4 border-b border-slate-200 flex items-center justify-between">
        <div class="flex items-center space-x-2">
            <i class="fa-solid fa-list-check text-[#005B94]"></i>
            <h2 class="text-sm font-bold text-slate-800">Cuentas Registradas en la Clínica</h2>
        </div>
        <span class="text-xs font-mono font-bold text-slate-500">
            <?= count($usuarios) ?> usuario(s)
        </span>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-xs">
            <thead>
                <tr class="bg-slate-50 text-[10px] font-bold uppercase tracking-wider text-slate-500 border-b border-slate-200">
                    <th class="py-3.5 px-4 sm:px-6">Usuario / DNI</th>
                    <th class="py-3.5 px-4">Rol & Nivel</th>
                    <th class="py-3.5 px-4 hidden sm:table-cell">Especialidad / Cargo</th>
                    <th class="py-3.5 px-4 hidden md:table-cell">Contacto</th>
                    <th class="py-3.5 px-4">Estado</th>
                    <th class="py-3.5 px-4 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-slate-700">
                <?php foreach ($usuarios as $u): ?>
                    <tr class="hover:bg-slate-50/70 transition">
                        <!-- Nombre y DNI -->
                        <td class="py-4 px-4 sm:px-6">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-xs shadow-sm <?= $u['rol_slug'] === 'superadmin' ? 'bg-purple-100 text-purple-800' : ($u['rol_slug'] === 'admin' ? 'bg-[#005B94]/10 text-[#005B94]' : 'bg-teal-100 text-teal-800') ?>">
                                    <?= strtoupper(substr($u['nombre'], 0, 1) . substr($u['apellido'], 0, 1)) ?>
                                </div>
                                <div>
                                    <div class="font-black text-slate-900 text-sm">
                                        <?= e($u['nombre']) ?> <?= e($u['apellido']) ?>
                                    </div>
                                    <div class="text-[11px] text-slate-500 mt-0.5 flex items-center space-x-2">
                                        <span class="inline-block px-2 py-0.5 rounded bg-slate-100 font-mono font-bold text-slate-700 border border-slate-200">
                                            DNI: <?= e($u['dni']) ?>
                                        </span>
                                        <?php if (!empty($u['debe_cambiar_password'])): ?>
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] bg-amber-50 text-amber-700 border border-amber-200" title="Aún no ha actualizado su contraseña inicial">
                                                <i class="fa-solid fa-key mr-1 text-[8px]"></i> Clave inicial pendiente
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </td>

                        <!-- Rol -->
                        <td class="py-4 px-4">
                            <?php if ($u['rol_slug'] === 'superadmin'): ?>
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold bg-purple-100 text-purple-800 border border-purple-200 shadow-sm">
                                    <i class="fa-solid fa-shield-halved mr-1 text-purple-600"></i> SuperAdmin Técnico
                                </span>
                            <?php elseif ($u['rol_slug'] === 'admin'): ?>
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold bg-[#005B94]/10 text-[#005B94] border border-[#005B94]/20 shadow-sm">
                                    <i class="fa-solid fa-crown mr-1 text-[#2EC4B6]"></i> Dueña / Administradora
                                </span>
                            <?php elseif ($u['rol_slug'] === 'especialista'): ?>
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-teal-50 text-teal-800 border border-teal-200">
                                    <i class="fa-solid fa-user-doctor mr-1 text-teal-600"></i> Podólogo / Especialista
                                </span>
                            <?php else: ?>
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-medium bg-slate-100 text-slate-700 border border-slate-200">
                                    <i class="fa-solid fa-headset mr-1 text-slate-500"></i> <?= e($u['rol_nombre']) ?>
                                </span>
                            <?php endif; ?>
                        </td>

                        <!-- Especialidad -->
                        <td class="py-4 px-4 hidden sm:table-cell">
                            <div class="font-medium text-slate-800">
                                <?= e($u['especialidad'] ?? 'Sin especialidad definida') ?>
                            </div>
                            <?php if (!empty($u['numero_colegiado'])): ?>
                                <div class="text-[10px] text-slate-400 font-mono mt-0.5">
                                    Colegiatura: <?= e($u['numero_colegiado']) ?>
                                </div>
                            <?php endif; ?>
                        </td>

                        <!-- Contacto -->
                        <td class="py-4 px-4 hidden md:table-cell">
                            <?php if (!empty($u['telefono'])): ?>
                                <div class="font-bold text-slate-700 flex items-center">
                                    <i class="fa-solid fa-phone text-[#005B94] mr-1.5 text-[10px]"></i> <?= e($u['telefono']) ?>
                                </div>
                            <?php endif; ?>
                            <?php if (!empty($u['email'])): ?>
                                <div class="text-[10px] text-slate-400 mt-0.5 truncate max-w-[160px]">
                                    <?= e($u['email']) ?>
                                </div>
                            <?php endif; ?>
                        </td>

                        <!-- Estado -->
                        <td class="py-4 px-4">
                            <?php if ($u['estado'] === 'activo'): ?>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                    <i class="fa-solid fa-circle text-[6px] mr-1 text-emerald-600"></i> Activo
                                </span>
                            <?php else: ?>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800 border border-rose-200">
                                    <i class="fa-solid fa-circle text-[6px] mr-1 text-rose-600"></i> Inactivo
                                </span>
                            <?php endif; ?>
                        </td>

                        <!-- Acciones -->
                        <td class="py-4 px-4 text-right">
                            <div class="inline-flex items-center space-x-1.5">
                                <!-- Botón Editar Información -->
                                <button type="button" 
                                        onclick='abrirModalEditar(<?= json_encode($u, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)'
                                        class="touch-target inline-flex items-center justify-center w-8 h-8 rounded-xl bg-slate-100 hover:bg-[#005B94] text-slate-600 hover:text-white transition shadow-sm" 
                                        title="Editar Datos del Usuario">
                                    <i class="fa-solid fa-pen text-[11px]"></i>
                                </button>

                                <!-- Botón Resetear Contraseña a DNI -->
                                <a href="index.php?accion=usuario_reset&id=<?= $u['id'] ?>" 
                                   onclick="return confirm('¿Restablecer la contraseña del usuario DNI <?= $u['dni'] ?> a su propio DNI? El usuario deberá cambiarla al ingresar.');"
                                   class="touch-target inline-flex items-center justify-center px-2 py-1.5 rounded-xl bg-amber-50 hover:bg-amber-600 text-amber-700 hover:text-white font-bold text-xs transition border border-amber-200" 
                                   title="Restablecer contraseña a su DNI">
                                    <i class="fa-solid fa-rotate-left mr-1"></i> Reset
                                </a>

                                <!-- Botón Activar / Desactivar -->
                                <?php if ($u['id'] !== $usuarioActivo['id']): ?>
                                    <a href="index.php?accion=usuario_estado&id=<?= $u['id'] ?>" 
                                       onclick="return confirm('¿Está seguro de cambiar el estado de acceso de <?= e($u['nombre']) ?>?');"
                                       class="touch-target inline-flex items-center justify-center w-8 h-8 rounded-xl <?= $u['estado'] === 'activo' ? 'bg-slate-100 hover:bg-rose-600 text-slate-600 hover:text-white' : 'bg-emerald-50 hover:bg-emerald-600 text-emerald-700 hover:text-white' ?> transition" 
                                       title="<?= $u['estado'] === 'activo' ? 'Desactivar cuenta' : 'Activar cuenta' ?>">
                                        <i class="fa-solid <?= $u['estado'] === 'activo' ? 'fa-user-slash' : 'fa-user-check' ?> text-[11px]"></i>
                                    </a>
                                <?php endif; ?>

                                <!-- Botón Eliminar Usuario -->
                                <?php if ($u['id'] !== $usuarioActivo['id'] && $u['rol_slug'] !== 'superadmin'): ?>
                                    <a href="index.php?accion=usuario_eliminar&id=<?= $u['id'] ?>" 
                                       onclick="return confirm('¿Está seguro de eliminar al usuario <?= e($u['nombre']) ?> <?= e($u['apellido']) ?>?\n\nSi el usuario tiene historias clínicas o pacientes creados, el sistema desactivará su cuenta para proteger la auditoría médica legal.');"
                                       class="touch-target inline-flex items-center justify-center w-8 h-8 rounded-xl bg-rose-50 hover:bg-rose-600 text-rose-600 hover:text-white transition shadow-sm" 
                                       title="Eliminar Usuario">
                                        <i class="fa-solid fa-trash-can text-[11px]"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- MODAL PARA REGISTRAR NUEVO USUARIO -->
<div id="modalNuevoUsuario" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-slate-100 animate-scale-in">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-[#005B94]/10 text-[#005B94] flex items-center justify-center text-lg">
                    <i class="fa-solid fa-user-plus text-[#005B94]"></i>
                </div>
                <div>
                    <h3 class="text-lg font-black text-slate-900">Registrar Nuevo Personal</h3>
                    <p class="text-xs text-slate-500">Credencial de acceso inicial mediante DNI peruano</p>
                </div>
            </div>
            <button type="button" onclick="document.getElementById('modalNuevoUsuario').classList.add('hidden')" 
                    class="text-slate-400 hover:text-slate-700 text-xl font-bold p-1">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form action="index.php?accion=usuario_guardar" method="POST" class="mt-5 space-y-4">
            
            <!-- DNI (Contraseña inicial) -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                    Número de DNI <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <i class="fa-solid fa-id-card"></i>
                    </span>
                    <input type="text" name="dni" required maxlength="15" placeholder="Ej: 43285473 o 00252525"
                           class="touch-target w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-300 text-slate-900 font-mono font-bold focus:border-[#005B94] focus:ring-2 focus:ring-[#005B94]/20 transition text-sm">
                </div>
                <p class="text-[11px] text-slate-500 mt-1">
                    <i class="fa-solid fa-circle-info text-[#005B94] mr-1"></i> Este DNI será tanto el usuario como la <strong>contraseña inicial</strong> de acceso.
                </p>
            </div>

            <!-- Nombres y Apellidos -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                        Nombres <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="nombre" required placeholder="Ej: Diana"
                           class="touch-target w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-slate-900 focus:border-[#005B94] focus:ring-2 focus:ring-[#005B94]/20 transition text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                        Apellidos <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="apellido" required placeholder="Ej: Morales"
                           class="touch-target w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-slate-900 focus:border-[#005B94] focus:ring-2 focus:ring-[#005B94]/20 transition text-sm">
                </div>
            </div>

            <!-- Rol de Acceso -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                    Rol y Nivel de Acceso <span class="text-rose-500">*</span>
                </label>
                <select name="rol_id" required 
                        class="touch-target w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-slate-900 focus:border-[#005B94] focus:ring-2 focus:ring-[#005B94]/20 transition text-sm">
                    <option value="">-- Seleccione un Rol --</option>
                    <?php foreach ($rolesDisponibles as $r): ?>
                        <option value="<?= $r['id'] ?>" <?= $r['slug'] === 'especialista' ? 'selected' : '' ?>>
                            <?= e($r['nombre']) ?> (<?= ucfirst($r['slug']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Especialidad / Cargo -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                        Especialidad / Cargo
                    </label>
                    <input type="text" name="especialidad" placeholder="Ej: Podóloga Clínica, Recepción"
                           class="touch-target w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-slate-900 focus:border-[#005B94] focus:ring-2 focus:ring-[#005B94]/20 transition text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                        Colegiatura (COP)
                    </label>
                    <input type="text" name="numero_colegiado" placeholder="Ej: COP-12849"
                           class="touch-target w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-slate-900 font-mono focus:border-[#005B94] focus:ring-2 focus:ring-[#005B94]/20 transition text-sm">
                </div>
            </div>

            <!-- Celular y Email -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                        Teléfono / Celular
                    </label>
                    <input type="tel" name="telefono" placeholder="Ej: 991234567"
                           class="touch-target w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-slate-900 focus:border-[#005B94] focus:ring-2 focus:ring-[#005B94]/20 transition text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                        Correo Electrónico
                    </label>
                    <input type="email" name="email" placeholder="ejemplo@clinica.com"
                           class="touch-target w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-slate-900 focus:border-[#005B94] focus:ring-2 focus:ring-[#005B94]/20 transition text-sm">
                </div>
            </div>

            <!-- Botones -->
            <div class="pt-4 flex items-center justify-end space-x-3 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('modalNuevoUsuario').classList.add('hidden')"
                        class="touch-target px-4 py-2.5 rounded-xl border border-slate-300 text-slate-700 text-xs font-bold hover:bg-slate-50 transition">
                    Cancelar
                </button>
                <button type="submit" 
                        class="touch-target px-6 py-2.5 rounded-xl bg-[#005B94] text-white text-xs font-bold hover:bg-[#0A2540] active:scale-95 transition shadow-sm">
                    <i class="fa-solid fa-save mr-1.5 text-[#2EC4B6]"></i> Crear Usuario
                </button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL PARA EDITAR USUARIO -->
<div id="modalEditarUsuario" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-slate-100 animate-scale-in">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-[#005B94]/10 text-[#005B94] flex items-center justify-center text-lg">
                    <i class="fa-solid fa-user-pen text-[#005B94]"></i>
                </div>
                <div>
                    <h3 class="text-lg font-black text-slate-900">Editar Información del Usuario</h3>
                    <p class="text-xs text-slate-500">Actualizar datos de personal, cargo y contacto</p>
                </div>
            </div>
            <button type="button" onclick="cerrarModalEditar()" 
                    class="text-slate-400 hover:text-slate-700 text-xl font-bold p-1">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form action="index.php?accion=usuario_actualizar" method="POST" class="mt-5 space-y-4">
            <input type="hidden" name="id" id="edit_id" value="">

            <!-- DNI -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                    Número de DNI <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <i class="fa-solid fa-id-card"></i>
                    </span>
                    <input type="text" name="dni" id="edit_dni" required maxlength="15"
                           class="touch-target w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-300 text-slate-900 font-mono font-bold focus:border-[#005B94] focus:ring-2 focus:ring-[#005B94]/20 transition text-sm">
                </div>
            </div>

            <!-- Nombres y Apellidos -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                        Nombres <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="nombre" id="edit_nombre" required
                           class="touch-target w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-slate-900 focus:border-[#005B94] focus:ring-2 focus:ring-[#005B94]/20 transition text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                        Apellidos <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="apellido" id="edit_apellido" required
                           class="touch-target w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-slate-900 focus:border-[#005B94] focus:ring-2 focus:ring-[#005B94]/20 transition text-sm">
                </div>
            </div>

            <!-- Rol de Acceso -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                    Rol y Nivel de Acceso <span class="text-rose-500">*</span>
                </label>
                <select name="rol_id" id="edit_rol_id" required 
                        class="touch-target w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-slate-900 focus:border-[#005B94] focus:ring-2 focus:ring-[#005B94]/20 transition text-sm">
                    <?php foreach ($rolesDisponibles as $r): ?>
                        <option value="<?= $r['id'] ?>">
                            <?= e($r['nombre']) ?> (<?= ucfirst($r['slug']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Especialidad / Cargo -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                        Especialidad / Cargo
                    </label>
                    <input type="text" name="especialidad" id="edit_especialidad" placeholder="Ej: Podóloga Clínica"
                           class="touch-target w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-slate-900 focus:border-[#005B94] focus:ring-2 focus:ring-[#005B94]/20 transition text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                        Colegiatura (COP)
                    </label>
                    <input type="text" name="numero_colegiado" id="edit_numero_colegiado" placeholder="Ej: COP-12849"
                           class="touch-target w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-slate-900 font-mono focus:border-[#005B94] focus:ring-2 focus:ring-[#005B94]/20 transition text-sm">
                </div>
            </div>

            <!-- Celular y Email -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                        Teléfono / Celular
                    </label>
                    <input type="tel" name="telefono" id="edit_telefono" placeholder="Ej: 991234567"
                           class="touch-target w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-slate-900 focus:border-[#005B94] focus:ring-2 focus:ring-[#005B94]/20 transition text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                        Correo Electrónico
                    </label>
                    <input type="email" name="email" id="edit_email" placeholder="ejemplo@clinica.com"
                           class="touch-target w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-slate-900 focus:border-[#005B94] focus:ring-2 focus:ring-[#005B94]/20 transition text-sm">
                </div>
            </div>

            <!-- Botones -->
            <div class="pt-4 flex items-center justify-end space-x-3 border-t border-slate-100">
                <button type="button" onclick="cerrarModalEditar()"
                        class="touch-target px-4 py-2.5 rounded-xl border border-slate-300 text-slate-700 text-xs font-bold hover:bg-slate-50 transition">
                    Cancelar
                </button>
                <button type="submit" 
                        class="touch-target px-6 py-2.5 rounded-xl bg-[#005B94] text-white text-xs font-bold hover:bg-[#0A2540] active:scale-95 transition shadow-sm">
                    <i class="fa-solid fa-check mr-1.5 text-[#2EC4B6]"></i> Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function abrirModalEditar(usuario) {
    document.getElementById('edit_id').value = usuario.id;
    document.getElementById('edit_dni').value = usuario.dni;
    document.getElementById('edit_nombre').value = usuario.nombre;
    document.getElementById('edit_apellido').value = usuario.apellido;
    document.getElementById('edit_rol_id').value = usuario.rol_id;
    document.getElementById('edit_especialidad').value = usuario.especialidad || '';
    document.getElementById('edit_numero_colegiado').value = usuario.numero_colegiado || '';
    document.getElementById('edit_telefono').value = usuario.telefono || '';
    document.getElementById('edit_email').value = usuario.email || '';
    document.getElementById('modalEditarUsuario').classList.remove('hidden');
}

function cerrarModalEditar() {
    document.getElementById('modalEditarUsuario').classList.add('hidden');
}
</script>

<?php require_once dirname(__DIR__) . '/layout/footer.php'; ?>
