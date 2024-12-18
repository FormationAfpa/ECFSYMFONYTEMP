// Initialize Bootstrap tooltips and popovers
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Initialize popovers
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });

    // Auto-hide alerts after 5 seconds
    var alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            var bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });

    // Smooth scroll to top button
    var scrollToTopBtn = document.querySelector('.scroll-to-top');
    if (scrollToTopBtn) {
        window.addEventListener('scroll', function() {
            if (window.pageYOffset > 100) {
                scrollToTopBtn.style.display = 'block';
            } else {
                scrollToTopBtn.style.display = 'none';
            }
        });

        scrollToTopBtn.addEventListener('click', function(e) {
            e.preventDefault();
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }

    // Form validation
    var forms = document.querySelectorAll('.needs-validation');
    Array.prototype.slice.call(forms).forEach(function(form) {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });

    // Dynamic book filtering
    var searchInput = document.querySelector('#book-search');
    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            var searchTerm = e.target.value.toLowerCase();
            var books = document.querySelectorAll('.book-card');
            
            books.forEach(function(book) {
                var title = book.querySelector('.card-title').textContent.toLowerCase();
                var author = book.querySelector('.book-author').textContent.toLowerCase();
                
                if (title.includes(searchTerm) || author.includes(searchTerm)) {
                    book.style.display = '';
                } else {
                    book.style.display = 'none';
                }
            });
        });
    }

    // Star rating system
    var ratingInputs = document.querySelectorAll('.rating input');
    ratingInputs.forEach(function(input) {
        input.addEventListener('change', function() {
            var selectedRating = this.value;
            var stars = this.parentElement.querySelectorAll('label');
            
            stars.forEach(function(star, index) {
                if (index < selectedRating) {
                    star.classList.add('active');
                } else {
                    star.classList.remove('active');
                }
            });
        });
    });
});
