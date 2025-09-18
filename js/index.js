
// Toggle sidebar
const toggle = document.querySelectorAll('.hamb');
toggle[0].onclick = function() {
    let sideNav = document.querySelector('header'); 
    sideNav.classList.toggle('active');  
}


// Toggle readMore
const read = document.querySelectorAll('.read_btn');
read[0].onclick = function() {
    let short = document.querySelector('.collapse'); 
    short.classList.toggle('active');  
}

// Toggle readMore2
const read2 = document.querySelectorAll('.read_btn-ii');
read2[0].onclick = function() {
    let short = document.querySelector('.collapse-ii'); 
    short.classList.toggle('active2');  
}

// Toggle readMore3
const read3 = document.querySelectorAll('.read_btn-iii');
read3[0].onclick = function() {
    let short = document.querySelector('.collapse-iii'); 
    short.classList.toggle('active3');  
}

// remove md-bck btn

