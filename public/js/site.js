document.body.classList.add('js');
let toggle    = document.querySelector('#toggle-navigation');
let menu      = document.querySelector('#menu');
let menuItems = document.querySelectorAll('#menu li a');

toggle.addEventListener('click', () => {
    if (menu.classList.contains('is-active')) {
        this.setAttribute('aria-expanded', 'false');
        menu.classList.remove('is-active');
    } else {
        menu.classList.add('is-active');
        this.setAttribute('aria-expanded', 'true');
    }
});