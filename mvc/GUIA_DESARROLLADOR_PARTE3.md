# GUÍA DEL DESARROLLADOR - SISTEMA DE INVENTARIO MVC
## Parte 3: Modelos y Funciones Avanzadas

---

## ÍNDICE PARTE 3

7. Modelos de Datos
   - 7.1 InventarioLote
   - 7.2 Equipo
   - 7.3 Usuario
8. Generación de Reportes PDF
9. Patrones y Mejores Prácticas
10. Ejemplos de Uso Completos

---

## 7. MODELOS DE DATOS

### 7.1 Modelo InventarioLote (`app/models/InventarioLote.php`)

**Propósito**: Gestionar lotes de inventario con ubicación, estado y movimientos.

**Tabla**: `inventario_lote`

#### Constructor

```php
public function __construct()
Lógica:
  1. Crea instancia de Base con tabla 'inventario_lote'
  2. Guarda en $this->db
```

#### Método `all()`

**Firma**: `public function all()`

**Retorna**: `array` - Todos los lotes con información completa

**SQL Generado**:
```sql
SELECT 
    il.*,
    e.sku, e.descripcion, e.tipo,
    u.nombre AS ubicacion_nombre,
    m.nombre AS marca_nombre
FROM inventario_lote il
LEFT JOIN equipo e ON il.id_equipo = e.id_equipo
LEFT JOIN ubicacion u ON il.id_ubicacion = u.id_ubicacion
LEFT JOIN marca m ON e.id_marca = m.id_marca
ORDER BY il.fecha_ingreso DESC
```

#### Método `find($id)`

**Firma**: `public function find($id)`

**Parámetros**:
- `$id` (int): ID del lote

**Retorna**: `array|false` - Lote con información completa

**Lógica**:
```php
1. Construye SQL con JOINs a equipo, ubicacion, marca
2. Agrega WHERE id_lote = :id
3. Ejecuta con bind(':id', $id)
4. Retorna single()
```

#### Método `create($data)`

**Firma**: `public function create($data)`

**Parámetros**:
```php
$data = [
    'id_equipo' => int,
    'id_ubicacion' => int,
    'cantidad' => int,
    'estado' => string,  // DISPONIBLE/AGOTADO/RESERVADO
    'fecha_ingreso' => date
]
```

**Retorna**: `int` - ID del lote creado

**Lógica**:
```php
1. Inicia transacción
2. Inserta lote en inventario_lote
3. Registra movimiento de ENTRADA:
   - tipo: 'ENTRADA'
   - cantidad: cantidad del lote
   - motivo: 'Ingreso de nuevo lote'
4. Commit
5. Retorna ID del lote
```

#### Método `update($id, $data)`

**Firma**: `public function update($id, $data)`

**Lógica**:
```php
1. Obtiene lote actual para comparar
2. Actualiza campos en inventario_lote
3. Si cambió la cantidad:
   a. Calcula diferencia
   b. Registra movimiento (ENTRADA o SALIDA)
4. Retorna true/false
```

#### Método `inventarioAgrupado($filtros = [])`

**Firma**: `public function inventarioAgrupado($filtros = [])`

**Parámetros**:
```php
$filtros = [
    'tipo' => 'CAMARA'|'SENSOR'|'',
    'marca' => int (id_marca),
    'sku' => string,
    'id_equipo' => int
]
```

**Retorna**: `array` - Inventario agrupado por equipo

**SQL Generado** (simplificado):
```sql
SELECT 
    e.id_equipo,
    e.sku,
    e.descripcion,
    e.tipo,
    m.nombre AS marca_nombre,
    SUM(CASE WHEN il.estado = 'DISPONIBLE' THEN il.cantidad ELSE 0 END) AS cantidad_disponible,
    SUM(CASE WHEN il.estado = 'RESERVADO' THEN il.cantidad ELSE 0 END) AS cantidad_reservada,
    SUM(il.cantidad) AS cantidad_total,
    COUNT(il.id_lote) AS num_lotes
FROM equipo e
LEFT JOIN inventario_lote il ON e.id_equipo = il.id_equipo
LEFT JOIN marca m ON e.id_marca = m.id_marca
WHERE 1=1
  [AND e.tipo = :tipo]
  [AND e.id_marca = :marca]
  [AND e.sku LIKE :sku]
GROUP BY e.id_equipo
HAVING cantidad_total > 0
ORDER BY e.sku
```

**Lógica de Filtros**:
```php
1. Construye SQL base con GROUP BY
2. Si filtro 'tipo': agrega AND e.tipo = :tipo
3. Si filtro 'marca': agrega AND e.id_marca = :marca
4. Si filtro 'sku': agrega AND e.sku LIKE :sku
5. Vincula parámetros
6. Ejecuta y retorna resultSet()
```

**Ejemplo de Resultado**:
```php
[
    [
        'id_equipo' => 1,
        'sku' => 'CAM-001',
        'descripcion' => 'Cámara IP 4MP Hikvision',
        'tipo' => 'CAMARA',
        'marca_nombre' => 'Hikvision',
        'cantidad_disponible' => 50,
        'cantidad_reservada' => 10,
        'cantidad_total' => 60,
        'num_lotes' => 3
    ]
]
```

#### Método `lotesPorEquipo($id_equipo)`

**Firma**: `public function lotesPorEquipo($id_equipo)`

**Propósito**: Obtener todos los lotes de un equipo ordenados por FIFO

**SQL**:
```sql
SELECT 
    il.*,
    u.nombre AS ubicacion_nombre
FROM inventario_lote il
LEFT JOIN ubicacion u ON il.id_ubicacion = u.id_ubicacion
WHERE il.id_equipo = :id_equipo
ORDER BY il.fecha_ingreso ASC  -- FIFO: primero los más antiguos
```

**Uso**: Para mostrar desglose de lotes en vista de detalle

#### Método `stockBajo($limite = 10)`

**Firma**: `public function stockBajo($limite = 10)`

**Parámetros**:
- `$limite` (int): Cantidad mínima considerada como "bajo"

**Retorna**: `array` - Equipos con stock bajo

**SQL**:
```sql
SELECT 
    e.id_equipo,
    e.sku,
    e.descripcion,
    SUM(CASE WHEN il.estado = 'DISPONIBLE' THEN il.cantidad ELSE 0 END) AS stock_disponible
FROM equipo e
LEFT JOIN inventario_lote il ON e.id_equipo = il.id_equipo
GROUP BY e.id_equipo
HAVING stock_disponible > 0 AND stock_disponible < :limite
ORDER BY stock_disponible ASC
```

#### Método `procesarVenta($detalle, $venta_id, $usuario_id)`

**Firma**: `public function procesarVenta($detalle, $venta_id, $usuario_id)`

**Parámetros**:
- `$detalle` (array): Array de productos vendidos
- `$venta_id` (int): ID de la venta
- `$usuario_id` (int): ID del usuario que confirma

**Retorna**: `array` - ['exito' => bool, 'error' => string]

**Lógica Completa (CRÍTICA)**:
```php
1. Inicia transacción: $this->db->beginTransaction()

2. Para cada producto en $detalle:
   a. Extrae datos:
      - id_equipo
      - cantidad_solicitada
   
   b. Obtiene lotes disponibles ordenados por FIFO:
      SELECT * FROM inventario_lote
      WHERE id_equipo = :id AND estado = 'DISPONIBLE'
      ORDER BY fecha_ingreso ASC
   
   c. Inicializa cantidad_restante = cantidad_solicitada
   
   d. Itera sobre lotes disponibles:
      i. Si cantidad_restante <= 0: break
      
      ii. cantidad_a_tomar = min(lote.cantidad, cantidad_restante)
      
      iii. Reduce cantidad del lote:
           nueva_cantidad = lote.cantidad - cantidad_a_tomar
           UPDATE inventario_lote 
           SET cantidad = nueva_cantidad
           WHERE id_lote = lote.id
      
      iv. Si nueva_cantidad == 0:
           UPDATE inventario_lote 
           SET estado = 'AGOTADO'
           WHERE id_lote = lote.id
      
      v. Registra movimiento de SALIDA:
          INSERT INTO movimiento (
              id_lote, tipo, cantidad, 
              motivo, id_usuario, fecha
          ) VALUES (
              lote.id, 'SALIDA', cantidad_a_tomar,
              'Venta #' + venta_id, usuario_id, NOW()
          )
      
      vi. cantidad_restante -= cantidad_a_tomar
   
   e. Si cantidad_restante > 0:
      - No hay suficiente stock
      - Rollback: $this->db->rollBack()
      - Retorna: ['exito' => false, 'error' => 'Stock insuficiente']

3. Actualiza estado de la venta:
   UPDATE compra SET estado = 'CONFIRMADA' WHERE id_compra = venta_id

4. Commit: $this->db->commit()

5. Retorna: ['exito' => true]
```

**Ejemplo de Ejecución**:
```
Venta de 150 unidades de CAM-001

Lotes disponibles:
- Lote 1: 100 unidades (fecha: 2024-01-01)
- Lote 2: 80 unidades (fecha: 2024-01-15)

Proceso:
1. Toma 100 de Lote 1 → Lote 1 queda AGOTADO
2. Toma 50 de Lote 2 → Lote 2 queda con 30
3. Registra 2 movimientos de SALIDA
4. Actualiza venta a CONFIRMADA
```

#### Método `historialMovimientos($id)`

**Firma**: `public function historialMovimientos($id)`

**Propósito**: Obtener historial de movimientos de un lote

**SQL**:
```sql
SELECT 
    m.*,
    u.nombre AS usuario_nombre
FROM movimiento m
LEFT JOIN usuario u ON m.id_usuario = u.id_usuario
WHERE m.id_lote = :id
ORDER BY m.fecha DESC
```

---

### 7.2 Modelo Equipo (`app/models/Equipo.php`)

**Propósito**: Gestionar catálogo maestro de equipos.

**Tabla**: `equipo`

#### Método `all()`

**SQL**:
```sql
SELECT 
    e.id_equipo,
    e.sku,
    e.tipo,
    e.descripcion,
    e.id_marca,
    e.id_categoria,
    m.nombre AS marca_nombre,
    c.nombre AS categoria_nombre
FROM equipo e
LEFT JOIN marca m ON e.id_marca = m.id_marca
LEFT JOIN categoria c ON e.id_categoria = c.id_categoria
ORDER BY e.sku
```

#### Método `buscar($termino, $filtros)`

**Firma**: `public function buscar($termino = '', $filtros = [])`

**Parámetros**:
```php
$termino = 'CAM';  // Búsqueda en SKU o descripción
$filtros = [
    'tipo' => 'CAMARA',
    'id_marca' => 1,
    'id_categoria' => 2
]
```

**SQL Dinámico**:
```sql
SELECT 
    e.id_equipo,
    e.sku,
    e.tipo,
    e.descripcion,
    m.nombre AS marca_nombre,
    c.nombre AS categoria_nombre
FROM equipo e
LEFT JOIN marca m ON e.id_marca = m.id_marca
LEFT JOIN categoria c ON e.id_categoria = c.id_categoria
WHERE 1=1
  [AND (e.sku LIKE :termino OR e.descripcion LIKE :termino)]
  [AND e.tipo = :tipo]
  [AND e.id_marca = :id_marca]
  [AND e.id_categoria = :id_categoria]
ORDER BY e.sku
LIMIT 50
```

**Lógica**:
```php
1. Construye SQL base
2. Si !empty($termino):
   - Agrega: AND (e.sku LIKE :termino OR e.descripcion LIKE :termino)
   - Bind: $this->db->bind(':termino', "%$termino%")
3. Si !empty($filtros['tipo']):
   - Agrega: AND e.tipo = :tipo
   - Bind: $this->db->bind(':tipo', $filtros['tipo'])
4. Repite para marca y categoría
5. Ejecuta y retorna resultSet()
```

#### Método `stockTotal($id)`

**Firma**: `public function stockTotal($id)`

**Propósito**: Calcular stock total disponible de un equipo

**SQL**:
```sql
SELECT SUM(cantidad) as total 
FROM inventario_lote 
WHERE id_equipo = :id AND estado = 'DISPONIBLE'
```

**Retorna**: `int` - Total de unidades disponibles

---

### 7.3 Modelo Usuario (`app/models/Usuario.php`)

**Propósito**: Gestionar usuarios y autenticación.

**Tabla**: `usuario`

#### Método `login($email, $password)`

**Firma**: `public function login($email, $password)`

**Parámetros**:
- `$email` (string): Email del usuario
- `$password` (string): Contraseña en texto plano

**Retorna**: `array|false` - Datos del usuario o false

**Lógica**:
```php
1. Busca usuario por email:
   SELECT * FROM usuario WHERE email = :email

2. Si no existe: return false

3. Verifica contraseña:
   if (password_verify($password, $usuario['password']))
   
4. Si coincide:
   - Retorna datos del usuario (sin password)
   
5. Si no coincide:
   - return false
```

**Seguridad**:
- Contraseñas hasheadas con `password_hash()`
- Verificación con `password_verify()`
- Nunca se retorna el hash de la contraseña

---

## 8. GENERACIÓN DE REPORTES PDF

### 8.1 Controlador Reportes (`app/controllers/Reportes.php`)

**Librería**: FPDF (vendor/fpdf/fpdf.php)

#### Método `diario()`

**Firma**: `public function diario()`

**Propósito**: Generar reporte PDF de inventario diario

**Lógica Completa**:
```php
1. Obtiene datos:
   a. Inventario agrupado: $this->loteModel->inventarioAgrupado([])
   b. Fecha actual
   c. Información del usuario

2. Crea instancia FPDF:
   $pdf = new FPDF('P', 'mm', 'Letter');
   - P: Portrait (vertical)
   - mm: milímetros
   - Letter: tamaño carta

3. Configura PDF:
   $pdf->AddPage();
   $pdf->SetFont('Arial', 'B', 16);

4. ENCABEZADO:
   a. Título centrado:
      $pdf->Cell(0, 10, 'REPORTE DIARIO DE INVENTARIO', 0, 1, 'C');
   b. Fecha:
      $pdf->SetFont('Arial', '', 10);
      $pdf->Cell(0, 6, 'Fecha: ' . date('d/m/Y H:i'), 0, 1);
   c. Usuario:
      $pdf->Cell(0, 6, 'Generado por: ' . $_SESSION['usuario_nombre'], 0, 1);
   d. Línea separadora:
      $pdf->Ln(5);

5. TABLA DE PRODUCTOS:
   a. Encabezados de columna:
      $pdf->SetFillColor(52, 152, 219);  // Azul
      $pdf->SetTextColor(255, 255, 255);  // Blanco
      $pdf->SetFont('Arial', 'B', 9);
      
      Columnas:
      - SKU (30mm)
      - Descripción (70mm)
      - Marca (30mm)
      - Disponible (20mm)
      - Reservado (20mm)
      - Total (20mm)
   
   b. Datos de productos:
      $pdf->SetFillColor(240, 240, 240);  // Gris claro
      $pdf->SetTextColor(0, 0, 0);  // Negro
      $pdf->SetFont('Arial', '', 8);
      
      foreach ($productos as $i => $p):
        // Alterna color de fondo
        $fill = ($i % 2 == 0);
        
        $pdf->Cell(30, 6, $p['sku'], 1, 0, 'L', $fill);
        $pdf->Cell(70, 6, $p['descripcion'], 1, 0, 'L', $fill);
        $pdf->Cell(30, 6, $p['marca_nombre'], 1, 0, 'L', $fill);
        $pdf->Cell(20, 6, $p['cantidad_disponible'], 1, 0, 'C', $fill);
        $pdf->Cell(20, 6, $p['cantidad_reservada'], 1, 0, 'C', $fill);
        $pdf->Cell(20, 6, $p['cantidad_total'], 1, 1, 'C', $fill);
      endforeach;

6. RESUMEN:
   a. Calcula totales:
      $total_disponible = array_sum(array_column($productos, 'cantidad_disponible'));
      $total_reservado = array_sum(array_column($productos, 'cantidad_reservada'));
      $total_general = array_sum(array_column($productos, 'cantidad_total'));
   
   b. Muestra totales:
      $pdf->SetFont('Arial', 'B', 9);
      $pdf->Cell(130, 6, 'TOTALES:', 1, 0, 'R');
      $pdf->Cell(20, 6, $total_disponible, 1, 0, 'C');
      $pdf->Cell(20, 6, $total_reservado, 1, 0, 'C');
      $pdf->Cell(20, 6, $total_general, 1, 1, 'C');

7. PIE DE PÁGINA:
   $pdf->Ln(10);
   $pdf->SetFont('Arial', 'I', 8);
   $pdf->Cell(0, 6, 'Sistema de Inventario - Reporte generado automáticamente', 0, 1, 'C');

8. Salida del PDF:
   $pdf->Output('I', 'reporte_inventario_' . date('Ymd') . '.pdf');
   // 'I': inline (mostrar en navegador)
   // 'D': download (forzar descarga)
```

**Parámetros de Cell()**:
```php
$pdf->Cell(ancho, alto, texto, borde, salto_linea, alineacion, relleno);

- ancho: en mm (0 = hasta el margen)
- alto: en mm
- texto: contenido
- borde: 0=sin borde, 1=con borde
- salto_linea: 0=continuar, 1=nueva línea
- alineacion: 'L'=izquierda, 'C'=centro, 'R'=derecha
- relleno: true/false (usar color de relleno)
```

---

## 9. PATRONES Y MEJORES PRÁCTICAS

### 9.1 Patrón de Controlador

**Estructura Estándar**:
```php
class MiControlador extends Controller {
    private $modelo;
    
    public function __construct() {
        $this->modelo = $this->model('MiModelo');
    }
    
    public function index() {
        // 1. Obtener datos
        $datos = $this->modelo->all();
        
        // 2. Preparar array para vista
        $data = [
            'titulo' => 'Mi Vista',
            'datos' => $datos
        ];
        
        // 3. Cargar vista
        $this->view('mi/vista', $data);
    }
    
    public function crear() {
        // 1. Proteger ruta
        requerirAuth();
        requerirPermiso(puedeCrear(), 'Sin permisos', 'ruta');
        
        // 2. Procesar POST
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Validar y crear
            $data = [...];
            if ($this->modelo->create($data)) {
                $_SESSION['mensaje'] = 'Éxito';
                $_SESSION['tipo_mensaje'] = 'success';
                redirect('ruta');
            }
        }
        
        // 3. Mostrar formulario (GET)
        $this->view('mi/crear', $data);
    }
}
```

### 9.2 Patrón de Modelo

```php
class MiModelo {
    private $db;
    
    public function __construct() {
        $this->db = new Base('mi_tabla');
    }
    
    public function all() {
        $sql = "SELECT * FROM mi_tabla ORDER BY id DESC";
        $this->db->query($sql);
        return $this->db->resultSet();
    }
    
    public function find($id) {
        $sql = "SELECT * FROM mi_tabla WHERE id = :id";
        $this->db->query($sql);
        $this->db->bind(':id', $id);
        return $this->db->single();
    }
    
    public function create($data) {
        $sql = "INSERT INTO mi_tabla (campo1, campo2) 
                VALUES (:campo1, :campo2)";
        $this->db->query($sql);
        $this->db->bind(':campo1', $data['campo1']);
        $this->db->bind(':campo2', $data['campo2']);
        
        if ($this->db->execute()) {
            return $this->db->lastInsertId();
        }
        return false;
    }
}
```

### 9.3 Manejo de Transacciones

```php
public function operacionCompleja($data) {
    try {
        $this->db->beginTransaction();
        
        // Operación 1
        $id1 = $this->db->create($data1);
        
        // Operación 2
        $id2 = $this->db->create($data2);
        
        // Si todo OK
        $this->db->commit();
        return true;
        
    } catch (Exception $e) {
        $this->db->rollBack();
        return false;
    }
}
```

---

## 10. EJEMPLOS DE USO COMPLETOS

### 10.1 Crear Nuevo Equipo (Flujo Completo)

**1. Usuario accede a URL**: `http://localhost/equipos/crear`

**2. Routes procesa**:
```php
- Controlador: Equipos
- Método: crear
- Parámetros: []
```

**3. Equipos->crear() ejecuta**:
```php
// Verifica permisos
requerirAuth();
requerirPermiso(puedeGestionarMaestros(), ...);

// Si es GET, muestra formulario
$data = [
    'marcas' => $this->marcaModel->all(),
    'categorias' => $this->categoriaModel->all()
];
$this->view('equipos/crear', $data);
```

**4. Usuario llena formulario y envía**:
```
SKU: CAM-005
Tipo: CAMARA
Descripción: Cámara Domo 2MP
Marca: Hikvision
Categoría: Cámaras IP
```

**5. POST a equipos/crear**:
```php
// Captura datos
$data = [
    'sku' => 'CAM-005',
    'tipo' => 'CAMARA',
    'descripcion' => 'Cámara Domo 2MP',
    'id_marca' => 1,
    'id_categoria' => 2
];

// Valida SKU único
if ($this->equipoModel->buscarPorSKU('CAM-005')) {
    // Ya existe
    $_SESSION['mensaje'] = 'El SKU ya existe';
    // Recarga formulario
}

// Crea equipo
$this->equipoModel->create($data);
```

**6. Equipo->create() ejecuta**:
```sql
INSERT INTO equipo (sku, tipo, descripcion, id_marca, id_categoria) 
VALUES ('CAM-005', 'CAMARA', 'Cámara Domo 2MP', 1, 2)
```

**7. Redirige con mensaje**:
```php
$_SESSION['mensaje'] = 'Equipo creado exitosamente';
redirect('equipos');
```

### 10.2 Confirmar Venta (Flujo Crítico)

**1. Usuario confirma venta ID 10**:
```
URL: ventas/confirmar/10
POST
```

**2. Ventas->confirmar(10)**:
```php
// Verifica permiso (solo ADMIN)
requerirPermiso(puedeConfirmar(), ...);

// Obtiene detalle
$detalle = $this->ventaModel->obtenerDetalle(10);
// Resultado:
[
    ['id_equipo' => 1, 'cantidad' => 100],
    ['id_equipo' => 2, 'cantidad' => 50]
]

// Procesa venta
$resultado = $this->loteModel->procesarVenta($detalle, 10, $usuario_id);
```

**3. InventarioLote->procesarVenta()**:
```php
BEGIN TRANSACTION;

// Producto 1: 100 unidades de equipo 1
Lotes disponibles:
- Lote A: 80 unidades
- Lote B: 50 unidades

Toma 80 de Lote A → Lote A = AGOTADO
Registra movimiento SALIDA: 80 unidades

Toma 20 de Lote B → Lote B = 30 unidades
Registra movimiento SALIDA: 20 unidades

// Producto 2: 50 unidades de equipo 2
Lotes disponibles:
- Lote C: 100 unidades

Toma 50 de Lote C → Lote C = 50 unidades
Registra movimiento SALIDA: 50 unidades

// Actualiza venta
UPDATE compra SET estado = 'CONFIRMADA' WHERE id_compra = 10;

COMMIT;
```

**4. Retorna éxito**:
```php
$_SESSION['mensaje'] = 'Venta confirmada. Inventario reducido exitosamente';
redirect('ventas');
```

---

## RESUMEN DE FUNCIONES PRINCIPALES

### Autenticación
- `estaAutenticado()`: Verifica sesión activa
- `requerirAuth()`: Protege rutas (redirige a login)
- `requerirPermiso()`: Verifica permisos específicos

### Permisos
- `esAdmin()`, `esAlmacen()`, `esLector()`: Verificar rol
- `puedeCrear()`: Admin o Almacén
- `puedeConfirmar()`, `puedeEditar()`, `puedeEliminar()`: Solo Admin
- `puedeGestionarMaestros()`: Solo Admin

### Base de Datos
- `query()`, `bind()`, `execute()`: Consultas preparadas
- `resultSet()`, `single()`: Obtener resultados
- `create()`, `update()`, `delete()`: Operaciones CRUD
- `where()`: Condiciones dinámicas
- `beginTransaction()`, `commit()`, `rollBack()`: Transacciones

### Controlador Base
- `view()`: Cargar vista con datos
- `model()`: Instanciar modelo

### Inventario
- `inventarioAgrupado()`: Resumen por producto
- `lotesPorEquipo()`: Lotes de un equipo
- `procesarVenta()`: Reducir stock (FIFO)
- `stockBajo()`: Productos con stock bajo

---

**FIN DE LA GUÍA DEL DESARROLLADOR**

Para más información, consulta el código fuente en:
- `app/controllers/` - Controladores
- `app/models/` - Modelos
- `app/core/` - Núcleo del framework
- `app/helpers/` - Funciones auxiliares
