(function () {
  var XAMPP_BASE = 'http://localhost/SDstaffing/';
  var XAMPP_JOBS = XAMPP_BASE + 'job.php';
  var XAMPP_ADMIN = XAMPP_BASE + 'admin-login.html';

  function isLiveServer() {
    return location.port === '5500' || location.protocol === 'file:';
  }

  function jobsPageUrl() {
    if (isLiveServer()) return XAMPP_JOBS;
    if (location.hostname === 'localhost' || location.hostname === '127.0.0.1') {
      if (location.pathname.indexOf('/SDstaffing') !== -1) {
        try {
          return new URL('job.php', location.href).href;
        } catch (e) {
          return XAMPP_JOBS;
        }
      }
      return XAMPP_JOBS;
    }
    return 'job.php';
  }

  window.SD_JOBS_URL = jobsPageUrl();

  function fixJobLinks() {
    document.querySelectorAll('a[href="job.php"], a[href="job.html"]').forEach(function (a) {
      a.setAttribute('href', window.SD_JOBS_URL);
    });
  }

  function fixPhpForms() {
    if (!isLiveServer()) return;
    document.querySelectorAll('form[action^="php/"]').forEach(function (f) {
      f.setAttribute('action', XAMPP_BASE + f.getAttribute('action'));
    });
  }

  function showLiveServerBanner() {
    if (!isLiveServer()) return;
    var bar = document.createElement('div');
    bar.setAttribute('role', 'alert');
    bar.style.cssText =
      'position:fixed;top:0;left:0;right:0;z-index:99999;background:#0f172a;color:#fff;' +
      'padding:10px 16px;font:14px/1.4 Outfit,sans-serif;text-align:center;box-shadow:0 4px 12px rgba(0,0,0,.2);';
    bar.innerHTML =
      'Live Server cannot run PHP or MySQL. ' +
      'Start <strong>Apache</strong> + <strong>MySQL</strong> in XAMPP, then open ' +
      '<a href="' + XAMPP_BASE + '" style="color:#f9a8d4;text-decoration:underline">localhost/SDstaffing</a> ' +
      'or run <strong>start-website.bat</strong>. ' +
      '<a href="' + XAMPP_ADMIN + '" style="color:#93c5fd;margin-left:8px">Admin login</a>';
    document.body.style.paddingTop = '48px';
    document.body.insertBefore(bar, document.body.firstChild);
  }

  function init() {
    fixJobLinks();
    fixPhpForms();
    showLiveServerBanner();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
