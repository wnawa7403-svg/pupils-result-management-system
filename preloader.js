// preloader.js
(function() {
  // Create preloader HTML element
  function createPreloader() {
    const preloaderDiv = document.createElement('div');
    preloaderDiv.id = 'global-preloader';
    preloaderDiv.className = 'preloader-overlay';
    preloaderDiv.innerHTML = `
      <div>
        <div class="dual-ring-spinner" role="status" aria-label="Loading content">
          <div class="breathing-scale">
            <div class="ring-outer"></div>
            <div class="ring-inner"></div>
          </div>
          <div class="ripple"></div>
          <div class="ripple ripple-delay"></div>
        </div>
        <div class="loading-text">
          <span>loading</span>
          <div class="loading-dots">
            <span></span>
            <span></span>
            <span></span>
          </div>
        </div>
      </div>
    `;
    document.body.appendChild(preloaderDiv);
    return preloaderDiv;
  }

  // Hide preloader with animation
  function hidePreloader(preloader) {
    if (preloader) {
      preloader.classList.add('hidden');
      setTimeout(() => {
        if (preloader.parentNode) {
          preloader.remove();
        }
      }, 600);
    }
  }

  // Initialize
  const preloader = createPreloader();
  const startTime = Date.now();
  const minimumLoadTime = 1200; // Show spinner for at least 1.2 seconds

  // Wait for everything to load
  window.addEventListener('load', function() {
    const elapsed = Date.now() - startTime;
    const remainingTime = Math.max(0, minimumLoadTime - elapsed);
    setTimeout(() => hidePreloader(preloader), remainingTime);
  });

  // Fallback: hide after 5 seconds max
  setTimeout(() => hidePreloader(preloader), 5000);
})();