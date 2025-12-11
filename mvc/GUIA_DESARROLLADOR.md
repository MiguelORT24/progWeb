# GUÍA DEL DESARROLLADOR - SISTEMA DE INVENTARIO MVC
## Índice Principal

---

## DESCRIPCIÓN

Esta guía técnica explica la arquitectura, funciones, métodos y lógica del sistema de inventario desarrollado con el patrón MVC (Modelo-Vista-Controlador). Está dirigida a desarrolladores que necesitan entender, mantener o extender el sistema.

---

## ESTRUCTURA DE LA GUÍA

La guía está dividida en 3 partes para facilitar la navegación:

### 📘 [PARTE 1: Arquitectura y Core](GUIA_DESARROLLADOR_PARTE1.md)

**Contenido**:
1. **Arquitectura MVC**
   - Estructura del proyecto
   - Flujo de ejecución
   - Patrón MVC implementado

2. **Sistema de Enrutamiento (Routes)**
   - Clase Routes
   - Análisis de URLs
   - Método getUrl()

3. **Capa de Base de Datos (Base)**
   - Conexión PDO
   - Métodos CRUD
   - Consultas preparadas
   - Transacciones

4. **Controlador Base**
   - Método view()
   - Método model()

5. **Sistema de Autenticación**
   - Funciones de roles
   - Funciones de permisos
   - Protección de rutas

---

### 📗 [PARTE 2: Controladores y Modelos](GUIA_DESARROLLADOR_PARTE2.md)

**Contenido**:
6. **Controladores Principales**
   - **Login**: Autenticación de usuarios
     - index(), entrar(), salir()
   
   - **Home**: Dashboard principal
     - index(), dashboard()
   
   - **Inventario**: Gestión de lotes
     - index(), verLotes(), crear(), editar()
     - cambiarEstado(), historial()
   
   - **Equipos**: Catálogo de equipos
     - index(), crear(), editar(), ver()
     - eliminar(), buscarAjax()
   
   - **Ventas**: Salidas de inventario
     - index(), crear(), confirmar(), eliminar()

7. **Inicio de Modelos**
   - Introducción a modelos de datos

---

### 📙 [PARTE 3: Modelos y Funciones Avanzadas](GUIA_DESARROLLADOR_PARTE3.md)

**Contenido**:
7. **Modelos de Datos (continuación)**
   - **InventarioLote**: Gestión de lotes
     - all(), find(), create(), update()
     - inventarioAgrupado(), lotesPorEquipo()
     - stockBajo(), procesarVenta() (CRÍTICO)
     - historialMovimientos()
   
   - **Equipo**: Catálogo de equipos
     - all(), find(), create(), update()
     - buscar(), stockTotal()
   
   - **Usuario**: Autenticación
     - login()

8. **Generación de Reportes PDF**
   - Controlador Reportes
   - Uso de FPDF
   - Método diario()
   - Estructura de reportes

9. **Patrones y Mejores Prácticas**
   - Patrón de controlador
   - Patrón de modelo
   - Manejo de transacciones

10. **Ejemplos de Uso Completos**
    - Crear nuevo equipo (flujo completo)
    - Confirmar venta (flujo crítico)

---

## FUNCIONES PRINCIPALES POR CATEGORÍA

### 🔐 Autenticación y Permisos

| Función | Descripción | Retorna |
|---------|-------------|---------|
| `estaAutenticado()` | Verifica si hay sesión activa | bool |
| `requerirAuth()` | Protege ruta (redirige a login) | void |
| `requerirPermiso($p, $m, $r)` | Verifica permiso específico | void |
| `esAdmin()` | Verifica rol ADMIN | bool |
| `esAlmacen()` | Verifica rol ALMACEN | bool |
| `esLector()` | Verifica rol LECTOR | bool |
| `puedeCrear()` | Permiso de creación | bool |
| `puedeConfirmar()` | Permiso de confirmación | bool |
| `puedeEditar()` | Permiso de edición | bool |
| `puedeEliminar()` | Permiso de eliminación | bool |
| `puedeGestionarMaestros()` | Permiso para catálogos | bool |

### 💾 Base de Datos (Clase Base)

| Método | Descripción | Parámetros |
|--------|-------------|------------|
| `query($sql)` | Prepara consulta SQL | SQL string |
| `bind($param, $val, $type)` | Vincula parámetro | Parámetro, valor, tipo |
| `execute()` | Ejecuta consulta | - |
| `resultSet()` | Obtiene múltiples resultados | - |
| `single()` | Obtiene un resultado | - |
| `create($data)` | Inserta registro | Array asociativo |
| `update($data)` | Actualiza registro | Array asociativo |
| `delete()` | Elimina registro | - |
| `where($col, $val, $op)` | Agrega condición WHERE | Columna, valor, operador |
| `beginTransaction()` | Inicia transacción | - |
| `commit()` | Confirma transacción | - |
| `rollBack()` | Revierte transacción | - |
| `lastInsertId()` | Último ID insertado | - |

### 📦 Inventario (Modelo InventarioLote)

| Método | Descripción | Uso Principal |
|--------|-------------|---------------|
| `all()` | Todos los lotes | Listado completo |
| `find($id)` | Buscar lote por ID | Detalle de lote |
| `create($data)` | Crear nuevo lote | Ingreso de mercancía |
| `update($id, $data)` | Actualizar lote | Modificar datos |
| `inventarioAgrupado($filtros)` | Inventario por producto | Dashboard, reportes |
| `lotesPorEquipo($id)` | Lotes de un equipo | Detalle de producto |
| `stockBajo($limite)` | Productos con stock bajo | Alertas |
| `procesarVenta($detalle, $id, $user)` | Reducir inventario (FIFO) | Confirmar ventas |
| `historialMovimientos($id)` | Movimientos de lote | Trazabilidad |

### 🎥 Equipos (Modelo Equipo)

| Método | Descripción | Uso |
|--------|-------------|-----|
| `all()` | Todos los equipos | Catálogo completo |
| `find($id)` | Buscar por ID | Detalle |
| `create($data)` | Crear equipo | Nuevo producto |
| `update($id, $data)` | Actualizar equipo | Modificar |
| `buscar($termino, $filtros)` | Búsqueda avanzada | Filtros |
| `buscarPorSKU($sku)` | Buscar por SKU | Validación |
| `stockTotal($id)` | Stock disponible | Consultas |

### 📄 Reportes PDF

| Método | Descripción | Formato |
|--------|-------------|---------|
| `diario()` | Reporte de inventario diario | PDF Letter |
| `movimientosHoy()` | Movimientos del día | PDF Letter |

---

## FLUJOS CRÍTICOS

### 🔴 Proceso de Venta (procesarVenta)

**Importancia**: Esta función es crítica porque reduce el inventario físico.

**Lógica**:
1. Inicia transacción
2. Para cada producto vendido:
   - Obtiene lotes disponibles (FIFO)
   - Reduce cantidad de lotes
   - Marca lotes agotados
   - Registra movimientos de SALIDA
3. Actualiza estado de venta a CONFIRMADA
4. Commit o Rollback

**Seguridad**:
- Solo usuarios ADMIN pueden confirmar
- Usa transacciones para integridad
- Valida stock antes de reducir
- Registra trazabilidad completa

---

## CONVENCIONES DEL CÓDIGO

### Nombres de Archivos
- Controladores: `PascalCase.php` (ej: `Inventario.php`)
- Modelos: `PascalCase.php` (ej: `InventarioLote.php`)
- Vistas: `snake_case.php` (ej: `inventario/index.php`)

### Nombres de Métodos
- Públicos: `camelCase` (ej: `inventarioAgrupado()`)
- Privados: `camelCase` con prefijo `_` (ej: `_validarDatos()`)

### Nombres de Variables
- `$camelCase` para variables locales
- `$snake_case` para arrays de datos

### Mensajes de Sesión
```php
$_SESSION['mensaje'] = 'Texto del mensaje';
$_SESSION['tipo_mensaje'] = 'success|danger|warning|info';
```

---

## ESTRUCTURA DE DATOS COMÚN

### Sesión de Usuario
```php
$_SESSION = [
    'usuario_id' => int,
    'usuario_nombre' => string,
    'usuario_email' => string,
    'usuario_rol' => 'ADMIN'|'ALMACEN'|'LECTOR'
];
```

### Inventario Agrupado
```php
[
    'id_equipo' => int,
    'sku' => string,
    'descripcion' => string,
    'tipo' => 'CAMARA'|'SENSOR',
    'marca_nombre' => string,
    'cantidad_disponible' => int,
    'cantidad_reservada' => int,
    'cantidad_total' => int,
    'num_lotes' => int
]
```

---

## INICIO RÁPIDO PARA DESARROLLADORES

### 1. Crear un Nuevo Controlador

```php
<?php
class MiControlador extends Controller {
    private $miModelo;
    
    public function __construct() {
        $this->miModelo = $this->model('MiModelo');
    }
    
    public function index() {
        requerirAuth();
        
        $datos = $this->miModelo->all();
        $data = ['datos' => $datos];
        $this->view('mi/index', $data);
    }
}
```

### 2. Crear un Nuevo Modelo

```php
<?php
class MiModelo {
    private $db;
    
    public function __construct() {
        $this->db = new Base('mi_tabla');
    }
    
    public function all() {
        $this->db->query("SELECT * FROM mi_tabla");
        return $this->db->resultSet();
    }
}
```

### 3. Crear una Nueva Vista

```php
<!-- app/views/mi/index.php -->
<?php require_once APPROOT . '/views/inc/header.php'; ?>

<h1><?= $datos['titulo'] ?></h1>

<?php foreach($datos as $d): ?>
    <p><?= $d['nombre'] ?></p>
<?php endforeach; ?>

<?php require_once APPROOT . '/views/inc/footer.php'; ?>
```

---

## RECURSOS ADICIONALES

- **Código Fuente**: `c:\xampp\htdocs\progWeb\mvc\`
- **Manual de Usuario**: `MANUAL_DE_USUARIO.txt`
- **Configuración**: `app/config/config.inc.php`
- **Base de Datos**: `database/` (scripts SQL)

---

## SOPORTE

Para dudas técnicas o contribuciones, consulta:
1. Esta guía del desarrollador (3 partes)
2. Comentarios en el código fuente
3. Manual de usuario para entender funcionalidades

---

**Última actualización**: Diciembre 2024  
**Versión del sistema**: 1.0  
**Framework**: MVC Custom PHP
