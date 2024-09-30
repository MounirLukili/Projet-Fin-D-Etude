<?php
namespace App\Service;

use TCPDF;

class PDFService
{
    public function createScoresPDF($nom, $prenom, $averages, $scoreDetails)
    {
        // Create new PDF document
        $pdf = new \TCPDF();
        $pdf->SetMargins(10, 10, 10);
        $pdf->AddPage();

          // Add title with styling
        $pdf->SetFont('helvetica', 'B', 30);
        $pdf->SetTextColor(0, 0, 0); // Black color for "Exercisseur"
        $pdf->Cell(0, 10, 'Exercisseur', 0, 0, 'C');
        $pdf->Ln(10);
        
        // Adjust the font and color for "Web"
        $pdf->SetFont('helvetica', 'B', 35);
        $pdf->SetTextColor(216, 42, 78); // #d82a4e color for "Web"
        $pdf->Cell(0, 10, ' Web', 0, 1, 'C');
        
        $pdf->Ln(10);

        // Reset color to black for the rest of the document
        $pdf->SetTextColor(0, 0, 0);

        // Add student information
        $pdf->SetFont('helvetica', '', 16);
        $pdf->Cell(0, 10, "Relevé De Notes Pour L'étudiant:            $nom $prenom", 0, 1, 'L');
        $pdf->Ln(5);

        

        // Add detailed scores table title
        $pdf->SetFont('helvetica', 'B', 14);
        $pdf->Cell(0, 10, 'Détail Des Notes', 0, 1, 'L');
        $pdf->Ln(5);

        // Add detailed scores table
        $pdf->SetFont('helvetica', '', 12);
        $pdf->SetFillColor(0, 123, 255);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(40, 8, 'Date', 1, 0, 'L', true);
        $pdf->Cell(40, 8, 'Module', 1, 0, 'L', true);
        $pdf->Cell(40, 8, 'Difficulty', 1, 0, 'L', true);
        $pdf->Cell(40, 8, 'Note (%)', 1, 1, 'L', true);

        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFillColor(233, 247, 255);
        foreach ($scoreDetails as $detail) {
            $pdf->Cell(40, 8, $detail['date'], 1, 0, 'L', true);
            $pdf->Cell(40, 8, $detail['module'], 1, 0, 'L', true);
            $pdf->Cell(40, 8, $detail['difficulty'], 1, 0, 'L', true);
            $pdf->Cell(40, 8, $detail['note'], 1, 1, 'L', true);
        }

        // Add average scores table title
        $pdf->SetFont('helvetica', 'B', 14);
        $pdf->Cell(0, 10, 'Moyenne Par Module', 0, 1, 'L');
        $pdf->Ln(5);

        // Add average scores table
        $pdf->SetFont('helvetica', '', 12);
        $pdf->SetFillColor(0, 123, 255);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(80, 8, 'Module', 1, 0, 'L', true);
        $pdf->Cell(80, 8, 'Note  (%)', 1, 1, 'L', true);

        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFillColor(233, 247, 255);
        foreach ($averages as $average) {
            $pdf->Cell(80, 8, $average['module'], 1, 0, 'L', true);
            $pdf->Cell(80, 8, $average['average'], 1, 1, 'L', true);
        }
        $pdf->Ln(10);

        // Add signature text
        $pdf->Ln(10);
        $pdf->SetFont('helvetica', '', 12);
        $pdf->MultiCell(0, 10, "Signature du responsable:\nLukili Mounir", 0, 'R');

        // Add signature image
        $signatureImagePath = __DIR__ . '/signature.png'; // Ensure this path is correct
        $pdf->Image($signatureImagePath, $pdf->GetX() + 130, $pdf->GetY() + 5, 50, 20, '', '', 'T', false, 300, '', false, false, 0, false, false, false);

        // Output the PDF
        return $pdf;
    }
}
