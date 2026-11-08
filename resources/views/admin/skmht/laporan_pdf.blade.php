<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Data SKMHT</title>

    <style>
        @page {
            margin: 25px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            color: #26352b;
        }

        .header {
            text-align: center;
            padding-bottom: 15px;
            margin-bottom: 18px;
            border-bottom: 2px solid #16834a;
        }

        .header h1 {
            margin: 0 0 6px;
            font-size: 19px;
            color: #176c3b;
        }

        .header p {
            margin: 0;
            font-size: 10px;
            color: #68746c;
        }

        .report-info {
            margin-bottom: 12px;
            font-size: 9px;
            color: #68746c;
            line-height: 1.7;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            padding: 9px 6px;
            border: 1px solid #d7e2da;
            background: #eaf5ed;
            color: #215c38;
            text-align: left;
            font-size: 9px;
        }

        td {
            padding: 8px 6px;
            border: 1px solid #dfe5e0;
            vertical-align: top;
            line-height: 1.5;
            overflow-wrap: anywhere;
        }

        tbody tr:nth-child(even) {
            background: #fafcfb;
        }

        .center {
            text-align: center;
        }

        .footer {
            margin-top: 16px;
            text-align: right;
            font-size: 9px;
            color: #68746c;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>LAPORAN DATA SKMHT</h1>
        <p>Sistem Informasi Pengelolaan Data Notaris — SIANOTA</p>
    </div>

    <div class="report-info">
        Tanggal cetak: {{ now()->format('d-m-Y H:i') }} WIB
        <br>
        Jumlah dokumen: {{ $skmhts->count() }}
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 4%;">No.</th>
                <th style="width: 13%;">Nomor Urut/Bulanan</th>
                <th style="width: 11%;">Tanggal Akta</th>
                <th style="width: 19%;">Sifat/Jenis Akta</th>
                <th style="width: 25%;">Alamat</th>
                <th style="width: 28%;">Nama Penghadap</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($skmhts as $index => $item)
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td>{{ $item->nomor_bulanan }}</td>
                    <td>{{ $item->tanggal_akta->format('d-m-Y') }}</td>
                    <td>{{ $item->sifat_jenis_akta }}</td>
                    <td>{{ $item->alamat ?: '-' }}</td>
                    <td>
                        @forelse ($item->penghadap as $penghadap)
                            {{ $penghadap->nama_penghadap }}@if (!$loop->last)<br>@endif
                        @empty
                            -
                        @endforelse
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="center">
                        Tidak ada data SKMHT untuk ditampilkan.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dicetak melalui SIANOTA
    </div>
</body>
</html>