<?php
header('Content-Type: application/json');
header('Content-Disposition: attachment; filename=productos.json');

// Limpiar datos para JSON
$productos = [];
foreach($data as $producto) {
    $productos[] = [
        'id' => $producto['id'],
        'codigo' => $producto['producto_codigo'],
        'nombre' => $producto['producto_nombre'],
        'descripcion' => $producto['producto_descripcion'],
        'precio_compra' => $producto['producto_precio_compra'],
        'precio_venta' => $producto['producto_precio_venta'],
        'stock' => $producto['producto_stock'],
        'stock_minimo' => $producto['producto_stock_minimo'],
        'categoria' => $producto['categoria_nombre'] ?? null,
        'proveedor' => $producto['proveedor_nombre'] ?? null
    ];
}

echo json_encode($productos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
