@echo off
chcp 65001 > nul
title Detener Servidor - Podología Ana Ramírez

echo ==============================================================
echo    DETENIENDO SERVIDOR CLINICO LOCAL (PUERTO 8000)
echo ==============================================================
echo.

powershell -NoProfile -Command "Get-NetTCPConnection -LocalPort 8000 -ErrorAction SilentlyContinue | ForEach-Object { Stop-Process -Id $_.OwningProcess -Force -ErrorAction SilentlyContinue; Write-Host ('[OK] Proceso detenido en puerto 8000 (PID ' + $_.OwningProcess + ')') }"

echo.
echo [OK] El servidor ha sido detenido correctamente.
timeout /t 2 > nul
