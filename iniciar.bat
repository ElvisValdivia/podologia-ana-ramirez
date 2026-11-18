@echo off
cd /d "%~dp0"
title Podologia Ana Ramirez - Sistema Clinico
cls

echo ======================================================================
echo           SISTEMA CLINICO: PODOLOGIA ANA RAMIREZ (PERU)
echo ======================================================================
echo.

echo [*] Verificando Base de Datos MySQL...
tasklist /FI "IMAGENAME eq mysqld.exe" 2>NUL | find /I /N "mysqld.exe">NUL
if "%ERRORLEVEL%"=="0" (
    echo [OK] MySQL ya esta en ejecucion.
) else (
    echo [*] Iniciando MySQL de XAMPP...
    if exist "C:\xampp\mysql\bin\mysqld.exe" (
        start /B "" "C:\xampp\mysql\bin\mysqld.exe" --defaults-file="C:\xampp\mysql\bin\my.ini" --standalone
        timeout /t 2 /nobreak >nul
        echo [OK] MySQL iniciado correctamente.
    ) else (
        echo [!] Inicie MySQL desde el Panel de XAMPP.
    )
)
echo.

set PHP_BIN=php
if exist "C:\xampp\php\php.exe" (
    set PHP_BIN=C:\xampp\php\php.exe
)

echo ======================================================================
echo [*] Iniciando servidor web en: http://localhost:8000
echo [*] Abriendo navegador...
echo [*] NO CIERRE ESTA VENTANA mientras use el sistema.
echo ======================================================================
echo.

start http://localhost:8000

"%PHP_BIN%" -S 0.0.0.0:8000 -t public
pause