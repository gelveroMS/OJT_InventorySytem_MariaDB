@echo off
title Start MariaDB and Connect

:: Step 1: Go to MariaDB bin folder
cd /d "%~dp0mariadb\bin"

echo.
echo [1] Checking if MariaDB is already running...
tasklist | findstr mysqld >nul
if %errorlevel%==0 (
    echo MariaDB is already running.
) else (
    echo Starting MariaDB server...
    start "" mysqld.exe --defaults-file=..\my.ini
    echo Waiting 8 seconds for MariaDB to start...
    timeout /t 8 >nul
)

:: Step 2: Try to connect
echo.
echo [2] Connecting to MariaDB as root...
mysql -u root -p1234 -P3307

:: Step 3: Done
echo.
echo [✓] Finished.
pause
