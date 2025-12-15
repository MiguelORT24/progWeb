# GUÍA DEL DESARROLLADOR - SISTEMA DE INVENTARIO MVC
## Parte 2: Controladores y Modelos

---

## ÍNDICE PARTE 2

6. Controladores Principales
   - 6.1 Login
   - 6.2 Home
   - 6.3 Inventario
   - 6.4 Equipos
   - 6.5 Ventas
7. Modelos de Datos
   - 7.1 InventarioLote
   - 7.2 Equipo
   - 7.3 Usuario

---

## 6. CONTROLADORES PRINCIPALES

### 6.1 Controlador Login (`app/controllers/Login.php`)

**Propósito**: Gestionar autenticación de usuarios (login/logout).

#### Constructor `__construct()`

```php
public function __construct()
Lógica:
  1. Carga el modelo Usuario
  2. Guarda en $this->usuarioModel
```

#### Método `index()`

**Firma**: `public function index()`

**Propósito**: Mostrar formulario de login

**Lógica**:
```php
1. Verifica si ya está logueado (isset($_SESSION['usuario_id']))
2. Si está logueado, redirige a 'home'
3. Si no, prepara datos para la vista:
   - titulo: 'Iniciar Sesión'
   - email: '' (vacío)
   - error: '' (sin errores)
4. Carga vista 'login/index'
```

#### Método `entrar()`

**Firma**: `public function entrar()`

**Propósito**: Procesar credenciales y crear sesión

**Lógica**:
```php
1. Verifica que sea POST
2. Obtiene email y password de $_POST
3. Limpia datos con trim()
4. Llama a $this->usuarioModel->login($email, $password)
5. Si login exitoso:
   a. Crea variables de sesión:
      - $_SESSION['usuario_id']
      - $_SESSION['usuario_nombre']
      - $_SESSION['usuario_email']
      - $_SESSION['usuario_rol']
   b. Redirige a 'home'
6. Si falla:
   a. Prepara datos con error
   b. Muestra vista de login con mensaje
```

**Ejemplo de Sesión Creada**:
```php
$_SESSION = [
    'usuario_id' => 1,
    'usuario_nombre' => 'Juan Pérez',
    'usuario_email' => 'admin@inventario.com',
    'usuario_rol' => 'ADMIN'
];
```

#### Método `salir()`

**Firma**: `public function salir()`

**Propósito**: Cerrar sesión

**Lógica**:
```php
1. session_unset() - Limpia variables de sesión
2. session_destroy() - Destruye la sesión
3. redirect('login') - Redirige al login
```

---

### 6.2 Controlador Home (`app/controllers/Home.php`)

**Propósito**: Dashboard principal del sistema.

#### Constructor

```php
public function __construct()
Lógica:
  1. Carga modelo InventarioLote
  2. Carga modelo Equipo
  3. Carga modelo Compra
```

#### Método `index()`

**Firma**: `public function index()`

**Propósito**: Página inicial con redirección inteligente

**Lógica**:
```php
1. Verifica autenticación con estaAutenticado()
2. Si NO está autenticado: redirect('login')
3. Si está autenticado: redirect('home/dashboard')
```

#### Método `dashboard()`

**Firma**: `public function dashboard()`

**Propósito**: Mostrar dashboard con estadísticas

**Lógica**:
```php
1. Requiere autenticación con requerirAuth()
2. Obtiene inventario agrupado:
   $inventario = $this->loteModel->inventarioAgrupado([])
3. Calcula total de productos: count($inventario)
4. Calcula total de unidades: array_sum(array_column($inventario, 'cantidad_total'))
5. Obtiene productos con stock bajo:
   $stock_bajo = $this->loteModel->stockBajo(10)
6. Si puede confirmar (puedeConfirmar()):
   a. Obtiene todas las compras
   b. Filtra solo las PENDIENTES con array_filter()
7. Prepara array de datos con todas las estadísticas
8. Carga vista 'home/dashboard'
```

**Datos Enviados a Vista**:
```php
$data = [
    'titulo' => 'Dashboard - Sistema de Inventario',
    'total_productos' => 25,
    'total_unidades' => 1500,
    'stock_bajo' => [...],
    'compras_pendientes' => [...],
    'usuario' => 'Juan Pérez',
    'rol' => 'ADMIN'
];
```

---

### 6.3 Controlador Inventario (`app/controllers/Inventario.php`)

**Propósito**: Gestionar lotes de inventario.

#### Constructor

```php
public function __construct()
Lógica:
  1. Carga modelo InventarioLote
  2. Carga modelo Equipo
  3. Carga modelo Ubicacion
  4. Carga modelo Marca
  5. Carga modelo Categoria
```

#### Método `index()`

**Firma**: `public function index()`

**Propósito**: Vista principal de inventario agrupado por producto

**Lógica**:
```php
1. Captura filtros de $_GET:
   - tipo: '' (vacío o CAMARA/SENSOR)
   - marca: '' (ID de marca)
   - sku: '' (código SKU)
2. Llama a $this->loteModel->inventarioAgrupado($filtros)
3. Obtiene todas las marcas para el filtro
4. Prepara datos y carga vista 'inventario/index'
```

**Resultado de inventarioAgrupado**:
```php
[
    [
        'id_equipo' => 1,
        'sku' => 'CAM-001',
        'descripcion' => 'Cámara IP 4MP',
        'tipo' => 'CAMARA',
        'marca_nombre' => 'Hikvision',
        'cantidad_disponible' => 50,
        'cantidad_reservada' => 10,
        'cantidad_total' => 60,
        'num_lotes' => 3
    ],
    ...
]
```

#### Método `verLotes($id_equipo)`

**Firma**: `public function verLotes($id_equipo)`

**Parámetros**:
- `$id_equipo` (int): ID del equipo

**Propósito**: Ver todos los lotes de un equipo específico

**Lógica**:
```php
1. Busca el equipo: $this->equipoModel->find($id_equipo)
2. Obtiene lotes: $this->loteModel->lotesPorEquipo($id_equipo)
3. Obtiene ubicaciones para mostrar en formularios
4. Prepara datos y carga vista 'inventario/lotes'
```

#### Método `crear()`

**Firma**: `public function crear()`

**Propósito**: Crear nuevo lote de inventario

**Lógica**:
```php
1. Requiere autenticación
2. Requiere permiso de creación: requerirPermiso(puedeCrear(), ...)
3. Si es POST:
   a. Captura datos del formulario:
      - id_equipo
      - id_ubicacion
      - cantidad
      - estado (default: 'DISPONIBLE')
      - fecha_ingreso (default: hoy)
   b. Llama a $this->loteModel->create($data)
   c. Si exitoso:
      - Guarda mensaje de éxito en sesión
      - Redirige a 'inventario'
   d. Si falla:
      - Guarda mensaje de error
4. Si es GET:
   a. Obtiene equipos y ubicaciones
   b. Carga vista 'inventario/crear'
```

#### Método `editar($id)`

**Firma**: `public function editar($id)`

**Parámetros**:
- `$id` (int): ID del lote

**Propósito**: Editar lote existente

**Lógica**:
```php
1. Requiere autenticación y permiso de edición
2. Si es POST:
   a. Captura datos del formulario
   b. Llama a $this->loteModel->update($id, $data)
   c. Guarda mensaje y redirige
3. Si es GET:
   a. Busca el lote: $this->loteModel->find($id)
   b. Obtiene equipos y ubicaciones
   c. Carga vista 'inventario/editar' con datos prellenados
```

#### Método `cambiarEstado($id)`

**Firma**: `public function cambiarEstado($id)`

**Parámetros**:
- `$id` (int): ID del lote

**Propósito**: Cambiar estado del lote (DISPONIBLE/AGOTADO/RESERVADO)

**Lógica**:
```php
1. Requiere autenticación y permiso de edición
2. Si es POST:
   a. Obtiene nuevo estado de $_POST['estado']
   b. Llama a $this->loteModel->cambiarEstado($id, $nuevoEstado)
   c. Guarda mensaje según resultado
3. Redirige a 'inventario'
```

#### Método `historial($id)`

**Firma**: `public function historial($id)`

**Parámetros**:
- `$id` (int): ID del lote

**Propósito**: Ver historial de movimientos del lote

**Lógica**:
```php
1. Busca el lote
2. Obtiene movimientos: $this->loteModel->historialMovimientos($id)
3. Carga vista 'inventario/historial' con:
   - Información del lote
   - Lista de movimientos (entradas/salidas)
```

---

### 6.4 Controlador Equipos (`app/controllers/Equipos.php`)

**Propósito**: Gestionar catálogo de equipos (cámaras y sensores).

#### Método `index()`

**Firma**: `public function index()`

**Propósito**: Listar equipos con búsqueda y filtros

**Lógica**:
```php
1. Captura parámetros de búsqueda:
   - termino: $_GET['buscar'] (texto libre)
   - filtros:
     * tipo: CAMARA/SENSOR
     * id_marca: ID de marca
     * id_categoria: ID de categoría
2. Si hay término o filtros:
   $equipos = $this->equipoModel->buscar($termino, $filtros)
3. Si no:
   $equipos = $this->equipoModel->all()
4. Obtiene marcas y categorías para filtros
5. Carga vista 'equipos/index'
```

#### Método `crear()`

**Firma**: `public function crear()`

**Propósito**: Crear nuevo equipo en el catálogo

**Lógica**:
```php
1. Requiere permiso: puedeGestionarMaestros()
2. Si es POST:
   a. Captura y limpia datos:
      - sku: strtoupper(trim($_POST['sku']))
      - tipo: CAMARA/SENSOR
      - descripcion
      - id_marca (nullable)
      - id_categoria (nullable)
   b. Valida SKU único:
      - Busca si existe: $this->equipoModel->buscarPorSKU($sku)
      - Si existe, muestra warning
   c. Si no existe:
      - Crea equipo: $this->equipoModel->create($data)
      - Redirige con mensaje de éxito
3. Si es GET:
   a. Obtiene marcas y categorías
   b. Carga vista 'equipos/crear'
```

#### Método `editar($id)`

**Firma**: `public function editar($id)`

**Lógica**:
```php
1. Requiere permiso de gestión de maestros
2. Si es POST:
   a. Captura datos (igual que crear)
   b. Actualiza: $this->equipoModel->update($id, $data)
   c. Redirige con mensaje
3. Si es GET:
   a. Busca equipo: $this->equipoModel->find($id)
   b. Obtiene marcas y categorías
   c. Carga vista 'equipos/editar' con datos
```

#### Método `ver($id)`

**Firma**: `public function ver($id)`

**Propósito**: Ver detalle de equipo con stock total

**Lógica**:
```php
1. Busca equipo: $this->equipoModel->find($id)
2. Calcula stock: $this->equipoModel->stockTotal($id)
3. Carga vista 'equipos/ver' con:
   - Información del equipo
   - Stock total disponible
   - Acciones rápidas (ver lotes, crear lote)
```

#### Método `eliminar($id)`

**Firma**: `public function eliminar($id)`

**Lógica**:
```php
1. Requiere permiso de gestión de maestros
2. Intenta eliminar: $this->equipoModel->delete($id)
3. Guarda mensaje según resultado
4. Redirige a 'equipos'
```

#### Método `buscarAjax()`

**Firma**: `public function buscarAjax()`

**Propósito**: API para búsqueda AJAX (autocompletado)

**Lógica**:
```php
1. Establece header JSON
2. Obtiene término: $_GET['q']
3. Busca equipos: $this->equipoModel->buscar($termino)
4. Retorna JSON: echo json_encode($equipos)
```

**Ejemplo de Respuesta**:
```json
[
    {
        "id_equipo": 1,
        "sku": "CAM-001",
        "descripcion": "Cámara IP 4MP",
        "marca_nombre": "Hikvision"
    }
]
```

---

### 6.5 Controlador Ventas (`app/controllers/Ventas.php`)

**Propósito**: Gestionar salidas de inventario (ventas/entregas).

#### Método `index()`

**Firma**: `public function index()`

**Lógica**:
```php
1. Obtiene todas las ventas: $this->ventaModel->all()
2. Carga vista 'ventas/index' con lista de ventas
```

#### Método `crear()`

**Firma**: `public function crear()`

**Propósito**: Crear nueva salida de inventario

**Lógica Completa**:
```php
1. Requiere autenticación y permiso de creación
2. Si es POST:
   a. Captura arrays de productos:
      - equipos[]: IDs de equipos
      - cantidades[]: Cantidades solicitadas
      - precios[]: Precios unitarios
   
   b. VALIDACIÓN DE INVENTARIO:
      Para cada equipo:
        i. Valida cantidad > 0
        ii. Obtiene inventario disponible:
            $inventario = $this->loteModel->inventarioAgrupado(['id_equipo' => $id])
        iii. Compara cantidad solicitada vs disponible
        iv. Si no hay suficiente, agrega error al array
   
   c. Si hay errores:
      - Muestra mensajes de warning
      - Recarga formulario con datos
      - return (no crea la venta)
   
   d. Si validación OK:
      i. Crea venta en estado PENDIENTE:
         $dataVenta = [
             'fecha' => date('Y-m-d'),
             'id_proveedor' => $id_cliente,
             'total' => 0,
             'estado' => 'PENDIENTE'
         ]
      ii. Obtiene ID: $venta_id = $this->ventaModel->create($dataVenta)
      iii. Agrega detalles:
           foreach equipos:
             $this->ventaModel->agregarDetalle($venta_id, $detalle)
             $total += cantidad * precio
      iv. Actualiza total de la venta
      v. Redirige con mensaje de éxito

3. Si es GET:
   a. Obtiene productos con stock:
      $productos = $this->loteModel->inventarioAgrupado([])
   b. Obtiene clientes
   c. Carga vista 'ventas/crear'
```

**Flujo de Validación**:
```
Usuario solicita 100 unidades de CAM-001
↓
Sistema consulta inventario agrupado
↓
Encuentra solo 50 disponibles
↓
Agrega error: "CAM-001 solo tiene 50 unidades disponibles"
↓
Muestra formulario con mensaje de warning
↓
Usuario corrige cantidad
```

#### Método `confirmar($id)`

**Firma**: `public function confirmar($id)`

**Propósito**: Confirmar venta y reducir inventario (CRÍTICO)

**Lógica**:
```php
1. Requiere permiso: puedeConfirmar() (solo ADMIN)
2. Si es POST:
   a. Obtiene usuario_id de sesión
   b. Busca la venta: $this->ventaModel->find($id)
   c. Obtiene detalle: $this->ventaModel->obtenerDetalle($id)
   d. PROCESA VENTA:
      $resultado = $this->loteModel->procesarVenta($detalle, $id, $usuario_id)
   e. Si exitoso:
      - Mensaje: "Venta confirmada. Inventario reducido"
   f. Si falla:
      - Mensaje: "Error: " + descripción del error
   g. Redirige a 'ventas'
3. Si es GET:
   a. Muestra vista de confirmación con detalle
```

**Método procesarVenta() (en InventarioLote)**:
```php
Lógica:
1. Inicia transacción
2. Para cada producto en el detalle:
   a. Obtiene lotes disponibles ordenados por FIFO
   b. Reduce cantidad de lotes hasta cubrir la venta:
      - Si lote tiene 100 y se venden 30, queda 70
      - Si lote tiene 20 y se venden 30, agota lote y busca siguiente
   c. Registra movimiento de SALIDA
   d. Si no hay suficiente stock, rollback y retorna error
3. Actualiza estado de venta a CONFIRMADA
4. Commit de transacción
5. Retorna ['exito' => true] o ['exito' => false, 'error' => '...']
```

#### Método `eliminar($id)`

**Firma**: `public function eliminar($id)`

**Lógica**:
```php
1. Requiere permiso de eliminación
2. Intenta eliminar: $this->ventaModel->delete($id)
3. Si exitoso: mensaje "Venta eliminada"
4. Si falla: "No se puede eliminar una venta confirmada"
5. Redirige a 'ventas'
```

---

**FIN DE LA PARTE 2**

Continúa en: `GUIA_DESARROLLADOR_PARTE3.md`
