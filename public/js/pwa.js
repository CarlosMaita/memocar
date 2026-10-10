// Registro del service worker y botón "Instalar app" (Alpine: x-data="pwaInstall").
(function () {
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', function () {
            navigator.serviceWorker.register('/sw.js').catch(function () {});
        });
    }

    var deferredPrompt = null;
    window.addEventListener('beforeinstallprompt', function (event) {
        event.preventDefault();
        deferredPrompt = event;
        window.dispatchEvent(new CustomEvent('pwa:available'));
    });
    window.addEventListener('appinstalled', function () {
        deferredPrompt = null;
        window.dispatchEvent(new CustomEvent('pwa:installed'));
    });

    var standalone = function () {
        return window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true;
    };
    var isIos = /iphone|ipad|ipod/i.test(navigator.userAgent) || (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1);

    document.addEventListener('alpine:init', function () {
        window.Alpine.data('pwaInstall', function () {
            return {
                canPrompt: deferredPrompt !== null,
                ios: isIos && !standalone(),
                installed: standalone(),
                get visible() { return !this.installed && (this.canPrompt || this.ios); },
                init: function () {
                    var self = this;
                    window.addEventListener('pwa:available', function () { self.canPrompt = true; });
                    window.addEventListener('pwa:installed', function () { self.installed = true; self.canPrompt = false; });
                },
                install: function () {
                    if (!deferredPrompt) { window.dispatchEvent(new CustomEvent('pwa:help')); return; }
                    var self = this;
                    deferredPrompt.prompt();
                    deferredPrompt.userChoice.finally(function () { deferredPrompt = null; self.canPrompt = false; });
                }
            };
        });
    });
})();
