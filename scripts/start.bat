@echo off
setlocal
cd /d "%~dp0.."

where php >nul 2>nul
if errorlevel 1 goto no_php
where composer >nul 2>nul
if errorlevel 1 goto no_composer
where node >nul 2>nul
if errorlevel 1 goto no_node
goto run

:no_php
echo Error: PHP not found. Install PHP 8.4 and add to PATH.
exit /b 1

:no_composer
echo Error: Composer not found. Install Composer and add to PATH.
exit /b 1

:no_node
echo Error: Node.js not found. Install Node.js and add to PATH.
exit /b 1

:run
echo Starting: PHP server, queue, Vite...
call npm run start
