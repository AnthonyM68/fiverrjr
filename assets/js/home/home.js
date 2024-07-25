// observer intersection
document.addEventListener('DOMContentLoaded', function () {
    console.log("home.js loaded");

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {

            if (entry.isIntersecting) {

                const animation = entry.target.getAttribute('data-animation');
                const duration = entry.target.getAttribute('data-duration');
                $(entry.target).transition({
                    animation: animation,
                    duration: duration
                });
            }
        });
    }, { threshold: 0.1 });
    const observer2 = new IntersectionObserver((entries) => {
        entries.forEach(entry => {

            if (entry.isIntersecting) {

                const animation = entry.target.getAttribute('data-animation');
                const duration = entry.target.getAttribute('data-duration');
                $(entry.target).transition({
                    animation: animation,
                    duration: duration
                });
            }
        });
    }, { threshold: 0.1 });


    document.querySelectorAll('.leaf').forEach(element => {
        observer.observe(element);
    });
    document.querySelectorAll('.slide').forEach(element => {
        observer2.observe(element);
    });


});