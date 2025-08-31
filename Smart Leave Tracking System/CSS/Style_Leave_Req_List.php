<?php header("Content-type: text/css"); ?>

:root{
  /* Brand palette (aligned with sample) */
  --brand-900: #004687;
  --brand-700: #005A9C;
  --brand-600: #04538bff;

  --panel: #ffffff;
  --ink: #0f172a;
  --muted: #6fa1c4ff;
  --line: #bfd4e2ff;
  --border: #ddd;
  --highlight: #f9f3ed;    /* soft warm hover */
  --text-light: #ffffff;
  --bg: #f7fbff;
}

* { box-sizing: border-box; }

/* ---------- Reset / body ---------- */
body {
  font-family: 'Segoe UI', system-ui, -apple-system, Arial, sans-serif;
  margin: 0;
  background-color: var(--bg);
  color: var(--ink);
}

/* Hide legacy sidebar if present */
.sidebar { display: none !important; }

/* ---------- Top Navigation ---------- */
.top-nav{
  position: fixed;
  top: 0; left: 0; right: 0;
  height: 70px;
  background-color: var(--brand-900);
  color: var(--text-light);
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  padding: 0 20px;
  z-index: 1000;
  box-shadow: 0 2px 8px rgba(0,0,0,0.12);
}
.search-row { display:flex; gap:8px; align-items:center; margin-top:6px; }
      .search-row input[type="text"] { padding:8px 10px; border:1px solid #ccc; border-radius:6px; width:240px; }
      .btn-primary { padding:8px 12px; background:#005A9C; color:#fff; border:none; border-radius:6px; cursor:pointer; font-weight:700; }
      .btn-primary:hover { background:#004687; }
      .nav-menu a.active { box-shadow: 0 2px 6px rgba(0,0,0,0.08); background: rgba(255,255,255,0.06); }
      .notice { background: #6fa1c4ff; color: white; padding:10px 14px; border-radius:8px; border:1px solid #f0d9a8; margin-bottom:14px; }
      /* ---------- header alignment fix (drop-in) ---------- */
:root{
  --nav-bg: #004687;
  --nav-border: rgba(15,34,62,0.04);
  --nav-height: 72px; /* matches logo size */
}

.top-nav{
  display: flex;
  align-items: center;            /* vertical centering */
  justify-content: space-between; /* left vs right */
  gap: 12px;
  height: 90px;
  padding: 12px 18px;
  box-sizing: border-box;
  background: var(--nav-bg);
  border-bottom: 1px solid var(--nav-border);
  min-height: var(--nav-height);
}

/* left side group (logo + welcome) */
.header-left{
  display: flex;
  align-items: center;  /* ensure welcome text center aligns with logo */
  gap: 12px;
  min-width: 0;
}
.header-left .logo-wrap{
  --size: 64px;               /* tune size */
  width: calc(var(--size));
  height: calc(var(--size));
  position: relative;
  display: inline-block;
  border-radius: 50%;
  padding: 6px;               /* inner breathing room */
  box-sizing: content-box;
  margin-right: 8px;
  overflow: visible;
  /* keep header flow intact */
}

.header-left .logo-wrap .site-logo{
  display: block;
  width: 100%;
  height: 100%;
  object-fit: contain;
  border-radius: 50%;
  background: transparent;
  position: relative;
  z-index: 1;
  box-shadow: 0 6px 18px rgba(3,12,30,0.35);
}
/* precise logo box — centers the image pixel-perfect */
.logo-wrap {
  --size: 64px;
  width: var(--size);
  height: var(--size);
  display: inline-flex;       /* use flexbox to center the image inside */
  align-items: center;
  background: white;
  justify-content: center;
  border-radius: 50%;
  padding: 0;                 /* remove padding that shifts visual center */
  margin: 0;
  box-sizing: border-box;
  position: relative;
}

/* optional faint circular plate behind logo for contrast (keeps header color) */
.logo-wrap::before{
  content: "";
  position: absolute;
  inset: 0;
  border-radius: 50%;
  background: white;
  filter: blur(0.8px);
  z-index: 0;
  pointer-events: none;
}

/* the image itself — no padding, block-level to avoid baseline shift */
.logo-wrap .site-logo{
  display: block;      /* prevents baseline whitespace */
  width: 100%;
  height: 100%;
  object-fit: contain; /* keeps aspect ratio */
  background: white;
  padding: 0;          /* CRITICAL: remove any padding on the img */
  border-radius: 50%;
  z-index: 1;
  line-height: 0;      /* prevents inline spacing */
  vertical-align: middle;
}

/* welcome text */
.welcome-text, .brand-title {
  color: #fff;
  font-weight: 700;
  white-space: nowrap;
  font-size: 22px;
  overflow: hidden;
  text-overflow: ellipsis;
}

/* right side nav */
.header-right { display:flex; align-items:center; }
.header-right .nav-menu, .nav-menu {
  display:flex;
  gap:14px;
  align-items:center;
}
.nav-menu a {
  color:#fff;
  text-decoration:none;
  padding:8px 10px;
  border-radius:6px;
  font-size: 20px;
  font-weight:600;
}
.nav-menu a:hover{ background: rgba(255,255,255,0.06); }
/* Optional title text if present */
.brand-title{
  font-size: 1.05rem;
  font-weight: 600;
  color: var(--text-light);
}

/* Left cluster (welcome text / logo) */
.nav-left{
  display: flex;
  align-items: center;
  gap: 10px;
}

.welcome-text{
  font-size: 22px;
  font-weight: 700;
  letter-spacing: .2px;
  color: #f9f9f9;
  margin: 0;
}

/* Horizontal menu */
.nav-menu{
  display:flex;
  gap: 12px;
  font-size: 18px;
  font-weight: 600;
  align-items: center;
}

.nav-menu a,
.nav-item > a{
  color: #f9f9f9;
  text-decoration: none;
  padding: 8px 12px;
  border-radius: 8px;
  transition: background 0.15s ease, transform 0.05s ease;
  line-height: 1;
  display: inline-flex;
  align-items: center;
  gap: 6px;
}
.nav-menu a:hover,
.nav-item > a:hover{ background: rgba(255,255,255,0.05); }

.nav-menu a.active,
.nav-item > a.active{
  background: var(--brand-600);
  box-shadow: 0 2px 6px rgba(0,0,0,0.12);
}

/* Focus states for a11y */
.nav-menu a:focus-visible,
.nav-item > a:focus-visible{
  outline: 2px solid #d2dee6;
  outline-offset: 2px;
}

/* Dropdown */
.nav-item{ position: relative; }

.dropdown{
  display: none;
  position: absolute;
  top: calc(100% + 8px);
  right: 0;
  min-width: 220px;
  background: var(--brand-900);
  border: 1px solid #2b537a;
  border-radius: 10px;
  box-shadow: 0 12px 28px rgba(0,0,0,0.22);
  padding: 6px;
  z-index: 1001;
}

.dropdown a{
  display: block;
  padding: 10px 12px;
  border-radius: 8px;
  color: #e9f2f9;
}
.dropdown a:hover{ background: var(--brand-700); }

.nav-item:hover .dropdown,
.nav-item:focus-within .dropdown{ display: block; }

/* ---------- Push page content below nav ---------- */
.header, .content, .table-container { margin-top: 66px; }

/* ---------- Main Content ---------- */
.main-content{
  margin-left: 0;
  padding: 28px;
  padding-top: 86px; /* space for fixed nav */
}

.page-title{
  font-size: 28px;
  font-weight: 800;
  color: var(--ink);
  margin: 0 0 12px 0;
}

/* ---------- Top Bar / Search ---------- */
.top-bar-container{
  display:flex;
  flex-wrap:wrap;
  gap:12px;
  justify-content:space-between;
  align-items:center;
  margin-bottom: 16px;
}

.search-bar-container{
  display: flex;
  align-items: center;
  gap: 8px;
}

.search-input{
  padding: 8px 10px;
  border-radius: 8px;
  border: 1px solid var(--line);
  background: var(--panel);
  width: 220px;
  transition: border-color .2s ease, box-shadow .2s ease;
}
.search-input:focus{
  border-color: var(--brand-700);
  outline: none;
  box-shadow: 0 0 0 4px rgba(0,90,156,0.18);
}

/* ---------- Buttons ---------- */
.btn-request-leave,
.btn-medium-action{
  background: var(--brand-700);
  color: var(--text-light);
  border: none;
  padding: 10px 18px;
  font-size: 0.95rem;
  font-weight: 600;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 3px 6px rgba(0,0,0,0.15);
}
.btn-request-leave:hover,
.btn-medium-action:hover{
  background: var(--brand-900);
  transform: translateY(-2px);
  box-shadow: 0 5px 12px rgba(0,0,0,0.25);
}
.btn-request-leave:active,
.btn-medium-action:active{
  transform: scale(0.96);
  background: var(--brand-700);
}

/* Right-aligned action buttons row (if used) */
.action-buttons{
  display:flex;
  justify-content:flex-end;
  gap:12px;
  margin-bottom:15px;
}
.action-buttons form{ margin:0; }

/* ---------- Table container (wrapper) ---------- */
.table-container{
  background-color: #ffffff;
  border-radius: 12px;
  padding: 20px;
  box-shadow: 0 6px 20px rgba(0,0,0,0.06);
  overflow-x: auto;
}

/* ---------- Table (your class names kept) ---------- */
.leave-table{
  width: 100%;
  border-collapse: collapse;
  min-width: 900px;
}

.leave-table th,
.leave-table td{
  padding: 12px 14px;
  border: 1px solid var(--border);
  text-align: left;
  vertical-align: middle;
}

.leave-table th{
  background-color: var(--brand-700);
  color: var(--text-light);
  font-size: 0.85rem;
  text-transform: uppercase;
  letter-spacing: 0.6px;
}

.leave-table td{
  font-size: 0.95rem;
  color: var(--ink);
  background: #fff;
}

.leave-table tbody tr:hover{
  background-color: var(--highlight);
  transition: background-color 0.18s ease;
}

/* Links inside tables */
.leave-table a{
  color: var(--brand-900);
  font-weight: 600;
  text-decoration: none;
}
.leave-table a:hover{
  text-decoration: underline;
  color: var(--brand-700);
}
.status-Approved {
  background: #e6f9e6;
  color: #2e7d32;
  padding: 3px 8px;
  border-radius: 12px;
  font-weight: bold;
}

.status-Rejected {
  background: #fde0e0;
  color: #c62828;
  padding: 3px 8px;
  border-radius: 12px;
  font-weight: bold;
}

.status-Pending {
  background: #fff4e5;
  color: #ef6c00;
  padding: 3px 8px;
  border-radius: 12px;
  font-weight: bold;
}


/* ---------- Responsive ---------- */
@media (max-width: 1024px){
  .search-input{ width: 200px; }
}

@media (max-width: 900px){
  .leave-table{ min-width: 700px; }
  .search-input{ width: 170px; }
}

@media (max-width: 768px){
  .nav-menu{ display: none; } /* keep header clean on small screens */
  .top-nav{ height: 60px; }
  .main-content{ padding-top: 76px; }
  .page-title{ font-size: 24px; }
  .search-input{ width: 160px; }
}

@media (max-width: 420px){
  .welcome-text{ display: none; }
  .search-input{ width: 130px; }
}
