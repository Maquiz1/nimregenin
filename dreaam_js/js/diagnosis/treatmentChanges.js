function addRow(id = 'new', date = '', drug = '', changes = '', reason = '', specify = '', diagnosis_id = '', enrollment_id = '', facility_id = '', staff_id = '') {
    let table = document.getElementById("treatmentChangesTable");
    let row = table.insertRow();
    row.dataset.id = id; // Store row ID for updates

    row.innerHTML = `
        <td><input type="date" name="date[]" class="form-control" value="${date}" required></td>
        <td><input type="text" name="drug[]" class="form-control" value="${drug}" required></td>
        <td>
            <label><input type="radio" name="changes_${id}" value="1" ${changes === "1" ? "checked" : ""}> Dose lowered</label><br>
            <label><input type="radio" name="changes_${id}" value="2" ${changes === "2" ? "checked" : ""}> Dose increased</label><br>
            <label><input type="radio" name="changes_${id}" value="3" ${changes === "3" ? "checked" : ""}> Interrupted</label><br>
            <label><input type="radio" name="changes_${id}" value="4" ${changes === "4" ? "checked" : ""}> Withdrawn</label>
        </td>
        <td>
            <label><input type="radio" name="reason_${id}" value="1" ${reason === "1" ? "checked" : ""}> Drug resistance</label><br>
            <label><input type="radio" name="reason_${id}" value="2" ${reason === "2" ? "checked" : ""}> Drug intolerance</label><br>
            <label><input type="radio" name="reason_${id}" value="96" ${reason === "96" ? "checked" : ""} onchange="toggleSpecify(this, '${id}')"> Other, specify</label>
            <input type="text" name="specify[]" class="form-control" value="${specify}" id="specify_${id}" ${reason !== "96" ? "style='display:none;'" : ""}>
        </td>
        <td>
            <input type="hidden" name="diagnosis_id" value="${diagnosis_id}">
            <input type="hidden" name="enrollment_id" value="${enrollment_id}">
            <input type="hidden" name="facility_id" value="${facility_id}">
            <input type="hidden" name="staff_id" value="${staff_id}">
            <button type="button" class="btn btn-success" onclick="saveRow(this)">Save</button>
            <button type="button" class="btn btn-danger" onclick="deleteRow(this, '${id}')">Delete</button>
        </td>
    `;
}

function toggleSpecify(element, id) {
    let specifyInput = document.getElementById(`specify_${id}`);
    specifyInput.style.display = element.value === "96" ? "block" : "none";
}


// ✅ Save Row (Helper function for saving a row from the table)

function saveRow(button) {
    let row = button.closest("tr");
    let id = row.dataset.id === 'new' ? "" : row.dataset.id;
    let diagnosis_id = row.querySelector("[name='diagnosis_id']").value;
    let enrollment_id = row.querySelector("[name='enrollment_id']").value;
    let date = row.querySelector("[name='date[]']").value;
    let drug = row.querySelector("[name='drug[]']").value;
    let changes = row.querySelector(`input[name="changes_${id}"]:checked`)?.value || "";
    let reason = row.querySelector(`input[name="reason_${id}"]:checked`)?.value || "";
    let specify = row.querySelector("[name='specify[]']").value;
    let facility_id = row.querySelector("[name='facility_id']").value;
    let staff_id = row.querySelector("[name='staff_id']").value;

    if (!date || !drug || !changes || !reason) {
        alert("All fields except 'Specify' are required!");
        return;
    }

    if (id) {
        updateTreatmentChange(id, diagnosis_id, enrollment_id, date, drug, changes, reason, specify, facility_id, staff_id);
    } else {
        addTreatmentChange(diagnosis_id, enrollment_id, date, drug, changes, reason, specify, facility_id, staff_id);
    }
}



// ✅ Add a Single Treatment Change Record (New Entry)
function addTreatmentChange(diagnosis_id, enrollment_id, date, drug, changes, reason, specify, facility_id, staff_id) {
    let formData = new FormData();
    formData.append("diagnosis_id", diagnosis_id);
    formData.append("enrollment_id", enrollment_id);
    formData.append("date", date);
    formData.append("drug", drug);
    formData.append("changes", changes);
    formData.append("reason", reason);
    formData.append("specify", specify);
    formData.append("facility_id", facility_id);
    formData.append("staff_id", staff_id);

    fetch("save_treatment_change.php", {
        method: "POST",
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.status === "success") {
                alert("Record added successfully!");
                loadTreatmentChanges(); // Refresh the table after adding a new record
            } else {
                alert(data.message || "Error adding record.");
            }
        })
        .catch(error => console.error("Error:", error));
}


// ✅ Delete a Single Treatment Change Record
function deleteTreatmentChange(id) {
    let formData = new FormData();
    formData.append("id", id);

    fetch("delete_treatment_change.php", {
        method: "POST",
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.status === "success") {
                alert("Record deleted successfully!");
                loadTreatmentChanges(); // Refresh the table after deleting the record
            } else {
                alert(data.message || "Error deleting record.");
            }
        })
        .catch(error => console.error("Error:", error));
}


// ✅ Update a Treatment Change Record
function updateTreatmentChange(id, diagnosis_id, enrollment_id, date, drug, changes, reason, specify, facility_id, staff_id) {
    let formData = new FormData();
    formData.append("id", id);
    formData.append("diagnosis_id", diagnosis_id);
    formData.append("enrollment_id", enrollment_id);
    formData.append("date", date);
    formData.append("drug", drug);
    formData.append("changes", changes);
    formData.append("reason", reason);
    formData.append("specify", specify);
    formData.append("facility_id", facility_id);
    formData.append("staff_id", staff_id);

    fetch("save_treatment_change.php", {
        method: "POST",
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.status === "success") {
                alert("Record updated successfully!");
                loadTreatmentChanges(); // Refresh the table after updating the record
            } else {
                alert(data.message || "Error updating record.");
            }
        })
        .catch(error => console.error("Error:", error));
}


// ✅ Load All Treatment Change Records
function loadTreatmentChanges() {
    fetch("get_treatment_changes.php")
        .then(response => response.json())
        .then(data => {
            let table = document.getElementById("treatmentChangesTable");
            table.innerHTML = ""; // Clear table before loading

            data.forEach(item => {
                addRow(item.id, item.date, item.drug, item.changes, item.reason, item.specify);
            });
        })
        .catch(error => console.error("Error fetching data:", error));
}

loadTreatmentChanges()