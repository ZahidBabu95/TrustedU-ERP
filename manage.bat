@echo off
setlocal
title TrustedU ERP - Project Manager

:: Set Laragon PHP Path dynamically
set "PHP_BIN=c:\laragon\bin\php\php-8.4.12-nts-Win32-vs17-x64\php.exe"
:menu
cls
echo ======================================================
echo           TrustedU ERP - Project Manager
echo ======================================================
echo.
echo  [1] Start Server (php artisan serve)
echo  [2] Stop Server (Kill all PHP processes)
echo  [3] Restart Server
echo  [4] Clear Cache (View, Route, Config)
echo  [5] Exit
echo.
echo ======================================================
set /p choice="Enter your choice (1-5): "

if "%choice%"=="1" goto start_server
if "%choice%"=="2" goto stop_server
if "%choice%"=="3" goto restart_server
if "%choice%"=="4" goto clear_cache
if "%choice%"=="5" exit
goto menu

:start_server
cls
echo Starting server at http://127.0.0.1:8000...
echo.
:: Use start /b to run in background if they prefer, but directly artisan serve is clearer for logs
start "TrustedU-ERP-Server" "%PHP_BIN%" artisan serve --port=8000
timeout /t 3 >nul
start "" "http://127.0.0.1:8000"
echo Server is running! Press any key to return to menu...
pause >nul
goto menu

:stop_server
cls
echo Stopping PHP artisan serve...
:: Taskkill will find the process running artisan serve
taskkill /FI "WINDOWTITLE eq TrustedU-ERP-Server*" /T /F >nul 2>&1
echo Server stopped.
pause
goto menu

:restart_server
echo Restarting...
taskkill /FI "WINDOWTITLE eq TrustedU-ERP-Server*" /T /F >nul 2>&1
timeout /t 2 >nul
start "TrustedU-ERP-Server" "%PHP_BIN%" artisan serve --port=8000
echo Server restarted at http://127.0.0.1:8000
timeout /t 2 >nul
goto menu

:clear_cache
cls
echo Clearing Laravel Caches...
"%PHP_BIN%" artisan view:clear
"%PHP_BIN%" artisan route:clear
"%PHP_BIN%" artisan config:clear
"%PHP_BIN%" artisan cache:clear
echo.
echo All caches cleared!
pause
goto menu
