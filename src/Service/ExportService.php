<?php

declare(strict_types=1);

namespace App\Service;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Font;

class ExportService
{
    private Spreadsheet $spreadsheet;

    public function __construct()
    {
        $this->spreadsheet = new Spreadsheet();
    }

    public function getSpreadsheet(): Spreadsheet
    {
        return $this->spreadsheet;
    }

    public function exportarReporteVentas(array $datos, string $fechaDesde, string $fechaHasta): void
    {
        $sheet = $this->spreadsheet->getActiveSheet();
        $sheet->setTitle("Ventas");

        $this->agregarEncabezadoReporte(
            $sheet,
            "Reporte de Ventas",
            $fechaDesde,
            $fechaHasta
        );

        $headers = ["#", "Fecha", "Cliente", "Sección", "Usuario", "Subtotal", "Descuento", "Impuesto", "Total"];
        $this->agregarFilaEncabezados($sheet, $headers, 4);

        $row = 5;
        foreach ($datos as $venta) {
            $sheet->setCellValue("A{$row}", (int)$venta["id_factura"]);
            $sheet->setCellValue("B{$row}", $venta["fecha"]);
            $sheet->setCellValue("C{$row}", $venta["cliente"]);
            $sheet->setCellValue("D{$row}", $venta["seccion"]);
            $sheet->setCellValue("E{$row}", $venta["usuario"]);
            $sheet->setCellValue("F{$row}", (float)$venta["subtotal"]);
            $sheet->setCellValue("G{$row}", (float)$venta["descuento"]);
            $sheet->setCellValue("H{$row}", (float)$venta["impuesto"]);
            $sheet->setCellValue("I{$row}", (float)$venta["total"]);

            $this->aplicarFormatoMoneda($sheet, "F{$row}:I{$row}");
            $row++;
        }

        if (!empty($datos)) {
            $totalRow = $row;
            $sheet->setCellValue("E{$totalRow}", "TOTALES:");
            $sheet->getStyle("E{$totalRow}")->getFont()->setBold(true);
            $sheet->setCellValue("F{$totalRow}", "=SUM(F5:F" . ($row - 1) . ")");
            $sheet->setCellValue("G{$totalRow}", "=SUM(G5:G" . ($row - 1) . ")");
            $sheet->setCellValue("H{$totalRow}", "=SUM(H5:H" . ($row - 1) . ")");
            $sheet->setCellValue("I{$row}", "=SUM(I5:I" . ($row - 1) . ")");
            $this->aplicarFormatoMoneda($sheet, "F{$totalRow}:I{$totalRow}");
            $this->aplicarFormatoNegrita($sheet, "E{$totalRow}:I{$totalRow}");
        }

        $this->autoAjustarColumnas($sheet, $headers, 9);
    }

    public function exportarReporteProductos(array $datos, string $fechaDesde, string $fechaHasta): void
    {
        $sheet = $this->spreadsheet->getActiveSheet();
        $sheet->setTitle("Productos");

        $this->agregarEncabezadoReporte(
            $sheet,
            "Reporte de Productos",
            $fechaDesde,
            $fechaHasta
        );

        $headers = ["Código", "Producto", "Stock", "Cantidad Vendida", "Total Vendido"];
        $this->agregarFilaEncabezados($sheet, $headers, 4);

        $row = 5;
        foreach ($datos as $producto) {
            $sheet->setCellValue("A{$row}", $producto["codigo"]);
            $sheet->setCellValue("B{$row}", $producto["nombre"]);
            $sheet->setCellValue("C{$row}", (int)$producto["stock"]);
            $sheet->setCellValue("D{$row}", (int)$producto["cantidad_vendida"]);
            $sheet->setCellValue("E{$row}", (float)$producto["total_vendido"]);

            $this->aplicarFormatoMoneda($sheet, "E{$row}");

            if ((int)$producto["stock"] <= 5) {
                $sheet->getStyle("C{$row}")->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setRGB("FEE2E2");
                $sheet->getStyle("C{$row}")->getFont()->getColor()->setRGB("991B1B");
            }

            $row++;
        }

        $this->autoAjustarColumnas($sheet, $headers, 5);
    }

    public function exportarReporteClientes(array $datos, string $fechaDesde, string $fechaHasta): void
    {
        $sheet = $this->spreadsheet->getActiveSheet();
        $sheet->setTitle("Clientes");

        $this->agregarEncabezadoReporte(
            $sheet,
            "Reporte de Clientes",
            $fechaDesde,
            $fechaHasta
        );

        $headers = ["Cliente", "Teléfono", "Tipo", "Facturas", "Total Comprado"];
        $this->agregarFilaEncabezados($sheet, $headers, 4);

        $row = 5;
        foreach ($datos as $cliente) {
            $sheet->setCellValue("A{$row}", $cliente["cliente"]);
            $sheet->setCellValue("B{$row}", $cliente["telefono"] ?? "N/A");
            $sheet->setCellValue("C{$row}", $cliente["tipo_cliente"] ?? "N/A");
            $sheet->setCellValue("D{$row}", (int)$cliente["cantidad_facturas"]);
            $sheet->setCellValue("E{$row}", (float)$cliente["total_comprado"]);

            $this->aplicarFormatoMoneda($sheet, "E{$row}");
            $row++;
        }

        $this->autoAjustarColumnas($sheet, $headers, 5);
    }

    public function exportarReporteCompleto(
        array $ventas,
        array $productos,
        array $clientes,
        string $fechaDesde,
        string $fechaHasta
    ): void {
        $this->exportarReporteVentas($ventas, $fechaDesde, $fechaHasta);

        $sheetProductos = $this->spreadsheet->createSheet();
        $this->spreadsheet->setActiveSheetIndex($this->spreadsheet->getSheetCount() - 1);
        $this->exportarReporteProductos($productos, $fechaDesde, $fechaHasta);

        $sheetClientes = $this->spreadsheet->createSheet();
        $this->spreadsheet->setActiveSheetIndex($this->spreadsheet->getSheetCount() - 1);
        $this->exportarReporteClientes($clientes, $fechaDesde, $fechaHasta);

        $this->spreadsheet->setActiveSheetIndex(0);
    }

    public function descargar(string $nombreArchivo): void
    {
        header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
        header("Content-Disposition: attachment;filename=\"{$nombreArchivo}.xlsx\"");
        header("Cache-Control: max-age=0");

        $writer = new Xlsx($this->spreadsheet);
        $writer->save("php://output");
        exit;
    }

    private function agregarEncabezadoReporte(
        \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet,
        string $titulo,
        string $fechaDesde,
        string $fechaHasta
    ): void {
        $sheet->setCellValue("A1", $titulo);
        $sheet->getStyle("A1")->getFont()
            ->setBold(true)
            ->setSize(16)
            ->setColor(new Color("111827"));

        $rangoFechas = "Período: ";
        if ($fechaDesde !== "" && $fechaHasta !== "") {
            $rangoFechas .= "{$fechaDesde} al {$fechaHasta}";
        } elseif ($fechaDesde !== "") {
            $rangoFechas .= "desde {$fechaDesde}";
        } elseif ($fechaHasta !== "") {
            $rangoFechas .= "hasta {$fechaHasta}";
        } else {
            $rangoFechas .= "Todos los registros";
        }

        $sheet->setCellValue("A2", $rangoFechas);
        $sheet->getStyle("A2")->getFont()
            ->setItalic(true)
            ->setColor(new Color("6B7280"));

        $sheet->setCellValue("A3", "Generado: " . date("d/m/Y H:i"));
        $sheet->getStyle("A3")->getFont()
            ->setColor(new Color("9CA3AF"))
            ->setSize(9);

        $sheet->mergeCells("A1:I1");
        $sheet->mergeCells("A2:I2");
        $sheet->mergeCells("A3:I3");
    }

    private function agregarFilaEncabezados(
        \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet,
        array $headers,
        int $fila
    ): void {
        $col = "A";
        foreach ($headers as $header) {
            $sheet->setCellValue("{$col}{$fila}", $header);
            $col++;
        }

        $ultimaCol = chr(ord("A") + count($headers) - 1);
        $range = "A{$fila}:{$ultimaCol}{$fila}";

        $sheet->getStyle($range)->getFont()
            ->setBold(true)
            ->setColor(new Color("FFFFFF"))
            ->setSize(11);

        $sheet->getStyle($range)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB("2563EB");

        $sheet->getStyle($range)->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->getStyle($range)->getBorders()
            ->getTop()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle($range)->getBorders()
            ->getBottom()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle($range)->getBorders()
            ->getLeft()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle($range)->getBorders()
            ->getRight()->setBorderStyle(Border::BORDER_THIN);

        $sheet->getRowDimension($fila)->setRowHeight(30);
    }

    private function aplicarFormatoMoneda(
        \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet,
        string $range
    ): void {
        $sheet->getStyle($range)->getNumberFormat()
            ->setFormatCode('#,##0.00');
    }

    private function aplicarFormatoNegrita(
        \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet,
        string $range
    ): void {
        $sheet->getStyle($range)->getFont()->setBold(true);
    }

    private function autoAjustarColumnas(
        \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet,
        array $headers,
        int $totalColumnas
    ): void {
        for ($i = 0; $i < $totalColumnas; $i++) {
            $col = chr(ord("A") + $i);
            $maxLen = strlen($headers[$i] ?? "");

            for ($row = 5; $row <= $sheet->getHighestRow(); $row++) {
                $val = $sheet->getCell("{$col}{$row}")->getValue();
                $len = strlen((string)$val);
                if ($len > $maxLen) {
                    $maxLen = $len;
                }
            }

            $sheet->getColumnDimension($col)->setWidth(min($maxLen + 4, 45));
        }
    }
}
