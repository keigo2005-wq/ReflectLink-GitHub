const chartCanvas = document.getElementById("positionChart");
const chartData = window.positionChartData;

if (chartCanvas && chartData) {
    new Chart(chartCanvas, {
        type: "bar",

        data: {
            labels: chartData.labels,

            datasets: [
                {
                    label: "コメント件数",
                    data: chartData.counts,
                    backgroundColor: "#2864a8",
                    borderColor: "#174574",
                    borderWidth: 1
                }
            ]
        },

        options: {
            responsive: true,
            maintainAspectRatio: false,

            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
}