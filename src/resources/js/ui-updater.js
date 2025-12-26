/**
 * UI Updater module for feedback widget
 */

/**
 * Shows a specific step in the form
 * @param {number} stepIndex - Index of the step to show
 * @param {NodeList} steps - List of all step elements
 * @param {HTMLElement} backBtn - Back button element
 * @param {HTMLElement} nextBtn - Next button element
 * @param {HTMLElement} submitBtn - Submit button element
 * @param {HTMLElement} formDescription - Description element
 */
export function showStep(stepIndex, steps, backBtn, nextBtn, submitBtn, formDescription) {
    steps.forEach((step, index) => {
        step.classList.remove('active');
        if (index === stepIndex) {
            step.classList.add('active');
        }
    });
    updateNavigationButtons(stepIndex, steps, backBtn, nextBtn, submitBtn);
    updateDescription(stepIndex, formDescription);
}

/**
 * Updates the navigation buttons based on the current step
 * @param {number} stepIndex - Index of the current step
 * @param {NodeList} steps - List of all step elements
 * @param {HTMLElement} backBtn - Back button element
 * @param {HTMLElement} nextBtn - Next button element
 * @param {HTMLElement} submitBtn - Submit button element
 */
export function updateNavigationButtons(stepIndex, steps, backBtn, nextBtn, submitBtn) {
    backBtn.style.display = (stepIndex === 0) ? 'none' : 'inline-block';

    if (stepIndex === steps.length - 1) {
        nextBtn.style.display = 'none';
        submitBtn.style.display = 'inline-block';
    } else {
        nextBtn.style.display = 'inline-block';
        submitBtn.style.display = 'none';
    }
}

/**
 * Updates the form description based on the current step
 * @param {number} stepIndex - Index of the current step
 * @param {HTMLElement} formDescription - Description element to update
 */
export function updateDescription(stepIndex, formDescription) {
    const descriptions = [
        "Step 1 of 3: Contact Information",
        "Step 2 of 3: Details",
        "Step 3 of 3: Attachments (optional)"
    ];
    formDescription.textContent = descriptions[stepIndex];
}

/**
 * Clears all error messages and resets input borders
 * @param {HTMLElement} form - The form element containing the inputs
 */
export function clearErrorMessages(form) {
    const errorElements = form.querySelectorAll('.error-message');
    errorElements.forEach(element => {
        element.textContent = '';
        element.style.display = 'none';
        element.style.color = '#dc3545';
        element.style.marginTop = '5px';
        element.style.fontSize = '0.85rem';
    });

    // Reset input borders
    const allInputs = form.querySelectorAll('input, textarea');
    allInputs.forEach(input => {
        input.style.borderColor = '#ccc';
    });

    // Clear the general error message container
    const generalErrorContainer = document.getElementById('error-message');
    if (generalErrorContainer) {
        generalErrorContainer.style.display = 'none';
        generalErrorContainer.innerHTML = '';
    }
}

/**
 * Displays validation errors
 * @param {Object} errors - Object containing field errors
 * @param {HTMLElement} form - The form element containing the inputs
 */
export function displayValidationErrors(errors, form) {
    for (const field in errors) {
        // Map the field name from the server to the form field name
        const fieldName = field === 'text' ? 'text' : field; // text field in form is now named 'text'
        const errorElement = document.getElementById(`${field}-error`);
        if (errorElement) {
            errorElement.textContent = errors[field][0]; // Display the first error for each field
            errorElement.style.display = 'block';
            errorElement.style.color = '#dc3545';
            errorElement.style.marginTop = '5px';
            errorElement.style.fontSize = '0.85rem';

            // Highlight the input field with an error
            const inputElement = document.getElementById(fieldName);
            if (inputElement) {
                inputElement.style.borderColor = '#dc3545';
            }
        }
    }
}

/**
 * Displays all validation errors in a general error message container
 * @param {Object} errors - Object containing field errors
 * @param {HTMLElement} form - The form element containing the inputs
 */
export function displayAllValidationErrors(errors, form) {
    const generalErrorContainer = document.getElementById('error-message');
    if (generalErrorContainer) {
        // Clear previous errors
        generalErrorContainer.innerHTML = '';

        // Create a list of all errors
        const errorList = document.createElement('ul');
        errorList.style.margin = '0';
        errorList.style.paddingLeft = '20px';

        for (const field in errors) {
            const errorItem = document.createElement('li');
            errorItem.textContent = `${field.charAt(0).toUpperCase() + field.slice(1)}: ${errors[field][0]}`;
            errorList.appendChild(errorItem);
        }

        generalErrorContainer.appendChild(errorList);
        generalErrorContainer.style.display = 'block';
    }
}

/**
 * Updates button state during form submission
 * @param {HTMLElement} submitButton - The submit button element
 * @param {boolean} isSubmitting - Whether the form is submitting
 * @param {string} originalText - Original button text
 */
export function updateSubmitButtonState(submitButton, isSubmitting, originalText = 'Submit') {
    if (isSubmitting) {
        submitButton.textContent = 'Submitting...';
        submitButton.disabled = true;
    } else {
        submitButton.textContent = originalText;
        submitButton.disabled = false;
    }
}
