<?php
header("Content-type: text/css");
?>
:root{
  --brand: #004687;
  --brand-2: #005A9C;
  --panel: #fff;
  --ink: black;
  --muted: #bfd5e5ff;
  --line: #d5e3edff;
  --accent: #d8e3eaff;
  --contrast: #222;
}
body {
    font-family: 'Segoe UI', sans-serif;
    margin: 0;
    background-color: var(--bg);
    color: var(--ink);
}
/* Top navigation — fixed at top */
.top-nav {
  background-color: var(--brand);
    color: #f3e9dc;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    padding: 0 20px;
    position: fixed;
    inset: 0 0 auto 0; /* top:0; left:0; right:0 */
    height: 90px;
    z-index: 1000;
    box-shadow: 0 2px 5px rgba(0,0,0,0.2);
}

.nav-left .welcome-text {
  font-weight: 700;
  font-size: 22px;
  color: #f9f9f9;
}

.nav-menu {
  display: flex;
  gap: 10px;
  align-items: center;
}
.nav-menu a {
  color: #f9f9f9;
  text-decoration: none;
  padding: 8px 12px;
  border-radius: 8px;
  font-weight: 700;
  font-size: 18px;
  display:inline-flex;
  align-items:center;
  gap:8px;
}
.nav-menu a:hover { background: rgba(255,255,255,0.06); }
.nav-menu a.active { background: rgba(255,255,255,0.08); box-shadow: 0 2px 6px rgba(0,0,0,0.08); }

/* main content offset to account for nav */
.main-content {
  padding-top: 92px;
  min-height: calc(100vh - 92px);
  background: var(--panel);
  color: var(--ink);
}

/* wrapper */
.content-wrapper {
  max-width: 1000px;
  margin: 18px auto;
  padding: 0 18px;
}

/* search form */
.search-form {
  margin-bottom: 16px;
  background: transparent;
}
.search-row {
  display:flex;
  gap:8px;
  margin-top:6px;
}
.search-row input[type="text"] {
  padding: 8px 12px;
  font-size: 14px;
  border: 1px solid var(--line);
  border-radius: 6px;
  width: 260px;
}
.btn-primary {
  padding: 8px 12px;
  background: var(--brand-2);
  color: #fff;
  border: none;
  border-radius: 6px;
  font-weight: 700;
  cursor: pointer;
}

/* page header */
.page-header h1 {
  margin: 6px 0 2px 0;
  font-size: 19px;
  color: var(--brand);
}
.page-header .sub {
  margin: 0 0 12px 0;
    font-size: 19px;

  color: var(--contrast);
}

/* actions row */
.actions-row {
  margin-bottom: 12px;
}
.btn {
  background: var(--brand-2);
  color: #fff;
  border: none;
  padding: 10px 14px;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 700;
}
.btn:hover { background: var(--brand); }

/* table */
.table-wrap {
  background: #fff;
  border-radius: 10px;
  padding: 18px;
  box-shadow: 0 8px 20px rgba(0,0,0,0.06);
}
.balance-table {
  width: 100%;
  border-collapse: collapse;
  font-family: Arial, sans-serif;
}
.balance-table th,
.balance-table td {
  padding: 12px 14px;
  border: 1px solid var(--line);
  text-align: center;
}
.balance-table thead th {
  background: linear-gradient(180deg,var(--brand-2),var(--brand));
  color: #f9f9f9;
  font-weight: 800;
}
.balance-table tbody tr:nth-child(even) { background: #dce7eeff; }
.total-summary-row {
  background: #c1dbedff;
  font-weight: 800;
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
/* responsive */
@media (max-width: 880px) {
  .content-wrapper { padding: 0 14px; }
  .search-row input[type="text"] { width: 180px; }
  .nav-menu a { font-size: 14px; padding: 6px 8px; }
  .top-nav { height: 60px; padding: 0 12px; }
  .main-content { padding-top: 84px; }
}

@media (max-width: 520px) {
  .search-row { flex-direction: column; gap:6px; }
  .search-row input[type="text"] { width: 100%; }
  .balance-table th, .balance-table td { padding: 10px 8px; font-size: 14px; }
  .page-header h1 { font-size: 1.1rem; }
}
