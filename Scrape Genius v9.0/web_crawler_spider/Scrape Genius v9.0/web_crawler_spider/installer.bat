@echo off
setlocal

REM Change to the directory where the .bat file is located
cd /d "%~dp0"

REM Check if Node.js is installed
where node >nul 2>&1
if %ERRORLEVEL% NEQ 0 (
    echo Error: Node.js is not installed or not in PATH.
    exit /b 1
)

REM Step 1: Run npm install
echo Running npm install...
call npm install
if %ERRORLEVEL% NEQ 0 (
    echo Error: npm install failed!
    exit /b %ERRORLEVEL%
)

REM Step 2: Run npx playwright install
echo Running npx playwright install...
call npx playwright install
if %ERRORLEVEL% NEQ 0 (
    echo Error: npx playwright install failed!
    exit /b %ERRORLEVEL%
)

echo All installations completed successfully!
exit /b 0
