@echo off
chcp 65001 > nul
color 0A
cls

echo ╔════════════════════════════════════════════════════════════╗
echo ║    SISTEMA NAPANCHITA - IMPORTADOR DE BASE DE DATOS       ║
echo ║                     Versión 1.0                            ║
echo ╚════════════════════════════════════════════════════════════╝
echo.
echo ⏳ Iniciando proceso de importación...
echo.

REM Verificar si XAMPP está instalado
if not exist "C:\xampp\mysql\bin\mysql.exe" (
    echo ❌ ERROR: XAMPP no está instalado en C:\xampp
    echo.
    echo Por favor instala XAMPP desde: https://www.apachefriends.org
    echo.
    pause
    exit /b 1
)

REM Cambiar al directorio de MySQL
cd /d C:\xampp\mysql\bin

REM Verificar si el archivo SQL existe
if not exist "%~dp0napanchita_db_full_backup.sql" (
    echo ❌ ERROR: No se encontró el archivo napanchita_db_full_backup.sql
    echo.
    echo Asegúrate de que el archivo esté en la carpeta database/
    echo.
    pause
    exit /b 1
)

echo 🗑️  Eliminando base de datos anterior (si existe)...
mysql -u root -e "DROP DATABASE IF EXISTS napanchita_db;" 2>nul

echo 📦 Creando nueva base de datos...
mysql -u root -e "CREATE DATABASE napanchita_db CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;"
if errorlevel 1 (
    echo ❌ ERROR: No se pudo crear la base de datos
    echo.
    echo Verifica que MySQL esté corriendo en XAMPP
    echo.
    pause
    exit /b 1
)

echo 📥 Importando datos (esto puede tomar unos segundos)...
mysql -u root napanchita_db < "%~dp0napanchita_db_full_backup.sql"
if errorlevel 1 (
    echo ❌ ERROR: Falló la importación de datos
    echo.
    pause
    exit /b 1
)

echo.
echo ✅ Verificando importación...
mysql -u root -e "USE napanchita_db; SELECT COUNT(*) as total_tablas FROM information_schema.tables WHERE table_schema = 'napanchita_db';"

echo.
echo ╔════════════════════════════════════════════════════════════╗
echo ║           ✅ IMPORTACIÓN COMPLETADA EXITOSAMENTE           ║
echo ╚════════════════════════════════════════════════════════════╝
echo.
echo 📊 Base de datos: napanchita_db
echo 🔐 Usuario: root
echo 🔑 Contraseña: (vacía)
echo.
echo 👤 Usuarios del sistema creados:
echo    - Admin:      usuario: admin      contraseña: admin123
echo    - Mesero:     usuario: mesero1    contraseña: mesero123
echo    - Repartidor: usuario: repartidor1 contraseña: repartidor123
echo.
echo 🌐 Accede al sistema en: http://localhost/napanchita-web
echo.
echo ⚠️  IMPORTANTE: Cambia las contraseñas por defecto después del primer login
echo.
pause
