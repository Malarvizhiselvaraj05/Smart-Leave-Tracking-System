<?php
header("Content-type: text/css");
?>
:root{
  --brand: #004687;
  --brand-2: #005A9C;
  --panel: #e3ecf7ff;
  --ink: black;
  --muted: #bdd8f6ff;
  --line: #005A9C;
  --accent: #f3e9dc;
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
    height: 70px;
    z-index: 1000;
    box-shadow: 0 2px 5px rgba(0,0,0,0.2);
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
/* nav left text */
.nav-left .welcome-text {
  font-weight: 700;
  font-size: 19px;
  color: var(--accent);
}

/* menu links */
.nav-menu {
  display: flex;
  gap: 10px;
    font-size: 19px;

  align-items: center;
}
.nav-menu a {
  color: var(--accent);
  text-decoration: none;
  padding: 8px 12px;
  border-radius: 6px;
  font-weight: 700;
  font-size: 19px;
  display:inline-flex;
  align-items:center;
  gap:8px;
}
.nav-menu a:hover { background: rgba(255,255,255,0.06); }
.nav-menu a.active { background: rgba(255,255,255,0.08); box-shadow: 0 2px 6px rgba(0,0,0,0.08); }

/* page content offset to account for nav */
.main-content {
  padding-top: 88px;
  min-height: calc(100vh - 88px);
  background: var(--panel);
}

/* content wrapper centers the form */
.content-wrapper {
  max-width: 980px;
  margin: 18px auto;
  padding: 0 18px;
}

/* Form container */
.form-container {
  background: #fff;
  border-radius: 10px;
  padding: 35px;
  box-shadow: 0 8px 30px rgba(0,0,0,0.06);
  color: var(--ink);
}

/* error message */
.error {
  background: #ffe9e6;
  color: #007bff;
  padding: 10px 12px;
  border-radius: 8px;
  margin-bottom: 12px;
  font-weight: 700;
  border: 1px solid rgba(139,94,60,0.06);
}

/* form grid */
.form-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 14px 40px;
  align-items: start;
}

/* full width row */
.form-grid .full-width { grid-column: 1 / -1; }

/* small groups */
.form-group label {
  display: block;
  font-weight: 600;
  font-size: 15px;
  margin-bottom: 6px;
  color: var(--ink);
}

.form-container input[type="text"],
.form-container select,
.form-container textarea {
  width: 100%;
  background: #fff;
  border: 1px solid var(--line);
  padding: 10px 12px;
  border-radius: 8px;
  font-size: 15px;
  color: var(--ink);
  transition: border-color .15s ease, box-shadow .12s ease;
}

.form-container input[readonly] {
  background: #fbfbfb;
}

.form-container input:focus,
.form-container select:focus,
.form-container textarea:focus {
  outline: none;
  border-color: var(--brand-2);
  box-shadow: 0 6px 18px rgba(139,94,60,0.09);
}

.form-container textarea { min-height: 120px; resize: vertical; }

/* buttons */
.btn-group {
  display:flex;
  gap:12px;
  align-items:center;
  margin-top:8px;
  grid-column: 1 / -1;
}

.btn {
  padding: 10px 14px;
  border-radius: 8px;
  border: none;
  cursor: pointer;
  font-weight: 700;
  background: var(--brand-2);
  color: #fff;
}

.btn[type="button"] {
  background: #004687;
  color: #fff;
}
.form-container textarea {
  width: 100%;
  background: #fff;
  border: 1px solid var(--line);
  padding: 10px 12px;
  border-radius: 8px;
  text-transform: capitalize; 
  font-size: 15px;
  color: var(--ink);
  transition: border-color .15s ease, box-shadow .12s ease;
}

.btn:hover { background: var(--brand); }

/* small screens — stack layout and compact nav */
@media (max-width: 760px) {
  .form-grid { grid-template-columns: 1fr; }
  .content-wrapper { margin: 12px; padding: 0 8px; }
  .top-nav { padding: 0 10px; height: 56px; }
  .nav-left .welcome-text { font-size: 15px; }
  .nav-menu a { font-size: 14px; padding: 6px 8px; }
  .form-container { padding: 14px; }
}
