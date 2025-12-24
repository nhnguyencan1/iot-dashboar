console.log("Main.js loaded");
let pendingEnroll = null;
let registeredFingerIDs = new Set();
let fireLatched = false;  

/*let pendingDelete = null;
let pendingDeleteAll = false;*/

// Tạo MQTT client (SSL)
var client = new Paho.Client(
    "7ee24ad9420048c39cd09f7ffc7d4b14.s1.eu.hivemq.cloud",
    Number(8884),
    "/mqtt",
    "client-" + Math.random()
);

// Nhận message MQTT
client.onMessageArrived = function (msg) {
    console.log("Received:", msg.destinationName, msg.payloadString);

    // TEMP
    if (msg.destinationName === "fingerprint/dht") {
        try {
            let data = JSON.parse(msg.payloadString);

            document.getElementById("tempValue").innerText = data.temp + " °C";
            document.getElementById("humiValue").innerText = data.humi + " %";

            // ✅ UPDATE CHART REALTIME
            pushRealtimeDht(data.temp, data.humi);

            // ✅ SAVE DB (mỗi 10s cho nhẹ)
            if (!window.lastSave || Date.now() - window.lastSave > 10000) {
                fetch("/iot-dashboar/api/save_dht.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({
                        temp: data.temp,
                        humi: data.humi
                    })
                });
                window.lastSave = Date.now();
            }

        } catch (e) {
            console.error("DHT error", e);
        }
    }


    // ====== FLAME ======
    if (msg.destinationName === "fingerprint/flame") {
        let val = msg.payloadString;
        let card = document.getElementById("fireCard");
        let btn = document.getElementById("btnBuzzerOff");

        if (val === "fire") {
            fireLatched = true;   //

            document.getElementById("fireValue").innerText = "🔥 CHÁY";
            card.classList.remove("fire-normal");
            card.classList.add("fire-alert");

            btn.disabled = false;
        }

        
    }


    // PIR
    if (msg.destinationName === "fingerprint/pir") {
        let val = msg.payloadString;
        let card = document.getElementById("motionCard");

        if (val === "motion") {
            document.getElementById("motionValue").innerText = "👣 Có người";
            card.classList.add("motion-alert");
            card.classList.remove("motion-normal");
        } else {
            document.getElementById("motionValue").innerText = "Không người";
            card.classList.add("motion-normal");
            card.classList.remove("motion-alert");
        }
    }

    // Light sensor
    if (msg.destinationName === "fingerprint/light_sensor") {
        let val = msg.payloadString;
        let card = document.getElementById("lightCard");

        if (val === "bright") {
            document.getElementById("lightValue").innerText = "💡 Sáng";
            card.classList.add("light-bright");
            card.classList.remove("light-dark");
            sendCommand("light4_off");
        } else {
            document.getElementById("lightValue").innerText = "Tối";
            card.classList.add("light-dark");
            card.classList.remove("light-bright");
            sendCommand("light4_on");

        }
    }

    // Đèn
    // ====== ĐÈN 1 ======
    if (msg.destinationName === "fingerprint/light1/state") {
        let btn = document.getElementById("btnLight1");

        if (msg.payloadString === "on") {
            btn.classList.add("light-on");
            btn.classList.remove("light-off");
            btn.innerHTML = '<i class="fas fa-lightbulb mr-2"></i> ĐÈN 1: ON';
            btn.dataset.state = "on";
        } else {
            btn.classList.add("light-off");
            btn.classList.remove("light-on");
            btn.innerHTML = '<i class="far fa-lightbulb mr-2"></i> ĐÈN 1: OFF';
            btn.dataset.state = "off";
        }
    }
    // ====== ĐÈN 2 ======
    if (msg.destinationName === "fingerprint/light2/state") {
        let btn = document.getElementById("btnLight2");

        if (msg.payloadString === "on") {
            btn.classList.add("light-on");
            btn.classList.remove("light-off");
            btn.innerHTML = '<i class="fas fa-lightbulb mr-2"></i> ĐÈN 2: ON';
            btn.dataset.state = "on";
        } else {
            btn.classList.add("light-off");
            btn.classList.remove("light-on");
            btn.innerHTML = '<i class="far fa-lightbulb mr-2"></i> ĐÈN 2: OFF';
            btn.dataset.state = "off";
        }
    }
    // ====== ĐÈN 3 ======
    if (msg.destinationName === "fingerprint/light3/state") {
        let btn = document.getElementById("btnLight3");

        if (msg.payloadString === "on") {
            btn.classList.add("light-on");
            btn.classList.remove("light-off");
            btn.innerHTML = '<i class="fas fa-lightbulb mr-2"></i> ĐÈN 3: ON';
            btn.dataset.state = "on";
        } else {
            btn.classList.add("light-off");
            btn.classList.remove("light-on");
            btn.innerHTML = '<i class="far fa-lightbulb mr-2"></i> ĐÈN 3: OFF';
            btn.dataset.state = "off";
        }
    }
    // ====== ĐÈN 4 ======
    if (msg.destinationName === "fingerprint/light4/state") {
        let btn = document.getElementById("btnLight4");

        if (msg.payloadString === "on") {
            btn.classList.add("light-on");
            btn.classList.remove("light-off");
            btn.innerHTML = '<i class="fas fa-lightbulb mr-2"></i> ĐÈN 4: ON';
            btn.dataset.state = "on";
        } else {
            btn.classList.add("light-off");
            btn.classList.remove("light-on");
            btn.innerHTML = '<i class="far fa-lightbulb mr-2"></i> ĐÈN 4: OFF';
            btn.dataset.state = "off";
        }
    }
    // ====== DOOR STATE ======
    if (msg.destinationName === "fingerprint/door/state") {
        let btn = document.getElementById("btnOpenDoor");

        if (msg.payloadString === "open") {
            btn.classList.remove("btn-primary");
            btn.classList.add("btn-danger");
            btn.innerHTML = '<i class="fas fa-door-open mr-2"></i> CỬA: ĐANG MỞ';
            btn.dataset.state = "open";
        } else {
            btn.classList.remove("btn-danger");
            btn.classList.add("btn-primary");
            btn.innerHTML = '<i class="fas fa-door-closed mr-2"></i> CỬA: ĐÃ ĐÓNG';
            btn.dataset.state = "close";
        }
    }
    
    // ====== ENROLL ACK ======
    if (msg.destinationName === "fingerprint/enroll/ack") {

        let payload = msg.payloadString;

        if (payload.startsWith("OK:")) {
            let id = payload.split(":")[1];

            if (pendingEnroll && pendingEnroll.id == id) {
                fetch("/iot-dashboar/api/save_fingerprint.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify(pendingEnroll)
                }).then(() => {
                    loadFingerList();   
                });

                alert("Enroll thành công: " + pendingEnroll.name);
                pendingEnroll = null;
            }
        }
    }
    // ====== DELETE ACK ======
    if (msg.destinationName === "fingerprint/delete/ack") {
        alert("ESP32 đã xóa vân tay: " + msg.payloadString);
        setTimeout(loadFingerList, 1000);

        /*console.log("DELETE ACK:", msg.payloadString);

        if (msg.payloadString.startsWith("OK:")) {
            let id = msg.payloadString.split(":")[1];

            if (pendingDelete && String(pendingDelete) === String(id)) {
                fetch("/iot-dashboar/api/delete_fingerprint.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({ id: id })
                }).then(() => {
                    loadFingerList();
                    alert("Đã xóa vân tay ID " + id);
                });

                pendingDelete = null;
            }
        }*/
    }
    // ====== DELETE ALL ACK ======
    if (msg.destinationName === "fingerprint/delete_all/ack") {
        alert("ESP32 đã xóa TOÀN BỘ vân tay");
        setTimeout(loadFingerList, 1000);
      
        /* console.log("DELETE ALL ACK:", msg.payloadString);

        if (msg.payloadString === "OK" && pendingDeleteAll) {

            fetch("/iot-dashboar/api/delete_all_fingerprints.php", {
                method: "POST"
            }).then(() => {
                loadFingerList();
                alert("Đã xóa TOÀN BỘ vân tay");
            });

            pendingDeleteAll = false;
        }*/
    }


    if (msg.destinationName === "fingerprint/id") {
        console.log("Matched ID:", msg.payloadString);

        setTimeout(loadFingerprintLogs, 300);
        sendCommand("door_open");
    }



};

// Mất kết nối
client.onConnectionLost = function (response) {
    console.log("Connection lost:", response.errorMessage);
};

// Kết nối MQTT
client.connect({
    useSSL: true,
    userName: "nguyen",
    password: "Nguyen123",
    onSuccess: function () {
        console.log("MQTT connected!");

        // SUBSCRIBE đúng theo ESP32
        client.subscribe("fingerprint/dht");
        client.subscribe("fingerprint/pir");
        client.subscribe("fingerprint/flame");
        client.subscribe("fingerprint/light_sensor");
        client.subscribe("fingerprint/status"); 
        client.subscribe("fingerprint/log"); 
        client.subscribe("fingerprint/id");
        client.subscribe("fingerprint/light1/state");
        client.subscribe("fingerprint/light2/state");
        client.subscribe("fingerprint/light3/state");
        client.subscribe("fingerprint/light4/state");
        client.subscribe("fingerprint/door/state");
        client.subscribe("fingerprint/enroll/ack");
        client.subscribe("fingerprint/delete/ack");
        client.subscribe("fingerprint/delete_all/ack");

        client.subscribe("iot/test");

    },
    onFailure: function (e) {
        console.error("Connect failed:", e);
    }
});


// ===============================
//  BUTTON + INPUT HANDLING
// ===============================
document.addEventListener("DOMContentLoaded", function () {

    loadFingerList();
    loadFingerprintLogs();

    function pulseButton(btn) {
        let original = btn.className;
        btn.className = "btn btn-success btn-block";
        setTimeout(() => {
            btn.className = original;
        }, 200);
    }

    // ---------- ENROLL ----------
    document.getElementById("btnEnroll").onclick = function () {
        let id = document.getElementById("fingerID").value.trim();
        let name = document.getElementById("fingerName").value.trim();

        if (!name) {
            alert("Vui lòng nhập tên người dùng!");
            return;
        }

        if (!id || id <= 0) {
            alert("Vui lòng nhập ID > 0 để Enroll!");
            return;
        }

        if (registeredFingerIDs.has(String(id))) {
            alert(`ID ${id} đã được đăng ký rồi!\nVui lòng chọn ID khác.`);
            return;
        }

        pulseButton(this);

        pendingEnroll = { id: id, name: name };

        sendCommand(`enroll:${id}`);
        alert("Đang enroll vân tay, vui lòng đặt tay...");
    };

    // ---------- DELETE ----------
    document.getElementById("btnDelete").onclick = function () {
        let id = document.getElementById("fingerID").value;
        if (!id || id < 0) return alert("ID không hợp lệ");
        pulseButton(this);

        if (!confirm(`Xóa vân tay ID ${id}?`)) return;
        //pendingDelete = id; 

        sendCommand(`delete:${id}`);
        alert("Đang xóa vân tay ID " + id);
    };




    // ---------- DELETE ALL ----------
    document.getElementById("btnDeleteAll").onclick = function () {
        if (!confirm("XÓA TOÀN BỘ VÂN TAY?")) return;

        //pendingDeleteAll = true;
        sendCommand("delete_all");

        alert("Đã gửi lệnh xóa toàn bộ, đang chờ ESP32 xác nhận...");
    };



    // ====== ĐÈN 1 ======
    document.getElementById("btnLight1").onclick = function () {
        let state = this.dataset.state;
        if (state === "on") sendCommand("light1_off");
        else sendCommand("light1_on");
    };
    // ====== ĐÈN 2 ======
    document.getElementById("btnLight2").onclick = function () {
        let state = this.dataset.state;
        if (state === "on") sendCommand("light2_off");
        else sendCommand("light2_on");
    };
    // ====== ĐÈN 3 ======
    document.getElementById("btnLight3").onclick = function () {
        let state = this.dataset.state;
        if (state === "on") sendCommand("light3_off");
        else sendCommand("light3_on");
    };
    // ====== ĐÈN 4 ======
    document.getElementById("btnLight4").onclick = function () {
        let state = this.dataset.state;
        if (state === "on") sendCommand("light4_off");
        else sendCommand("light4_on");
    };
    // ====== OPEN DOOR ======
    document.getElementById("btnOpenDoor").onclick = function () {
        if (this.dataset.state === "open") return;
        sendCommand("door_open");
    };
    document.getElementById("btnClearFingerLogs").onclick = function () {

        if (!confirm("XÓA TOÀN BỘ LỊCH SỬ QUÉT VÂN TAY?")) return;

        fetch("/iot-dashboar/api/delete_fingerprint_logs.php", {
            method: "POST"
        })
        .then(res => res.json())
        .then(result => {
            if (result.status === "ok") {
                alert("Đã xóa lịch sử quét vân tay");
                loadFingerprintLogs(); // reload bảng
            } else {
                alert("Xóa thất bại!");
            }
        })
        .catch(err => {
            console.error(err);
            alert("Lỗi server");
        });
    };

    // ====== TẮT CÒI BÁO CHÁY ======
    document.getElementById("btnBuzzerOff").onclick = function () {
        if (!confirm("Xác nhận TẮT còi báo cháy?")) return;

        sendCommand("buzzer_off");

        // 🔁 RESET TRẠNG THÁI CHÁY TRÊN WEB
        fireLatched = false;

        let card = document.getElementById("fireCard");
        document.getElementById("fireValue").innerText = "Bình thường";
        card.classList.remove("fire-alert");
        card.classList.add("fire-normal");

        console.log("Sent: buzzer_off");
    };

    
});

// Gửi MQTT đúng topic của ESP32
function sendCommand(cmd) {
    if (!client.isConnected()) {
        console.warn("MQTT not ready, drop:", cmd);
        return;
    }

    let msg = new Paho.Message(cmd);
    msg.destinationName = "fingerprint/cmd";
    client.send(msg);
}

function loadFingerList() {
    fetch("/iot-dashboar/api/get_fingerprints.php?ts=" + Date.now())
        .then(res => res.json())
        .then(list => {
            let ul = document.getElementById("fingerList");
            ul.innerHTML = "";

            registeredFingerIDs.clear(); // reset cache

            if (list.length === 0) {
                ul.innerHTML = `<li class="list-group-item text-muted">Chưa có vân tay</li>`;
                return;
            }

            list.forEach(f => {
                registeredFingerIDs.add(String(f.id)); // 🔥 lưu ID

                let li = document.createElement("li");
                li.className = "list-group-item d-flex justify-content-between";
                li.innerHTML = `<span>ID ${f.id}</span><strong>${f.name}</strong>`;
                ul.appendChild(li);
            });
        });
}


function loadFingerprintLogs() {
    fetch("/iot-dashboar/api/get_fingerprint_logs.php?ts=" + Date.now())
        .then(res => res.json())
        .then(list => {
            let tbody = document.getElementById("fingerLogTable");
            tbody.innerHTML = "";

            if (list.length === 0) {
                tbody.innerHTML = `<tr><td colspan="4" class="text-muted text-center">Chưa có lịch sử</td></tr>`;
                return;
            }

            list.forEach(row => {
                tbody.innerHTML += `
                    <tr>
                        <td>${row.created_at}</td>
                        <td>${row.finger_id}</td>
                        <td>${row.finger_name}</td>
                        <td><span class="badge badge-success">${row.event}</span></td>
                    </tr>`;
            });
        });
}

// TEST GIẢ LẬP
//setInterval(() => {
  //  let temp = (25 + Math.random() * 5).toFixed(1);
    //let hum  = (60 + Math.random() * 10).toFixed(1);

  //  document.getElementById("tempValue").innerText = temp + " °C";
   // document.getElementById("humiValue").innerText = hum + " %";

  //  updateTempHumChart(temp, hum);
//}, 2000);
//updateTempHumChart(data.temp, data.hum);
function updateDateTime() {
    const now = new Date();

    const time = now.toLocaleTimeString("vi-VN", {
        hour: "2-digit",
        minute: "2-digit",
        second: "2-digit"
    });

    const date = now.toLocaleDateString("vi-VN", {
        day: "2-digit",
        month: "2-digit",
        year: "numeric"
    });

    document.getElementById("timeValue").innerText = time;
    document.getElementById("dateValue").innerText = date;
}

setInterval(updateDateTime, 1000);
updateDateTime();
