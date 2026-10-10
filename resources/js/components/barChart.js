import { BarController, BarElement, CategoryScale, Chart, Legend, LinearScale, Tooltip } from 'chart.js';
import { cssToken, SERIES_TOKENS } from './chartTokens';

Chart.register(BarController, BarElement, CategoryScale, LinearScale, Legend, Tooltip);

/**
 * Bar chart for the dashboard. Grouped by default; `stacked` stacks the series,
 * `horizontal` turns it sideways. `colors` = CSS token names per series.
 * The same numbers are rendered as a table next to the canvas for screen readers.
 */
export default ({ labels = [], series = [], stacked = false, horizontal = false, colors = SERIES_TOKENS }) => ({
    chart: null,

    init() {
        const valueAxis = { beginAtZero: true, stacked, ticks: { precision: 0, color: cssToken('--color-muted') }, grid: { color: cssToken('--color-line') } };
        const categoryAxis = { stacked, grid: { display: false }, ticks: { color: cssToken('--color-muted') } };

        this.chart = new Chart(this.$refs.canvas, {
            type: 'bar',
            data: {
                labels,
                datasets: series.map((item, index) => ({
                    label: item.label,
                    data: item.data,
                    backgroundColor: cssToken(colors[index % colors.length]),
                    borderRadius: stacked ? 0 : 4,
                    maxBarThickness: horizontal ? 22 : 28,
                })),
            },
            options: {
                indexAxis: horizontal ? 'y' : 'x',
                responsive: true,
                maintainAspectRatio: false,
                animation: false,
                scales: horizontal ? { x: valueAxis, y: categoryAxis } : { x: categoryAxis, y: valueAxis },
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
