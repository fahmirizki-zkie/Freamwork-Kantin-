<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            text-align: center;
            font-family: Arial;
        }
        .container {
            border: 8px solid #000;
            padding: 100px;
        }
        h1 {
            font-size: 40px;
        }
        h2 {
            font-size: 32px;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>SERTIFIKAT</h1>
    <p>Diberikan kepada:</p>
    <h2>{{ $nama }}</h2>
    <p>Atas partisipasi dalam kegiatan</p>
    <p><strong>{{ $kegiatan }}</strong></p>
</div>
<br><br>
<a href="/pdf/sertifikat/download">
    <button>Download PDF</button>
</a>
</body>
</html>
