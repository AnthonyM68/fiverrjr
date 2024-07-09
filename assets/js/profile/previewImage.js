document.addEventListener('DOMContentLoaded', () => {
    console.log('=> previewImage.js loaded!');
    window.previewImage = function (event) {  // make it globally accessible
        var input = event.target;
        console.log('Input file selected:', input.files[0]);
        var reader = new FileReader();
        reader.onload = function () {
            var imagePreview = document.getElementById('imagePreview');
            console.log('File reader loaded:', reader.result);
            imagePreview.src = reader.result;
            imagePreview.style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    }
});
