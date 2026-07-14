@echo off
setlocal

REM Step 1: Change directory to the folder where the .bat file is located
cd /d "%~dp0"

REM Step 2: Make sure a production build exists for the frontend (next start needs it)
if not exist ".next\BUILD_ID" (
    echo No production build found. Building frontend...
    call npm run build
    if %ERRORLEVEL% NEQ 0 (
        echo Error: Frontend build failed! Fix the errors above and try again.
        exit /b %ERRORLEVEL%
    )
)

REM Step 3: Make sure backend dependencies are installed
if not exist "backend\node_modules" (
    echo Backend dependencies not found. Run installer.bat first.
    exit /b 1
)

REM Step 4: Open project folder in File Explorer
start "" explorer "%cd%"

REM Step 5: Start the backend API (Express, http://localhost:4000)
REM (the spawned window inherits this script's current directory, set in Step 1)
start "ScrapeGenius Backend" cmd /k "cd backend && npm start"

REM Step 6: Start the frontend (Next.js, http://localhost:3000)
start "ScrapeGenius Frontend" cmd /k "npm start"

exit /b 0
