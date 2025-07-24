<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\HttpFoundation\StreamedResponse;

class exportCSV 
{
    /**
     * Exports CSV file.
     *
     * @param array $data The data to be exported.
     * @param array $headersData The header row for the CSV.
     * @param string $filename The name of the CSV file.
     * @param array $mapping An associative array mapping CSV headers to model attributes.
     * @return StreamedResponse
     */
    public function export(array $data, array $headersData, string $filename, array $mapping) : StreamedResponse
    {

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($data, $headersData, $mapping) {
            try {
                $file = fopen('php://output', 'w');
                fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
                fputcsv($file, $headersData); 

                foreach ($data as $row) {
                    $rowData = [];
                    foreach ($mapping as $csvHeader => $modelAttribute) {
                        $rowData[] = $row->{$modelAttribute} ?? ''; 
                    }
                    fputcsv($file, $rowData);
                }
                fclose($file);
            } catch (\Exception $e) {
                error_log('CSV Export Error: ' . $e->getMessage());
            }
        };

        return new StreamedResponse($callback, 200, $headers);

    }
}