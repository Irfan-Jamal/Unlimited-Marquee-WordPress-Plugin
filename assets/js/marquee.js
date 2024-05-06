document.addEventListener('DOMContentLoaded', function() {
    const marquee = document.querySelector('.marquee-text');

    marquee.addEventListener('mouseenter', function() {
        marquee.setAttribute('scrollamount', '0');
    });

    marquee.addEventListener('mouseleave', function() {
        marquee.setAttribute('scrollamount', '20');
    });
});
