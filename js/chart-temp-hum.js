
// ================= GLOBAL =================
let tempHumChart = null;

// ================= INIT CHART =================
document.addEventListener("DOMContentLoaded", function () {

    Chart.defaults.global.defaultFontFamily =
        'Nunito,-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
    Chart.defaults.global.defaultFontColor = '#858796';

    const ctx = document.getElementById("tempHumChart");
    if (!ctx) return;

    tempHumChart = new Chart(ctx, {
        type: "line",
        data: {
            labels: [],
            datasets: [
                {
                    label: "Nhiệt độ (°C)",
                    lineTension: 0.3,
                    backgroundColor: "rgba(231,74,59,0.15)",
                    borderColor: "rgba(231,74,59,1)",
                    pointRadius: 2,
                    data: []
                },
                {
                    label: "Độ ẩm (%)",
                    lineTension: 0.3,
                    backgroundColor: "rgba(78,115,223,0.15)",
                    borderColor: "rgba(78,115,223,1)",
                    pointRadius: 2,
                    data: []
                }
            ]
        },
        options: {
            maintainAspectRatio: false,
            animation: { duration: 400 },
            scales: {
                xAxes: [{ gridLines: { display: false } }],
                yAxes: [{ ticks: {
                    min: 0,
                    max: 100,
                    stepSize: 10,
                    beginAtZero: false 
                } }]
            }
        }
    });

    setTimeout(loadDhtHistory, 100);
});

// ================= LOAD DB =================
function loadDhtHistory() {
    if (!tempHumChart) {
        console.error("❌ Chart chưa sẵn sàng, không load DB");
        return;
    }

    fetch("/iot-dashboar/api/get_dht_history.php")
        .then(res => res.json())
        .then(list => {
            console.log("📦 DB trả về:", list);

            tempHumChart.data.labels = [];
            tempHumChart.data.datasets[0].data = [];
            tempHumChart.data.datasets[1].data = [];

            list.forEach(row => {
                tempHumChart.data.labels.push(row.time);
                tempHumChart.data.datasets[0].data.push(row.temperature);
                tempHumChart.data.datasets[1].data.push(row.humidity);
            });

            tempHumChart.update(0);
            console.log("✅ Chart đã load DB");
        })
        .catch(err => {
            console.error("❌ Lỗi load DB:", err);
        });
}


// ================= REALTIME PUSH =================
function pushRealtimeDht(temp, hum) {
    if (!tempHumChart) return;

    const time = new Date().toLocaleTimeString();

    tempHumChart.data.labels.push(time);
    tempHumChart.data.datasets[0].data.push(temp);
    tempHumChart.data.datasets[1].data.push(hum);

    if (tempHumChart.data.labels.length > 10) {
        tempHumChart.data.labels.shift();
        tempHumChart.data.datasets.forEach(ds => ds.data.shift());
    }

    tempHumChart.update();
}
