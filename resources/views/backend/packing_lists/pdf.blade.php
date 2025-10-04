<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Packing List - {{ $packingList->document_number }}</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 10px;
            color: #333;
        }

        @page {
            margin: 40px 50px;
        }

        .header-container {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }

        .header-container h1 {
            margin: 0;
            font-size: 24px;
            font-weight: bold;
        }

        .header-container p {
            margin: 5px 0 0;
            font-size: 14px;
        }

        .info-section {
            margin-top: 25px;
            margin-bottom: 25px;
            font-size: 11px;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
        }

        .info-table td {
            padding: 4px 0;
            vertical-align: top;
        }

        .info-label {
            width: 120px;
            font-weight: bold;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .items-table th,
        .items-table td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }

        .items-table th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: center;
        }

        .items-table td.center {
            text-align: center;
        }

        .items-table td.right {
            text-align: right;
        }

        .signatures-container {
            margin-top: 50px;
            width: 100%;
            text-align: center;
        }

        .signature-box {
            display: inline-block;
            width: 30%;
            margin: 0 1.5%;
            vertical-align: top;
        }

        .signature-box .signature-line {
            border-bottom: 1px solid #333;
            height: 70px;
            margin-bottom: 5px;
        }

        .footer {
            position: fixed;
            bottom: -30px;
            left: 0;
            right: 0;
            height: 50px;
            text-align: center;
            font-size: 9px;
            color: #888;
        }
    </style>
</head>

<body>
    <div class="header-container">
        <h1>SURAT JALAN</h1>
        <p>Nomor Dokumen: {{ $packingList->document_number }}</p>
    </div>

    <div class="info-section">
        <table class="info-table">
            <tr>
                <td class="info-label">Tanggal Terbit</td>
                <td>:</td>
                <td>{{ \Carbon\Carbon::parse($packingList->created_at)->translatedFormat('d F Y') }}</td>
            </tr>
            <tr>
                <td class="info-label">Nama Penerima</td>
                <td>:</td>
                <td>{{ $packingList->recipient_name }}</td>
            </tr>
            <tr>
                <td class="info-label">Dibuat Oleh</td>
                <td>:</td>
                <td>{{ $packingList->creator->name ?? 'Sistem' }}</td>
            </tr>
            <tr>
                <td class="info-label" valign="top">Catatan</td>
                <td valign="top">:</td>
                <td>{{ $packingList->notes ?? '-' }}</td>
            </tr>
        </table>
    </div>

    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 5%;">No.</th>
                <th>Nama Aset / Barang</th>
                <th>Nomor Seri</th>
                <th style="width: 15%;">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @foreach($packingList->assets as $index => $asset)
            <tr>
                <td class="center">{{ $index + 1 }}</td>
                <td>{{ $asset->name_asset }}</td>
                <td class="center">{{ $asset->serial_number ?? '-' }}</td>
                <td class="center">
                    @if($asset->asset_type == 'fixed_asset')
                    1 Unit
                    @else
                    1 Ea
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="signatures-container">
        <div class="signature-box">
            <p>Disiapkan Oleh,</p>
            <div class="signature-line"></div>
            <strong>{{ $packingList->creator->name ?? 'Sistem' }}</strong>
            <p>(Staff Gudang)</p>
        </div>
        <div class="signature-box">
            <p>Disetujui Oleh,</p>
            <div class="signature-line"></div>
            <strong>(...............................)</strong>
            <p>(Manager)</p>
        </div>
        <div class="signature-box">
            <p>Diterima Oleh,</p>
            <div class="signature-line"></div>
            <strong>{{ $packingList->recipient_name }}</strong>
            <p>(Penerima)</p>
        </div>
    </div>

    <div class="footer">
        <p>Dokumen ini dicetak secara otomatis oleh sistem ManproApp pada {{ now()->translatedFormat('d F Y, H:i') }}
        </p>
    </div>
</body>

</html>