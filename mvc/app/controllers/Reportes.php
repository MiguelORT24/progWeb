<?php
/**
 * Controlador de Reportes
 * Genera reportes en PDF para el sistema
 */

// Importar librería FPDF
require_once VENDORS . '/fpdf/fpdf.php';

class Reportes extends Controller {
    private $loteModel;
    private $movimientoModel;

    public function __construct() {
        // Verificar sesión
        if (!isset($_SESSION['usuario_id'])) {
            redirect('login');
        }
        
        $this->loteModel = $this->model('InventarioLote');
        $this->movimientoModel = $this->model('Movimiento'); // Asegurar que este modelo existe o usar el correcto
    }

    /**
     * Reporte Diario de Inventario y Movimientos
     * Muestra inventario agrupado + movimientos del día separados por tipo
     */
    public function diario() {
        // Obtener inventario agrupado
        $inventario = $this->loteModel->inventarioAgrupado([]);
        
        // Obtener movimientos del día
        $movimientos = $this->movimientoModel->hoy();
        
        // Separar movimientos por tipo
        $entradas = array_filter($movimientos, function($m) {
            return $m['tipo'] == 'ENTRADA';
        });
        
        $salidas = array_filter($movimientos, function($m) {
            return $m['tipo'] == 'SALIDA';
        });
        
        // Crear PDF
        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 16);
        
        // ==== TÍTULO ====
        $pdf->Cell(0, 10, utf8_decode('Reporte Diario de Inventario'), 0, 1, 'C');
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(0, 7, utf8_decode('Fecha: ' . date('d/m/Y H:i:s')), 0, 1, 'C');
        $pdf->Cell(0, 7, utf8_decode('Generado por: ' . $_SESSION['usuario_nombre']), 0, 1, 'C');
        $pdf->Ln(5);
        
        // ==== INVENTARIO ACTUAL ====
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(0, 10, utf8_decode('INVENTARIO ACTUAL'), 0, 1, 'L');
        $pdf->Ln(2);
        
        // Encabezados tabla inventario
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->SetFillColor(200, 220, 255);
        $pdf->Cell(30, 8, 'SKU', 1, 0, 'C', true);
        $pdf->Cell(80, 8, utf8_decode('Descripción'), 1, 0, 'C', true);
        $pdf->Cell(25, 8, 'Disponible', 1, 0, 'C', true);
        $pdf->Cell(25, 8, 'Reservado', 1, 0, 'C', true);
        $pdf->Cell(30, 8, 'Total', 1, 1, 'C', true);
        
        // Datos inventario
        $pdf->SetFont('Arial', '', 8);
        $total_general = 0;
        
        foreach ($inventario as $prod) {
            $pdf->Cell(30, 7, utf8_decode($prod['sku']), 1);
            $pdf->Cell(80, 7, utf8_decode(substr($prod['descripcion'], 0, 45)), 1);
            $pdf->Cell(25, 7, $prod['cantidad_disponible'], 1, 0, 'C');
            $pdf->Cell(25, 7, $prod['cantidad_reservada'], 1, 0, 'C');
            $pdf->Cell(30, 7, $prod['cantidad_total'], 1, 1, 'C');
            $total_general += $prod['cantidad_total'];
        }
        
        // Total inventario
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(160, 7, 'TOTAL UNIDADES:', 0, 0, 'R');
        $pdf->Cell(30, 7, $total_general, 1, 1, 'C');
        $pdf->Ln(8);
        
        // ==== MOVIMIENTOS DEL DÍA ====
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(0, 10, utf8_decode('MOVIMIENTOS DEL DÍA'), 0, 1, 'L');
        $pdf->Ln(2);
        
        // ==== SECCIÓN ENTRADAS ====
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetTextColor(0, 128, 0); // Verde
        $pdf->Cell(0, 8, utf8_decode('ENTRADAS (Nuevos Lotes)'), 0, 1, 'L');
        $pdf->SetTextColor(0, 0, 0); // Negro
        $pdf->Ln(2);
        
        if (empty($entradas)) {
            $pdf->SetFont('Arial', 'I', 9);
            $pdf->Cell(0, 7, utf8_decode('No hay entradas registradas hoy'), 0, 1);
        } else {
            // Encabezados entradas
            $pdf->SetFont('Arial', 'B', 8);
            $pdf->SetFillColor(220, 255, 220);
            $pdf->Cell(25, 7, 'Hora', 1, 0, 'C', true);
            $pdf->Cell(30, 7, 'SKU', 1, 0, 'C', true);
            $pdf->Cell(70, 7, utf8_decode('Producto'), 1, 0, 'C', true);
            $pdf->Cell(20, 7, 'Cant.', 1, 0, 'C', true);
            $pdf->Cell(45, 7, 'Motivo', 1, 1, 'C', true);
            
            // Datos entradas
            $pdf->SetFont('Arial', '', 7);
            $total_entradas = 0;
            
            foreach ($entradas as $mov) {
                $hora = date('H:i', strtotime($mov['created_at']));
                $pdf->Cell(25, 6, $hora, 1);
                $pdf->Cell(30, 6, utf8_decode($mov['sku']), 1);
                $pdf->Cell(70, 6, utf8_decode(substr($mov['descripcion'], 0, 35)), 1);
                $pdf->Cell(20, 6, $mov['cantidad'], 1, 0, 'C');
                $pdf->Cell(45, 6, utf8_decode(substr($mov['motivo'] ?? '-', 0, 22)), 1, 1);
                $total_entradas += $mov['cantidad'];
            }
            
            // Total entradas
            $pdf->SetFont('Arial', 'B', 8);
            $pdf->Cell(125, 6, 'Total Entradas:', 0, 0, 'R');
            $pdf->Cell(20, 6, $total_entradas, 1, 1, 'C');
        }
        
        $pdf->Ln(6);
        
        // ==== SECCIÓN SALIDAS ====
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetTextColor(255, 0, 0); // Rojo
        $pdf->Cell(0, 8, utf8_decode('SALIDAS (Ventas)'), 0, 1, 'L');
        $pdf->SetTextColor(0, 0, 0); // Negro
        $pdf->Ln(2);
        
        if (empty($salidas)) {
            $pdf->SetFont('Arial', 'I', 9);
            $pdf->Cell(0, 7, utf8_decode('No hay salidas registradas hoy'), 0, 1);
        } else {
            // Encabezados salidas
            $pdf->SetFont('Arial', 'B', 8);
            $pdf->SetFillColor(255, 220, 220);
            $pdf->Cell(25, 7, 'Hora', 1, 0, 'C', true);
            $pdf->Cell(30, 7, 'SKU', 1, 0, 'C', true);
            $pdf->Cell(70, 7, utf8_decode('Producto'), 1, 0, 'C', true);
            $pdf->Cell(20, 7, 'Cant.', 1, 0, 'C', true);
            $pdf->Cell(45, 7, 'Motivo', 1, 1, 'C', true);
            
            // Datos salidas
            $pdf->SetFont('Arial', '', 7);
            $total_salidas = 0;
            
            foreach ($salidas as $mov) {
                $hora = date('H:i', strtotime($mov['created_at']));
                $pdf->Cell(25, 6, $hora, 1);
                $pdf->Cell(30, 6, utf8_decode($mov['sku']), 1);
                $pdf->Cell(70, 6, utf8_decode(substr($mov['descripcion'], 0, 35)), 1);
                $pdf->Cell(20, 6, $mov['cantidad'], 1, 0, 'C');
                $pdf->Cell(45, 6, utf8_decode(substr($mov['motivo'] ?? '-', 0, 22)), 1, 1);
                $total_salidas += $mov['cantidad'];
            }
            
            // Total salidas
            $pdf->SetFont('Arial', 'B', 8);
            $pdf->Cell(125, 6, 'Total Salidas:', 0, 0, 'R');
            $pdf->Cell(20, 6, $total_salidas, 1, 1, 'C');
        }
        
        // Salida del PDF
        $pdf->Output('D', 'Inventario_' . date('Y-m-d') . '.pdf');
    }

    /**
     * Reporte de Movimientos del Día Actual
     * Muestra todos los movimientos registrados hoy
     */
    public function movimientosHoy() {
        // Obtener movimientos del día actual
        $movimientos = $this->movimientoModel->hoy();
        
        // Crear PDF
        $pdf = new FPDF();
        $pdf->AliasNbPages();
        $pdf->AddPage();
        
        // Header personalizado
        $pdf->Image(VENDORS . '/img/logo.jpg', 10, 8, 20);
        $pdf->SetFont('Arial', 'B', 15);
        $pdf->Cell(80);
        $pdf->Cell(30, 10, 'Web-Olitos', 0, 0, 'C');
        $pdf->Ln(20);
        
        // Título
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFillColor(128, 128, 128);
        $pdf->SetXY(10, 30);
        $pdf->Cell(190, 8, 'Movimientos del Dia - ' . date('d/m/Y'), 'B', 1, 'C', 1);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Ln(3);
        $pdf->SetFont('Arial', '', 10);
        
        // Encabezados de tabla
        $pdf->Cell(10, 5, 'ID', 'LTBR', 0, 'C');
        $pdf->Cell(30, 5, 'Fecha/Hora', 'TBR', 0, 'C');
        $pdf->Cell(50, 5, 'Producto', 'TBR', 0, 'C');
        $pdf->Cell(20, 5, 'Tipo', 'TBR', 0, 'C');
        $pdf->Cell(15, 5, 'Cant.', 'TBR', 0, 'C');
        $pdf->Cell(20, 5, 'P. Unit.', 'TBR', 0, 'C');
        $pdf->Cell(20, 5, 'Total', 'TBR', 0, 'C');
        $pdf->Cell(25, 5, 'Usuario', 'TBR', 1, 'C');
        
        // Variables para totales
        $total_entradas = 0;
        $total_salidas = 0;
        $valor_entradas = 0;
        $valor_salidas = 0;
        
        // Datos de movimientos
        foreach ($movimientos as $mov) {
            $pdf->Cell(10, 5, $mov['id'], 0, 0, 'C');
            $pdf->Cell(30, 5, date('d/m H:i', strtotime($mov['created_at'])), 0, 0, 'C');
            
            // Truncar nombre del producto si es muy largo
            $producto = strlen($mov['producto_nombre']) > 25 
                ? substr($mov['producto_nombre'], 0, 22) . '...' 
                : $mov['producto_nombre'];
            $pdf->Cell(50, 5, utf8_decode($producto), 0, 0, 'L');
            
            // Tipo de movimiento
            $tipo = $mov['movimiento_tipo'] == 'entrada' ? 'ENT' : 'SAL';
            $pdf->Cell(20, 5, $tipo, 0, 0, 'C');
            
            $pdf->Cell(15, 5, $mov['movimiento_cantidad'], 0, 0, 'C');
            $pdf->Cell(20, 5, '$' . number_format($mov['movimiento_precio_unitario'], 2), 0, 0, 'R');
            $pdf->Cell(20, 5, '$' . number_format($mov['movimiento_total'], 2), 0, 0, 'R');
            
            // Usuario (truncar si es muy largo)
            $usuario = $mov['usuario_nombre'] ?? 'Sistema';
            $usuario = strlen($usuario) > 15 ? substr($usuario, 0, 12) . '...' : $usuario;
            $pdf->Cell(25, 5, utf8_decode($usuario), 0, 1, 'C');
            
            // Acumular totales
            if ($mov['movimiento_tipo'] == 'entrada') {
                $total_entradas += $mov['movimiento_cantidad'];
                $valor_entradas += $mov['movimiento_total'];
            } else {
                $total_salidas += $mov['movimiento_cantidad'];
                $valor_salidas += $mov['movimiento_total'];
            }
        }
        
        // Línea de separación
        $pdf->Ln(5);
        $pdf->SetFont('Arial', 'B', 10);
        
        // Totales
        $pdf->Cell(95, 5, 'TOTALES:', 0, 1, 'R');
        $pdf->SetFont('Arial', '', 9);
        $pdf->Cell(95, 5, 'Entradas: ' . $total_entradas . ' unidades', 0, 0, 'R');
        $pdf->Cell(95, 5, 'Valor: $' . number_format($valor_entradas, 2), 0, 1, 'R');
        $pdf->Cell(95, 5, 'Salidas: ' . $total_salidas . ' unidades', 0, 0, 'R');
        $pdf->Cell(95, 5, 'Valor: $' . number_format($valor_salidas, 2), 0, 1, 'R');
        
        // Footer
        $pdf->SetY(-15);
        $pdf->SetFont('Arial', 'I', 8);
        $pdf->Cell(0, 10, 'Pagina ' . $pdf->PageNo(), 0, 0, 'C');
        
        // Salida del PDF
        $pdf->Output('movimientos_' . date('Y-m-d') . '.pdf', 'D');
    }
}

