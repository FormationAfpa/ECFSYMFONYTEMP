document.addEventListener('DOMContentLoaded', function() {
    // Système de notation
    const ratingGroups = document.querySelectorAll('.rating-group');
    
    ratingGroups.forEach(group => {
        const stars = group.querySelectorAll('.star');
        const input = group.querySelector('input[type="hidden"]');
        
        // S'assurer que le champ caché a une valeur par défaut
        if (!input.value) {
            input.value = "0";
        }
        
        stars.forEach(star => {
            star.addEventListener('click', () => {
                const value = star.dataset.value;
                
                // Mettre à jour la valeur du champ caché
                input.value = value;
                
                // Mettre à jour l'affichage des étoiles
                stars.forEach(s => {
                    if (parseInt(s.dataset.value) <= parseInt(value)) {
                        s.classList.add('active');
                    } else {
                        s.classList.remove('active');
                    }
                });
            });
        });
        
        // If there's an initial value, show it
        if (input.value) {
            stars.forEach(star => {
                if (parseInt(star.dataset.value) <= parseInt(input.value)) {
                    star.classList.add('active');
                }
            });
        }
    });
});
