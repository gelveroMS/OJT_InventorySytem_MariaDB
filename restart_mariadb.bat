@echo off
title Restart MariaDB

:: Stop MariaDB first
echo [1] Stopping MariaDB (if running)...
taskkill /F /IM mysqld.exe >nul 2>&1
timeout /t 2 >nul

:: Start MariaDB again
echo [2] Starting MariaDB server...
cd /d "%~dp0mariadb\bin"
start "" mysqld.exe --defaults-file=..\my.ini

:: Wait for the service to start
timeout /t 8 >nul

:: Check if running
tasklist | findstr mysqld >nul
if %ERRORLEVEL%==0 (
    echo [✓] MariaDB restarted successfully.
) else (
    echo [X] Failed to start MariaDB. Please check my.ini or logs.
)

pause
