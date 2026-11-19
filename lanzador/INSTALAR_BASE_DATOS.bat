@echo off
chcp 65001 > nul
title Instalador de Base de Datos - Podología Ana Ramírez

set "SCRIPT_DIR=%~dp0"
pushd "%SCRIPT_DIR%.."
set "ROOT_DIR=%CD%"
popd
set "SQL_FILE=%ROOT_DIR%\sql\schema.sql"

cls
echo ==============================================================
echo    INSTALADOR DE BASE DE DATOS: PODOLOGIA ANA RAMIREZ
echo    Software desarrollado por KilaCode
echo ==============================================================
echo.
echo Este asistente creara la base de datos 'podoana_db' y cargara
echo las tablas, roles, usuarios iniciales y modelos de prueba.
echo.

:: 1. LOCALIZAR CLIENTE MYSQL
set "MYSQL_BIN="
where mysql >nul 2>&1
if %errorlevel% equ 0 (
    set "MYSQL_BIN=mysql"
    goto :mysql_encontrado
)

if exist "C:\xampp\mysql\bin\mysql.exe" (
    set "MYSQL_BIN=C:\xampp\mysql\bin\mysql.exe"
    goto :mysql_encontrado
)
if exist "D:\xampp\mysql\bin\mysql.exe" (
    set "MYSQL_BIN=D:\xampp\mysql\bin\mysql.exe"
    goto :mysql_encontrado
)

echo [!] ERROR: No se encontro 'mysql.exe' en las rutas estandar.
echo Asegurate de tener XAMPP instalado en C:\xampp o MySQL en tu PATH.
echo.
pause
exit /b 1

:mysql_encontrado
echo [OK] Cliente MySQL detectado: %MYSQL_BIN%
echo.

:: 2. VERIFICAR QUE EL ARCHIVO SCHEMA.SQL EXISTA
if not exist "%SQL_FILE%" (
    echo [!] ERROR: No se encontro el archivo SQL en:
    echo     %SQL_FILE%
    echo.
    pause
    exit /b 1
)

echo [*] Importando estructura y datos iniciales desde:
echo     sql\schema.sql ...
echo.

"%MYSQL_BIN%" -u root --default-character-set=utf8mb4 < "%SQL_FILE%"

if %errorlevel% equ 0 (
    echo ==============================================================
    echo    ¡BASE DE DATOS INSTALADA CON TOTAL EXITO!
    echo ==============================================================
    echo.
    echo Cuentas iniciales listas para pruebas:
    echo   - Administradora: DNI 10234567  (Clave inicial: 10234567)
    echo   - Especialista:   DNI 20345678  (Clave inicial: 20345678)
    echo   - Recepcionista:  DNI 30456789  (Clave inicial: 30456789)
    echo   - SuperAdmin:     DNI 43285473  (Clave inicial: 43285473)
    echo.
    echo Ya puedes abrir 'INICIAR_SISTEMA.bat' para empezar a usar el sistema.
    echo.
) else (
    echo.
    echo [!] Ocurrio un error al importar. Verifica que el servicio MySQL este ACTIVO en XAMPP.
    echo.
)

pause
