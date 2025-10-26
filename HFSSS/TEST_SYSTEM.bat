@echo off
title System Test
color 0E

echo ========================================================
echo   NUCLEON SCHOLARSHIP SYSTEM - TEST
echo ========================================================
echo.
echo Running system diagnostics...
echo.

cd /d "%~dp0"
php test_system.php

echo.
echo ========================================================
echo.
pause
