@echo off
REM Set paths
set BASEDIR=%~dp0mariadb
set DATADIR=%BASEDIR%\data

REM Start MariaDB with custom configuration
"%BASEDIR%\bin\mysqld.exe" --defaults-file="%BASEDIR%\my.ini" --console

pause