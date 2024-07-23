document.addEventListener('DOMContentLoaded', () => {

    
    // console.log('=> cart.js loaded');
    // // Constants
    // let KEY_UP = 38, KEY_DOWN = 40;

    // // Variables
    // let min = 0,
    //     max = 10,
    //     step = 1;

    // $('.ui.icon.button').click(function () {
    //     let command = $(this).attr('command');
    //     HandleUpDown(command);
    // });

    // $('#quantity-spinner').keypress(function (e) {
    //     let code = e.keyCode;
    //     // si le code ne correspond pas on quitte
    //     if (code != KEY_UP && code != KEY_DOWN) return;

    //     let command = code == KEY_UP ? 'Up' : code == KEY_DOWN ? 'Down' : '';
    //     HandleUpDown(command);
    // });

    // function HandleUpDown(command) {
    //     let val = $('#quantity-spinner').val().trim();
    //     // si la value est différent de vide, on force la value a être un int
    //     let num = val !== '' ? parseInt(val) : 0;


    //     switch (command) {
    //         case 'Up':
    //             // si num est inférieur a la limite max on incrémente de 1
    //             if (num < max) num += step;
    //             break;
    //             // si num est supérieur a la limite minimal on décrémente de 1
    //         case 'Down':
    //             if (num > min) num -= step;
    //             break;
    //     }

    //     $('#quantity-spinner').val(num);
    // }

});