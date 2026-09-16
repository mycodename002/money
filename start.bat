
@echo off
:: ดึงตำแหน่งโฟลเดอร์ปัจจุบันที่ไฟล์ .bat นี้ตั้งอยู่
set "TARGET_DIR=%~dp0"

:: 1. เปิด VS Code ที่โฟลเดอร์เป้าหมาย


:: 2. เปิด CMD หน้าต่างแรก แล้วรัน PHP Development Server (พอร์ท 8000)
start cmd /k "cd /d "%TARGET_DIR%" && php -S localhost:8000"
code "%TARGET_DIR%"
:: 3. เปิด CMD หน้าต่างที่สอง พร้อมใช้งานคำสั่งทั่วไปในโฟลเดอร์เดียวกัน
:: start cmd /k "cd /d "%TARGET_DIR%""

:: 4. ปิดหน้าต่าง Batch script ตัวหลักทิ้ง
exit