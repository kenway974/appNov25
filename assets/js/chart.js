import Chart from 'chart.js/auto';

document.addEventListener('DOMContentLoaded', () => {
    const canvas = document.getElementById('scoreChart');

    if (canvas && typeof score !== 'undefined') {

        // Plugin pour texte centré
        const centerTextPlugin = {
            id: 'centerText',
            afterDatasetDraw(chart) {
                const { ctx, chartArea: { width, height } } = chart;
                ctx.save();
                const fontSize = (height / 4).toFixed(2); // ajustable
                ctx.font = `${fontSize}em sans-serif`;
                ctx.textBaseline = "middle";
                const text = score + "%";
                const textX = width / 2;
                const textY = height / 2;
                ctx.fillStyle = '#000';
                ctx.textAlign = 'center';
                ctx.fillText(text, textX, textY);
                ctx.restore();
            }
        };

        new Chart(canvas, {
            type: 'doughnut',
            data: {
                labels: ['Done', 'Remaining'],
                datasets: [{
                    data: [score, 100 - score],
                    backgroundColor: ['#4caf50', '#e0e0e0']
                }]
            },
            options: {
                cutout: '70%',
                responsive: false,
                plugins: {
                    legend: { display: false },
                    tooltip: { enabled: true }
                },
                animation: {
                    animateRotate: true,
                    animateScale: true
                }
            },
            plugins: [centerTextPlugin]
        });
    }
});
