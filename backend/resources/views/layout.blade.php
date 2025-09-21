<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>GreenCart Logistics — Manager</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/modern-normalize/modern-normalize.css">
  <style>
    body{font-family:Inter, ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial; background:#f6f7fb; color:#111}
    .container{max-width:1100px;margin:28px auto;padding:20px}
    .card{background:#fff;border-radius:8px;padding:16px;box-shadow:0 6px 18px rgba(10,10,20,0.06);margin-bottom:16px}
    header.nav{display:flex;justify-content:space-between;align-items:center;margin-bottom:18px}
    .btn{display:inline-block;padding:8px 12px;border-radius:6px;border:none;background:#0ea5a3;color:#fff;cursor:pointer}
    .btn.secondary{background:#334155}
    .grid{display:grid;gap:12px}
    .grid.cols-2{grid-template-columns:repeat(2,1fr)}
    .muted{color:#6b7280}
    table{width:100%;border-collapse:collapse}
    th,td{padding:8px;border-bottom:1px solid #eee;text-align:left}
    input,select{padding:8px;border:1px solid #ddd;border-radius:6px;width:100%}
    .small{font-size:0.9rem}
    .actions button{margin-right:6px}
    .flex{display:flex;gap:8px;align-items:center}
    .right{justify-content:flex-end}
  </style>

  <!-- Axios and Chart.js via CDN -->
  <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

  <script>
    // axios default to include token if present
    axios.interceptors.request.use(config => {
      const token = localStorage.getItem('gc_token');
      if (token) config.headers.Authorization = 'Bearer ' + token;
      return config;
    }, err => Promise.reject(err));
  </script>
</head>
<body>
  <div class="container">
    <header class="nav">
      <div><h1>GreenCart Logistics — Manager</h1><div class="muted small">Internal KPI & Simulation</div></div>
      <div class="flex">
        <a href="{{ route('dashboard') }}" class="small muted">Dashboard</a>
        <a href="{{ route('simulation') }}" class="small muted" style="margin-left:12px">Simulation</a>
        <a href="{{ route('management') }}" class="small muted" style="margin-left:12px">Management</a>
        <div style="width:12px"></div>
        <button id="loginBtn" class="btn secondary small"></button>
      </div>
    </header>

    @yield('content')
  </div>

  <script>
    function updateLoginBtn() {
      const btn = document.getElementById('loginBtn');
      const token = localStorage.getItem('gc_token');
      if (token) {
        btn.textContent = 'Logout';
        btn.onclick = () => { localStorage.removeItem('gc_token'); alert('Logged out'); updateLoginBtn(); };
      } else {
        btn.textContent = 'Login';
        btn.onclick = async () => {
          const email = prompt('Manager email', 'manager@example.com');
          if (!email) return;
          const password = prompt('Password', 'secret123');
          if (!password) return;
          try {
            const res = await axios.post('/api/login', { email, password });
            if (res.data && res.data.token) {
              localStorage.setItem('gc_token', res.data.token);
              alert('Login success');
              updateLoginBtn();
            } else {
              alert('Login failed: ' + (res.data.message || 'No token'));
            }
          } catch (err) {
            alert('Login error: ' + (err.response?.data?.message || err.message));
          }
        };
      }
    }
    updateLoginBtn();
  </script>

  @stack('scripts')
</body>
</html>
