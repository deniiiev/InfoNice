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

// Enable tooltip
$(function () {
    $('[data-toggle="tooltip"]').tooltip({
        trigger : 'hover'
    })
});

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

// Add post to featured

const axios = require('axios').default;

let featured = document.querySelectorAll('.featured-toggle');
let bookmarker = document.querySelectorAll('.bookmark-toggle');

function switcher(event) {
    event.preventDefault();
    let url = this.href;

    axios.get(url).then((response) => {
        let status = String(response.data.response.status);
        (status === 'added') ? this.classList.add('added') : this.classList.remove('added');
    });
}

featured.forEach((featured) => {
    featured.addEventListener('click', switcher);
});

bookmarker.forEach((bookmarker) => {
    bookmarker.addEventListener('click', switcher);
});

// Comment reply

const commentWrite = document.querySelector('.md-comment-write');
const commentTextArea = document.getElementById('comment_message');
const commentReply = document.querySelectorAll('.comment-reply');
const replyTo = document.getElementById('comment_replyTo');
const commentReplyUser = document.querySelector('.md-comment-reply-user');
const replyingDelete = document.querySelector('.md-replying-delete');

if (replyingDelete) {
    replyingDelete.addEventListener('click', () => {
        replyTo.removeAttribute('value');
        commentWrite.classList.remove('md-replying');
    });
}

if (commentReply) {
    commentReply.forEach((reply) => {
        let replyUser = reply.querySelector('.reply-user');
        reply.addEventListener('click', (reply) => {
            replyTo.value = replyUser.innerHTML;
            commentTextArea.focus();

            if (replyTo.value !== '') {
                commentWrite.classList.add('md-replying');
                commentReplyUser.innerHTML = replyTo.value;
            }
        })
    });
}
