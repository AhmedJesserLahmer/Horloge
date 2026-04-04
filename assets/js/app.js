document.addEventListener('DOMContentLoaded', () => {
    const cards = document.querySelectorAll('.product-card');

    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(14px)';

        window.setTimeout(() => {
            card.style.transition = 'opacity 350ms ease, transform 350ms ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 70);
    });
});
