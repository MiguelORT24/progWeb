<?php

/**
 * asegurar que no haya nada, absolutamente nada de html
 */
require_once VENDORS . '/fpdf/fpdf.php';

class PDF extends FPDF
{
    // Cabecera de página
    function Header()
    {
        $this->Image(VENDORS . '/img/logo.jpg', 10, 8, 20);
        $this->SetFont('Arial', 'B', 15);
        $this->Cell(80);
        $this->Cell(30, 10, 'Web-Olitos', 0, 0, 'C');
        $this->Ln(20);
    }

    // Pie de página
    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Pagina ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }
}

$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFillColor(128, 128, 128);


$pdf->SetXY(10, 30);
// $pdf->Cell(100,4,);
$pdf->Cell(190, 8, 'Listado de Usuarios', 'B', 1, 'C',1);
$pdf->SetTextColor(0, 0, 0);
$pdf->Ln(3); // salto de linea
$pdf->SetFont('Arial', '', 10);


$pdf->Cell(15, 5, 'ID', 'LTBR', 0, 'C');    
$pdf->Cell(70, 5, 'Nombre Usuario', 'TBR', 0, 'C');
$pdf->Cell(70, 5, 'Email Usuario', 'TBR', 0, 'C');
$pdf->Cell(15, 5, 'Nivel', 'TBR', 1, 'C');

// tabla
foreach ($data as $usuario):
    extract($usuario);

    $pdf->Cell(15, 5, $id, 0, 0, 'C');
    $pdf->Cell(70, 5, $usuario_nombre, 0, 0, 'C');
    $pdf->Cell(70, 5, $usuario_email, 0, 0, 'C');
    $pdf->Cell(15, 5, $usuario_nivel, 0, 1, 'C');
endforeach;


$pdf->Output('usuarios_' . time() . '.pdf', 'D');

