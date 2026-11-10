// streams.js - Streams Management JavaScript
console.log('streams.js loading...');

// ================ DEBUGGING HELPER ================
function debugLog(message, data = null) {
    console.log(`[Streams Manager] ${message}`, data || '');
}

// ================ CONFIGURATION ================
const CONFIG = {
    tableId: 'list',
    sortableClass: 'sortable',
    deleteButtonClass: '.btn-danger.action-btn'
};

// ================ VANILLA JS FEATURES ================
function initializeCustomSorting() {
    debugLog('Initializing custom sorting...');
    const sortableHeaders = document.querySelectorAll(`th.${CONFIG.sortableClass}`);
    debugLog(`Found ${sortableHeaders.length} sortable headers`);
    
    if (sortableHeaders.length === 0) {
        debugLog('No sortable headers found');
        return;
    }

    let currentSortColumn = null;
    let currentSortDirection = 'asc';
    const rows = Array.from(document.querySelectorAll(`#${CONFIG.tableId} tbody tr`));
    const tbody = document.querySelector(`#${CONFIG.tableId} tbody`);
    
    debugLog(`Found ${rows.length} rows and tbody:`, !!tbody);
    
    if (rows.length === 0 || !tbody) {
        debugLog('No rows or tbody found for sorting');
        return;
    }
    
    sortableHeaders.forEach((header, index) => {
        header.style.cursor = 'pointer';
        header.addEventListener('click', function() {
            debugLog(`Sorting column ${index}, direction: ${currentSortDirection}`);
            
            // Toggle sort direction
            currentSortDirection = (currentSortColumn === index) 
                ? (currentSortDirection === 'asc' ? 'desc' : 'asc')
                : 'asc';
            currentSortColumn = index;
            
            // Update visual indicators
            sortableHeaders.forEach(h => h.classList.remove('asc', 'desc'));
            this.classList.add(currentSortDirection);
            
            // Sort and reorder rows
            rows.sort(createRowComparator(index, currentSortDirection));
            rows.forEach(row => tbody.appendChild(row));
            
            debugLog('Sorting completed');
        });
    });
    
    debugLog('Custom sorting initialized');
}

function createRowComparator(columnIndex, direction) {
    return (a, b) => {
        const aCell = a.children[columnIndex];
        const bCell = b.children[columnIndex];
        
        if (!aCell || !bCell) return 0;
        
        const aText = aCell.textContent.trim().toLowerCase();
        const bText = bCell.textContent.trim().toLowerCase();
        
        return direction === 'asc' 
            ? aText.localeCompare(bText)
            : bText.localeCompare(aText);
    };
}

// ================ JQUERY/DATATABLES FEATURES ================
function initializeDataTable() {
    debugLog('Initializing DataTable...');
    
    // Check if jQuery is available
    if (typeof jQuery === 'undefined') {
        debugLog('jQuery not found!');
        return null;
    }
    
    const $table = $(`#${CONFIG.tableId}`);
    debugLog(`Looking for table #${CONFIG.tableId}:`, $table.length);
    
    if (!$table.length) {
        debugLog('Table not found for DataTable initialization');
        return null;
    }
    
    // Check if DataTables is loaded
    if (typeof $.fn.DataTable === 'undefined') {
        debugLog('DataTables plugin not loaded!');
        return null;
    }
    
    try {
        const dataTable = $table.DataTable({
            paging: true,
            searching: true,
            ordering: true,
            responsive: true,
            pageLength: 10,
            lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
           
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search streams...",
                lengthMenu: "Show _MENU_ entries",
                info: "Showing _START_ to _END_ of _TOTAL_ entries",
                paginate: {
                    previous: "<i class='fas fa-chevron-left'></i>",
                    next: "<i class='fas fa-chevron-right'></i>"
                }
            },
            initComplete: function() {
                debugLog('DataTable initialization complete');
            }
        });
        
        debugLog('DataTable initialized successfully');
        return dataTable;
    } catch (error) {
        debugLog('DataTable initialization error:', error);
        return null;
    }
}

function setupDeleteConfirmation() {
    debugLog('Setting up delete confirmation...');
    
    if (typeof jQuery === 'undefined') {
        debugLog('jQuery not available for delete confirmation');
        return;
    }
    
    $(document).on('click', CONFIG.deleteButtonClass, function(e) {
        e.preventDefault();
        const deleteUrl = $(this).attr('href');
        const streamName = $(this).closest('tr').find('td:first').text().trim() || 'this stream';
        
        debugLog(`Delete clicked for: ${streamName}`, deleteUrl);
        
        // Check if SweetAlert is available
        if (typeof swal === 'function') {
            swal({
                title: "Are you sure?",
                text: `You are about to delete "${streamName}". This action cannot be undone!`,
                icon: "warning",
                buttons: {
                    cancel: {
                        text: "Cancel",
                        value: null,
                        visible: true,
                        className: "btn-secondary",
                        closeModal: true,
                    },
                    confirm: {
                        text: "Yes, delete it!",
                        value: true,
                        visible: true,
                        className: "btn-danger",
                        closeModal: false
                    }
                },
                dangerMode: true
            }).then((willDelete) => {
                if (willDelete) {
                    debugLog(`Redirecting to delete URL: ${deleteUrl}`);
                    window.location.href = deleteUrl;
                }
            });
        } else {
            // Fallback to native confirm
            debugLog('SweetAlert not available, using native confirm');
            if (confirm(`Are you sure you want to delete "${streamName}"?`)) {
                window.location.href = deleteUrl;
            }
        }
    });
    
    debugLog('Delete confirmation setup complete');
}

function showFlashMessages() {
    debugLog('Checking for flash messages...');
    
    // Check for messages in data attributes
    const successEl = document.querySelector('[data-success-message]');
    const errorEl = document.querySelector('[data-error-message]');
    
    if (successEl) {
        const message = successEl.getAttribute('data-success-message');
        debugLog('Found success message:', message);
        if (message && typeof swal === 'function') {
            swal("Success!", message, "success");
        }
        // Clear the attribute
        successEl.removeAttribute('data-success-message');
    }
    
    if (errorEl) {
        const message = errorEl.getAttribute('data-error-message');
        debugLog('Found error message:', message);
        if (message && typeof swal === 'function') {
            swal("Error!", message, "error");
        }
        // Clear the attribute
        errorEl.removeAttribute('data-error-message');
    }
    
    // Also check window variables (for backward compatibility)
    if (typeof window.successMessage !== 'undefined' && window.successMessage) {
        debugLog('Found window.successMessage:', window.successMessage);
        if (typeof swal === 'function') {
            swal("Success!", window.successMessage, "success");
        }
        window.successMessage = undefined;
    }
    
    if (typeof window.errorMessage !== 'undefined' && window.errorMessage) {
        debugLog('Found window.errorMessage:', window.errorMessage);
        if (typeof swal === 'function') {
            swal("Error!", window.errorMessage, "error");
        }
        window.errorMessage = undefined;
    }
}

function enhanceModalAnimations() {
    debugLog('Enhancing modal animations...');
    
    if (typeof jQuery === 'undefined') {
        debugLog('jQuery not available for modal animations');
        return;
    }
    
    $('.modal').on('show.bs.modal', function() {
        debugLog('Modal showing');
        $(this).find('.modal-content')
            .css({
                'transform': 'scale(0.95)',
                'opacity': '0',
                'transition': 'all 0.3s ease'
            });
    });
    
    $('.modal').on('shown.bs.modal', function() {
        debugLog('Modal shown');
        $(this).find('.modal-content')
            .css({
                'transform': 'scale(1)',
                'opacity': '1'
            });
    });
    
    debugLog('Modal animations enhanced');
}

// ================ MAIN INITIALIZATION ================
function initAllFeatures() {
    debugLog('=== Initializing Streams Manager ===');
    debugLog('Document ready state:', document.readyState);
    debugLog('jQuery available:', typeof jQuery !== 'undefined');
    debugLog('DataTables available:', typeof $.fn !== 'undefined' && typeof $.fn.DataTable !== 'undefined');
    debugLog('SweetAlert available:', typeof swal !== 'undefined');
    
    // Initialize vanilla JS features
    initializeCustomSorting();
    
    // Check if jQuery is loaded
    if (typeof jQuery !== 'undefined') {
        // Wait for jQuery to be ready
        $(document).ready(function() {
            debugLog('jQuery document ready fired');
            
            // Initialize DataTable
            const dataTable = initializeDataTable();
            
            // If DataTable failed to initialize or doesn't handle sorting,
            // ensure our custom sorting is active
            if (!dataTable) {
                debugLog('DataTable not initialized, using custom sorting');
                initializeCustomSorting();
            }
            
            // Setup other features
            setupDeleteConfirmation();
            showFlashMessages();
            enhanceModalAnimations();
            
            // Add custom CSS for DataTable if needed
            if (dataTable) {
                $('.dt-buttons .btn').addClass('mr-1');
                $('.dataTables_filter input').addClass('form-control form-control-sm');
            }
        });
    } else {
        // jQuery not available, just run vanilla JS features
        debugLog('Running vanilla JS only (no jQuery)');
        showFlashMessages();
        
        // Setup delete confirmation without jQuery
        document.querySelectorAll(CONFIG.deleteButtonClass).forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const deleteUrl = this.getAttribute('href');
                const row = this.closest('tr');
                const streamName = row.querySelector('td:first-child').textContent.trim() || 'this stream';
                
                if (confirm(`Are you sure you want to delete "${streamName}"?`)) {
                    window.location.href = deleteUrl;
                }
            });
        });
    }
    
    debugLog('=== Streams Manager Initialization Complete ===');
}

// ================ AUTO-INITIALIZE ================
debugLog('Starting auto-initialization...');

// Check if we should wait for DOMContentLoaded
if (document.readyState === 'loading') {
    debugLog('Waiting for DOMContentLoaded...');
    document.addEventListener('DOMContentLoaded', function() {
        debugLog('DOMContentLoaded fired');
        initAllFeatures();
    });
} else {
    debugLog('DOM already loaded, initializing now');
    initAllFeatures();
}

// Global error handler
window.addEventListener('error', function(e) {
    console.error('[Streams Manager] Global error:', e.error);
    debugLog('Global error occurred:', e.message);
});

// Make debug functions available
window.debugStreams = function() {
    debugLog('Manual debug call');
    console.log('Available functions:', Object.keys(window.StreamsManager || {}));
};

// Export manager object
window.StreamsManager = {
    init: initAllFeatures,
    debug: debugLog,
    refresh: function() {
        debugLog('Manual refresh called');
        initAllFeatures();
    }
};

debugLog('streams.js loaded and ready');