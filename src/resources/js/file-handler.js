/**
 * File handler module for feedback widget
 */

/**
 * Handles file selection and display
 * @param {FileList} files - List of selected files
 * @param {HTMLElement} fileListContainer - Container to display file list
 */
export function handleFiles(files, fileListContainer) {
    fileListContainer.innerHTML = '';
    if (files.length > 0) {
        for (const file of files) {
            const fileElement = document.createElement('div');
            fileElement.textContent = `✅ ${file.name}`;
            fileListContainer.appendChild(fileElement);
        }
    }
}

/**
 * Sets up drag and drop functionality for a drop zone
 * @param {HTMLElement} dropZone - The drop zone element
 * @param {HTMLElement} fileInput - The file input element
 * @param {Function} onFilesSelected - Callback function when files are selected
 */
export function setupDragAndDrop(dropZone, fileInput, onFilesSelected) {
    dropZone.addEventListener('click', () => fileInput.click());
    fileInput.addEventListener('change', () => onFilesSelected(fileInput.files));

    dropZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropZone.style.borderColor = '#0056b3';
    });

    dropZone.addEventListener('dragleave', () => {
        dropZone.style.borderColor = '#007bff';
    });

    dropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropZone.style.borderColor = '#007bff';
        fileInput.files = e.dataTransfer.files;
        onFilesSelected(e.dataTransfer.files);
    });
}
