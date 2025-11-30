@echo off
setlocal ENABLEDELAYEDEXPANSION

REM Get absolute path of this .bat file
set SCRIPT_PATH=%~dp0

REM Run PHP setup script
php "%SCRIPT_PATH%bin\setup.php"

if %errorlevel% neq 0 (
    echo.
    echo ❌ Setup failed.
    exit /b %errorlevel%
)

echo.
echo ============================================
echo  ✔ README.md successfully generated!
echo ============================================
echo.
pause
