# ใช้ PHP 8.2
FROM php:8.2-cli

# ตั้ง working directory
WORKDIR /app

# คัดลอกไฟล์ทั้งหมดในโฟลเดอร์ hotel เข้าไปใน container
COPY ./hotel /app

# เปิดพอร์ต Render ใช้ตัวแปร $PORT
ENV PORT=10000

# คำสั่งเริ่มรัน PHP server
CMD php -S 0.0.0.0:$PORT -t .
