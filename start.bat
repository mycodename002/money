@echo off
set "TARGET_DIR=%~dp0"
start cmd /k "cd /d "%TARGET_DIR%" && php -S localhost:8000"
start cmd /k "code "%TARGET_DIR%" && exit"
exit


