<?php
if (!function_exists('exportToExcel')) {
    function exportToExcel($data, $filename) {
        // Set header untuk file Excel
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '.xls"');
        header('Cache-Control: max-age=0');
        
        // Output header Excel
        echo "<!DOCTYPE html>";
        echo "<html>";
        echo "<head>";
        echo "<meta charset='UTF-8'>";
        echo "</head>";
        echo "<body>";
        echo "<table border='1'>";
        
        // Header tabel
        echo "<tr style='background-color: #f0f0f0;'>";
        echo "<th>No</th>";
        echo "<th>Nama</th>";
        echo "<th>Alamat</th>";
        echo "<th>PBB Terutang</th>";
        echo "<th>Status Pembayaran</th>";
        echo "</tr>";
        
        // Isi data
        $no = 1;
        while ($row = $data->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $no++ . "</td>";
            echo "<td>" . $row['nama'] . "</td>";
            echo "<td>" . $row['alamat'] . "</td>";
            echo "<td>" . $row['pbb_terutang'] . "</td>";
            echo "<td>" . $row['status_pembayaran'] . "</td>";
            echo "</tr>";
        }
        
        echo "</table>";
        echo "</body>";
        echo "</html>";
        exit;
    }
}

if (!function_exists('formatRupiah')) {
    function formatRupiah($angka) {
        return "Rp " . number_format($angka, 0, ',', '.');
    }
}

if (!function_exists('createPagination')) {
    function createPagination($current_page, $total_pages, $page_param, $keyword) {
        if ($total_pages <= 1) return '';
        
        $html = '<div class="flex gap-2 justify-end mt-2">';
        
        // First & Previous
        if ($current_page > 1) {
            $html .= sprintf('<a href="?%s=1%s" class="px-3 py-2 bg-gray-800 text-gray-300 rounded hover:bg-gray-700">&lt;&lt;</a>', $page_param, $keyword ? '&search='.urlencode($keyword) : '');
            $html .= sprintf('<a href="?%s=%d%s" class="px-3 py-2 bg-gray-800 text-gray-300 rounded hover:bg-gray-700">&lt;</a>', $page_param, $current_page - 1, $keyword ? '&search='.urlencode($keyword) : '');
        }
        
        // Page numbers
        $start = max(1, $current_page - 2);
        $end = min($total_pages, $current_page + 2);
        
        for ($i = $start; $i <= $end; $i++) {
            $html .= sprintf(
                '<a href="?%s=%d%s" class="px-3 py-2 rounded %s">%d</a>',
                $page_param,
                $i,
                $keyword ? '&search='.urlencode($keyword) : '',
                $i === $current_page ? 'bg-blue-500 text-white' : 'bg-gray-800 text-gray-300 hover:bg-gray-700',
                $i
            );
        }
        
        // Next & Last
        if ($current_page < $total_pages) {
            $html .= sprintf('<a href="?%s=%d%s" class="px-3 py-2 bg-gray-800 text-gray-300 rounded hover:bg-gray-700">&gt;</a>', $page_param, $current_page + 1, $keyword ? '&search='.urlencode($keyword) : '');
            $html .= sprintf('<a href="?%s=%d%s" class="px-3 py-2 bg-gray-800 text-gray-300 rounded hover:bg-gray-700">&gt;&gt;</a>', $page_param, $total_pages, $keyword ? '&search='.urlencode($keyword) : '');
        }
        
        $html .= '</div>';
        return $html;
    }
}

if (!function_exists('createTable')) {
    function createTable($result, $role, $current_page, $offset = 0) {
        $html = '<table class="min-w-full table-auto border-collapse bg-gray-800 text-white mb-4">
            <thead>
                <tr class="bg-gray-700 rounded-lg">
                    <th class="py-3 px-4">No</th>
                    <th class="py-3 px-4">Nama</th>
                    <th class="py-3 px-4">Alamat</th>
                    <th class="py-3 px-4">PBB Terutang</th>
                    <th class="py-3 px-4">Detail</th>
                    ' . ($role === 'admin' ? '<th class="py-3 px-4">Aksi</th>' : '') . '
                </tr>
            </thead>
            <tbody>';
        
        $no = $offset + 1;
        while ($row = $result->fetch_assoc()) {
            $html .= '<tr class="hover:bg-gray-700">
                <td class="py-3 px-4">' . $no . '</td>
                <td class="py-3 px-4">
                    <input type="hidden" name="id" value="' . $row['id'] . '">
                    <a href="detail.php?id=' . $row['id'] . '&page=' . $current_page . '" class="text-blue-400 hover:underline">' 
                    . htmlspecialchars($row['nama']) . '</a>
                </td>
                <td class="py-3 px-4">' . htmlspecialchars($row['alamat']) . '</td>
                <td class="py-3 px-4">' . formatRupiah($row['pbb_terutang']) . '</td>
                <td class="py-3 px-4">
                    <a href="detail.php?id=' . $row['id'] . '&page=' . $current_page . '" class="text-blue-400 hover:underline">Lihat Detail</a>
                </td>';
            
            if ($role === 'admin') {
                $html .= '<td class="py-3 px-4">
                    <a href="edit.php?id=' . $row['id'] . '" class="text-yellow-400 hover:underline mr-2">Edit</a>
                    <a href="hapus.php?id=' . $row['id'] . '" class="text-red-400 hover:underline" 
                        onclick="return confirm(\'Yakin ingin menghapus data ini?\')">Hapus</a>
                </td>';
            }
            
            $html .= '</tr>';
            $no++;
        }
        
        $html .= '</tbody></table>';
        return $html;
    }
}