# Sistema de Gestión Clínica - Podología Ana Ramírez (v2.0)

Sistema integral para gestión de expedientes clínicos podológicos, antecedentes de salud, valoración biomecánica, citas, control de acceso por roles (RBAC estricto) y auditoría inmutable de cambios.

---

## 1. Identidad Visual de Marca Integrada
El sistema incorpora la línea gráfica oficial de la clínica:
- **Logo Corporativo Oficial**: Silueta del pie con detalle botánico y firma de la Dra. Ana Ramírez en [`public/assets/img/logo.jpg`](file:///c:/Users/Usuario/Documents/PodoAna/public/assets/img/logo.jpg).
- **Paleta de Colores Clínicos**:
  - Azul Oscuro Médico: `#0A2540`
  - Azul Clínico Dominante: `#005B94`
  - Verde Menta / Salud: `#2EC4B6`
  - Acentos Cian: `#00B4D8`
  - Fondo Ergonómico: `#F8FAFC`

---

## 2. Esquema de Roles y Permisos (RBAC de 3 Niveles)

| Nivel | Rol / Perfil | Credenciales por Defecto | Permisos y Restricciones |
| :--- | :--- | :--- | :--- |
| **Nivel 1** | **SuperAdmin** *(Técnico / Creador)* | Credenciales entregadas de forma privada | **Acceso exclusivo al panel técnico, salud de BD y logs globales.** |
| **Nivel 2** | **Dueña / Admin** | Credenciales entregadas de forma privada | **Control del negocio y gestión de empleados.** Edición de datos secundarios y borrado lógico, con auditoría. |
| **Nivel 3** | **Especialista (Podólogo)** | Credenciales asignadas por administración | **Creación de pacientes, fichas y atenciones.** No puede modificar ni eliminar registros guardados. |
| **Nivel 3** | **Recepcionista** | Credenciales asignadas por administración | **Registro de pacientes y agenda de turnos.** |

---

## 3. Auditoría, Trazabilidad Forense y Borrado Lógico

1. **Trazabilidad de Creación**: Cada paciente, cita o historia clínica guarda de forma automática e indeleble:
   - `creado_por_usuario_id`
   - `fecha_creacion`
   - `hora_creacion`
2. **Historial Inmutable de Modificaciones (`historial_cambios_pacientes`)**:
   - Cuando el Administrador actualiza datos secundarios (teléfono, domicilio, calzado, antecedentes), el valor anterior **NO se sobrescribe a ciegas**.
   - Se registra: `campo_modificado`, `valor_anterior`, `valor_nuevo`, `modificado_por_usuario_id`, `fecha_hora` y `motivo_cambio`.
3. **Borrado Lógico (*Soft Delete*)**: Los registros nunca se destruyen con `DELETE FROM`. Se marcan con `estado = 'inactivo'`, `eliminado_en = NOW()` y `eliminado_por_usuario_id`.
4. **Logs del Sistema (`logs_sistema`)**: Registra intentos de acceso no autorizados, bloqueos RBAC y operaciones del sistema visibles exclusivamente para el SuperAdmin.

---

## 4. Estructura del Código

```text
PodoAna/
├── config/
│   ├── app.php                    # Helpers de escape XSS con e(), sesiones y mensajes flash
│   ├── auth.php                   # Control de acceso RBAC, autenticación y registro de logs
│   └── database.php               # Conexión PDO Singleton desacoplada
├── public/
│   ├── assets/
│   │   ├── css/custom.css         # Estilos complementarios
│   │   ├── img/logo.jpg           # Logo oficial de la clínica
│   │   └── js/app.js              # Prevención de doble envío táctil y lógica de alertas
│   └── index.php                  # Front Controller y enrutador central
├── sql/
│   └── schema.sql                 # Script DDL completo (MySQL 8 / MariaDB) con auditoría y semillas
├── src/
│   ├── controllers/
│   │   ├── AuthController.php     # Flujo de login seguro con password_verify() y logout
│   │   └── PacienteController.php # CRUD con inyección de creador y bloqueos RBAC
│   ├── models/
│   │   └── Paciente.php           # Modelo con transacciones PDO y tabla historial_cambios
│   └── views/
│       ├── admin/
│       │   └── sistema.php        # Panel técnico exclusivo para SuperAdmin (salud BD y logs)
│       ├── auth/
│       │   └── login.php          # Pantalla de inicio de sesión sin selector público de perfiles
│       ├── layout/
│       │   ├── header.php         # Navbar corporativa, badges de rol y cierre de sesión
│       │   └── footer.php         # Pie de página y scripts
│       └── pacientes/
│           ├── detalle.php        # Ficha con tarjeta de trazabilidad e historial inmutable
│           ├── editar.php         # Formulario de datos secundarios con bloqueo DNI/Nombres
│           ├── listado.php        # Directorio con badges médicos y enlaces al expediente
│           └── registro.php       # Formulario táctil con alertas dinámicas
├── uploads/
│   └── pacientes/
├── iniciar.bat                    # Lanzador rápido de un clic para Windows
└── README.md                      # Esta documentación
```

---

## 5. Guía de Ejecución Local

### En Windows (con XAMPP):
1. Asegúrate de tener **MySQL** activo en el XAMPP Control Panel.
2. Haz doble clic en **`iniciar.bat`** (o ejecuta `C:\xampp\php\php.exe -S 0.0.0.0:8000 -t public`).
3. Abre en tu navegador: **`http://localhost:8000`**

### En macOS (Terminal):
1. Importar la base de datos:
   ```bash
   mysql -u root -p < sql/schema.sql
   ```
2. Obtener la IP local de tu Mac:
   ```bash
   ipconfig getifaddr en0
   ```
3. Arrancar el servidor integrado:
   ```bash
   php -S 0.0.0.0:8000 -t public
   ```
4. Acceder desde cualquier computadora o tablet iPad/Android en la misma red Wi-Fi:
   ```text
   http://TU_IP_LOCAL:8000
   ```

---

## 6. Acceso, Seguridad y Gestión de Personal

- **Autenticación Estricta por DNI**: Ingreso mediante DNI (formato Perú, 8 dígitos, compatible con ceros iniciales) y contraseña encriptada con BCRYPT.
- **Cambio Obligatorio de Contraseña Inicial**: Para el nuevo personal registrado, su clave inicial es su propio DNI. Al iniciar sesión por primera vez, el sistema bloquea la navegación y exige definir una nueva contraseña privada.
- **Módulo de Personal y Usuarios**: Disponible para SuperAdmin y Administradores para dar de alta nuevo personal, editar información de contacto/especialidad, resetear claves y activar/desactivar cuentas.
- **Trazabilidad & Auditoría**: Registro inmutable de modificaciones filiatorias y visor de eventos del servidor (`logs_sistema`).
