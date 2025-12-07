<?php
/**
 * Controlador de Reportes
 * Genera reportes en PDF para el sistema
 */

// Importar librería FPDF
require_once APPROOT . '/lib/fpdf.php';

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
     * Reporte Diario de Inventario
     * Muestra cantidad de unidades por componente y ubicación
     */
    public function diario() {
        // Obtener datos del inventario agrupados
        // Necesitamos una consulta personalizada o procesar los datos
        // Para este reporte: Componente (Equipo) | Ubicación | Cantidad | Estado
        
        $lotes = $this->loteModel->all();
        
        // Crear PDF
        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 16);
        
        // Título
        $pdf->Cell(0, 10, utf8_decode('Reporte Diario de Inventario'), 0, 1, 'C');
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(0, 10, utf8_decode('Fecha: ' . date('d/m/Y H:i:s')), 0, 1, 'C');
        $pdf->Cell(0, 10, utf8_decode('Generado por: ' . $_SESSION['usuario_nombre']), 0, 1, 'C');
        $pdf->Ln(10);
        
        // Encabezados de tabla
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetFillColor(200, 220, 255);
        $pdf->Cell(30, 10, 'SKU', 1, 0, 'C', true);
        $pdf->Cell(70, 10, utf8_decode('Descripción'), 1, 0, 'C', true);
        $pdf->Cell(40, 10, utf8_decode('Ubicación'), 1, 0, 'C', true);
        $pdf->Cell(25, 10, 'Estado', 1, 0, 'C', true);
        $pdf->Cell(25, 10, 'Cant.', 1, 1, 'C', true);
        
        // Datos
        $pdf->SetFont('Arial', '', 9);
        $total_unidades = 0;
        
        foreach ($lotes as $lote) {
            // Solo mostrar lotes con cantidad > 0 para el reporte diario operativo
            if ($lote['cantidad'] > 0) {
                $pdf->Cell(30, 8, utf8_decode($lote['sku']), 1);
                $pdf->Cell(70, 8, utf8_decode(substr($lote['equipo_descripcion'], 0, 40)), 1);
                $pdf->Cell(40, 8, utf8_decode($lote['ubicacion_nombre']), 1);
                $pdf->Cell(25, 8, utf8_decode($lote['estado']), 1);
                $pdf->Cell(25, 8, $lote['cantidad'], 1, 1, 'C');
                
                $total_unidades += $lote['cantidad'];
            }
        }
        
        // Totales
        $pdf->Ln(5);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(165, 10, 'Total Unidades en Inventario:', 0, 0, 'R');
        $pdf->Cell(25, 10, $total_unidades, 1, 1, 'C');
        
        // Salida del PDF (D = Descargar)
        $pdf->Output('D', 'Reporte_Inventario_' . date('Y-m-d') . '.pdf');
    }
}
