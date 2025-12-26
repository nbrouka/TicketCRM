import { validateStep } from './validation.js';
import { showStep, clearErrorMessages, displayValidationErrors, updateSubmitButtonState } from './ui-updater.js';
import { handleFiles, setupDragAndDrop } from './file-handler.js';

document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('multistep-form');
    const steps = document.querySelectorAll('.step');
    const nextBtn = document.getElementById('next-btn');
    const backBtn = document.getElementById('back-btn');
    const submitBtn = document.getElementById('submit-btn');
    const formDescription = document.getElementById('form-description');

    let currentStep = 0;

    nextBtn.addEventListener('click', () => {
        if (validateStep(currentStep, steps)) {
            currentStep++;
            showStep(currentStep, steps, backBtn, nextBtn, submitBtn, formDescription);
        }
    });

    backBtn.addEventListener('click', () => {
        currentStep--;
        showStep(currentStep, steps, backBtn, nextBtn, submitBtn, formDescription);
    });

    const dropZone = document.getElementById('drop-zone');
    const fileInput = document.getElementById('attachments');
    const fileListContainer = document.getElementById('file-list');

    // Set up drag and drop functionality
    setupDragAndDrop(dropZone, fileInput, (files) => {
        handleFiles(files, fileListContainer);
    });

    // Initialize the first step
    showStep(currentStep, steps, backBtn, nextBtn, submitBtn, formDescription);

    // Handle form submission
    form.addEventListener('submit', function (e) {
        e.preventDefault();

        // Clear previous error messages
        clearErrorMessages(form);

        // Get form data
        const formData = new FormData(this);

        // Show loading state
        const submitButton = this.querySelector('button[type="submit"]');
        const originalText = submitButton.textContent;
        updateSubmitButtonState(submitButton, true, originalText);

        // Send feedback via AJAX to create a ticket
        fetch('/api/feedback', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
            .then(response => {
                return response.json().then(data => {
                    if (response.ok) {
                        // Create and show success message in a section instead of alert
                        const successMessage = document.createElement('div');
                        successMessage.className = 'alert alert-success mt-3';
                        successMessage.id = 'success-message';
                        successMessage.textContent = data.message || 'Feedback submitted successfully!';

                        // Insert the success message before the form
                        const formContainer = document.querySelector('.form-container');
                        formContainer.insertBefore(successMessage, formContainer.firstChild);

                        // Auto-hide the success message after 5 seconds
                        setTimeout(() => {
                            successMessage.remove();
                        }, 5000);
                        this.reset();
                        currentStep = 0;
                        showStep(currentStep, steps, backBtn, nextBtn, submitBtn, formDescription);
                    } else {
                        // Handle validation errors or other issues
                        if (data.errors) {
                            displayValidationErrors(data.errors, form);
                        } else {
                            const errorMsg = data.message || 'An error occurred while submitting feedback.';
                            // Create and show error message in a section instead of alert
                            const errorMessage = document.createElement('div');
                            errorMessage.className = 'alert alert-danger mt-3';
                            errorMessage.id = 'error-message';
                            errorMessage.textContent = errorMsg;

                            // Insert the error message before the form
                            const formContainer = document.querySelector('.form-container');
                            formContainer.insertBefore(errorMessage, formContainer.firstChild);

                            // Auto-hide the error message after 5 seconds
                            setTimeout(() => {
                                errorMessage.remove();
                            }, 5000);
                        }
                    }
                });
            })
            .catch(error => {
                console.error('Error:', error);
                // Create and show error message in a section instead of alert
                const errorMessage = document.createElement('div');
                errorMessage.className = 'alert alert-danger mt-3';
                errorMessage.id = 'error-message';
                errorMessage.textContent = 'An error occurred while submitting feedback.';

                // Insert the error message before the form
                const formContainer = document.querySelector('.form-container');
                formContainer.insertBefore(errorMessage, formContainer.firstChild);

                // Auto-hide the error message after 5 seconds
                setTimeout(() => {
                    errorMessage.remove();
                }, 5000);
            })
            .finally(() => {
                // Reset button state
                updateSubmitButtonState(submitButton, false, originalText);
            });
    });
});
