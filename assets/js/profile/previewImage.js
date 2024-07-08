document.addEventListener('DOMContentLoaded', () => {
    console.log('DOM fully loaded and parsed: previewImage.js');
    window.previewImage = function(event) { 
        let input = event.target;
        console.log('Input file selected:', input.files[0]);
        let reader = new FileReader();
        reader.onload = function () {
            let imagePreview = document.getElementById('imagePreview');
            console.log('File reader loaded:', reader.result);
            imagePreview.src = reader.result;
            imagePreview.style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    }
});