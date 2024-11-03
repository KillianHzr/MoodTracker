import { Chart, LineController, LineElement, PointElement, LinearScale, Title, CategoryScale, BarController, BarElement, RadialLinearScale, RadarController, ArcElement, PieController } from 'chart.js';

Chart.register(LineController, LineElement, PointElement, LinearScale, Title, CategoryScale);
Chart.register(BarController, BarElement, CategoryScale, LinearScale);
Chart.register(RadarController, RadialLinearScale, PointElement, LineElement);
Chart.register(PieController, ArcElement, Title);

let currentPeriod = 'month';

function fetchMoodData(period) {
    fetch(`/insights/data?period=${period}`)
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(data => {
            updateCharts(data);
        })
        .catch(error => {
            console.error('Error fetching mood data:', error);
        });
}

function fetchAdminMoodData(period) {
    fetch(`/admin/insights/data?period=${period}`)
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(data => {
            updateCharts(data);
        })
        .catch(error => {
            console.error('Error fetching admin mood data:', error);
        });
}

function calculateMedian(values) {
    if (values.length === 0) return null;
    values.sort((a, b) => a - b);
    const middle = Math.floor(values.length / 2);

    if (values.length % 2 === 0) {
        return (values[middle - 1] + values[middle]) / 2;
    } else {
        return values[middle];
    }
}

function sampleDataWithMedian(dates, moods, period) {
    const sampledDates = [];
    const sampledMoods = [];
    let segmentSize;

    switch (period) {
        case 'week':
            segmentSize = 1;
            break;
        case 'month':
            segmentSize = 7;
            break;
        case 'quarter':
            segmentSize = Math.ceil(dates.length / 12);
            break;
        case 'semester':
            segmentSize = Math.ceil(dates.length / 6);
            break;
        case 'year':
            segmentSize = Math.ceil(dates.length / 12);
            break;
        default:
            segmentSize = Math.ceil(dates.length / 24);
    }

    for (let i = 0; i < dates.length; i += segmentSize) {
        const dateSegment = dates.slice(i, i + segmentSize);
        const moodSegment = moods.slice(i, i + segmentSize);

        if (moodSegment.length > 0) {
            sampledDates.push(dateSegment[0]);
            sampledMoods.push(calculateMedian(moodSegment));
        }
    }

    return { dates: sampledDates, moods: sampledMoods };
}

function updateCharts(data) {
    let sampledData;

    if (currentPeriod !== 'week') {
        sampledData = sampleDataWithMedian(data.dates, data.moods, currentPeriod);
    } else {
        sampledData = { dates: data.dates, moods: data.moods };
    }

    linearChart.data.labels = sampledData.dates;
    linearChart.data.datasets[0].data = sampledData.moods;
    linearChart.update();

    histogramChart.data.datasets[0].data = data.moodFrequency;
    histogramChart.update();

    radarChart.data.datasets[0].data = data.moodFrequency;
    radarChart.update();

    pieChart.data.datasets[0].data = data.moodFrequency;
    pieChart.update();
}

document.addEventListener('DOMContentLoaded', () => {
    const insightsPage = document.getElementById('insights-page');
    const adminInsightsPage = document.getElementById('admin-insights-page');

    const ctx1 = document.getElementById('linearChart').getContext('2d');
    const ctx2 = document.getElementById('histogramChart').getContext('2d');
    const ctx3 = document.getElementById('radarChart').getContext('2d');
    const ctx4 = document.getElementById('pieChart').getContext('2d');

    window.linearChart = new Chart(ctx1, {
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                label: 'Humeur quotidienne',
                backgroundColor: 'rgba(59, 130, 246, 0)',
                borderColor: 'rgb(59, 130, 246)',
                data: [],
                tension: 0.4
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: false,
                    ticks: {
                        stepSize: 1,
                        callback: function(value) {
                            return Number.isInteger(value) ? value : null;
                        }
                    },
                    suggestedMin: 1,
                    suggestedMax: 5
                }
            }
        }
    });

    window.histogramChart = new Chart(ctx2, {
        type: 'bar',
        data: {
            labels: [1, 2, 3, 4, 5],
            datasets: [{
                label: 'Fréquence de chaque niveau d\'humeur',
                backgroundColor: [
                    'rgb(220, 38, 38)',
                    'rgb(249, 115, 22)',
                    'rgb(250, 204, 21)',
                    'rgb(163, 230, 53)',
                    'rgb(22, 163, 74)'
                ],
                data: [] }]
        }
    });

    window.radarChart = new Chart(ctx3, {
        type: 'radar',
        data: {
            labels: [1, 2, 3, 4, 5],
            datasets: [{
                label: 'Distribution des humeurs',
                backgroundColor: [
                    'rgb(220, 38, 38)',
                    'rgb(249, 115, 22)',
                    'rgb(250, 204, 21)',
                    'rgb(163, 230, 53)',
                    'rgb(22, 163, 74)'
                ],
                borderColor: 'rgb(59, 130, 246)', data: []
            }]
        }
    });

    window.pieChart = new Chart(ctx4, {
        type: 'pie',
        data: {
            labels: [1, 2, 3, 4, 5],
            datasets: [{
                label: 'Distribution des humeurs',
                backgroundColor: [
                    'rgb(220, 38, 38)',
                    'rgb(249, 115, 22)',
                    'rgb(250, 204, 21)',
                    'rgb(163, 230, 53)',
                    'rgb(22, 163, 74)'
                ],
                data: []
            }]
        }
    });

    const defaultButton = document.querySelector('.period-button[data-period="month"]');
    if (defaultButton) {
        defaultButton.classList.add('bg-blue-700');
    }

    if (insightsPage) {
        fetchMoodData(currentPeriod);
    } else if (adminInsightsPage) {
        fetchAdminMoodData(currentPeriod);
    }

    document.querySelectorAll('.period-button').forEach(button => {
        button.addEventListener('click', () => {
            currentPeriod = button.getAttribute('data-period');

            document.querySelectorAll('.period-button').forEach(btn => btn.classList.remove('bg-blue-700'));

            button.classList.add('bg-blue-700');

            if (insightsPage) {
                fetchMoodData(currentPeriod);
            } else if (adminInsightsPage) {
                fetchAdminMoodData(currentPeriod);
            }
        });
    });
});
