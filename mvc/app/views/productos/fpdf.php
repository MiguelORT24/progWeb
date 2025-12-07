<?php
require_once VENDORS . '/fpdf/fpdf.php';

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);

// Título
$pdf->Cell(0, 10, 'Lista de Productos', 0, 1, 'C');
$pdf->Ln(5);

// Encabezados de tabla
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(30, 7, 'Codigo', 1);
$pdf->Cell(60, 7, 'Nombre', 1);
$pdf->Cell(30, 7, 'Precio Venta', 1);
$pdf->Cell(20, 7, 'Stock', 1);
$pdf->Cell(30, 7, 'Estado', 1);
$pdf->Ln();

// Datos
$pdf->SetFont('Arial', '', 9);
foreach($data as $producto) {
    $pdf->Cell(30, 6, $producto['producto_codigo'], 1);
    $pdf->Cell(60, 6, substr($producto['producto_nombre'], 0, 30), 1);
    $pdf->Cell(30, 6, '$' . number_format($producto['producto_precio_venta'], 2), 1);
    $pdf->Cell(20, 6, $producto['producto_stock'], 1);
    $pdf->Cell(30, 6, $producto['nivel_stock'], 1);
    $pdf->Ln();
}

$pdf->Output('D', 'productos.pdf');
