// document.addEventListener("DOMContentLoaded", function () {
//     updateFormStatus();

//     // Add event listeners for validation
//     document.getElementById('date_completed').addEventListener('input', checkDateCompleted);
//     document.getElementById('date_verified').addEventListener('input', validateForm);
//     document.getElementById('screening').addEventListener('submit', validateForm);

//     let formStatusRadios = document.querySelectorAll('input[name="form_status"]');
//     formStatusRadios.forEach(radio => {
//         radio.addEventListener('change', function () {
//             if (this.value == "3" && !document.getElementById('date_completed').value) {
//                 alert("You cannot select 'Verified' until 'Completed Date' is filled.");
//                 this.checked = false;
//             } else {
//                 updateFormStatus();
//             }
//         });
//     });
// });

// function updateFormStatus() {
//     let formStatus = document.querySelector('input[name="form_status"]:checked')?.value;
//     let dateCompleted = document.getElementById('date_completed');
//     let dateVerified = document.getElementById('date_verified');

//     if (formStatus == "1") {
//         dateCompleted.disabled = true;
//         dateVerified.disabled = true;
//         dateCompleted.required = false;
//         dateVerified.required = false;
//         dateCompleted.value = "";
//         dateVerified.value = "";
//     } else if (formStatus == "2") {
//         dateCompleted.disabled = false;
//         dateVerified.disabled = true;
//         dateCompleted.required = true;
//         dateVerified.required = false;
//         dateVerified.value = "";
//     } else if (formStatus == "3") {
//         dateCompleted.disabled = true;
//         dateVerified.disabled = !dateCompleted.value;
//         dateCompleted.required = false;
//         dateVerified.required = !!dateCompleted.value;
//     }

//     checkDateCompleted();
// }

// function checkDateCompleted() {
//     let dateCompleted = document.getElementById('date_completed').value;
//     let formStatus3 = document.getElementById('form_status3');
//     let dateVerified = document.getElementById('date_verified');

//     if (!dateCompleted) {
//         dateVerified.disabled = true;
//         dateVerified.required = false;
//         dateVerified.value = "";
//         if (formStatus3?.checked) {
//             formStatus3.checked = false;
//         }
//     }
// }

// function validateForm(event) {
//     let formStatus = document.querySelector('input[name="form_status"]:checked')?.value;
//     let dateCompleted = document.getElementById('date_completed');
//     let dateVerified = document.getElementById('date_verified');
//     let dateCompletedError = document.getElementById('date_completed_error');
//     let dateVerifiedError = document.getElementById('date_verified_error');
//     let isValid = true;

//     // Clear previous errors
//     clearError(dateCompleted, dateCompletedError);
//     clearError(dateVerified, dateVerifiedError);

//     if (formStatus == "2" && !dateCompleted.value) {
//         showError(dateCompleted, dateCompletedError, "Completed date is required.");
//         isValid = false;
//     }

//     if (formStatus == "3" && !dateCompleted.value) {
//         alert("You cannot select 'Verified' until 'Completed Date' is filled.");
//         isValid = false;
//     }

//     if (formStatus == "3" && !dateVerified.value) {
//         showError(dateVerified, dateVerifiedError, "Verified date is required.");
//         isValid = false;
//     }

//     if (!isValid) {
//         event.preventDefault();
//     }
// }

// // Function to show error styling using <span>
// function showError(inputField, errorField, message) {
//     errorField.innerText = message;
//     inputField.classList.add("is-invalid");
//     inputField.style.border = "2px solid red"; // Ensure red border is applied
// }

// // Function to clear error styling using <span>
// function clearError(inputField, errorField) {
//     errorField.innerText = "";
//     inputField.classList.remove("is-invalid");
//     inputField.style.border = ""; // Remove red border if no error
// }
