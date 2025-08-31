<?php
header("Content-type: text/css");
?>
/* Leave History — Brown theme stylesheet */

:root{
  --bg: #fff;
  --ink: #2a3136ff;
  --panel: #fffaf3;
  --brand: #004687;   /* dark brown */
  --brand-2: #005A9C; /* mid brown */
  --brand-3: #04538bff; /* warm brown */
  --muted: #6fa1c4ff;
  --line: #b6cbdaff;
  --border: #c6ddedff;
  --highlight: #c6ddedff;
  --text-light: #c7d7e1ff;
  --event-bg: #527f9eff;
  --event-text: #ffffff;
}

/* Reset / body */
* { box-sizing: border-box; }
html, body { height: 100%; }
body {
    font-family: 'Segoe UI', sans-serif;
    margin: 0;
    background-color: var(--bg);
    color: var(--ink);
}

/* Top navigation */
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
    height: 100px;
    z-index: 1000;
    box-shadow: 0 2px 5px rgba(0,0,0,0.2);
}
.brand-title { font-size: 22px ; font-weight: 700; color: #f9f9f9; }
.nav-menu { display:flex; gap:12px; align-items:center; }
.nav-menu a {
  color: #f9f9f9;
  text-decoration: none;
  padding: 8px 12px;
  border-radius: 8px;
  font-weight: 600;
  font-size: 20px;
  transition: background 0.15s ease;
}
.nav-menu a:hover { background: rgba(255,255,255,0.05); }
.nav-menu a.active { background: var(--brand-3); }

/* Header & content spacing */
.header, .content, .table-wrapper { margin-top: 85px; }

.header {
  padding: 16px 24px;
  background: transparent;
  border-bottom: 1px solid rgba(0,0,0,0.03);
}
.page-title { font-size: 28px; font-weight:800; margin: 0; color: var(--ink); }
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
.content {
  padding: 20px 24px;
  max-width: 2000px;
  margin-left: auto;
  margin-right: auto;
}

/* Search & Filter Form */
form {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
  margin-bottom: 16px;
  align-items: center;
}
.search-input {
  padding: 8px 12px;
  border-radius: 8px;
  border: 1px solid var(--line);
  background: var(--panel);
  font-size: 0.95rem;
}
.year-filter-btn, .btn {
  padding: 8px 14px;
  border-radius: 8px;
  border: none;
  background: var(--brand-2);
  color: #f9f9f9;
  cursor: pointer;
  font-weight: 600;
}
.year-filter-btn:hover, .btn:hover { background: var(--brand-3); }


/* Table wrapper */
.table-wrapper {
  width: 100%;
  padding: 0;
  box-shadow: none;
  border-radius: 0;
  overflow-x: auto;
}

.table-wrapper table {
  width: 100%;
  border-collapse: collapse;
  background: #fff;
  border-radius: 0;
}

.table-wrapper thead th {
  background: linear-gradient(180deg, var(--brand-2), var(--brand));
  color: #f9f9f9;
  padding: 12px 14px;
  text-align: left;
  border: none;
}

.table-wrapper thead th:first-child  { border-top-left-radius: 10px;  }
.table-wrapper thead th:last-child   { border-top-right-radius: 10px; }

.table-wrapper tbody td {
  padding: 12px 14px;
  border-top: 1px solid var(--border);
  background: #fff;
}

.table-wrapper tbody tr:hover td { background: var(--highlight); transition: background 0.12s; }

/* Responsive */
@media (max-width: 900px) {
  .table-wrapper table { min-width: 600px; }
}
@media (max-width: 640px) {
  .table-wrapper table { min-width: 480px; }
  .nav-menu { display: none; }
}
