@echo off
cd /d "C:\laragon\www\uploading"

echo Starting Laravel development server...
start "Laravel Server" cmd /k php artisan serve

echo Building frontend assets...
start "NPM Build" cmd /k npm run build

echo Starting Vite development server...
start "NPM Dev" cmd /k npm run dev

echo Starting Reverb WebSocket server...
start "Reverb" cmd /k php artisan reverb:start --debug

echo All services started!