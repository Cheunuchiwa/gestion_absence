// Fonction pour afficher une notification
function showNotification(message) {
    const notification = document.getElementById('notification');
    const notificationMessage = document.getElementById('notification-message');
    
    if (notification && notificationMessage) {
        notificationMessage.textContent = message;
        notification.classList.add('show');
        
        setTimeout(() => {
            notification.classList.remove('show');
        }, 3000);
    }
}

// Initialisation de la page en fonction de l'URL actuelle
document.addEventListener('DOMContentLoaded', function() {
    const currentPage = window.location.pathname.split('/').pop();
    
    // Gestion de la recherche d'étudiant
    const searchInput = document.getElementById('search-student');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('#students-body tr');
            
            rows.forEach(row => {
                const firstName = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
                const lastName = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
                
                if (firstName.includes(searchTerm) || lastName.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }
    
    // Gestion du modal pour les justificatifs
    const modal = document.getElementById('excuse-modal');
    const newExcuseBtn = document.getElementById('new-excuse-btn');
    const closeBtn = document.querySelector('.close');
    const cancelBtn = document.getElementById('cancel-btn');
    
    if (newExcuseBtn && modal) {
        newExcuseBtn.addEventListener('click', function() {
            modal.style.display = 'block';
        });
    }
    
    if (closeBtn && modal) {
        closeBtn.addEventListener('click', function() {
            modal.style.display = 'none';
        });
    }
    
    if (cancelBtn && modal) {
        cancelBtn.addEventListener('click', function() {
            modal.style.display = 'none';
        });
    }
    
    // Fermer le modal en cliquant en dehors
    window.addEventListener('click', function(event) {
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    });
    
    // Gestion de l'upload de fichier
    const fileInput = document.getElementById('excuse_file');
    const fileName = document.getElementById('file-name');
    
    if (fileInput && fileName) {
        fileInput.addEventListener('change', function() {
            if (this.files.length > 0) {
                fileName.textContent = this.files[0].name;
            } else {
                fileName.textContent = 'Aucun fichier sélectionné';
            }
        });
    }
    
    // Gestion des boutons d'action pour les justificatifs
    const viewButtons = document.querySelectorAll('.view-btn');
    const validateButtons = document.querySelectorAll('.validate-btn');
    const rejectButtons = document.querySelectorAll('.reject-btn');
    
    viewButtons.forEach(button => {
        button.addEventListener('click', function() {
            showNotification('Ouverture du document...');
        });
    });
    
    validateButtons.forEach(button => {
        button.addEventListener('click', function() {
            const card = this.closest('.excuse-card');
            card.style.opacity = '0.5';
            showNotification('Justificatif validé avec succès');
            setTimeout(() => {
                card.remove();
            }, 1000);
        });
    });
    
    rejectButtons.forEach(button => {
        button.addEventListener('click', function() {
            const card = this.closest('.excuse-card');
            card.style.opacity = '0.5';
            showNotification('Justificatif rejeté');
            setTimeout(() => {
                card.remove();
            }, 1000);
        });
    });
});