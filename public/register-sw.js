console.log('register-sw.js chargé'); // Test

if ('serviceWorker' in navigator) {
    window.addEventListener('load', function() {
        navigator.serviceWorker.register('/sw.js')
        .then(function(registration) {
            console.log('✅ SW enregistré :', registration);
        })
        .catch(function(error) {
            console.error('❌ Erreur SW :', error);
        });
    });
} else {
    console.log('pas de sw');
}
