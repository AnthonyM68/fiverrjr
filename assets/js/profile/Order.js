document.addEventListener('DOMContentLoaded', () => {
    console.log('=> Order.js loaded');
    // Pagination Orders Commandes Profil
    function attachPaginationOrdersEventListeners() {
        document.querySelectorAll('.pagination.menu .item').forEach(function (link) {

            link.addEventListener('click', async function (event) {
                event.preventDefault();
                try {
                    const url = this.getAttribute('href');
                    fetch(url, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                            return response.json();
                        })
                        .then(data => {
                            const ordersContainer = document.querySelector(`#${data.type_order}`);
                            ordersContainer.innerHTML = data.orders;
                            attachPaginationOrdersEventListeners(); // Re-attach event listeners for the new pagination links
                        })
                        .catch(error => {
                            console.error('Error during fetch operation:', error);
                        });
                } catch (error) {
                    console.error('Failed to fetch or parse JSON for service form:', error);
                }
            });
        });
    };
    attachPaginationOrdersEventListeners();
});