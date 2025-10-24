@echo off
title Install FPDF Library
color 0B

echo ========================================================
echo   FPDF LIBRARY INSTALLER
echo ========================================================
echo.
echo Installing FPDF library for PDF generation...
echo.

cd /d "%~dp0"
php install_fpdf.php

echo.
echo ========================================================
echo.
pause
