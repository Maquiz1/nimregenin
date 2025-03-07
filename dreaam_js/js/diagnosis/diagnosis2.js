function addRow() {
    let table = document.getElementById('treatmentChangesTable');
    let newRow = table.insertRow();
    newRow.innerHTML = `
            <td><input type="date" name="regimen_changed__date[]" class="form-control" placeholder="Enter here" /></td>
            <td><input type="text" name="regimen_added_name[]" class="form-control" placeholder="Enter here" /></td>
            <td>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="regimen_added_type[]" value="Dose lowered"> Dose lowered<br>
                    <input class="form-check-input" type="radio" name="regimen_added_type[]" value="Dose increased"> Dose increased<br>
                    <input class="form-check-input" type="radio" name="regimen_added_type[]" value="Interrupted"> Interrupted<br>
                    <input class="form-check-input" type="radio" name="regimen_added_type[]" value="Withdrawn"> Withdrawn
                </div>
            </td>
            <td>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="regimen_changed__reason[]" value="Drug resistance"> Drug resistance<br>
                    <input class="form-check-input" type="radio" name="regimen_changed__reason[]" value="Drug intolerance"> Drug intolerance<br>
                    <input class="form-check-input other-reason" type="radio" name="regimen_changed__reason[]" value="96"> Other, specify
                </div>
            </td>
            <td><input type="text" name="regimen_changed_other_reason[]" class="form-control" placeholder="Specify here" disabled /></td>
            <td><button type="button" class="btn btn-danger" onclick="removeRow(this)">Remove</button></td>
        `;
    attachReasonEventListeners();
}

function removeRow(button) {
    let row = button.parentNode.parentNode;
    row.parentNode.removeChild(row);
}

function attachReasonEventListeners() {
    document.querySelectorAll('.other-reason').forEach(radio => {
        radio.addEventListener('change', function () {
            let input = this.closest('tr').querySelector('[name="regimen_changed_other_reason[]"]');
            if (this.checked) {
                input.disabled = false;
                input.required = true;
            } else {
                input.disabled = true;
                input.required = false;
            }
        });
    });
}

attachReasonEventListeners();