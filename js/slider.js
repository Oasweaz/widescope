var slides = document.querySelectorAll('.section_one .slides');
var btns = document.querySelectorAll('.btn');
let currentSlide = 1;

var manualNav = function(manual){

    slides.forEach((slide) => {
        slide.classList.remove('active');
    });

    btns.forEach((btn) => {
        btn.classList.remove('active');
    });


    slides[manual].classList.add('active');
    btns[manual].classList.add('active');

}
    

    btns.forEach((btn, i) => {
        btn.addEventListener('click', () =>{
            manualNav(i);
            currentSlide = i;
        });
    });



// Auto-slide
var repeat = function() {
let slides = document.querySelectorAll('.section_one .slides'); 
let btns = document.querySelectorAll('.btn'); 
let i = 0;  


var repeater = () => {
setTimeout(function() {

    
    slides.forEach((slide) => {
        slide.classList.remove('active');
    });
    btns.forEach((btn) => {
        btn.classList.remove('active');
    });

    
    slides[i].classList.add('active');
    btns[i].classList.add('active');

    
    i++;
    if (i >= slides.length) {
        i = 0;  
    }

    
    repeater();

}, 8000);  
}


repeater();
}

repeat();
