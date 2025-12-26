/**
 * Validation module for feedback widget
 */

/**
 * Validates a form step
 * @param {number} stepIndex - Index of the step to validate
 * @param {NodeList} steps - List of all step elements
 * @returns {boolean} - Whether the step is valid
 */
export function validateStep(stepIndex, steps) {
    const currentStepEl = steps[stepIndex];
    const requiredInputs = currentStepEl.querySelectorAll('[required]');
    let isValid = true;

    // Reset all borders and error messages
    const allInputs = currentStepEl.querySelectorAll('input, textarea');
    allInputs.forEach(input => {
        input.style.borderColor = '#ccc';
    });

    // Hide all error messages initially
    const errorElements = currentStepEl.querySelectorAll('.error-message');
    errorElements.forEach(element => {
        element.style.display = 'none';
        element.textContent = '';
    });

    // Validate required inputs
    for (const input of requiredInputs) {
        // Check if field is empty
        if (!input.value.trim()) {
            input.style.borderColor = '#dc3545';
            // Show error message for empty field
            const errorElement = document.getElementById(`${input.name}-error`);
            if (errorElement) {
                errorElement.textContent = `${input.name.charAt(0).toUpperCase() + input.name.slice(1)} is required.`;
                errorElement.style.display = 'block';
                errorElement.style.color = '#dc3545';
                errorElement.style.marginTop = '5px';
                errorElement.style.fontSize = '0.85rem';
            }
            isValid = false;
            continue; // Continue to check other fields
        }

        // Additional validation based on field type
        if (input.type === 'email') {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(input.value.trim())) {
                input.style.borderColor = '#dc3545';
                // Show error message for invalid email
                const errorElement = document.getElementById(`${input.name}-error`);
                if (errorElement) {
                    errorElement.textContent = 'Please enter a valid email address.';
                    errorElement.style.display = 'block';
                    errorElement.style.color = '#dc3545';
                    errorElement.style.marginTop = '5px';
                    errorElement.style.fontSize = '0.85rem';
                }
                isValid = false;
                continue;
            }
        } else if (input.name === 'theme') {
            // Theme should be at least 5 characters and max 255
            if (input.value.trim().length < 5) {
                input.style.borderColor = '#dc3545';
                // Show error message for short theme
                const errorElement = document.getElementById(`${input.name}-error`);
                if (errorElement) {
                    errorElement.textContent = 'Theme must be at least 5 characters.';
                    errorElement.style.display = 'block';
                    errorElement.style.color = '#dc3545';
                    errorElement.style.marginTop = '5px';
                    errorElement.style.fontSize = '0.85rem';
                }
                isValid = false;
                continue;
            } else if (input.value.trim().length > 255) {
                input.style.borderColor = '#dc3545';
                // Show error message for long theme
                const errorElement = document.getElementById(`${input.name}-error`);
                if (errorElement) {
                    errorElement.textContent = 'Theme must not exceed 255 characters.';
                    errorElement.style.display = 'block';
                    errorElement.style.color = '#dc3545';
                    errorElement.style.marginTop = '5px';
                    errorElement.style.fontSize = '0.85rem';
                }
                isValid = false;
                continue;
            }
        } else if (input.name === 'text') {
            // Text should be at least 10 characters
            if (input.value.trim().length < 10) {
                input.style.borderColor = '#dc3545';
                // Show error message for short text
                const errorElement = document.getElementById(`${input.name}-error`);
                if (errorElement) {
                    errorElement.textContent = 'Text must be at least 10 characters.';
                    errorElement.style.display = 'block';
                    errorElement.style.color = '#dc3545';
                    errorElement.style.marginTop = '5px';
                    errorElement.style.fontSize = '0.85rem';
                }
                isValid = false;
                continue;
            }
        } else if (input.name === 'phone') {
            // Phone should follow E.164 format: + followed by 1-15 digits, first digit after + cannot be 0
            const e164Regex = /^\+[1-9]\d{1,14}$/;
            if (!e164Regex.test(input.value.trim())) {
                input.style.borderColor = '#dc3545';
                // Show error message for invalid E.164 format
                const errorElement = document.getElementById(`${input.name}-error`);
                if (errorElement) {
                    errorElement.textContent = 'Phone number must follow E.164 format (e.g., +1234567890).';
                    errorElement.style.display = 'block';
                    errorElement.style.color = '#dc3545';
                    errorElement.style.marginTop = '5px';
                    errorElement.style.fontSize = '0.85rem';
                }
                isValid = false;
                continue;
            }
        }

        // If validation passes, set border to normal
        input.style.borderColor = '#28a745'; // Green border for valid field
    }

    // Validate optional inputs that still need format validation
    const optionalInputs = currentStepEl.querySelectorAll('input:not([required]), textarea:not([required])');
    for (const input of optionalInputs) {
        if (input.value.trim() !== '') { // Only validate if the optional field has a value
            if (input.type === 'email') {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(input.value.trim())) {
                    input.style.borderColor = '#dc3545';
                    // Show error message for invalid email
                    const errorElement = document.getElementById(`${input.name}-error`);
                    if (errorElement) {
                        errorElement.textContent = 'Please enter a valid email address.';
                        errorElement.style.display = 'block';
                        errorElement.style.color = '#dc3545';
                        errorElement.style.marginTop = '5px';
                        errorElement.style.fontSize = '0.85rem';
                    }
                    isValid = false;
                    continue;
                }
            }
        }
    }

    return isValid;
}

/**
 * Creates a validation error message element
 * @param {string} message - Error message to display
 * @param {string} color - Color for the error message
 * @returns {HTMLElement} - The error message element
 */
export function createErrorMessage(message, color = '#dc3545') {
    const errorElement = document.createElement('div');
    errorElement.className = 'error-message';
    errorElement.textContent = message;
    errorElement.style.display = 'block';
    errorElement.style.color = color;
    errorElement.style.marginTop = '5px';
    errorElement.style.fontSize = '0.85rem';
    return errorElement;
}
