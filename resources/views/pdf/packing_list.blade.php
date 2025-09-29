<!DOCTYPE html>
<html>

<head>
    <title>Packing List - {{ $packingList->document_number }}</title>
    <style>
        body {
            font-family: 'Helvetica', sans-serif;
            font-size: 12px;
        }

        .container {
            width: 100%;
            margin: 0 auto;
        }

        .header,
        .footer {
            text-align: center;
        }

        .header h1 {
            margin: 0;
        }

        .content {
            margin-top: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .info-table {
            border: none;
            margin-bottom: 20px;
        }

        .info-table td {
            border: none;
            padding: 2px 0;
        }

        .signatures {
            margin-top: 40px;
            width: 100%;
        }

        .signature-box {
            display: inline-block;
            width: 30%;
            text-align: center;
            margin: 0 1.5%;
        }

        .signature-box .line {
            border-top: 1px solid #000;
            margin-top: 60px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>PACKING LIST / SURAT JALAN</h1>
            <p><strong>Nomor Dokumen:</strong> {{ $packingList->document_number }}</p>
        </div>

        <div class="content">
            <table class="info-table">
                <tr>
                    <td width="15%"><strong>Tanggal</strong></td>
                    <td width="1%">:</td>
                    <td>{{ \Carbon\Carbon::parse($packingList->created_at)->translatedFormat('d F Y') }}</td>
                </tr>
                <tr>
                    <td><strong>Nama Penerima</strong></td>
                    <td>:</td>
                    <td>{{ $packingList->recipient_name }}</td>
                </tr>
                <tr>
                    <td><strong>Dibuat Oleh</strong></td>
                    <td>:</td>
                    <td>{{ $packingList->creator->name }}</td>
                </tr>
                <tr>
                    <td valign="top"><strong>Catatan</strong></td>
                    <td valign="top">:</td>
                    <td valign="top">{{ $packingList->notes ?? '-' }}</td>
                </tr>
            </table>

            <h4>Daftar Aset/Barang:</h4>
            <table>
                <thead>
                    <tr>
                        <th width="5%">No.</th>
                        <th>Nama Aset/Barang</th>
                        <th>Nomor Seri</th>
                        <th>Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($packingList->assets as $index => $asset)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $asset->name_asset }}</td>
                        <td>{{ $asset->serial_number ?? '-' }}</td>
                        <td>1 Unit</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="signatures">
            <div class="signature-box">
                <p>Disiapkan Oleh,</p>
                <div class="line"></div>
                <p>{{ $packingList->creator->name }}</p>
            </div>
            <div class="signature-box">
                <p>Disetujui Oleh,</p>
                <div class="line"></div>
                <p>(Manager)</p>
            </div>
            <div class="signature-box">
                <p>Diterima Oleh,</p>
                <div class="line"></div>
                <p>{{ $packingList->recipient_name }}</p>
            </div>
        </div>
    </div>
</body>

</html>