@echo off
REM Menjalankan Meta Sarpras dari folder tempat file ini berada (tidak perlu htdocs).
REM Pastikan MySQL di XAMPP sudah Start.
cd /d "%~dp0"
set PHPEXE=php
where php >nul 2>nul
if errorlevel 1 (
  for %%P in ("C:\xampp\php\php.exe" "D:\xampp\php\php.exe" "E:\xampp\php\php.exe" "M:\XAMPP\php\php.exe") do if exist %%P set PHPEXE=%%~P
)
echo.
echo  Setup database (sekali saja) : http://localhost:8000/setup.php
echo  Mahasiswa / PWA              : http://localhost:8000/Meta_Sarpras_PWA/splash.php
echo  Admin                        : http://localhost:8000/Meta_Sarpras_Admin/login.php
echo.
"%PHPEXE%" -S localhost:8000
pause
