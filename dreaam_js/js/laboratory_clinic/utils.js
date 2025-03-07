// Utility functions to manage element visibility and required attributes

function toggleElementVisibility(elementId, shouldShow) {
    const element = document.getElementById(elementId);
    if (element) {
        element.style.display = shouldShow ? "block" : "none";
        element.querySelectorAll("input, select").forEach(input => {
            input.required = shouldShow;
        });
    }
}

function isChecked(name, value) {
    return document.querySelector(`input[name="${name}"]:checked`)?.value === value;
}

function addEventListeners(elements, event, handler) {
    elements.forEach(element => element.addEventListener(event, handler));
}
