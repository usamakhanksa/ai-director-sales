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

REM Step 1: Install frontend dependencies
echo ============================================
echo Installing frontend dependencies...
echo ============================================
call npm install
if %ERRORLEVEL% NEQ 0 (
    echo Error: Frontend npm install failed!
    exit /b %ERRORLEVEL%
)

REM Step 2: Install Playwright browsers
echo ============================================
echo Running npx playwright install...
echo ============================================
call npx playwright install
if %ERRORLEVEL% NEQ 0 (
    echo Error: npx playwright install failed!
    exit /b %ERRORLEVEL%
)

REM Step 3: Install backend dependencies (Express scraping microservice —
REM Facebook/LinkedIn/Twitter/Haraj/classified/Google Maps job engine)
echo ============================================
echo Installing backend dependencies...
echo ============================================
cd backend
call npm install
if %ERRORLEVEL% NEQ 0 (
    echo Error: Backend npm install failed!
    cd /d "%~dp0"
    exit /b %ERRORLEVEL%
)

REM Step 3b: Install Playwright's Chromium build for the backend's own
REM playwright version (may differ from the frontend's — installed separately)
echo ============================================
echo Installing backend Playwright Chromium...
echo ============================================
call npx playwright install chromium
if %ERRORLEVEL% NEQ 0 (
    echo Warning: Backend Playwright Chromium install failed. Facebook/LinkedIn/
    echo Twitter/Google Maps/Haraj scrapers need it — run manually:
    echo     cd backend ^&^& npx playwright install chromium
)

REM Step 4: Create the backend database if it doesn't exist yet (non-fatal if MySQL is down)
REM (still inside the backend directory from Step 3)
echo ============================================
echo Ensuring backend database exists...
echo ============================================
call npm run db:ensure
if %ERRORLEVEL% NEQ 0 (
    echo Warning: Could not create the database. Make sure MySQL is running in Laragon,
    echo then run: cd backend ^&^& npm run db:ensure
)

REM Step 5: Run backend database migrations + seed (non-fatal if DB is unreachable)
echo ============================================
echo Running backend database migrations...
echo ============================================
call npx knex migrate:latest
if %ERRORLEVEL% NEQ 0 (
    echo Warning: Database migrations failed. Make sure MySQL is running in Laragon
    echo and the database from backend\.env exists, then run:
    echo     cd backend ^&^& npx knex migrate:latest
) else (
    call npx knex seed:run
)
cd /d "%~dp0"

REM Step 6: Build the frontend for production
echo ============================================
echo Building frontend for production...
echo ============================================
call npm run build
if %ERRORLEVEL% NEQ 0 (
    echo Error: Frontend build failed!
    exit /b %ERRORLEVEL%
)

echo ============================================
echo All installations completed successfully!
echo Run starter.bat to launch the backend and frontend.
echo ============================================
exit /b 0
