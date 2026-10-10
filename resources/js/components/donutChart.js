import { ArcElement, Chart, DoughnutController, Legend, Tooltip } from 'chart.js';
import { cssToken, SERIES_TOKENS } from './chartTokens';

Chart.register(ArcElement, DoughnutController, Legend, Tooltip);

/**
 * Doughnut chart: share of each label (e.g. verified records per module).
 */
export default ({ labels = [], data = [], colors = SERIES_TOKENS }) => ({
    chart: null,

    init() {
        this.chart = new Chart(this.$refs.canvas, {
            type: 'doughnut',
            data: {
                labels,
                datasets: [{
                    data,
                    backgroundColor: labels.map((label, index) => cssToken(colors[index % colors.length])),
                    borderColor: cssToken('--color-surface'),
                    borderWidth: 2,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: false,
                cutout: '68%',
                plugins: {
                    legend: { position: 'bottom', labels: { boxWidth: 10, boxHeight: 10, color: cssToken('--color-ink') } },
                },
            },
        });
    },

    destroy() {
        this.chart?.destroy();
    },
});
