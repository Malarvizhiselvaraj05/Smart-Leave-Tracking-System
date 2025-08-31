<?php
header("Content-type: text/css");
?>
:root{
  --brand: #004687;
  --brand-2: #005A9C;
  --panel: #fffaf3;
  --ink: black;
  --muted: #6fa1c4ff;
  --line: #c1d1ddff;
}

body {
    font-family: 'Segoe UI', sans-serif;
    margin: 0;
    background-color: var(--bg);
    color: var(--ink);
}

/* Top navigation — slightly slimmer and balanced */
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
/* nav left text */
.nav-left .welcome-text {
  font-size: 1.05em;
    opacity: 0.95;
    font-weight: bold;
    font-size: 22px;
}

.nav-menu {
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: bold;
    font-size: 20px;
}


/* menu links */
.nav-menu a {
  color: #fff;
  text-decoration: none;
  padding: 8px 12px;
  border-radius: 6px;
  font-weight: 700;
  font-size: 20px;
}
.nav-menu a:hover { background: rgba(255,255,255,0.06); }
.nav-menu a.active { background: rgba(255,255,255,0.08); box-shadow: 0 2px 6px rgba(0,0,0,0.08); }

.main-content, .content-wrapper {
  padding-top: 78px;
  gap: 40px;
}

.form-container {
  max-width: 920px;
  margin: 30px auto;   /* increased top-bottom margin */
  background: #fff;
  border-radius: 10px;
  padding: 62px;       /* more inner padding */
  box-shadow: 0 8px 30px rgba(0,0,0,0.06);
  color: var(--ink);
}

.form-container h2 {
  text-align: center;
  margin: 0 0 18px;
  font-size: 1.4rem;
  color: var(--brand);
  font-weight: 700;
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 35px; 
  column-gap: 90px;   
  align-items: start;
  margin-bottom: 0px;
}

.form-row.full { grid-template-columns: 1fr; }

.form-row label {
  display: block;
  font-weight: 600;
  font-size: 18px;
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
  text-transform: capitalize; 
  font-size: 16px;
  gap: 40px;
  color: var(--ink);
  box-shadow: none;
  transition: border-color .15s ease, box-shadow .12s ease;
}

.form-container input[readonly] {
  background: #fbfbfb;              /* subtle muted white for readonly */
}

.form-container input:focus,
.form-container select:focus,
.form-container textarea:focus {
  outline: none;
  border-color: var(--brand-2);
  box-shadow: 0 6px 18px rgba(139,94,60,0.09);
}

/* Reason textarea */
.form-container textarea {
  min-height: 120px;
  resize: vertical;
}

/* Buttons area */
.form-container .actions {
  display: flex;
  gap: 20px;           /* spacing between buttons */
  margin-top: 22px;
}

.btn-request-leave, .btn-medium-action, .form-container button[type="submit"]{
  padding: 10px 16px;
  border-radius: 8px;
  border: none;
  cursor: pointer;
  font-weight: 700;
}

.btn-request-leave, .form-container button[type="submit"] {
  background: var(--brand-2);
  color: #fff;
}

.btn-medium-action {
  background: #8b5e3c;
  color: #f9f9f9;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  border: 1px solid #e6e6e6;
}

.form-container .back-btn {
  background: #8b5e3c !important;
  color: #fff !important;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  border: none;
}

.form-container .back-btn:hover {
  background: #8b5e3c !important;   /* stays same on hover */
  color: #fff !important;
}

/* Hover states */
.btn-request-leave:hover, .form-container button[type="submit"]:hover { background: var(--brand); }
.btn-medium-action:hover { background: var(--brand-2); }

/* Responsive: stack to single column below 760px */
@media (max-width: 760px) {
  .form-row { grid-template-columns: 1fr; }
  .form-container { padding: 18px; margin: 12px; }
  .main-content, .content-wrapper { padding-top: 78px; }
}
/* Actions area */
.form-container .actions {
  display: flex;
  gap: 20px;
  margin-top: 22px;
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
/* Common style for all buttons */
.form-container button {
  padding: 10px 16px;
  border-radius: 8px;
  border: none;
  cursor: pointer;
  font-weight: 700;
  background: #004687;  /* Blue */
  color: #fff;
  transition: background 0.3s ease;
}

.form-container button:hover {
  background:#004687;  /* Darker blue on hover */
}
