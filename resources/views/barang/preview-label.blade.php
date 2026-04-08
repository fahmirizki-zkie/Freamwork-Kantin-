<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Label Harga - Tom n Jerry 108</title>
<style>
@page {
    size: A4 portrait;
    margin: 4mm 5mm;
}

body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
}

.grid-container {
    width: 200mm;
    display: block;
}

.label-row {
    display: table;
    width: 100%;
    table-layout: fixed;
    margin: 0 0 2mm 0;
    padding: 0;
    border-collapse: separate;
    border-spacing: 2mm 0;
}

.label {
    display: table-cell;
    width: 38mm;
    height: 18mm;
    border: none;
    text-align: center;
    vertical-align: top;
    padding: 0.15cm;
    margin: 0;
    position: relative;
    box-sizing: border-box;
}

.label-content {
    font-size: 7pt;
    line-height: 1.1;
}

.item-name {
    display: block;
    font-size: 10pt;
    font-weight: normal;
    margin-bottom: 0.5mm;
    color: #333;
}

.price-line {
    display: block;
    font-size: 10pt;
}

.currency {
    font-weight: normal;
    margin-right: 0.5mm;
}

.price {
    font-weight: bold;
}
</style>
</head>
<body>
<div class="grid-container">
@php
$cols = 5;
$rows = 8;
$dataIndex = 0;
$startCol = $x;
$startRow = $y;
$startNumber = ($startRow - 1) * $cols + $startCol;
@endphp

@for($row = 1; $row <= $rows; $row++)
    <div class="label-row">
    @for($col = 1; $col <= $cols; $col++)
        @php
            $labelNumber = ($row - 1) * $cols + $col;
        @endphp
        
        @if($labelNumber < $startNumber)
            <div class="label marked"></div>
        @elseif(isset($barangs[$dataIndex]))
            <div class="label">
                <div class="label-content">                    <span class="item-name">{{ $barangs[$dataIndex]->nama }}</span>                    <span class="price-line">
                        <span class="currency">Rp</span><span class="price">{{ number_format($barangs[$dataIndex]->harga, 0, ',', '.') }}</span>
                    </span>
                </div>
            </div>
            @php $dataIndex++; @endphp
        @else
            <div class="label"></div>
        @endif
    @endfor
    </div>
@endfor
</div>
</body>
</html>

