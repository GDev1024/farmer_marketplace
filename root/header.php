<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Grenada Farmers Marketplace</title>
<link rel="stylesheet" href="assets/css/variables.css">
<link rel="stylesheet" href="assets/css/base.css">
<link rel="stylesheet" href="assets/css/components.css">
<link rel="stylesheet" href="assets/css/layout.css">
<link rel="stylesheet" href="assets/css/marketplace.css">
<link rel="stylesheet" href="assets/css/ui-fixes.css">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@300;400;700;900&display=swap" rel="stylesheet">
<link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🌾</text></svg>">
<meta name="theme-color" content="#3d5a3a">
</head>
<body class="page">
<!-- Skip Links for Accessibility -->
<div class="skip-links">
  <a href="#main-content" class="skip-link">Skip to main content</a>
  <a href="#navigation" class="skip-link">Skip to navigation</a>
  <?php if($isLoggedIn): ?>
    <a href="#user-menu" class="skip-link">Skip to user menu</a>
  <?php endif; ?>
</div>

<header class="header" role="banner">
  <div class="header-container">
    <nav class="nav" role="navigation" aria-label="Main navigation" id="navigation">
      <a href="index.php" class="nav-brand" aria-label="Grenada Farmers Marketplace - Home">
        <span class="nav-brand-icon" aria-hidden="true">🌾</span>
        <span class="logo-text">Grenada Farmers Marketplace</span>
      </a>


    </nav>
  </div>
</header>

<!-- Loading overlay -->
<div id="loading-overlay" class="loading-overlay" style="display: none;" role="status" aria-label="Loading">
  <div class="loading-content">
    <div class="loading-spinner spinner-lg">
      <div class="spinner"></div>
    </div>
    <p class="loading-message">Loading...</p>
  </div>
</div>

<main class="page-main" id="main-content" role="main">
<div id="alert-container" aria-live="polite" aria-atomic="true" class="alert-container"></div>

<!-- ARIA Live Regions for Dynamic Content -->
<div id="live-region-polite" aria-live="polite" aria-atomic="true" class="sr-only"></div>
<div id="live-region-assertive" aria-live="assertive" aria-atomic="true" class="sr-only"></div>
<div id="status-region" role="status" aria-live="polite" class="sr-only"></div>
<div id="loading-announcements" aria-live="polite" aria-atomic="true" class="sr-only"></div>