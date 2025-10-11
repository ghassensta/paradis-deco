'use strict';

(function() {
    const deleteButton=document.querySelector('#deleteButton');

    if (deleteButton) {
        deleteButton.onclick = function () {
            Swal.fire({
              title: 'Êtes-vous sûr?',
              text: "Vous ne pourrez pas revenir en arrière!",
              icon: 'warning',
              showCancelButton: true,
              confirmButtonText: 'Oui, supprimez-le!',
              customClass: {
                confirmButton: 'btn btn-primary me-3',
                cancelButton: 'btn btn-label-secondary'
              },
              buttonsStyling: false
            }).then(function (result) {
              if (result.value) {
                Swal.fire({
                  icon: 'success',
                  title: 'Supprimé!',
                  text: 'Votre fichier a été supprimé.',
                  customClass: {
                    confirmButton: 'btn btn-success'
                  }
                });
              }
            });
          };          
      }
} ());