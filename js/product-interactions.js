// Description toggle functionality
function toggleDescription(button) {
    const container = button.closest('.description-container');
    const text = button.querySelector('#descriptionText');
    
    container.classList.toggle('expanded');
    
    if (container.classList.contains('expanded')) {
        text.textContent = 'Read Less';
        button.querySelector('span').setAttribute('data-uk-icon', 'icon: chevron-up');
    } else {
        text.textContent = 'Read More';
        button.querySelector('span').setAttribute('data-uk-icon', 'icon: chevron-down');
    }
}

// Initialize UIkit components
document.addEventListener('DOMContentLoaded', function() {
    // Add scrollspy animations
    UIkit.scrollspy('.seller-info', { 
        cls: 'uk-animation-slide-bottom', 
        delay: 200 
    });
    
    UIkit.scrollspy('.product-card', { 
        cls: 'uk-animation-slide-left', 
        target: '> div > div', 
        delay: 200 
    });
    
    UIkit.scrollspy('.uk-button-primary', { 
        cls: 'uk-animation-scale-up', 
        delay: 300 
    });
    
    // Initialize slider if there are multiple images
    const slider = document.querySelector('.uk-slider');
    if (slider) {
        UIkit.slider(slider, {
            autoplay: true,
            autoplayInterval: 5000,
            pauseOnHover: true
        });
    }
});

// Smooth scroll for anchor links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        document.querySelector(this.getAttribute('href')).scrollIntoView({
            behavior: 'smooth'
        });
    });
});

// Add click event listener for related products
document.querySelectorAll('.related-products-grid').forEach(grid => {
    grid.addEventListener('click', function(e) {
        const productCard = e.target.closest('.uk-card');
        if (productCard) {
            const productId = productCard.getAttribute('data-product-id');
            if (productId) {
                window.location.href = `product.php?id=${productId}`;
            }
        }
    });
});
