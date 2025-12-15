<?php
require_once VENDORS . '/fpdf/fpdf.php';

$fecha = $data['fecha'] ?? date('Y-m-d H:i:s');
$reporte = $data['reporte'] ?? [];
$entradas = $data['entradas'] ?? 0;
$salidas = $data['salidas'] ?? 0;
$entradasDetalle = $data['entradas_detalle'] ?? [];
$salidasDetalle = $data['salidas_detalle'] ?? [];
$productos = $data['productos'] ?? [];
$totalUnidades = $data['total_unidades'] ?? 0;
$usuario = $data['usuario'] ?? 'Sistema';

class PDFReporteInventario extends FPDF {
    function Header() {
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(0, 8, utf8_decode('Reporte Diario de Inventario'), 0, 1, 'C');
        $this->Ln(2);
    }

    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, utf8_decode('Página ') . $this->PageNo(), 0, 0, 'C');
    }
}

$pdf = new PDFReporteInventario('P', 'mm', 'A4');
$pdf->AddPage();
$pdf->SetFont('Arial', '', 10);

// Fecha y autor
$pdf->Cell(0, 6, utf8_decode('Fecha: ') . date('d/m/Y H:i:s'), 0, 1, 'C');
$pdf->Cell(0, 6, utf8_decode('Generado por: ') . utf8_decode($usuario), 0, 1, 'C');
$pdf->Ln(8);

// Inventario actual
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(0, 7, utf8_decode('INVENTARIO ACTUAL'), 0, 1, 'L');
$pdf->Ln(1);

$pdf->SetFont('Arial', 'B', 9);
$pdf->SetFillColor(210, 220, 245);
$pdf->Cell(35, 7, 'SKU', 1, 0, 'L', true);
$pdf->Cell(90, 7, utf8_decode('Descripción'), 1, 0, 'L', true);
$pdf->Cell(20, 7, utf8_decode('Disponible'), 1, 0, 'C', true);
$pdf->Cell(20, 7, 'Reservado', 1, 0, 'C', true);
$pdf->Cell(20, 7, 'Total', 1, 1, 'C', true);

$pdf->SetFont('Arial', '', 9);
if (!empty($productos)) {
    foreach ($productos as $p) {
        $pdf->Cell(35, 6, $p['sku'] ?? '-', 1);
        $pdf->Cell(90, 6, utf8_decode(substr($p['descripcion'] ?? '', 0, 70)), 1);
        $pdf->Cell(20, 6, $p['cantidad_disponible'] ?? 0, 1, 0, 'C');
        $pdf->Cell(20, 6, $p['cantidad_reservada'] ?? 0, 1, 0, 'C');
        $pdf->Cell(20, 6, $p['cantidad_total'] ?? 0, 1, 1, 'C');
    }
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->Cell(145, 7, utf8_decode('TOTAL UNIDADES:'), 1, 0, 'R');
    $pdf->Cell(20, 7, $totalUnidades, 1, 1, 'C');
} else {
    $pdf->Cell(185, 6, utf8_decode('No hay inventario registrado.'), 1, 1, 'L');
}

$pdf->Ln(10);

// Movimientos del día
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(0, 7, utf8_decode('MOVIMIENTOS DEL DÍA'), 0, 1, 'L');
$pdf->Ln(2);

$renderMovs = function($titulo, $movimientos, $colorRGB) use ($pdf) {
    [$r, $g, $b] = $colorRGB;
    $pdf->SetTextColor($r, $g, $b);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(0, 6, utf8_decode($titulo), 0, 1, 'L');
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('Arial', 'I', 9);

    if (empty($movimientos)) {
        $pdf->Cell(0, 6, utf8_decode('No hay registros hoy'), 0, 1, 'L');
        $pdf->Ln(4);
        return;
    }

    $pdf->SetFont('Arial', 'B', 8);
    $pdf->SetFillColor(235, 235, 235);
    $pdf->Cell(22, 6, 'Hora', 1, 0, 'C', true);
    $pdf->Cell(25, 6, 'SKU', 1, 0, 'C', true);
    $pdf->Cell(70, 6, utf8_decode('Descripción'), 1, 0, 'C', true);
    $pdf->Cell(18, 6, 'Cant.', 1, 0, 'C', true);
    $pdf->Cell(50, 6, 'Usuario', 1, 1, 'C', true);

    $pdf->SetFont('Arial', '', 8);
    foreach ($movimientos as $mov) {
        $hora = date('H:i', strtotime($mov['fecha_hora']));
        $cantidadMostrar = ($mov['tipo'] === 'entrada')
            ? ($mov['lote_cantidad'] ?? $mov['cantidad'] ?? 0)
            : ($mov['cantidad'] ?? 0);
        $pdf->Cell(22, 6, $hora, 1, 0, 'C');
        $pdf->Cell(25, 6, $mov['sku'] ?? '', 1);
        $pdf->Cell(70, 6, utf8_decode(substr($mov['equipo_descripcion'] ?? '', 0, 40)), 1);
        $pdf->Cell(18, 6, $cantidadMostrar, 1, 0, 'C');
        $pdf->Cell(50, 6, utf8_decode($mov['usuario_nombre'] ?? 'Sistema'), 1, 1);
    }
    $pdf->Ln(4);
};

$renderMovs('ENTRADAS (Nuevos Lotes)', $entradasDetalle, [0, 128, 0]);
$renderMovs('SALIDAS (Ventas)', $salidasDetalle, [200, 0, 0]);

$pdf->Output('reporte_inventario_' . date('Ymd_His') . '.pdf', 'D');
exit;
