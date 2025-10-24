@echo off
title Nucleon Scholarship System - Server
color 0A

echo ========================================================
echo   NUCLEON SCHOLARSHIP APPLICATION SYSTEM
echo ========================================================
echo.
echo Starting web server...
echo.
echo Server will start at: http://localhost:8000
echo.
echo Press Ctrl+C to stop the server
echo ========================================================
echo.

cd /d "%~dp0"
php -S localhost:8000

pause
