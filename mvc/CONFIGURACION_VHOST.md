# Configuración del Virtual Host para Sistema de Inventarios

## Paso 1: Configurar el Virtual Host en Apache

### Editar el archivo httpd-vhosts.conf

**Ubicación:** `C:\xampp\apache\conf\extra\httpd-vhosts.conf`

Agregar al final del archivo:

```apache
<VirtualHost *:80>
    DocumentRoot "C:/xampp/htdocs/progWeb/mvc/public"
    ServerName inventario
    
    <Directory "C:/xampp/htdocs/progWeb/mvc/public">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

## Paso 2: Configurar el archivo hosts de Windows

### Editar el archivo hosts

**Ubicación:** `C:\Windows\System32\drivers\etc\hosts`

**IMPORTANTE:** Debes abrir el Bloc de notas como Administrador para poder editar este archivo.

Agregar al final del archivo:

```
127.0.0.1    inventario
```

## Paso 3: Reiniciar Apache

1. Abrir el Panel de Control de XAMPP
2. Detener Apache (botón "Stop")
3. Iniciar Apache nuevamente (botón "Start")

## Paso 4: Verificar la Configuración

1. Abrir un navegador
2. Ir a: `http://inventario`
3. Deberías ver la página de login del sistema

## Paso 5: Migrar la Base de Datos (si aún no lo has hecho)

1. Abrir phpMyAdmin: `http://localhost/phpmyadmin`
2. Seleccionar la base de datos `pw20253`
3. Ir a la pestaña "SQL"
4. Abrir el archivo: `C:\xampp\htdocs\progWeb\mvc\database\migracion_inventario.sql`
5. Copiar todo el contenido
6. Pegarlo en phpMyAdmin
7. Hacer clic en "Ejecutar"

## Configuración Actualizada

✅ **URLROOT actualizado a:** `http://inventario`

El archivo `config.inc.php` ya ha sido modificado para usar el nuevo dominio.

## Solución de Problemas

### Error 404 - Página no encontrada
- Verificar que el virtual host está configurado correctamente
- Verificar que Apache se reinició después de los cambios
- Verificar que el archivo hosts tiene la entrada correcta

### Error "Access Forbidden"
- Verificar que la directiva `<Directory>` tiene `AllowOverride All`
- Verificar que existe el archivo `.htaccess` en `mvc/public/`

### La página se ve sin estilos
- Verificar que la ruta en `config.inc.php` es correcta
- Verificar que los archivos CSS están en `mvc/public/assets/`

## URLs del Sistema

- **Dashboard:** `http://inventario`
- **Productos:** `http://inventario/productos`
- **Categorías:** `http://inventario/categorias`
- **Proveedores:** `http://inventario/proveedores`
- **Movimientos:** `http://inventario/movimientos`
- **Usuarios:** `http://inventario/usuarios`
- **Login:** `http://inventario/usuarios/login`

---

**¡Listo para usar!** 🚀
