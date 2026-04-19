<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Invoice App') — InvoiceGen</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --primary: #0099d8;
            --primary-dark: #007ab8;
            --sidebar-bg: #1a2332;
            --sidebar-text: #a0aec0;
            --sidebar-active: #0099d8;
        }
        body { font-family: 'Inter', sans-serif; background: #f0f4f8; }

        /* Sidebar */
        .sidebar { width: 240px; height: 100vh; background: var(--sidebar-bg); position: fixed; top: 0; left: 0; z-index: 100; overflow-y: auto; }
        .sidebar-logo { padding: 20px 24px; border-bottom: 1px solid rgba(255,255,255,0.08); }
        .sidebar-logo span.logo-main { font-size: 22px; font-weight: 800; color: #fff; }
        .sidebar-logo span.logo-accent { color: var(--primary); }
        .sidebar-nav { padding: 16px 0; }
        .sidebar-nav a { display: flex; align-items: center; gap: 10px; padding: 10px 24px; color: var(--sidebar-text); text-decoration: none; font-size: 14px; transition: all .2s; }
        .sidebar-nav a:hover, .sidebar-nav a.active { color: #fff; background: rgba(0,153,216,.15); border-left: 3px solid var(--primary); }
        .sidebar-nav a svg { width: 18px; height: 18px; flex-shrink: 0; }
        .sidebar-section { padding: 8px 24px 4px; font-size: 11px; font-weight: 600; color: #4a5568; text-transform: uppercase; letter-spacing: .08em; }

        /* Main */
        .main-wrap { margin-left: 240px; min-height: 100vh; }
        .topbar { background: #fff; border-bottom: 1px solid #e2e8f0; padding: 0 32px; height: 60px; display: flex; align-items: center; justify-content: space-between; position: fixed; top: 0; left: 240px; right: 0; z-index: 50; }
        .topbar-title { font-size: 16px; font-weight: 600; color: #1a202c; }
        .topbar-user { display: flex; align-items: center; gap: 12px; }
        .avatar { width: 36px; height: 36px; border-radius: 50%; background: var(--primary); color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 14px; }
        .content { padding: 32px; padding-top: calc(60px + 24px); }

        /* Cards */
        .card { background: #fff; border-radius: 10px; box-shadow: 0 1px 3px rgba(0,0,0,.08); }
        .card-header { padding: 20px 24px; border-bottom: 1px solid #f0f4f8; display: flex; align-items: center; justify-content: space-between; }
        .card-body { padding: 24px; }

        /* Stat cards */
        .stat-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 20px; margin-bottom: 28px; }
        .stat-card { background: #fff; border-radius: 10px; padding: 20px 24px; box-shadow: 0 1px 3px rgba(0,0,0,.08); border-left: 4px solid transparent; }
        .stat-card.blue { border-color: #0099d8; }
        .stat-card.green { border-color: #38a169; }
        .stat-card.red { border-color: #e53e3e; }
        .stat-card.yellow { border-color: #d69e2e; }
        .stat-label { font-size: 12px; color: #718096; font-weight: 500; text-transform: uppercase; letter-spacing: .05em; }
        .stat-value { font-size: 26px; font-weight: 700; color: #1a202c; margin-top: 4px; }
        .stat-value.small { font-size: 18px; }

        /* Badges */
        .badge { display: inline-flex; align-items: center; padding: 3px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; text-transform: uppercase; }
        .badge-paid { background: #c6f6d5; color: #276749; }
        .badge-unpaid { background: #fed7d7; color: #9b2c2c; }
        .badge-pending { background: #fefcbf; color: #744210; }

        /* Buttons */
        .btn { display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; border-radius: 6px; font-size: 14px; font-weight: 500; cursor: pointer; border: none; text-decoration: none; transition: all .2s; }
        .btn-primary { background: var(--primary); color: #fff; }
        .btn-primary:hover { background: var(--primary-dark); color: #fff; }
        .btn-outline { background: transparent; color: var(--primary); border: 1px solid var(--primary); }
        .btn-outline:hover { background: var(--primary); color: #fff; }
        .btn-danger { background: #e53e3e; color: #fff; }
        .btn-danger:hover { background: #c53030; color: #fff; }
        .btn-sm { padding: 5px 12px; font-size: 13px; }
        .btn-ghost { background: transparent; color: #718096; border: 1px solid #e2e8f0; }
        .btn-ghost:hover { background: #f7fafc; color: #1a202c; }

        /* Table */
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #f7fafc; padding: 12px 16px; text-align: left; font-size: 12px; font-weight: 600; color: #718096; text-transform: uppercase; letter-spacing: .05em; border-bottom: 1px solid #e2e8f0; }
        td { padding: 14px 16px; border-bottom: 1px solid #f0f4f8; font-size: 14px; color: #2d3748; }
        tr:last-child td { border-bottom: none; }
        tr:hover td { background: #f7fafc; }

        /* Forms */
        .form-group { margin-bottom: 20px; }
        label { display: block; font-size: 13px; font-weight: 600; color: #4a5568; margin-bottom: 6px; }
        input[type=text], input[type=email], input[type=number], input[type=date], input[type=datetime-local], textarea, select {
            width: 100%; padding: 9px 12px; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 14px; color: #2d3748; background: #fff; transition: border .2s;
        }
        input:focus, textarea:focus, select:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px rgba(0,153,216,.1); }
        textarea { resize: vertical; min-height: 80px; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .form-row-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px; }
        .form-2col { display: grid; grid-template-columns: 1fr 1fr; gap: 28px; }

        /* Alert */
        .alert { padding: 12px 16px; border-radius: 6px; font-size: 14px; margin-bottom: 20px; }
        .alert-success { background: #c6f6d5; color: #276749; border: 1px solid #9ae6b4; }
        .alert-error { background: #fed7d7; color: #9b2c2c; border: 1px solid #fc8181; }

        /* Pagination */
        .pagination { display: flex; gap: 4px; align-items: center; }
        .pagination a, .pagination span { padding: 6px 12px; border-radius: 6px; font-size: 13px; border: 1px solid #e2e8f0; color: #4a5568; text-decoration: none; }
        .pagination a:hover { background: var(--primary); color: #fff; border-color: var(--primary); }
        .pagination .active span { background: var(--primary); color: #fff; border-color: var(--primary); }

        /* Invoice detail */
        .invoice-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 32px; }
        .invoice-logo { font-size: 28px; font-weight: 800; color: #1a202c; }
        .invoice-logo span { color: var(--primary); }
        .invoice-meta { text-align: right; }
        .invoice-number { font-size: 18px; font-weight: 700; color: #1a202c; }
        .invoice-status-label { font-size: 22px; font-weight: 800; margin: 8px 0 4px; }
        .status-paid { color: #38a169; }
        .status-unpaid { color: #e53e3e; }
        .status-pending { color: #d69e2e; }
        .invoice-date { color: var(--primary); font-size: 13px; }
        .invoice-parties { display: grid; grid-template-columns: 1fr 1fr; gap: 40px; margin: 28px 0; padding: 24px; background: #f7fafc; border-radius: 8px; }
        .party-label { font-weight: 700; font-size: 13px; color: #1a202c; margin-bottom: 8px; }
        .party-info { font-size: 13px; line-height: 1.7; color: #4a5568; }
        .party-info .highlight { color: var(--primary); }
        .items-table th { background: #f0f4f8; }
        .items-table td.amount { text-align: right; font-weight: 500; }
        .items-table .taxed-note { font-size: 11px; color: #718096; }
        .totals-section { margin-top: 0; }
        .totals-section tr td { border-bottom: 1px solid #f0f4f8; }
        .totals-section tr.total-row td { font-weight: 700; font-size: 15px; background: #f7fafc; }
        .totals-section td.label { text-align: right; color: #718096; font-size: 13px; }
        .totals-section td.value { text-align: right; font-weight: 600; width: 160px; }
        .tax-note { font-size: 12px; color: #718096; margin: 8px 0 16px; }
        .transactions-section { margin-top: 24px; }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); transition: transform .25s ease; }
            .sidebar.open { transform: translateX(0); }
            .main-wrap { margin-left: 0; }
            .topbar { left: 0; padding: 0 16px; }
            .form-row, .form-row-3, .form-2col { grid-template-columns: 1fr; }
            .invoice-parties { grid-template-columns: 1fr; }
            .stat-grid { grid-template-columns: 1fr 1fr; }
            .content { padding: 16px; padding-top: calc(60px + 12px); }
            .card-body { padding: 16px; }
            .table-wrap { font-size: 13px; }
            .topbar-user div:last-child { display: none; }
            /* Fix inline grid di form */
            [style*="grid-template-columns:1fr 1fr"],
            [style*="grid-template-columns: 1fr 1fr"] { grid-template-columns: 1fr !important; }
            /* Fix 2-col layout form invoice */
            div[style*="display:grid"][style*="1fr 1fr"],
            div[style*="display: grid"] { grid-template-columns: 1fr !important; }
        }
        .sidebar-overlay { display: none; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0,0,0,.5); z-index: 99; }
        .sidebar-overlay.open { display: block; }
        .hamburger { display: none; background: none; border: none; cursor: pointer; padding: 4px; }
        @media (max-width: 768px) { .hamburger { display: flex; align-items: center; } }
        body.sidebar-open { overflow: hidden; position: fixed; width: 100%; }
    </style>
    @stack('styles')
</head>
<body>
    <div class="sidebar-overlay" id="overlay" onclick="closeSidebar()"></div>

    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-logo">
            <span class="logo-main">Invoice<span class="logo-accent">Gen</span></span>
        </div>
        <nav class="sidebar-nav">
            <div class="sidebar-section">Menu</div>
            <a href="{{ route('invoices.index') }}" class="{{ request()->routeIs('invoices.index') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Dashboard
            </a>
            <a href="{{ route('invoices.create') }}" class="{{ request()->routeIs('invoices.create') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Buat Invoice
            </a>
            <a href="{{ route('customers.index') }}" class="{{ request()->routeIs('customers.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Customers
            </a>
            <div class="sidebar-section" style="margin-top:12px">Akun</div>
            <a href="{{ route('profile.edit') }}" class="{{ request()->routeIs('profile.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                Profil
            </a>
            <a href="{{ route('settings.edit') }}" class="{{ request()->routeIs('settings.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Pengaturan
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" style="width:100%;background:none;border:none;cursor:pointer;text-align:left;">
                    <a href="#" onclick="this.closest('form').submit()" style="color:#e53e3e;">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:18px;height:18px;display:inline;margin-right:10px;vertical-align:middle"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        Logout
                    </a>
                </button>
            </form>
        </nav>
    </aside>

    <!-- Main -->
    <div class="main-wrap">
        <header class="topbar">
            <div style="display:flex;align-items:center;gap:12px">
                <button class="hamburger" onclick="toggleSidebar()" aria-label="Menu">
                    <svg width="22" height="22" fill="none" stroke="#1a202c" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <span class="topbar-title">@yield('page-title', 'Dashboard')</span>
            </div>
            <div class="topbar-user">
                <div class="avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                <div>
                    <div style="font-size:13px;font-weight:600;color:#1a202c">{{ auth()->user()->name }}</div>
                    <div style="font-size:12px;color:#718096">{{ auth()->user()->email }}</div>
                </div>
            </div>
        </header>

        <main class="content">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if($errors->any())
                <div class="alert alert-error">
                    <ul style="margin:0;padding-left:16px">
                        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                    </ul>
                </div>
            @endif
            @yield('content')
        </main>
    </div>

    @stack('scripts')
<script>
let scrollY = 0;

function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('overlay');
    const isOpen  = sidebar.classList.toggle('open');
    overlay.classList.toggle('open', isOpen);
    if (isOpen) {
        scrollY = window.scrollY;
        document.body.style.top = `-${scrollY}px`;
        document.body.classList.add('sidebar-open');
    } else {
        closeSidebar();
    }
}

function closeSidebar() {
    document.getElementById('sidebar').classList.remove('open');
    document.getElementById('overlay').classList.remove('open');
    document.body.classList.remove('sidebar-open');
    document.body.style.top = '';
    window.scrollTo(0, scrollY);
}

document.querySelectorAll('.sidebar-nav a').forEach(a => {
    a.addEventListener('click', closeSidebar);
});
</script>
</body>
</html>
