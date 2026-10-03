
class DJVHeader extends HTMLElement {
  connectedCallback() {
    // Determine the base path based on current URL
    const depth = window.location.pathname.split('/').filter(Boolean).length - 1;
    const base = depth > 0 && !window.location.pathname.endsWith('.html') ? '../'.repeat(depth) : '/';
    
    // We inject the header and mobile nav
    this.innerHTML = `
      <header class="site-header" id="site-header" role="banner">
  <div class="header-inner">

    <!-- Logo -->
    <a href="/" class="site-logo" aria-label="Dharma Jyothi Vedika — Home">
      <div class="logo-symbol">
        <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg" class="logo-flame" aria-hidden="true">
          <!-- Diya base -->
          <ellipse cx="20" cy="30" rx="12" ry="4.5" fill="#C89432" opacity="0.9"/>
          <path d="M10 30 Q8 28 9 25 L31 25 Q32 28 30 30 Z" fill="#7A2419"/>
          <path d="M12 25 Q11 22 14 20 Q18 22 20 22 Q22 22 26 20 Q29 22 28 25 Z" fill="#9A7020"/>
          <!-- Lotus petals -->
          <ellipse cx="20" cy="21" rx="4" ry="6" fill="none" stroke="#C89432" stroke-width="1" opacity="0.6" transform="rotate(-20 20 21)"/>
          <ellipse cx="20" cy="21" rx="4" ry="6" fill="none" stroke="#C89432" stroke-width="1" opacity="0.6" transform="rotate(20 20 21)"/>
          <!-- Flame -->
          <path d="M20 22 Q17 17 19 12 Q20 8 20 6 Q21 9 22 12 Q23.5 16 21 20 Q20.5 21 20 22 Z" fill="url(#flameGrad)"/>
          <path d="M20 22 Q18.5 19 19.5 15 Q20 13 20 12 Q20.5 14 21 16 Q21.5 19 20 22 Z" fill="url(#innerFlame)" opacity="0.8"/>
          <defs>
            <linearGradient id="flameGrad" x1="20" y1="22" x2="20" y2="6" gradientUnits="userSpaceOnUse">
              <stop offset="0%" stop-color="#C89432"/>
              <stop offset="60%" stop-color="#E7B75A"/>
              <stop offset="100%" stop-color="#FFF0CC"/>
            </linearGradient>
            <linearGradient id="innerFlame" x1="20" y1="22" x2="20" y2="12" gradientUnits="userSpaceOnUse">
              <stop offset="0%" stop-color="#FFFFFF" stop-opacity="0.6"/>
              <stop offset="100%" stop-color="#FFFFFF" stop-opacity="0"/>
            </linearGradient>
          </defs>
        </svg>
      </div>
      <div class="logo-text">
        <span class="brand-en">DHARMA JYOTHI VEDIKA</span>
        <span class="brand-te">ధర్మ జ్యోతి వేదిక</span>
      </div>
    </a>

    <!-- Desktop Navigation -->
    <nav class="header-nav" aria-label="Main Navigation">
      <a href="/" class="nav-link active" id="nav-home">Home</a>
      <a href="/panchangam/" class="nav-link" id="nav-panchangam">Panchangam</a>
      <a href="/festivals/" class="nav-link" id="nav-festivals">Festivals</a>
      <a href="/muhurtham/" class="nav-link" id="nav-muhurtham">Muhurtham</a>
      <a href="/pooja/" class="nav-link" id="nav-pooja">Pooja</a>
      <a href="/mantras/" class="nav-link" id="nav-mantras">Mantras</a>
      <a href="/temples/" class="nav-link" id="nav-temples">Temples</a>
      <a href="/articles/" class="nav-link" id="nav-articles">Articles</a>
      <a href="/calendar/" class="nav-link" id="nav-calendar">Calendar</a>
      <a href="/services/" class="nav-link" id="nav-services">Services</a>
    </nav>

    <!-- Header Actions -->
    <div class="header-actions">
      <button class="header-btn header-btn--icon" id="search-open-btn" aria-label="Search">
        <span aria-hidden="true">🔍</span>
      </button>
      <button class="lang-btn" id="lang-toggle-btn" aria-label="Switch Language">
        తెలుగు
      </button>
      <a href="/panchangam/today/" class="header-btn header-btn--panchangam" id="header-today-btn">
        <span aria-hidden="true">🕉</span>
        Today's Panchangam
      </a>
      <button class="menu-toggle" id="menu-toggle-btn" aria-label="Open navigation menu" aria-expanded="false">
        ☰
      </button>
    </div>
  </div>
</header>
      <!-- Mobile Navigation -->
<div class="mobile-nav" id="mobile-nav" role="dialog" aria-modal="true" aria-label="Navigation menu">
  <div class="mobile-nav-drawer" id="mobile-nav-drawer">
    <nav aria-label="Mobile Navigation">
      <a href="/" class="nav-link active">🏠 Home</a>
      <a href="/panchangam/" class="nav-link">📅 Panchangam</a>
      <a href="/festivals/" class="nav-link">🎊 Festivals</a>
      <a href="/muhurtham/" class="nav-link">⏰ Muhurtham</a>
      <a href="/pooja/" class="nav-link">🪔 Pooja</a>
      <a href="/mantras/" class="nav-link">📿 Mantras</a>
      <a href="/temples/" class="nav-link">🛕 Temples</a>
      <a href="/articles/" class="nav-link">📰 Articles</a>
      <a href="/calendar/" class="nav-link">🗓 Calendar</a>
      <a href="/services/" class="nav-link">✨ Services</a>
    </nav>
  </div>
</div>
    `;

    // Fix links for local file system viewing (file:// protocol)
    const isLocal = window.location.protocol === 'file:';
    if (isLocal) {
      let rootPath = '';
      const pathParts = window.location.pathname.split('/');
      const websiteIndex = pathParts.lastIndexOf('website');
      if (websiteIndex !== -1) {
        rootPath = pathParts.slice(0, websiteIndex + 1).join('/');
      }
      this.querySelectorAll('a').forEach(a => {
        let href = a.getAttribute('href');
        if (href && href.startsWith('/')) {
          if (href === '/') {
            a.href = rootPath + '/index.html';
          } else if (href.endsWith('/')) {
            a.href = rootPath + href + 'index.html';
          } else {
            a.href = rootPath + href;
          }
        }
      });
    }

    // Re-bind the mobile menu toggle logic since it's dynamically inserted
    const menuToggle = this.querySelector('#menu-toggle-btn');
    const mobileNav = this.querySelector('#mobile-nav');
    
    if (menuToggle && mobileNav) {
      const openMobileNav = () => {
        mobileNav.classList.add('open');
        menuToggle.setAttribute('aria-expanded', 'true');
        menuToggle.textContent = '✕';
        document.body.style.overflow = 'hidden';
      };
      const closeMobileNav = () => {
        mobileNav.classList.remove('open');
        menuToggle.setAttribute('aria-expanded', 'false');
        menuToggle.textContent = '☰';
        document.body.style.overflow = '';
      };
      
      menuToggle.addEventListener('click', () => {
        mobileNav.classList.contains('open') ? closeMobileNav() : openMobileNav();
      });
      
      mobileNav.addEventListener('click', (e) => {
        if (e.target === mobileNav) closeMobileNav();
      });
      
      // Close on escape key
      document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && mobileNav.classList.contains('open')) {
          closeMobileNav();
        }
      });
    }

    // Scroll logic
    const headerEl = this.querySelector('#site-header');
    if (headerEl) {
      window.addEventListener('scroll', () => {
        headerEl.classList.toggle('scrolled', window.scrollY > 10);
      }, { passive: true });
    }
  }
}

customElements.define('djv-header', DJVHeader);
