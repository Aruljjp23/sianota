<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">

    <title>{{ $judul }}</title>

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

        .info {
            margin-bottom: 12px;
            color: #68746c;
            line-height: 1.8;
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
            word-wrap: break-word;
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
        <h1>{{ $judul }}</h1>
        <p>Sistem Informasi Pengelolaan Data Notaris — SIANOTA</p>
    </div>

    <div class="info">
        Tanggal cetak: {{ now()->format('d-m-Y H:i') }} WIB
        <br>
        Jenis laporan: {{ $jenis === 'skmht' ? 'SKMHT' : 'Legalisasi' }}
        <br>
        Jumlah dokumen: {{ $laporan->count() }}
        @if (request('tahun'))
            <br>Tahun: {{ request('tahun') }}
        @endif
        @if (request('tanggal_mulai') || request('tanggal_selesai'))
            <br>
            Rentang tanggal:
            {{ request('tanggal_mulai') ?: 'Awal' }}
            s.d.
            {{ request('tanggal_selesai') ?: 'Akhir' }}
        @endif
        @if (request('search'))
            <br>Kata kunci: {{ request('search') }}
        @endif
        @if (request('sifat'))
            <br>Sifat/Jenis Akta: {{ request('sifat') }}
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No.</th>
                <th style="width: 17%;">Nomor Urut/Bulanan</th>
                <th style="width: 13%;">Tanggal Akta</th>
                <th style="width: 22%;">Sifat/Jenis Akta</th>

                @if ($jenis === 'skmht')
                    <th style="width: 21%;">Alamat</th>
                    <th style="width: 22%;">Nama Penghadap</th>
                @else
                    <th style="width: 43%;">Nama Penghadap</th>
                @endif
            </tr>
        </thead>

        <tbody>
            @forelse ($laporan as $index => $item)
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td>{{ $item['nomor'] }}</td>
                    <td>{{ $item['tanggal'] }}</td>
                    <td>{{ $item['sifat'] }}</td>

                    @if ($jenis === 'skmht')
                        <td>{{ $item['alamat'] }}</td>
                    @endif

                    <td>
                        @forelse ($item['penghadap'] as $nama)
                            {{ $nama }}@if (!$loop->last)<br>@endif
                        @empty
                            -
                        @endforelse
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="{{ $jenis === 'skmht' ? 6 : 5 }}"
                        class="center">
                        Tidak ada data yang sesuai dengan filter laporan.
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