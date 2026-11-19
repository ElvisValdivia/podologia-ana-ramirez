# Guía de Uso del Lanzador - Podología Ana Ramírez
**Desarrollado por KilaCode • Todos los derechos reservados**

Esta carpeta contiene todo lo necesario para ejecutar y probar el sistema clínico en cualquier computadora o tablet sin necesidad de ingresar comandos manualmente en una terminal.

---

## 📁 Contenido de esta Carpeta

| Archivo | Función |
| :--- | :--- |
| **`INICIAR_SISTEMA.bat`** | **Lanzador principal con 1 doble clic.** Inicia el servidor web en segundo plano, detecta tu IP local para tablets y abre el sistema en modo aplicación (sin barras de navegador). |
| **`DETENER_SISTEMA.bat`** | Detiene de forma limpia y segura el servidor en el puerto 8000. |
| **`INSTALAR_BASE_DATOS.bat`** | Crea automáticamente la base de datos `podoana_db` e importa todas las tablas y usuarios de prueba. Ideal cuando llevas el sistema a una PC nueva. |
| **`CREAR_ACCESO_DIRECTO_ESCRITORIO.bat`** | Coloca un acceso directo llamado **"Podología Ana Ramírez"** en el Escritorio de Windows para abrir el sistema con un solo clic. |

---

## 🚀 Pasos para Probar el Sistema en OTRA Computadora

Si llevas el proyecto en una memoria USB o lo descargas desde GitHub en otra PC:

### Paso 1: Requisitos en la nueva PC
* Asegúrate de tener instalado **XAMPP** (con PHP 8.1+ y MySQL).
* Abre el **XAMPP Control Panel** e inicia el módulo de **MySQL** (botón *Start*).

### Paso 2: Instalar la Base de Datos (Solo la primera vez)
1. Entra a la carpeta `lanzador/`.
2. Haz doble clic en **`INSTALAR_BASE_DATOS.bat`**.
3. El script creará la base de datos `podoana_db` con todas las tablas y cuentas de prueba automáticamente.

### Paso 3: Iniciar el Sistema
1. Haz doble clic en **`INICIAR_SISTEMA.bat`**.
2. Automáticamente se abrirá la pantalla de inicio de sesión en modo aplicación limpia (Google Chrome o Microsoft Edge).

---

## 📱 ¿Cómo conectar una Tablet o Teléfono en el Consultorio?

El sistema cuenta con diseño adaptable (botones táctiles de 48px, podograma interactivo, fichas digitales):

1. Conecta la Tablet a la **misma red Wi-Fi** donde está la computadora principal.
2. Al ejecutar `INICIAR_SISTEMA.bat`, la pantalla negra te mostrará la dirección IP de tu red, por ejemplo:
   ```text
   http://192.168.1.35:8000
   ```
3. En la Tablet, abre Chrome o Safari y escribe esa dirección.
4. **Para que quede como una App en la pantalla de la Tablet**:
   * **En Android (Chrome)**: Toca los 3 puntos de la esquina superior derecha y selecciona **"Agregar a la pantalla principal"**.
   * **En iPad (Safari)**: Toca el botón de Compartir (el ícono del cuadrado con la flecha arriba) y selecciona **"Agregar al inicio"**.
5. ¡Listo! Tendrás el ícono de la clínica en la pantalla de la tablet listo para usar junto al paciente.

---

## 🔐 Cuentas de Acceso Preconfiguradas para Pruebas

| Rol | Usuario (DNI) | Contraseña Inicial |
| :--- | :--- | :--- |
| **Administradora (Dueña)** | `10234567` | `10234567` |
| **Especialista (Podólogo)** | `20345678` | `20345678` |
| **Recepcionista** | `30456789` | `30456789` |
| **SuperAdmin (Técnico)** | `43285473` | `43285473` |

*(El sistema permite cambiar la contraseña al iniciar sesión por primera vez).*
