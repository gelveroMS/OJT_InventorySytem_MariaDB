@echo off
title Stop MariaDB

echo [1] Stopping MariaDB Server...
taskkill /F /IM mysqld.exe >nul 2>&1

if %ERRORLEVEL%==0 (
    echo [✓] MariaDB stopped successfully.
) else (
    echo [i] MariaDB was not running.
)

pause
