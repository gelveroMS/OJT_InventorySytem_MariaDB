@echo off
:: Start MariaDB in true background
start "" /B cmd /c "mariadb\bin\mysqld.exe --defaults-file=mariadb\my.ini"

:: Wait a few seconds for MariaDB to boot
timeout /t 3 > nul

:: Start PHP Desktop app and wait
start /wait "" phpdesktop-chrome.exe

:: After PHP app exits, stop MariaDB
taskkill /f /im mysqld.exe >nul 2>&1

exit