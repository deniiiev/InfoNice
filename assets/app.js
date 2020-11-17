/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.scss in this case)
import './styles/app.scss';

// Need jQuery? Install it with "yarn add jquery", then uncomment to import it.
// import $ from 'jquery';
let $ = require('jquery');
global.$ = global.jQuery = $;

// Awesome fonts
require('@fortawesome/fontawesome-free/css/all.min.css');
require('@fortawesome/fontawesome-free/js/all.js');

// Bootstrap js
require('bootstrap');

// Prevent scroll

let keys = {37: 1, 38: 1, 39: 1, 40: 1};

function preventDefault(e) {
    e.preventDefault();
}

function preventDefaultForScrollKeys(e) {
    if (keys[e.keyCode]) {
        preventDefault(e);
        return false;
    }
}

let supportsPassive = false;
try {
    window.addEventListener("test", null, Object.defineProperty({}, 'passive', {
        get: function () { supportsPassive = true; }
    }));
} catch(e) {}

let wheelOpt = supportsPassive ? { passive: false } : false;
let wheelEvent = 'onwheel' in document.createElement('div') ? 'wheel' : 'mousewheel';

function disableScroll() {
    window.addEventListener('DOMMouseScroll', preventDefault, false); // older FF
    window.addEventListener(wheelEvent, preventDefault, wheelOpt); // modern desktop
    window.addEventListener('touchmove', preventDefault, wheelOpt); // mobile
    window.addEventListener('keydown', preventDefaultForScrollKeys, false);
}

function enableScroll() {
    window.removeEventListener('DOMMouseScroll', preventDefault, false);
    window.removeEventListener(wheelEvent, preventDefault, wheelOpt);
    window.removeEventListener('touchmove', preventDefault, wheelOpt);
    window.removeEventListener('keydown', preventDefaultForScrollKeys, false);
}


// Mobile navbar

const sideNav = document.getElementById('sideNav');
const sideNavOpener = document.getElementById('sideNavOpener');
const sideNavCloser = document.getElementById('sideNavCloser');
const sideNavBack = document.getElementById('sideNavBack');

function openNav() {
    disableScroll();
    sideNav.style.width = '240px';
    sideNavBack.style.opacity = '1';
    sideNavCloser.style.width = '100%';
}

function closeNav() {
    enableScroll();
    sideNav.style.width = '0';
    sideNavBack.style.opacity = '0';
    sideNavCloser.style.width = '0';
}

if (sideNav) {
    sideNavOpener.addEventListener('click', openNav);
    sideNavCloser.addEventListener('click', closeNav);
}
