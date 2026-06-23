<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pengajuan Reimbursement Baru</title>
  <style>
    body {
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
      background-color: #f4f5fa;
      color: #3f4254;
      margin: 0;
      padding: 0;
      -webkit-font-smoothing: antialiased;
    }
    .wrapper {
      width: 100%;
      background-color: #f4f5fa;
      padding: 40px 20px;
      box-sizing: border-box;
    }
    .container {
      max-width: 600px;
      margin: 0 auto;
      background-color: #ffffff;
      border-radius: 16px;
      overflow: hidden;
      box-shadow: 0 4px 18px rgba(0, 0, 0, 0.05);
    }
    .header {
      background: linear-gradient(135deg, #7367f0 0%, #4c41c3 100%);
      padding: 30px 40px;
      text-align: center;
    }
    .header h2 {
      color: #ffffff;
      margin: 0;
      font-size: 22px;
      font-weight: 700;
      letter-spacing: -0.5px;
    }
    .content {
      padding: 40px;
    }
    .intro {
      font-size: 16px;
      line-height: 24px;
      margin-bottom: 25px;
    }
    .detail-card {
      background-color: #f8f9fa;
      border-left: 4px solid #7367f0;
      padding: 24px;
      border-radius: 8px;
      margin-bottom: 30px;
    }
    .detail-row {
      margin-bottom: 12px;
      display: table;
      width: 100%;
    }
    .detail-label {
      font-weight: 600;
      color: #5e6278;
      width: 150px;
      display: table-cell;
      font-size: 14px;
    }
    .detail-value {
      color: #181c32;
      display: table-cell;
      font-size: 14px;
    }
    .detail-value-highlight {
      font-weight: 700;
      color: #28c76f;
      font-size: 16px;
    }
    .btn-container {
      text-align: center;
      margin-top: 30px;
    }
    .btn {
      display: inline-block;
      background-color: #7367f0;
      color: #ffffff !important;
      font-weight: 600;
      text-decoration: none;
      padding: 14px 30px;
      border-radius: 8px;
      font-size: 14px;
      transition: background-color 0.2s;
    }
    .footer {
      background-color: #f1f1f4;
      padding: 20px 40px;
      text-align: center;
      font-size: 12px;
      color: #a1a5b7;
    }
  </style>
</head>
<body>
  <div class="wrapper">
    <div class="container">
      <div class="header">
        <h2>Supernata Management System</h2>
      </div>
      <div class="content">
        <p class="intro">Halo Supervisor,</p>
        <p class="intro">Ada pengajuan reimbursement baru yang perlu ditinjau. Berikut adalah detail pengajuannya:</p>
        
        <div class="detail-card">
          <div class="detail-row">
            <div class="detail-label">Pengaju:</div>
            <div class="detail-value">{{ $reimbursement->submittedBy->name ?? 'Karyawan' }}</div>
          </div>
          <div class="detail-row">
            <div class="detail-label">Judul:</div>
            <div class="detail-value" style="font-weight: 600;">{{ $reimbursement->title }}</div>
          </div>
          <div class="detail-row">
            <div class="detail-label">Kategori:</div>
            <div class="detail-value">{{ $reimbursement->category }}</div>
          </div>
          <div class="detail-row">
            <div class="detail-label">Jumlah:</div>
            <div class="detail-value detail-value-highlight">Rp {{ number_format($reimbursement->amount, 0, ',', '.') }}</div>
          </div>
          <div class="detail-row">
            <div class="detail-label">Tanggal:</div>
            <div class="detail-value">{{ \Carbon\Carbon::parse($reimbursement->date)->format('d F Y') }}</div>
          </div>
          @if($reimbursement->description)
            <div class="detail-row">
              <div class="detail-label">Deskripsi:</div>
              <div class="detail-value">{{ $reimbursement->description }}</div>
            </div>
          @endif
          <div class="detail-row">
            <div class="detail-label">Link Detail:</div>
            <div class="detail-value">
              <a href="{{ route('reimburs.detail', $reimbursement->id) }}" style="color: #7367f0; text-decoration: underline; font-weight: 500;">
                {{ route('reimburs.detail', $reimbursement->id) }}
              </a>
            </div>
          </div>
        </div>

        <div class="btn-container">
          <a href="{{ route('reimburs.detail', $reimbursement->id) }}" class="btn">Tinjau Pengajuan</a>
        </div>
      </div>
      <div class="footer">
        <p>Email ini dikirim secara otomatis oleh Supernata Management System.</p>
        <p>&copy; {{ date('Y') }} Supernata. All rights reserved.</p>
      </div>
    </div>
  </div>
</body>
</html>
