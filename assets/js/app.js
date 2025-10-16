/*
import 'bootstrap/dist/js/bootstrap.bundle.min.js'; 
import './register-sw.js'; 


* Welcome to your app's main JavaScript file!
*
* This file will be included onto the page via the importmap() Twig function,
* which should already be in your base.html.twig.
import './styles/app.css';
*/
console.log('app.js chargé'); // Test


document.addEventListener('DOMContentLoaded', () => {
    const items = document.querySelectorAll('.benefits-item');

    const observer = new IntersectionObserver(entries => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const index = parseInt(entry.target.dataset.index);
                if (index % 2 === 0) {
                    entry.target.classList.add('slide-right');
                } else {
                    entry.target.classList.add('slide-left');
                }
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.5 });

    items.forEach(item => observer.observe(item));
});


// MARQUE NOTIF COMME LUE
document.addEventListener('DOMContentLoaded', () => {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const notif = entry.target;
                const notifId = notif.dataset.id;

                // Envoi fetch POST pour marquer comme lue
                fetch(`/notification/${notifId}/read`, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Content-Type': 'application/json'
                    }
                }).then(res => res.json())
                  .then(data => {
                      if (data.success) {
                          const badge = notif.querySelector('.badge.bg-warning');
                          if (badge) badge.remove(); // retire le badge "Non lue"
                      }
                  });

                observer.unobserve(notif); // on ne l'observe plus après lecture
            }
        });
    }, { threshold: 0.5 }); // 50% de la notif visible

    document.querySelectorAll('.notification-item').forEach(item => observer.observe(item));
});

function toggleForm(id) {
    const div = document.getElementById("form-" + id);
    div.style.display = div.style.display === "none" ? "block" : "none";
}

document.addEventListener('click', function(e) {
    const btn = e.target.closest('.toggle-recurring');
    if (!btn) return;

    e.stopPropagation(); // si nécessaire pour le collapse
    const id = btn.dataset.id;

    const isRecurringInput = document.getElementById('isRecurring-' + id);
    const deadlineField = document.getElementById('deadlineField-' + id);
    const startDateField = document.getElementById('startDateField-' + id);
    const frequencyField = document.getElementById('frequencyField-' + id);

    if (!isRecurringInput || !deadlineField || !startDateField || !frequencyField) return;

    const isRecurring = isRecurringInput.value === "1";

    if (isRecurring) {
        isRecurringInput.value = 0;
        btn.textContent = "Répéter cette action dans le temps";
        deadlineField.style.display = '';
        startDateField.style.display = 'none';
        frequencyField.style.display = 'none';
    } else {
        isRecurringInput.value = 1;
        btn.textContent = "Définir une date limite unique";
        deadlineField.style.display = 'none';
        startDateField.style.display = '';
        frequencyField.style.display = '';
    }
});





document.addEventListener('DOMContentLoaded', () => {
    const canvas = document.getElementById('scoreChart');
    if (canvas) {
        const score = parseInt(canvas.dataset.score, 10);

        new Chart(canvas.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: ['Score', 'Restant'],
                datasets: [{
                    data: [score, 100 - score],
                    backgroundColor: [
                        score >= 70 ? '#4caf50' : score >= 40 ? '#ff9800' : '#f44336',
                        '#e0e0e0'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                cutout: '70%',
                responsive: false,
                plugins: {
                    legend: { display: false },
                    tooltip: { enabled: true },
                    beforeDraw: (chart) => {
                        const width = chart.width;
                        const height = chart.height;
                        const ctx = chart.ctx;
                        ctx.restore();
                        const fontSize = (height / 114).toFixed(2);
                        ctx.font = fontSize + "em sans-serif";
                        ctx.textBaseline = "middle";
                        const text = sscore + "%";
                        const textX = Math.round((width - ctx.measureText(text).width) / 2);
                        const textY = height / 2;
                        ctx.fillStyle = '#000';
                        ctx.fillText(text, textX, textY);
                        ctx.save();
                    }
                },
                animation: {
                    animateRotate: true,
                    animateScale: true
                }
            }
        });
    }
});

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.user-action-toggle').forEach(el => {
        el.addEventListener('click', () => {
            const id = el.getAttribute('data-action-id');
            const content = document.getElementById('contentAction' + id);
            const wrapper = document.querySelector('.img-wrap[data-action-id="' + id + '"]');
            if (!wrapper) return;

            const overlay = wrapper.querySelector('.action-overlay');
            const image = wrapper.querySelector('img');

            if (!content.classList.contains('d-none')) {
                // contenu visible → revenir à image + overlay
                content.classList.add('d-none');
                image.classList.remove('d-none');
                overlay.classList.remove('d-none');
            } else {
                // afficher contenu → cacher image + overlay
                content.classList.remove('d-none');
                image.classList.add('d-none');
                overlay.classList.add('d-none');
            }
        });
    });
});

document.addEventListener('DOMContentLoaded', () => {
    const toggles = document.querySelectorAll('.feeling-toggle-title');

    toggles.forEach(toggle => {
        toggle.addEventListener('click', () => {
            const feelingId = toggle.dataset.feelingId;
            const content = document.getElementById(`contentFeeling${feelingId}`);
            
            // Affiche uniquement si c'est caché
            if (content.classList.contains('d-none')) {
                content.classList.remove('d-none');
                // Scroll vers le contenu
                content.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
            else {
                content.classList.add('d-none')
            }
        });
    });
});

document.addEventListener('DOMContentLoaded', () => {
    const toggles = document.querySelectorAll('#user-action-toggle');
    toggles.forEach(toggle => {
        toggle.addEventListener('click', () => {
            const actionId = toggle.dataset.userActionId;
            const content = document.getElementById(`contentUserAction${feelingId}`);
            
            document.querySelectorAll('.user-action-details').forEach(d => d.classList.add('d-none'));
            const detail = document.getElementById('userActionDetails' + actionId);
            if(detail) detail.classList.remove('d-none');
            detail.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    });
});

