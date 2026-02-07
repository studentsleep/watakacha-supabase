# 1. ใช้ PHP 8.2 (เวอร์ชันที่เสถียรและรองรับ Laravel ใหม่ๆ)
FROM php:8.2-cli

# 2. ติดตั้งโปรแกรมพื้นฐาน Linux ที่จำเป็น
# - libpq-dev: สำหรับต่อ PostgreSQL (Supabase)
# - libzip-dev, zip, unzip: สำหรับแตกไฟล์ zip
# - git: สำหรับโหลด Library ผ่าน Composer
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    curl \
    && docker-php-ext-install pdo pdo_pgsql zip

# 3. ติดตั้ง Composer (ตัวจัดการ Library ของ PHP)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 4. กำหนดโฟลเดอร์ทำงานใน Docker
WORKDIR /var/www/html

# 5. ก๊อปปี้ไฟล์โปรเจกต์ทั้งหมดจากเครื่องเรา ขึ้นไปบน Docker
COPY . .

# 6. สั่ง Composer ให้ติดตั้ง Library ของ Laravel
# --no-dev: ไม่ลงตัวทดสอบ (ประหยัดที่)
# --optimize-autoloader: ปรับแต่งให้โหลดเร็วขึ้น
RUN composer install --no-dev --optimize-autoloader

# 7. แก้ปัญหาสิทธิ์การเขียนไฟล์ (Permission)
# เพื่อให้ Laravel เขียน Log และ Cache ได้ ไม่งั้นจะ Error 500
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# 8. คำสั่งรันเว็บเมื่อ Render สั่ง Start
# ใช้ $PORT ที่ Render ส่งมาให้อัตโนมัติ
CMD php artisan serve --host=0.0.0.0 --port=$PORT