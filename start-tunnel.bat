@echo off
title SwanFlow HTTPS Tunnel (Face ID iPhone)
echo ===================================================================
echo   SwanFlow - Cloudflare HTTPS Tunnel (100% Gratis Tanpa Login)
echo ===================================================================
echo   Menghubungkan http://127.0.0.1:8000 ke internet dengan HTTPS...
echo   Silakan cari dan salin URL: https://[nama-acak].trycloudflare.com
echo   Lalu buka URL tersebut di Safari iPhone Anda.
echo ===================================================================
echo.
C:\laragon\bin\cloudflared\cloudflared.exe tunnel --url http://127.0.0.1:8000
pause
