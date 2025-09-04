<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>{{ $packingList->document_number }}</title>
    <style>
        body {
            font-family: 'Helvetica', sans-serif;
            font-size: 12px;
            color: #333;
        }

        .container {
            width: 100%;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
        }

        .header p {
            margin: 5px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .details {
            margin-bottom: 30px;
        }

        .details td {
            border: none;
            padding: 3px 0;
        }

        .signatures {
            margin-top: 60px;
            width: 100%;
        }

        .signature-box {
            width: 45%;
            display: inline-block;
            text-align: center;
        }

        .signature-box .name {
            margin-top: 60px;
            border-top: 1px solid #333;
            padding-top: 5px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>PACKING LIST</h1>
            <p><strong>Nomor Dokumen:</strong> {{ $packingList->document_number }}</p>
        </div>

        <table class="details">
            <tr>
                <td width="150px"><strong>Tanggal Dibuat:</strong></td>
                <td>{{ $packingList->created_at->format('d F Y') }}</td>
            </tr>
            <tr>
                <td><strong>Dibuat Oleh (Warehouse):</strong></td>
                <td>{{ $packingList->creator->name }}</td>
            </tr>
            <tr>
                <td><strong>Nama Penerima:</strong></td>
                <td>{{ $packingList->recipient_name }}</td>
            </tr>
            <tr>
                <td><strong>Catatan:</strong></td>
                <td>{{ $packingList->notes ?? '-' }}</td>
            </tr>
        </table>

        <h3>Daftar Barang Keluar</h3>
        <table>
            <thead>
                <tr>
                    <th width="5%">No.</th>
                    <th>Nama Aset</th>
                    <th>Serial Number (S/N)</th>
                    <th>Kategori</th>
                </tr>
            </thead>
            <tbody>
                @foreach($packingList->assets as $index => $asset)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $asset->name_asset }}</td>
                    <td>{{ $asset->serial_number ?? 'N/A' }}</td>
                    <td>{{ $asset->category }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="signatures">
            <div class="signature-box" style="float: left;">
                <p>Disiapkan Oleh,</p>
                <p class="name">{{ $packingList->creator->name }}</p>
            </div>
            <div class="signature-box" style="float: right;">
                <p>Diterima Oleh,</p>
                <p class="name">{{ $packingList->recipient_name }}</p>
            </div>
        </div>
    </div>
</body>

</html>