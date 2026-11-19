<?php
declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/config/app.php';
require_once dirname(__DIR__, 2) . '/config/database.php';
require_once dirname(__DIR__, 2) . '/config/auth.php';

/**
 * Controlador de Gestión de Personal y Usuarios del Sistema
 * Podología Ana Ramírez
 */
class UsuarioController
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::pdo();
    }

    /**
     * Lista todos los usuarios con sus roles y estados
     */
    public function listar(): void
    {
        requerirRol(['superadmin', 'admin']);
        $usuarioActivo = usuarioActual();

        $stmt = $this->db->query("
            SELECT u.*, r.nombre AS rol_nombre, r.slug AS rol_slug, r.nivel_jerarquia
            FROM usuarios u
            INNER JOIN roles r ON u.rol_id = r.id
            ORDER BY r.nivel_jerarquia ASC, u.nombre ASC
        ");
        $usuarios = $stmt->fetchAll();

        // Roles disponibles para creación según permisos
        if (esSuperAdmin()) {
            $stmtRoles = $this->db->query("SELECT * FROM roles ORDER BY nivel_jerarquia ASC");
        } else {
            $stmtRoles = $this->db->query("SELECT * FROM roles WHERE slug != 'superadmin' ORDER BY nivel_jerarquia ASC");
        }
        $rolesDisponibles = $stmtRoles->fetchAll();

        $mensajeExito = flash('exito');
        $mensajeError = flash('error');

        require_once dirname(__DIR__) . '/views/admin/usuarios.php';
    }

    /**
     * Registra un nuevo usuario en el sistema
     * La contraseña inicial es su número de DNI (admite ceros iniciales)
     */
    public function guardar(): void
    {
        requerirRol(['superadmin', 'admin']);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?accion=usuarios');
            exit;
        }

        $dni          = trim($_POST['dni'] ?? '');
        $nombre       = trim($_POST['nombre'] ?? '');
        $apellido     = trim($_POST['apellido'] ?? '');
        $email        = !empty($_POST['email']) ? trim($_POST['email']) : null;
        $telefono     = !empty($_POST['telefono']) ? trim($_POST['telefono']) : null;
        $rolId        = (int) ($_POST['rol_id'] ?? 0);
        $especialidad = !empty($_POST['especialidad']) ? trim($_POST['especialidad']) : null;
        $colegiatura  = !empty($_POST['numero_colegiado']) ? trim($_POST['numero_colegiado']) : null;

        // Validaciones
        if (empty($dni) || strlen($dni) < 6 || strlen($dni) > 15) {
            flash('error', 'El número de DNI debe contener entre 6 y 15 dígitos válidos.');
            header('Location: index.php?accion=usuarios');
            exit;
        }

        if (empty($nombre) || empty($apellido) || $rolId <= 0) {
            flash('error', 'Nombres, Apellidos y Rol son campos obligatorios.');
            header('Location: index.php?accion=usuarios');
            exit;
        }

        // Validar si el DNI ya existe
        $stmtCheck = $this->db->prepare("SELECT id FROM usuarios WHERE dni = :dni LIMIT 1");
        $stmtCheck->execute([':dni' => $dni]);
        if ($stmtCheck->fetch()) {
            flash('error', "El DNI {$dni} ya se encuentra registrado para otro usuario.");
            header('Location: index.php?accion=usuarios');
            exit;
        }

        // Restringir creación de SuperAdmin a usuarios que no sean SuperAdmin
        if (!esSuperAdmin()) {
            $stmtRolCheck = $this->db->prepare("SELECT slug FROM roles WHERE id = :id");
            $stmtRolCheck->execute([':id' => $rolId]);
            $slug = $stmtRolCheck->fetchColumn();
            if ($slug === 'superadmin') {
                flash('error', 'Solo el SuperAdmin técnico puede otorgar permisos de SuperAdmin.');
                header('Location: index.php?accion=usuarios');
                exit;
            }
        }

        // Contraseña inicial = Número de DNI (con cambio obligatorio al ingresar)
        $passwordHash = password_hash($dni, PASSWORD_BCRYPT);
        $debeCambiar = 1;

        $sql = "INSERT INTO usuarios 
                (rol_id, dni, nombre, apellido, email, password_hash, debe_cambiar_password, telefono, especialidad, numero_colegiado, estado)
                VALUES 
                (:rol_id, :dni, :nombre, :apellido, :email, :password_hash, :debe_cambiar, :telefono, :especialidad, :colegiado, 'activo')";

        $stmt = $this->db->prepare($sql);
        $res = $stmt->execute([
            ':rol_id'       => $rolId,
            ':dni'          => $dni,
            ':nombre'       => $nombre,
            ':apellido'     => $apellido,
            ':email'        => $email,
            ':password_hash'=> $passwordHash,
            ':debe_cambiar' => $debeCambiar,
            ':telefono'     => $telefono,
            ':especialidad' => $especialidad,
            ':colegiado'    => $colegiatura
        ]);

        if ($res) {
            $u = usuarioActual();
            registrarLog('INFO', 'USUARIOS', 'CREAR_USUARIO', 
                "Nuevo usuario registrado: DNI {$dni} ({$nombre} {$apellido}) por {$u['nombre_completo']}", (int)$u['id']);
            flash('exito', "Usuario <strong>{$nombre} {$apellido}</strong> registrado con éxito. Su contraseña inicial es su número de DNI (<strong>{$dni}</strong>) y deberá cambiarla al primer ingreso.");
        } else {
            flash('error', 'Error al registrar el nuevo usuario en el sistema.');
        }

        header('Location: index.php?accion=usuarios');
        exit;
    }

    /**
     * Alterna el estado activo/inactivo de un usuario
     */
    public function cambiarEstado(): void
    {
        requerirRol(['superadmin', 'admin']);
        $id = (int) ($_GET['id'] ?? 0);
        $u = usuarioActual();

        if ($id <= 0) {
            header('Location: index.php?accion=usuarios');
            exit;
        }

        // No desactivarse a uno mismo
        if ($id === (int) $u['id']) {
            flash('error', 'Seguridad: No puede cambiar el estado de su propia cuenta activa.');
            header('Location: index.php?accion=usuarios');
            exit;
        }

        $stmt = $this->db->prepare("SELECT u.*, r.slug AS rol_slug FROM usuarios u INNER JOIN roles r ON u.rol_id = r.id WHERE u.id = :id");
        $stmt->execute([':id' => $id]);
        $target = $stmt->fetch();

        if (!$target) {
            flash('error', 'Usuario no encontrado.');
            header('Location: index.php?accion=usuarios');
            exit;
        }

        // Proteger cuenta SuperAdmin
        if ($target['rol_slug'] === 'superadmin' && !esSuperAdmin()) {
            flash('error', 'No tiene permisos para modificar la cuenta de SuperAdmin.');
            header('Location: index.php?accion=usuarios');
            exit;
        }

        $nuevoEstado = $target['estado'] === 'activo' ? 'inactivo' : 'activo';
        $up = $this->db->prepare("UPDATE usuarios SET estado = :estado WHERE id = :id");
        $up->execute([':estado' => $nuevoEstado, ':id' => $id]);

        registrarLog('WARNING', 'USUARIOS', 'CAMBIO_ESTADO', 
            "El usuario DNI {$target['dni']} ({$target['nombre']} {$target['apellido']}) fue establecido en estado '{$nuevoEstado}' por {$u['nombre_completo']}", (int)$u['id']);

        flash('exito', "El usuario <strong>{$target['nombre']} {$target['apellido']}</strong> ahora está <strong>{$nuevoEstado}</strong>.");
        header('Location: index.php?accion=usuarios');
        exit;
    }

    /**
     * Restablece la contraseña de un usuario a su número de DNI
     */
    public function resetearPassword(): void
    {
        requerirRol(['superadmin', 'admin']);
        $id = (int) ($_GET['id'] ?? 0);
        $u = usuarioActual();

        if ($id <= 0) {
            header('Location: index.php?accion=usuarios');
            exit;
        }

        $stmt = $this->db->prepare("SELECT u.*, r.slug AS rol_slug FROM usuarios u INNER JOIN roles r ON u.rol_id = r.id WHERE u.id = :id");
        $stmt->execute([':id' => $id]);
        $target = $stmt->fetch();

        if (!$target) {
            flash('error', 'Usuario no encontrado.');
            header('Location: index.php?accion=usuarios');
            exit;
        }

        if ($target['rol_slug'] === 'superadmin' && !esSuperAdmin()) {
            flash('error', 'No tiene autorización para restablecer la contraseña de un SuperAdmin.');
            header('Location: index.php?accion=usuarios');
            exit;
        }

        $hash = password_hash($target['dni'], PASSWORD_BCRYPT);
        $up = $this->db->prepare("UPDATE usuarios SET password_hash = :hash, debe_cambiar_password = 1 WHERE id = :id");
        $up->execute([':hash' => $hash, ':id' => $id]);

        registrarLog('SECURITY', 'USUARIOS', 'RESET_PASSWORD', 
            "La contraseña del usuario DNI {$target['dni']} fue reseteada a su DNI por {$u['nombre_completo']}", (int)$u['id']);

        flash('exito', "Contraseña restablecida con éxito para <strong>{$target['nombre']} {$target['apellido']}</strong>. Ahora su clave vuelve a ser su DNI (<strong>{$target['dni']}</strong>) y se le exigirá cambiarla al ingresar.");
        header('Location: index.php?accion=usuarios');
        exit;
    }

    /**
     * Actualiza la información de un usuario existente
     */
    public function actualizar(): void
    {
        requerirRol(['superadmin', 'admin']);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?accion=usuarios');
            exit;
        }

        $id           = (int) ($_POST['id'] ?? 0);
        $dni          = trim($_POST['dni'] ?? '');
        $nombre       = trim($_POST['nombre'] ?? '');
        $apellido     = trim($_POST['apellido'] ?? '');
        $email        = !empty($_POST['email']) ? trim($_POST['email']) : null;
        $telefono     = !empty($_POST['telefono']) ? trim($_POST['telefono']) : null;
        $rolId        = (int) ($_POST['rol_id'] ?? 0);
        $especialidad = !empty($_POST['especialidad']) ? trim($_POST['especialidad']) : null;
        $colegiatura  = !empty($_POST['numero_colegiado']) ? trim($_POST['numero_colegiado']) : null;

        if ($id <= 0) {
            flash('error', 'Usuario inválido.');
            header('Location: index.php?accion=usuarios');
            exit;
        }

        // Obtener usuario objetivo
        $stmtTarget = $this->db->prepare("SELECT u.*, r.slug AS rol_slug FROM usuarios u INNER JOIN roles r ON u.rol_id = r.id WHERE u.id = :id");
        $stmtTarget->execute([':id' => $id]);
        $target = $stmtTarget->fetch();

        if (!$target) {
            flash('error', 'El usuario a editar no existe.');
            header('Location: index.php?accion=usuarios');
            exit;
        }

        // Proteger SuperAdmin si el editor no es SuperAdmin
        if ($target['rol_slug'] === 'superadmin' && !esSuperAdmin()) {
            flash('error', 'No tiene permisos para modificar la cuenta de un SuperAdmin.');
            header('Location: index.php?accion=usuarios');
            exit;
        }

        // Validar si el nuevo rol es superadmin y el editor no es superadmin
        if (!esSuperAdmin()) {
            $stmtRolCheck = $this->db->prepare("SELECT slug FROM roles WHERE id = :id");
            $stmtRolCheck->execute([':id' => $rolId]);
            $nuevoSlug = $stmtRolCheck->fetchColumn();
            if ($nuevoSlug === 'superadmin') {
                flash('error', 'Solo un SuperAdmin puede asignar el rol de SuperAdmin.');
                header('Location: index.php?accion=usuarios');
                exit;
            }
        }

        // Validaciones de campos
        if (empty($dni) || strlen($dni) < 6 || strlen($dni) > 15) {
            flash('error', 'El número de DNI debe contener entre 6 y 15 dígitos válidos.');
            header('Location: index.php?accion=usuarios');
            exit;
        }

        if (empty($nombre) || empty($apellido) || $rolId <= 0) {
            flash('error', 'Nombres, Apellidos y Rol son obligatorios.');
            header('Location: index.php?accion=usuarios');
            exit;
        }

        // Validar unicidad del DNI
        $stmtCheck = $this->db->prepare("SELECT id FROM usuarios WHERE dni = :dni AND id != :id LIMIT 1");
        $stmtCheck->execute([':dni' => $dni, ':id' => $id]);
        if ($stmtCheck->fetch()) {
            flash('error', "El DNI {$dni} ya está registrado para otro usuario.");
            header('Location: index.php?accion=usuarios');
            exit;
        }

        $sql = "UPDATE usuarios SET 
                    dni = :dni,
                    nombre = :nombre,
                    apellido = :apellido,
                    email = :email,
                    telefono = :telefono,
                    rol_id = :rol_id,
                    especialidad = :especialidad,
                    numero_colegiado = :colegiado
                WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        $res = $stmt->execute([
            ':dni'          => $dni,
            ':nombre'       => $nombre,
            ':apellido'     => $apellido,
            ':email'        => $email,
            ':telefono'     => $telefono,
            ':rol_id'       => $rolId,
            ':especialidad' => $especialidad,
            ':colegiado'    => $colegiatura,
            ':id'           => $id
        ]);

        if ($res) {
            $u = usuarioActual();
            registrarLog('INFO', 'USUARIOS', 'ACTUALIZAR_USUARIO', 
                "El usuario DNI {$dni} ({$nombre} {$apellido}) fue modificado por {$u['nombre_completo']}", (int)$u['id']);
            flash('exito', "Usuario <strong>{$nombre} {$apellido}</strong> actualizado correctamente.");
        } else {
            flash('error', 'Error al actualizar los datos del usuario.');
        }

        header('Location: index.php?accion=usuarios');
        exit;
    }

    /**
     * Elimina un usuario del sistema (o lo da de baja si tiene historias o auditoría)
     */
    public function eliminar(): void
    {
        requerirRol(['superadmin', 'admin']);
        $id = (int) ($_GET['id'] ?? 0);
        $u = usuarioActual();

        if ($id <= 0) {
            header('Location: index.php?accion=usuarios');
            exit;
        }

        // No eliminarse a sí mismo
        if ($id === (int) $u['id']) {
            flash('error', 'Seguridad: No puede eliminar su propia cuenta en uso.');
            header('Location: index.php?accion=usuarios');
            exit;
        }

        $stmt = $this->db->prepare("SELECT u.*, r.slug AS rol_slug FROM usuarios u INNER JOIN roles r ON u.rol_id = r.id WHERE u.id = :id");
        $stmt->execute([':id' => $id]);
        $target = $stmt->fetch();

        if (!$target) {
            flash('error', 'Usuario no encontrado.');
            header('Location: index.php?accion=usuarios');
            exit;
        }

        // Proteger SuperAdmin
        if ($target['rol_slug'] === 'superadmin') {
            flash('error', 'Seguridad: La cuenta principal de SuperAdmin no puede ser eliminada.');
            header('Location: index.php?accion=usuarios');
            exit;
        }

        // Proteger si quien elimina es admin y el target es admin (solo superadmin puede eliminar a otros admins)
        if ($target['rol_slug'] === 'admin' && !esSuperAdmin()) {
            flash('error', 'Solo el SuperAdmin técnico puede eliminar a otro Administrador.');
            header('Location: index.php?accion=usuarios');
            exit;
        }

        // Comprobar si tiene registros clínicos vinculados que impidan eliminación física por integridad médica
        $stmtP = $this->db->prepare("SELECT COUNT(*) FROM pacientes WHERE creado_por_usuario_id = :id_creador OR eliminado_por_usuario_id = :id_eliminador");
        $stmtP->execute([
            ':id_creador'    => $id,
            ':id_eliminador' => $id
        ]);
        $cantPacientes = (int) $stmtP->fetchColumn();

        $stmtHC = $this->db->prepare("SELECT COUNT(*) FROM historias_clinicas WHERE especialista_id = :id_esp OR creado_por_usuario_id = :id_creador");
        $stmtHC->execute([
            ':id_esp'     => $id,
            ':id_creador' => $id
        ]);
        $cantHistorias = (int) $stmtHC->fetchColumn();

        $stmtH = $this->db->prepare("SELECT COUNT(*) FROM historial_cambios_pacientes WHERE modificado_por_usuario_id = :id");
        $stmtH->execute([':id' => $id]);
        $cantAuditorias = (int) $stmtH->fetchColumn();

        $stmtC = $this->db->prepare("SELECT COUNT(*) FROM citas WHERE especialista_id = :id");
        $stmtC->execute([':id' => $id]);
        $cantCitas = (int) $stmtC->fetchColumn();

        $totalVinculos = $cantPacientes + $cantHistorias + $cantAuditorias + $cantCitas;

        if ($totalVinculos > 0) {
            // Por integridad médico-legal, no se destruye la autoría de historias clínicas. Se da de baja (inactivo)
            $up = $this->db->prepare("UPDATE usuarios SET estado = 'inactivo' WHERE id = :id");
            $up->execute([':id' => $id]);

            registrarLog('WARNING', 'USUARIOS', 'BAJA_USUARIO_CON_REGISTROS', 
                "El usuario DNI {$target['dni']} ({$target['nombre']} {$target['apellido']}) tiene {$totalVinculos} registros clínicos/auditoría. Fue dado de baja (inactivo) por {$u['nombre_completo']}", (int)$u['id']);

            flash('error', "El usuario <strong>{$target['nombre']} {$target['apellido']}</strong> tiene {$totalVinculos} registro(s) clínicos/auditorías asociados que deben preservarse por normativa médica. <strong>Se ha desactivado su cuenta</strong> para revocar su acceso.");
        } else {
            // No tiene registros clínicos vinculados (ej. usuario de prueba o recién creado): eliminación física completa
            $del = $this->db->prepare("DELETE FROM usuarios WHERE id = :id");
            $del->execute([':id' => $id]);

            registrarLog('WARNING', 'USUARIOS', 'ELIMINAR_USUARIO', 
                "El usuario DNI {$target['dni']} ({$target['nombre']} {$target['apellido']}) fue eliminado de la base de datos por {$u['nombre_completo']}", (int)$u['id']);

            flash('exito', "El usuario <strong>{$target['nombre']} {$target['apellido']}</strong> ha sido eliminado completamente del sistema.");
        }

        header('Location: index.php?accion=usuarios');
        exit;
    }
}
