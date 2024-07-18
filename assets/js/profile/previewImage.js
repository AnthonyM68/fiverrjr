document.addEventListener('DOMContentLoaded', () => {
    console.log('=> previewImage.js loaded');

    window.previewImage = function (event) {  
        // au chargement du fichier dans le input file
        let input = event.target;
        // on récupère les infos du fichier pour le nom de fichier
        let reader = new FileReader();
        // au chargement du fichier dans le reader
        reader.onload = function () {
            // on récupére l'élément preview
            let imagePreview = document.getElementById('imagePreview');
            // on lui indique en attribut src l'url du fichier
            imagePreview.src = reader.result;
            // on affiche l'image, le block
            imagePreview.style.display = 'block';
        };
        // on sauvegarde l'url de l'image dans le reader
        reader.readAsDataURL(input.files[0]);
    }
});
