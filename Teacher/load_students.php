// Update the marks form submission handler in your main page
document.getElementById('marksForm')?.addEventListener('submit', function(e) {
    e.preventDefault(); // Prevent default form submission
    
    const marks = document.querySelectorAll('input[name="marks[]"]');
    let isValid = true;
    let neCount = 0;
    let hasMarks = false;
    
    marks.forEach(input => {
        const value = input.value.trim();
        
        if (value === '') {
            neCount++;
            input.classList.remove('is-invalid');
            return;
        }
        
        hasMarks = true;
        const numValue = parseFloat(value);
        if (isNaN(numValue) || numValue < 0 || numValue > 100) {
            isValid = false;
            input.classList.add('is-invalid');
        } else {
            input.classList.remove('is-invalid');
        }
    });
    
    if (!isValid) {
        Swal.fire({
            icon: 'error',
            title: 'Invalid Marks',
            text: 'Please ensure all entered marks are between 0 and 100',
            confirmButtonColor: '#0B6E4F'
        });
        return;
    }
    
    if (!hasMarks && neCount > 0) {
        Swal.fire({
            icon: 'warning',
            title: 'No Marks Entered',
            text: 'Please enter at least one mark before saving',
            confirmButtonColor: '#0B6E4F'
        });
        return;
    }
    
    // Show confirmation for NE entries
    if (neCount > 0) {
        Swal.fire({
            title: 'Not Examined Students',
            html: `${neCount} student(s) will be marked as <strong>NE (Not Examined)</strong>.<br><br>Do you want to continue?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#0B6E4F',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, save',
            cancelButtonText: 'Review'
        }).then((result) => {
            if (result.isConfirmed) {
                submitMarksForm();
            }
        });
    } else {
        submitMarksForm();
    }
});

// Function to submit form via AJAX
function submitMarksForm() {
    const form = document.getElementById('marksForm');
    const formData = new FormData(form);
    
    // Show loading
    Swal.fire({
        title: 'Saving marks...',
        text: 'Please wait',
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    // Send AJAX request
    fetch('insertMark.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: data.message,
                confirmButtonColor: '#0B6E4F',
                timer: 3000,
                timerProgressBar: true
            }).then(() => {
                // Optionally reload the page or update the UI
                // location.reload();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: data.message,
                confirmButtonColor: '#0B6E4F'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'An error occurred while saving marks. Please try again.',
            confirmButtonColor: '#0B6E4F'
        });
    });
}

// Also update the Mark All NE function
function markAllNE() {
    Swal.fire({
        title: 'Mark All as Not Examined?',
        text: 'This will clear all entered marks',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#0B6E4F',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, clear all'
    }).then((result) => {
        if (result.isConfirmed) {
            const inputs = document.querySelectorAll('input[name="marks[]"]');
            inputs.forEach(input => {
                input.value = '';
                input.classList.remove('is-invalid');
                input.style.borderColor = '#ffc107';
            });
            
            // Update NE badges
            document.querySelectorAll('.input-group-text').forEach(badge => {
                if (badge.innerText.trim() === 'NE') {
                    // Keep as is
                }
            });
            
            Swal.fire({
                icon: 'success',
                title: 'All marks cleared',
                text: 'All students are now set to NE (Not Examined)',
                timer: 2000,
                showConfirmButton: false
            });
        }
    });
}