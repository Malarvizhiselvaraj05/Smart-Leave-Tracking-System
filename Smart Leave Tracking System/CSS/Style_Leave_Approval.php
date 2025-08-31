<?php
header("Content-type: text/css");
?>

:root{
  /* Brown theme palette */
  --brand-900: #004687;
  --brand-700: #005A9C;
  --brand-600: #04538bff;
  --cream: #f3e9dc;
  --panel: #fffaf3;
  --ink: black;
  --muted: #6fa1c4ff;
  --line: #bfd4e2ff;
  --border: #ddd;
  --highlight: #f9f3ed;
  --text-light: #ffffff;
}

/* Reset / body */
body {
    font-family: 'Segoe UI', sans-serif;
    margin: 0;
    background-color: var(--bg);
    color: var(--ink);
}

/* ---------- Top Navigation ---------- */
.top-nav {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  height: 90px;
  display: flex;
  align-items: center;
  justify-content: space-between;
    background-color: #004687;
  color: var(--cream);
  gap: 12px;
  padding: 0 20px;
  z-index: 1000;
  box-shadow: 0 2px 8px rgba(0,0,0,0.12);
}
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

.header-left{
  display: flex;
  align-items: center;
  gap: 12px;
  min-width: 0;
  flex: 0 1 auto; 
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
.brand-title {
  font-size: 1.05rem;
  font-weight: 600;
  color: var(--cream);
}

.nav-menu {
  display:flex;
  gap: 12px;
  font-size: 20px;
  font-weight: 600;
  align-items: center;
}

.nav-menu a {
  color: #f9f9f9;
  text-decoration: none;
  padding: 8px 12px;
  border-radius: 8px;
  font-weight: 600;
  font-size: 20px;
  transition: background 0.15s ease;
}


.nav-menu a:hover {
  background: rgba(255,255,255,0.05);
}

.nav-menu a.active {
  background: var(--brand-600);
  box-shadow: 0 2px 6px rgba(0,0,0,0.12);
}

/* Push page content below nav */
.header, .content, .table-container {
  margin-top: 66px; /* give a little breathing room */
}

/* ---------- Header ---------- */
.header {
  padding: 18px 28px;
  background: transparent;
  border-bottom: 1px solid rgba(0,0,0,0.02);
}

.page-title {
  font-size: 28px;
  color: var(--ink);
  margin: 0;
}

/* ---------- Content ---------- */
.content {
  padding: 28px;
}

/* Search / top-bar */
.top-bar-container {
  display:flex;
  flex-wrap:wrap;
  gap:12px;
  justify-content:space-between;
  align-items:center;
}

.search-input {
  padding: 8px 10px;
  border-radius: 8px;
  border: 1px solid var(--line);
  background: var(--panel);
  width: 220px;
}

.btn-medium-action {
  background: #005A9C;        /* warm brown */
  color: #fff;
  border: none;
  padding: 10px 18px;
  font-size: 0.95rem;
  font-weight: 600;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 3px 6px rgba(0,0,0,0.15);
}

.btn-medium-action:hover {
  background: #004687;        /* darker brown */
  transform: translateY(-2px);
  box-shadow: 0 5px 12px rgba(0,0,0,0.25);
}

.btn-medium-action:active {
  transform: scale(0.96);
  background: #005A9C;
}

.action-buttons {
  display: flex;
  justify-content: flex-end;  /* align to right */
  gap: 12px;                  /* spacing between buttons */
  margin-bottom: 15px;
}

.action-buttons form {
  margin: 0;                  /* remove default spacing */
}

/* year-filter */
.year-filter-btn {
  background: var(--brand-700);
  color: var(--text-light);
  border: none;
  padding: 8px 12px;
  border-radius: 8px;
  cursor: pointer;
}
.year-filter-btn:hover { background: var(--brand-600); }

/* Export / small buttons */
.btn {
  padding: 8px 14px;
  background-color: var(--brand-700);
  color: var(--text-light);
  border: none;
  border-radius: 6px;
  cursor: pointer;
}
.btn:hover { background-color: var(--brand-600); }

/* Table container */
.table-container {
  background-color: #ffffff;
  border-radius: 12px;
  padding: 20px;
  box-shadow: 0 6px 20px rgba(0,0,0,0.06);
  overflow-x: auto;
}

/* Table */
.main-table {
  width: 100%;
  border-collapse: collapse;
  min-width: 900px;
}

.main-table th,
.main-table td {
  padding: 12px 14px;
  border: 1px solid var(--border);
  text-align: left;
  vertical-align: middle;
}

.main-table th {
  background-color: var(--brand-700);
  color: var(--text-light);
  font-size: 0.85rem;
  text-transform: uppercase;
  letter-spacing: 0.6px;
}

.main-table td {
  font-size: 0.95rem;
  color: var(--ink);
  background: #fff;
}

.brand-title { font-size: 22px ; font-weight: 700; color: #f9f9f9; }

.status {
  font-weight: bold;
  text-decoration: none;
  padding: 4px 8px;
  border-radius: 6px;
}

.status.approved {
  color: white;
  background-color: green;
}

.status.rejected {
  color: white;
  background-color: red;
}

.status.pending {
  color: black;
  background-color: orange;
}

.main-table tbody tr:hover {
  background-color: var(--highlight);
  transition: background-color 0.18s ease;
}

/* status link plain */
.main-table a {
  color: var(--brand-900);
  font-weight: 600;
  text-decoration: none;
}
.main-table a:hover { text-decoration: underline; color: var(--brand-700); }

/* responsive */
@media (max-width: 900px) {
  .search-input { width: 160px; }
  .main-table { min-width: 700px; }
}

@media (max-width: 600px) {
  .nav-menu { display: none; } /* hide horizontal links on very small screens */
  .brand-title { font-size: 1rem; }
  .header, .content, .table-container { margin-top: 60px; padding-left: 14px; padding-right: 14px; }
}
