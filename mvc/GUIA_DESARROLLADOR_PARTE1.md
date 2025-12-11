# GUÍA DEL DESARROLLADOR - SISTEMA DE INVENTARIO MVC
## Parte 1: Arquitectura y Core

---

## ÍNDICE GENERAL

**PARTE 1: Arquitectura y Core**
1. Arquitectura MVC
2. Sistema de Enrutamiento (Routes)
3. Capa de Base de Datos (Base)
4. Controlador Base
5. Sistema de Autenticación

**PARTE 2: Controladores y Modelos** (ver GUIA_DESARROLLADOR_PARTE2.md)
**PARTE 3: Funciones Avanzadas** (ver GUIA_DESARROLLADOR_PARTE3.md)

---

## 1. ARQUITECTURA MVC

### 1.1 Estructura del Proyecto

```
mvc/
├── app/
│   ├── config/          # Configuración de la aplicación
│   ├── controllers/     # Controladores (lógica de negocio)
│   ├── core/           # Núcleo del framework MVC
│   ├── helpers/        # Funciones auxiliares
│   ├── models/         # Modelos (acceso a datos)
│   └── views/          # Vistas (presentación)
├── public/             # Archivos públicos (CSS, JS, imágenes)
├── vendor/             # Librerías de terceros (FPDF, etc.)
└── database/           # Scripts SQL
```

### 1.2 Flujo de Ejecución

```
1. Usuario accede a URL: http://localhost/inventario/crear
2. .htaccess redirige a index.php?url=inventario/crear
3. Routes analiza la URL y determina:
   - Controlador: Inventario
   - Método: crear
   - Parámetros: []
4. Routes instancia el controlador y llama al método
5. El controlador procesa la lógica y carga la vista
6. La vista se renderiza y se envía al navegador
```

### 1.3 Patrón MVC Implementado

**Modelo (Model)**: Gestiona datos y lógica de negocio
- Ubicación: `app/models/`
- Responsabilidad: Interactuar con la base de datos
- Ejemplo: `InventarioLote.php`, `Equipo.php`

**Vista (View)**: Presenta información al usuario
- Ubicación: `app/views/`
- Responsabilidad: Renderizar HTML
- Ejemplo: `inventario/index.php`, `equipos/crear.php`

**Controlador (Controller)**: Coordina Modelo y Vista
- Ubicación: `app/controllers/`
- Responsabilidad: Procesar solicitudes, ejecutar lógica
- Ejemplo: `Inventario.php`, `Equipos.php`

---

## 2. SISTEMA DE ENRUTAMIENTO

### 2.1 Clase Routes (`app/core/Routes.php`)

**Propósito**: Analizar URLs y enrutar solicitudes al controlador y método correspondiente.

#### Propiedades

```php
protected $controladorActual = 'Home';  // Controlador por defecto
protected $metodoActual = 'index';      // Método por defecto
protected $parametros = [];             // Parámetros de la URL
```

#### Constructor `__construct()`

**Lógica**:
1. Obtiene la URL mediante `getUrl()`
2. Determina el controlador desde `$url[0]`
3. Carga el archivo del controlador
4. Instancia el controlador
5. Determina el método desde `$url[1]`
6. Extrae parámetros restantes
7. Ejecuta el método con `call_user_func_array()`

**Ejemplo de URL**:
```
URL: inventario/verLotes/5
- Controlador: Inventario
- Método: verLotes
- Parámetros: [5]
- Ejecuta: Inventario->verLotes(5)
```

#### Método `getUrl()`

**Firma**: `private function getUrl()`

**Retorna**: `array|null` - URL dividida en segmentos

**Lógica**:
```php
1. Verifica si existe $_GET['url']
2. Elimina espacios y barras finales con rtrim()
3. Sanitiza la URL con filter_var(FILTER_SANITIZE_URL)
4. Divide la cadena en array con explode('/')
5. Retorna el array de segmentos
```

**Ejemplo**:
```php
// URL: inventario/editar/10
getUrl() retorna: ['inventario', 'editar', '10']
```

---

## 3. CAPA DE BASE DE DATOS

### 3.1 Clase Base (`app/core/Base.php`)

**Propósito**: Proporcionar una capa de abstracción para operaciones de base de datos con PDO.

#### Propiedades

```php
private $host = DB_HOST;        // Servidor de BD
private $user = DB_USER;        // Usuario de BD
private $pass = DB_PASS;        // Contraseña
private $dbname = DB_NAME;      // Nombre de BD
private $dbh;                   // Database Handler (PDO)
private $stmt;                  // Statement (consulta preparada)
private $error;                 // Mensaje de error
private $table;                 // Tabla actual
private $wheres = [];           // Condiciones WHERE
private $limit;                 // Límite de resultados
```

#### Constructor `__construct($table)`

**Parámetros**:
- `$table` (string): Nombre de la tabla a gestionar

**Lógica**:
```php
1. Guarda el nombre de la tabla
2. Crea DSN (Data Source Name) para MySQL
3. Establece opciones de PDO:
   - PDO::ATTR_PERSISTENT => true (conexión persistente)
   - PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
4. Crea instancia PDO
5. Captura excepciones y guarda errores
```

#### Método `query($sql)`

**Firma**: `public function query($sql)`

**Parámetros**:
- `$sql` (string): Consulta SQL a preparar

**Lógica**:
```php
1. Prepara la consulta con $this->dbh->prepare($sql)
2. Guarda el statement en $this->stmt
3. Permite binding de parámetros posterior
```

**Uso**:
```php
$this->db->query("SELECT * FROM equipo WHERE id_equipo = :id");
$this->db->bind(':id', $id);
$result = $this->db->single();
```

#### Método `bind($parametro, $valor, $tipo = null)`

**Firma**: `public function bind($parametro, $valor, $tipo = null)`

**Parámetros**:
- `$parametro` (string): Nombre del parámetro (ej: ':id')
- `$valor` (mixed): Valor a vincular
- `$tipo` (int|null): Tipo de dato PDO (opcional)

**Lógica**:
```php
1. Si no se especifica tipo, lo detecta automáticamente:
   - is_int() => PDO::PARAM_INT
   - is_bool() => PDO::PARAM_BOOL
   - is_null() => PDO::PARAM_NULL
   - default => PDO::PARAM_STR
2. Vincula el parámetro con bindValue()
```

#### Método `execute()`

**Firma**: `public function execute()`

**Retorna**: `bool` - true si la ejecución fue exitosa

**Lógica**:
```php
1. Ejecuta el statement preparado
2. Retorna true/false según el resultado
```

#### Método `resultSet()`

**Firma**: `public function resultSet()`

**Retorna**: `array` - Array de resultados

**Lógica**:
```php
1. Ejecuta la consulta con execute()
2. Obtiene todos los resultados con fetchAll(PDO::FETCH_ASSOC)
3. Retorna array asociativo de filas
```

**Uso**:
```php
$this->db->query("SELECT * FROM equipo");
$equipos = $this->db->resultSet();
// Retorna: [['id_equipo' => 1, 'sku' => 'CAM-001'], ...]
```

#### Método `single()`

**Firma**: `public function single()`

**Retorna**: `array|false` - Una fila o false

**Lógica**:
```php
1. Ejecuta la consulta
2. Obtiene una sola fila con fetch(PDO::FETCH_ASSOC)
3. Retorna array asociativo o false
```

#### Método `create($data)`

**Firma**: `public function create($data)`

**Parámetros**:
- `$data` (array): Array asociativo [columna => valor]

**Retorna**: `int` - ID del registro insertado

**Lógica**:
```php
1. Extrae las columnas del array: array_keys($data)
2. Crea placeholders: :columna1, :columna2, ...
3. Construye SQL: INSERT INTO tabla (col1, col2) VALUES (:col1, :col2)
4. Prepara la consulta
5. Vincula cada valor con bind()
6. Ejecuta
7. Retorna lastInsertId()
```

**Ejemplo**:
```php
$data = [
    'sku' => 'CAM-001',
    'tipo' => 'CAMARA',
    'descripcion' => 'Cámara IP 4MP'
];
$id = $this->db->create($data);
```

#### Método `update($data)`

**Firma**: `public function update($data)`

**Parámetros**:
- `$data` (array): Datos a actualizar

**Retorna**: `bool` - true si se actualizó

**Lógica**:
```php
1. Construye SET clause: col1 = :col1, col2 = :col2
2. Agrega condiciones WHERE con addWheres()
3. Construye SQL: UPDATE tabla SET ... WHERE ...
4. Vincula valores
5. Ejecuta y retorna resultado
```

**Ejemplo**:
```php
$this->db->where('id_equipo', 5);
$this->db->update(['descripcion' => 'Nueva descripción']);
```

#### Método `delete()`

**Firma**: `public function delete()`

**Retorna**: `bool` - true si se eliminó

**Lógica**:
```php
1. Construye SQL: DELETE FROM tabla WHERE ...
2. Agrega condiciones WHERE
3. Ejecuta y retorna resultado
```

#### Método `where($column, $value, $operator = '=')`

**Firma**: `public function where($column, $value, $operator = '=')`

**Parámetros**:
- `$column` (string): Nombre de la columna
- `$value` (mixed): Valor a comparar
- `$operator` (string): Operador (=, >, <, LIKE, etc.)

**Lógica**:
```php
1. Agrega condición al array $wheres
2. Permite encadenamiento de métodos
3. Se usa en update(), delete(), get()
```

**Ejemplo**:
```php
$this->db->where('estado', 'DISPONIBLE')
         ->where('cantidad', 0, '>')
         ->get();
```

#### Transacciones

**beginTransaction()**: Inicia transacción
**commit()**: Confirma cambios
**rollBack()**: Revierte cambios

**Uso**:
```php
try {
    $this->db->beginTransaction();
    $this->db->create($data1);
    $this->db->create($data2);
    $this->db->commit();
} catch (Exception $e) {
    $this->db->rollBack();
}
```

---

## 4. CONTROLADOR BASE

### 4.1 Clase Controller (`app/core/Controller.php`)

**Propósito**: Clase base para todos los controladores, proporciona métodos comunes.

#### Método `view($view, $data = [])`

**Firma**: `public function view($view, $data = [])`

**Parámetros**:
- `$view` (string): Ruta de la vista (ej: 'inventario/index')
- `$data` (array): Datos a pasar a la vista

**Lógica**:
```php
1. Construye ruta: APPROOT . '/views/' . $view . '.php'
2. Verifica si el archivo existe
3. Si no existe, muestra error
4. Si existe, incluye el archivo con require_once
5. Los datos en $data están disponibles en la vista
```

**Ejemplo**:
```php
$data = [
    'titulo' => 'Inventario',
    'productos' => $productos
];
$this->view('inventario/index', $data);

// En la vista: echo $titulo; foreach($productos as $p) { ... }
```

#### Método `model($model)`

**Firma**: `public function model($model)`

**Parámetros**:
- `$model` (string): Nombre del modelo (debe iniciar con mayúscula)

**Retorna**: `object` - Instancia del modelo

**Lógica**:
```php
1. Construye ruta: APPROOT . '/models/' . ucwords($model) . '.php'
2. Verifica si existe
3. Si no existe, muestra error
4. Incluye el archivo
5. Instancia y retorna el modelo
```

**Ejemplo**:
```php
$this->loteModel = $this->model('InventarioLote');
$lotes = $this->loteModel->all();
```

---

## 5. SISTEMA DE AUTENTICACIÓN

### 5.1 Helper de Permisos (`app/helpers/helpers.php`)

**Propósito**: Funciones para verificar autenticación y permisos basados en roles.

#### Roles del Sistema

```php
- ADMIN: Administrador (acceso completo)
- ALMACEN: Personal de almacén (crear/editar inventario)
- LECTOR: Solo lectura (consultas y reportes)
```

#### Funciones de Verificación de Rol

**`esAdmin()`**
```php
function esAdmin()
Retorna: bool
Lógica: Verifica si $_SESSION['usuario_rol'] === 'ADMIN'
```

**`esAlmacen()`**
```php
function esAlmacen()
Retorna: bool
Lógica: Verifica si $_SESSION['usuario_rol'] === 'ALMACEN'
```

**`esLector()`**
```php
function esLector()
Retorna: bool
Lógica: Verifica si $_SESSION['usuario_rol'] === 'LECTOR'
```

#### Funciones de Permisos

**`puedeCrear()`**
```php
function puedeCrear()
Retorna: bool
Lógica: return esAdmin() || esAlmacen()
Uso: Verificar si puede crear lotes, equipos, etc.
```

**`puedeConfirmar()`**
```php
function puedeConfirmar()
Retorna: bool
Lógica: return esAdmin()
Uso: Solo admin puede confirmar salidas/compras
```

**`puedeEditar()`**
```php
function puedeEditar()
Retorna: bool
Lógica: return esAdmin()
Uso: Solo admin puede editar registros
```

**`puedeEliminar()`**
```php
function puedeEliminar()
Retorna: bool
Lógica: return esAdmin()
Uso: Solo admin puede eliminar
```

**`puedeGestionarMaestros()`**
```php
function puedeGestionarMaestros()
Retorna: bool
Lógica: return esAdmin()
Uso: Gestionar marcas, categorías, ubicaciones
```

#### Funciones de Autenticación

**`estaAutenticado()`**
```php
function estaAutenticado()
Retorna: bool
Lógica: return isset($_SESSION['usuario_id'])
Uso: Verificar si hay sesión activa
```

**`requerirAuth()`**
```php
function requerirAuth()
Retorna: void
Lógica:
  1. Si !estaAutenticado()
  2. redirect('login')
  3. exit
Uso: Proteger rutas que requieren login
```

**`requerirPermiso($permiso, $mensaje, $redirigirA)`**
```php
function requerirPermiso($permiso, $mensaje = '', $redirigirA = null)
Parámetros:
  - $permiso (bool): Resultado de función de permiso
  - $mensaje (string): Mensaje de error
  - $redirigirA (string): Ruta de redirección
Lógica:
  1. Si !$permiso
  2. Guarda mensaje en sesión
  3. Redirige a $redirigirA o 'home/dashboard'
  4. exit
```

**Ejemplo de Uso en Controlador**:
```php
public function crear() {
    requerirAuth();  // Requiere login
    requerirPermiso(
        puedeCrear(), 
        'No tienes permisos para crear lotes', 
        'inventario'
    );
    
    // Lógica de creación...
}
```

---

**FIN DE LA PARTE 1**

Continúa en: `GUIA_DESARROLLADOR_PARTE2.md`
