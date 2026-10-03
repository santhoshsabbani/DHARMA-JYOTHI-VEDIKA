
class DJVFooter extends HTMLElement {
  connectedCallback() {
    // Determine depth for assets if needed
    const isLocal = window.location.protocol === 'file:';
    const pathParts = window.location.pathname.split('/');
    const websiteIndex = pathParts.lastIndexOf('website');
    
    let rootPath = '';
    if (websiteIndex !== -1 || isLocal) {
      if (websiteIndex !== -1) {
        rootPath = pathParts.slice(0, websiteIndex + 1).join('/');
      }
    }

    const resolveLink = (href) => {
      if (href.startsWith('/')) {
        if (href === '/') return rootPath + (isLocal ? '/index.html' : '/');
        if (href.endsWith('/')) return rootPath + href + (isLocal ? 'index.html' : '');
        return rootPath + href;
      }
      return href;
    };

    this.innerHTML = `
      <footer class="site-footer">
        <div class="footer-inner">
          <div class="footer-brand">
            <h2 class="footer-title">DHARMA JYOTHI VEDIKA</h2>
            <p class="footer-tagline">ధర్మ జ్యోతి వేదిక</p>
            <p class="footer-desc">Your trusted daily Hindu Panchangam and devotional platform.</p>
          </div>
          <div class="footer-links">
            <h3>Quick Links</h3>
            <nav>
              <a href="${resolveLink('/panchangam/')}">Panchangam</a>
              <a href="${resolveLink('/festivals/')}">Festivals</a>
              <a href="${resolveLink('/muhurtham/')}">Muhurtham</a>
              <a href="${resolveLink('/pooja/')}">Pooja</a>
              <a href="${resolveLink('/mantras/')}">Mantras</a>
            </nav>
          </div>
          <div class="footer-links">
            <h3>Explore</h3>
            <nav>
              <a href="${resolveLink('/temples/')}">Temples</a>
              <a href="${resolveLink('/articles/')}">Articles</a>
              <a href="${resolveLink('/calendar/')}">Calendar</a>
              <a href="${resolveLink('/services/')}">Services</a>
            </nav>
          </div>
          <div class="footer-links">
            <h3>Legal</h3>
            <nav>
              <a href="#">About Us</a>
              <a href="#">Contact</a>
              <a href="#">Privacy Policy</a>
              <a href="#">Terms of Service</a>
            </nav>
          </div>
        </div>
        <div class="footer-bottom">
          <p>&copy; ${new Date().getFullYear()} Dharma Jyothi Vedika. All rights reserved.</p>
        </div>
      </footer>
    `;
  }
}
customElements.define('djv-footer', DJVFooter);
