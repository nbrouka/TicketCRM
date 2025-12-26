/**
 * Utility functions for feedback widget
 */

/**
 * Creates an element with specified attributes and children
 * @param {string} tagName - The tag name of the element to create
 * @param {Object} attributes - Object containing attributes to set
 * @param {Array} children - Array of child elements or text nodes
 * @returns {HTMLElement} - The created element
 */
export function createElement(tagName, attributes = {}, children = []) {
    const element = document.createElement(tagName);

    // Set attributes
    for (const [key, value] of Object.entries(attributes)) {
        if (key.startsWith('on')) {
            // Event handler
            element[key.toLowerCase()] = value;
        } else if (key === 'style') {
            // Style object
            Object.assign(element.style, value);
        } else {
            // Regular attribute
            element.setAttribute(key, value);
        }
    }

    // Append children
    for (const child of children) {
        if (typeof child === 'string') {
            element.appendChild(document.createTextNode(child));
        } else {
            element.appendChild(child);
        }
    }

    return element;
}

/**
 * Creates an element with specified attributes and children
 * @param {string} tagName - The tag name of the element to create
 * @param {Object} attributes - Object containing attributes to set
 * @param {Array} children - Array of child elements or text nodes
 * @returns {HTMLElement} - The created element
 */
export function createElement(tagName, attributes = {}, children = []) {
    const element = document.createElement(tagName);

    // Set attributes
    for (const [key, value] of Object.entries(attributes)) {
        if (key.startsWith('on')) {
            // Event handler
            element[key.toLowerCase()] = value;
        } else if (key === 'style') {
            // Style object
            Object.assign(element.style, value);
        } else {
            // Regular attribute
            element.setAttribute(key, value);
        }
    }

    // Append children
    for (const child of children) {
        if (typeof child === 'string') {
            element.appendChild(document.createTextNode(child));
        } else {
            element.appendChild(child);
        }
    }

    return element;
}
