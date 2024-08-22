
import { showAlert } from './../alert/messageFlash.js';

document.addEventListener('DOMContentLoaded', () => {
    console.log('=> previewImage.js loaded');

    window.previewImage = function (event) {
        let input = event.target; // obtenir l'élément d'entrée
        console.log('file input changed:', input);

        if (input.files && input.files[0]) {
            let file = input.files[0]; // obtenir le fichier sélectionné
            console.log('file selected:', file);
            let reader = new FileReader(); // créer un nouvel objet FileReader

            // trouver le conteneur de l'image et le loader
            let imageContainer = input.closest('.image-container');
            let imagePreview = imageContainer.querySelector('.image-preview');
            const loader = $('#loader-picture'); // loader pour l'image

            // afficher le loader
            loader.show();
            showAlert('positive', 'Chargement de l\'image...'); // alerte pour indiquer le chargement

            reader.onload = function () {
                console.log('fileReader onload event:', reader.result);
                // mettre à jour l'image d'aperçu
                if (imagePreview) {
                    imagePreview.src = reader.result;
                    imagePreview.style.display = 'block'; // s'assurer que l'image est visible
                } else {
                    console.error('Element with class "image-preview" not found.');
                }

                // cacher le loader après le chargement de l'image
                loader.hide();
                showAlert('positive', 'Image chargée avec succès !'); // alerte pour le succès
            };

            reader.onerror = function (error) {
                console.error('fileReader error:', error);

                // cacher le loader en cas d'erreur
                loader.hide();
                showAlert('negative', 'Erreur lors du chargement de l\'image.'); // alerte pour l'erreur
            };

            reader.readAsDataURL(file); // lire le fichier comme URL de données
        } else {
            console.error('No file selected or file input is not supported.');
            showAlert('negative', 'Aucun fichier sélectionné ou le champ de fichier n\'est pas pris en charge.'); // alerte pour l'absence de fichier
        }
    };
});
