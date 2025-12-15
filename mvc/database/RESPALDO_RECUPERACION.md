# Respaldo y Recuperación de la BD

Se agregó un script CLI para crear y restaurar respaldos de la base `inventario`. Los archivos `.sql` se guardan en `database/backups` y se conservan los últimos 30 días (configurable).

## Comandos rápidos

- Crear respaldo: `php database/backup.php`
- Cambiar retención: `php database/backup.php --keep-days=15`
- Listar respaldos: `php database/backup.php --list`
- Restaurar: `php database/backup.php --restore nombre_archivo.sql`
- Ayuda: `php database/backup.php --help`

> El script usa los datos de conexión definidos en `app/config/config.inc.php`. Necesita tener `mysqldump` y `mysql` en el PATH (vienen con XAMPP/WAMP/MAMP).

## Programar respaldo diario

### Windows (Task Scheduler)
1. Abrir **Programador de tareas** → **Crear tarea básica**.
2. Programa diario a la hora deseada.
3. Acción: **Iniciar un programa**. Programa: `php.exe`. Argumentos: `C:\xampp\htdocs\progWeb\mvc\database\backup.php`.
4. Iniciar en: `C:\xampp\htdocs\progWeb\mvc`.

### Linux/macOS (cron)
Editar crontab con `crontab -e` y agregar, por ejemplo a la 1:00 AM:
```
0 1 * * * php /ruta/a/progWeb/mvc/database/backup.php --keep-days=30 >> /var/log/inventario-backup.log 2>&1
```

## Restaurar manualmente
1. Detén el uso de la app para evitar escrituras durante la restauración.
2. Lista respaldos: `php database/backup.php --list`.
3. Restaura el deseado: `php database/backup.php --restore inventario_YYYYmmdd_HHMMSS.sql`.
4. Verifica el acceso al sistema y, si aplica, vuelve a habilitar la app.

## Notas y buenas prácticas
- Guarda `database/backups` fuera del repositorio (se añadió al `.gitignore`).
- Copia periódicamente los `.sql` a un disco externo/nube.
- Si cambias credenciales o nombre de BD, actualiza `app/config/config.inc.php` para que el script siga funcionando.
