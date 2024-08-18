document.addEventListener('DOMContentLoaded', () => {
    console.log('=> cart.js loaded');
    // Exemple : 
    // document.querySelectorAll('.add-to-cart').forEach(button => {
    //     button.addEventListener('click', async (event) => {
    //         const productId = event.target.dataset.productId;
    //         const response = await fetch(`/cart/add/service/${productId}`, {
    //             method: 'POST'
    //         });
    //         const data = await response.json();
    //         console.log('Cart updated:', data);
    //         // maj
    //     });
    // });
    //Exemple : 
    // document.querySelectorAll('.remove-from-cart').forEach(button => {
    //     button.addEventListener('click', async (event) => {
    //         const productId = event.target.dataset.productId;
    //         const response = await fetch(`/cart/remove/${productId}`, {
    //             method: 'POST'
    //         });
    //         const data = await response.json();
    //         console.log('Cart updated:', data);
    //         maj
    //     });
    // });
});