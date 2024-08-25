document.addEventListener("DOMContentLoaded", function() {
    console.log('=> app.js loaded');
    // bouton back to top home
    const backToTopButton = document.querySelector(".back-to-top");

    backToTopButton.addEventListener("click", function(e) {
        e.preventDefault(); 

        $('html, body').animate({ scrollTop: 0 }, 'smooth');
    });
});
