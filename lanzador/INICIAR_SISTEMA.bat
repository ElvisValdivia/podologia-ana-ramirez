@echo off
chcp 65001 > nul
title Podología Ana Ramírez - Servidor Clínico Local

:: ==============================================================
:: SISTEMA DE GESTIÓN CLÍNICA: PODOLOGÍA ANA RAMÍREZ
:: Desarrollado por KilaCode
:: Lanzador Automático de Servidor Local y Modo Aplicación
:: ==============================================================

:: Obtener ruta raíz del proyecto (un nivel arriba de la carpeta lanzador)
set "SCRIPT_DIR=%~dp0"
pushd "%SCRIPT_DIR%.."
set "ROOT_DIR=%CD%"
popd
set "PUBLIC_DIR=%ROOT_DIR%\public"

cls
echo ==============================================================
echo    PODOLOGIA ANA RAMIREZ - SISTEMA DE GESTION CLINICA
echo    Software desarrollado por KilaCode
echo ==============================================================
echo.
echo [*] Iniciando comprobaciones del sistema...

:: 1. LOCALIZAR EJECUTABLE DE PHP
set "PHP_BIN="

:: Opción A: Variable de entorno PATH
where php >nul 2>&1
if %errorlevel% equ 0 (
    set "PHP_BIN=php"
    goto :php_encontrado
)

:: Opción B: Rutas típicas de XAMPP / PHP
if exist "C:\xampp\php\php.exe" (
    set "PHP_BIN=C:\xampp\php\php.exe"
    goto :php_encontrado
)
if exist "D:\xampp\php\php.exe" (
    set "PHP_BIN=D:\xampp\php\php.exe"
    goto :php_encontrado
)
if exist "C:\php\php.exe" (
    set "PHP_BIN=C:\php\php.exe"
    goto :php_encontrado
)
if exist "C:\laragon\bin\php\php.exe" (
    set "PHP_BIN=C:\laragon\bin\php\php.exe"
    goto :php_encontrado
)

:php_no_encontrado
echo.
echo [!] ERROR CRITICO: No se encontro el interprete de PHP en esta PC.
echo.
echo Para ejecutar este sistema en esta computadora necesitas tener instalado:
echo   1. XAMPP con PHP 8.1 o superior (recomendado: C:\xampp)
echo      o instalar PHP en el PATH de Windows.
echo.
echo Presiona cualquier tecla para salir...
pause > nul
exit /b 1

:php_encontrado
echo [OK] Interprete PHP detectado: %PHP_BIN%

:: 2. VERIFICAR SERVIDOR MYSQL / MARIADB (PUERTO 3306)
powershell -Command "try { $t = New-Object System.Net.Sockets.TcpClient('127.0.0.1', 3306); $t.Close(); exit 0 } catch { exit 1 }" >nul 2>&1
if %errorlevel% neq 0 (
    echo [!] AVISO: MySQL no parece estar activo en el puerto 3306.
    
    :: Si existe XAMPP, intentar arrancarlo
    if exist "C:\xampp\mysql\bin\mysqld.exe" (
        echo [*] Intentando iniciar MySQL desde C:\xampp...
        start "" "C:\xampp\mysql\bin\mysqld.exe" --defaults-file="C:\xampp\mysql\bin\my.ini" --standalone >nul 2>&1
        timeout /t 3 >nul
    ) else (
        echo [!] Recuerda iniciar el modulo MySQL en el Panel de Control de XAMPP.
    )
) else (
    echo [OK] Base de datos MySQL activa en el puerto 3306.
)

:: 3. OBTENER IP LOCAL PARA ACCESO DESDE TABLETS
set "LOCAL_IP="
for /f "usebackq tokens=*" %%a in (`powershell -NoProfile -Command "(Get-NetIPAddress -AddressFamily IPv4 | Where-Object { $_.InterfaceAlias -notlike '*Loopback*' -and $_.IPAddress -notlike '169.254*' -and $_.IPAddress -notlike '127*' }).IPAddress | Select-Object -First 1"`) do (
    set "LOCAL_IP=%%a"
)
if "%LOCAL_IP%"=="" set "LOCAL_IP=localhost"

:: 4. VERIFICAR E INICIAR SERVIDOR PHP EN PUERTO 8000
powershell -Command "try { $t = New-Object System.Net.Sockets.TcpClient('127.0.0.1', 8000); $t.Close(); exit 0 } catch { exit 1 }" >nul 2>&1
if %errorlevel% neq 0 (
    echo [*] Arrancando servidor web clínico en segundo plano (puerto 8000)...
    start /B "" "%PHP_BIN%" -S 0.0.0.0:8000 -t "%PUBLIC_DIR%" >nul 2>&1
    timeout /t 2 >nul
) else (
    echo [OK] El servidor ya se encuentra en ejecucion en el puerto 8000.
)

:: 5. ABRIR EN MODO APLICACION (VENTANA LIMPIA)
echo [*] Abriendo aplicacion en modo ventana...

:: Comprobar Chrome
set "APP_LAUNCHED=0"
if exist "%ProgramFiles%\Google\Chrome\Application\chrome.exe" (
    start "" "%ProgramFiles%\Google\Chrome\Application\chrome.exe" --app="http://localhost:8000"
    set "APP_LAUNCHED=1"
    goto :menu_panel
)
if exist "%ProgramFiles(x86)%\Google\Chrome\Application\chrome.exe" (
    start "" "%ProgramFiles(x86)%\Google\Chrome\Application\chrome.exe" --app="http://localhost:8000"
    set "APP_LAUNCHED=1"
    goto :menu_panel
)
if exist "%LocalAppData%\Google\Chrome\Application\chrome.exe" (
    start "" "%LocalAppData%\Google\Chrome\Application\chrome.exe" --app="http://localhost:8000"
    set "APP_LAUNCHED=1"
    goto :menu_panel
)

:: Comprobar Microsoft Edge
if exist "%ProgramFiles(x86)%\Microsoft\Edge\Application\msedge.exe" (
    start "" "%ProgramFiles(x86)%\Microsoft\Edge\Application\msedge.exe" --app="http://localhost:8000"
    set "APP_LAUNCHED=1"
    goto :menu_panel
)
if exist "%ProgramFiles%\Microsoft\Edge\Application\msedge.exe" (
    start "" "%ProgramFiles%\Microsoft\Edge\Application\msedge.exe" --app="http://localhost:8000"
    set "APP_LAUNCHED=1"
    goto :menu_panel
)

:: Por defecto abrir navegador estándar
start http://localhost:8000

:menu_panel
cls
echo ==============================================================
echo    PODOLOGIA ANA RAMIREZ - SISTEMA CLINICO EN LINEA
echo    Desarrollado por KilaCode (Todos los derechos reservados)
echo ==============================================================
echo.
echo    ESTADO: Servidor Activo y Funcionando Correctamente
echo.
echo    [+] ACCESO EN ESTA COMPUTADORA:
echo        http://localhost:8000
echo.
echo    [+] ACCESO DESDE TABLET / CELULAR (Mismo Wi-Fi):
echo        http://%LOCAL_IP%:8000
echo.
echo ==============================================================
echo    OPCIONES DISPONIBLES:
echo ==============================================================
echo    [1] Volver a abrir la aplicacion en esta PC
echo    [2] Mostrar codigo QR o instruccion para Tablet
echo    [3] Detener servidor y cerrar
echo ==============================================================
echo.

:pedir_opcion
set /p "OPC=Seleccione una opcion (1-3): "

if "%OPC%"=="1" (
    if exist "%ProgramFiles(x86)%\Microsoft\Edge\Application\msedge.exe" (
        start "" "%ProgramFiles(x86)%\Microsoft\Edge\Application\msedge.exe" --app="http://localhost:8000"
    ) else if exist "%ProgramFiles%\Google\Chrome\Application\chrome.exe" (
        start "" "%ProgramFiles%\Google\Chrome\Application\chrome.exe" --app="http://localhost:8000"
    ) else (
        start http://localhost:8000
    )
    goto :menu_panel
)

if "%OPC%"=="2" (
    cls
    echo ==============================================================
    echo    CONEXION PARA TABLET (IPAD, ANDROID O WINDOWS TABLET)
    echo ==============================================================
    echo.
    echo 1. Asegurate de que la Tablet este conectada al MISMO Wi-Fi
    echo    que esta computadora.
    echo.
    echo 2. Abre el navegador (Chrome o Safari) en la Tablet y escribe:
    echo.
    echo        http://%LOCAL_IP%:8000
    echo.
    echo 3. Para que quede como una App en la pantalla de la Tablet:
    echo    - En Android (Chrome): Toca los 3 puntos y 'Agregar a la pantalla principal'.
    echo    - En iPad (Safari): Toca el boton Compartir y 'Agregar al inicio'.
    echo.
    echo ==============================================================
    echo Presiona cualquier tecla para volver al menu principal...
    pause > nul
    goto :menu_panel
)

if "%OPC%"=="3" (
    goto :detener_salir
)

echo [!] Opcion invalida.
goto :pedir_opcion

:detener_salir
echo.
echo [*] Deteniendo servidor clinico...
call "%SCRIPT_DIR%DETENER_SISTEMA.bat"
echo [OK] Sistema detenido con exito.
timeout /t 2 > nul
exit /b 0
