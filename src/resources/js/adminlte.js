// AdminLTE App
import './bootstrap';

// Import AdminLTE CSS
import '../css/adminlte.css';

// Initialize AdminLTE components when document is ready
document.addEventListener('DOMContentLoaded', function() {
    // Initialize AdminLTE components
    if (typeof $ !== 'undefined') {
        // Initialize the sidebar toggle
        if ($('[data-widget="pushmenu"]').length) {
            $('[data-widget="pushmenu"]').PushMenu();
        }

        // Initialize card widgets
        if ($('[data-card-widget]').length) {
            $('[data-card-widget]').CardWidget();
        }

        // Initialize treeview
        if ($('[data-widget="treeview"]').length) {
            $('[data-widget="treeview"]').Treeview();
        }
    }
});
