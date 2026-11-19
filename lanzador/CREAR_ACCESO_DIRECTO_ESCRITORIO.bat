@echo off
chcp 65001 > nul
title Crear Acceso Directo - Podología Ana Ramírez

set "SCRIPT_DIR=%~dp0"
set "TARGET_BAT=%SCRIPT_DIR%INICIAR_SISTEMA.bat"

echo ==============================================================
echo    CREANDO ACCESO DIRECTO EN EL ESCRITORIO DE WINDOWS
echo ==============================================================
echo.

powershell -NoProfile -Command ^
  "$ws = New-Object -ComObject WScript.Shell; " ^
  "$desktop = [Environment]::GetFolderPath('Desktop'); " ^
  "$shortcut = $ws.CreateShortcut(\"$desktop\Podologia Ana Ramirez.lnk\"); " ^
  "$shortcut.TargetPath = \"%TARGET_BAT%\"; " ^
  "$shortcut.WorkingDirectory = \"%SCRIPT_DIR%\"; " ^
  "$shortcut.Description = \"Sistema Clinico Podologia Ana Ramirez - KilaCode\"; " ^
  "$shortcut.Save(); " ^
  "Write-Host '[OK] Acceso directo creado exitosamente en tu Escritorio de Windows.'"

echo.
echo Ahora puedes abrir el sistema directamente haciendo doble clic
echo en el icono 'Podologia Ana Ramirez' que esta en tu Escritorio.
echo.
pause
