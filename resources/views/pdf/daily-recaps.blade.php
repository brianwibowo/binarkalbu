<!DOCTYPE html>
<html>
<head>
    <title>Rekap Harian</title>
    <style>
        body { font-family: sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #dddddd; text-align: left; padding: 8px; }
        th { background-color: #f2f2f2; }
        h1 { text-align: center; }
    </style>
</head>
<body>
    <h1>Laporan Rekap Harian</h1>
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Jumlah Sesi</th>
                <th>Chat Baru</th>
                <th>Goals Klien Baru</th>
                <th>Ulasan GMap</th>
                <th>Dari TikTok</th>
                <th>Dari Google</th>
                <th>Dari Instagram</th>
                <th>Dari Teman</th>
                <th>Jam Gandeng</th>
                <th>Tambahan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($records as $record)
                <tr>
                    <td>{{ $record->recap_date }}</td>
                    <td>{{ $record->session_count }}</td>
                    <td>{{ $record->new_chats }}</td>
                    <td>{{ $record->new_client_goals }}</td>
                    <td>{{ $record->gmap_reviews }}</td>
                    <td>{{ $record->source_tiktok }}</td>
                    <td>{{ $record->source_google }}</td>
                    <td>{{ $record->source_instagram }}</td>
                    <td>{{ $record->source_friend }}</td>
                    <td>{{ $record->jam_gandeng }}</td>
                    <td>{{ $record->extra_notes }}</td>

                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>