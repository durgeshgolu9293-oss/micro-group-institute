@echo off
title Micro Group Institute - Localhost Server & VS Code Launcher
color 0B
cls
echo =======================================================================
echo          MICRO GROUP INSTITUTE - OFFLINE LOCALHOST LAUNCHER
echo =======================================================================
echo.
echo [1/3] Opening Project in Visual Studio Code...
start "" "C:\Users\durge\AppData\Local\Programs\Microsoft VS Code\Code.exe" "C:\xampp\htdocs\micro-group"

echo [2/3] Opening Web Browser to http://localhost:8080/micro-group/ ...
timeout /t 2 /nobreak >nul
start "" "http://localhost:8080/micro-group/"

echo [3/3] Starting Localhost Offline Server on Port 8080...
echo.
echo =======================================================================
echo  SERVER STATUS: RUNNING (100%% OFFLINE - NO INTERNET REQUIRED)
echo  LOCAL URL:     http://localhost:8080/micro-group/
echo.
echo  Press Ctrl+C to stop the server when finished.
echo =======================================================================
echo.

"C:\xampp\php\php.exe" -S localhost:8080 -t "C:\xampp\htdocs"
pause
