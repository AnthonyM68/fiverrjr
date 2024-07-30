document.addEventListener('DOMContentLoaded', () => {
    console.log('=> previewImage.js loaded');

    window.previewImage = function (event) {
        let input = event.target;
        console.log('File input changed:', input);

        if (input.files && input.files[0]) {
            let file = input.files[0];
            console.log('File selected:', file);
            let reader = new FileReader();
            reader.onload = function () {
                console.log('FileReader onload event:', reader.result);
                let imagePreview = document.getElementById('imagePreview ');
                if (imagePreview) {
                    imagePreview.src = reader.result;
                    imagePreview.style.display = 'block';
                } else {
                    console.error('Element with id "imagePreview" not found.');
                }
            };

            reader.onerror = function (error) {
                console.error('FileReader error:', error);
            };

            reader.readAsDataURL(file);
        } else {
            console.error('No file selected or file input is not supported.');
        }
    };
});
