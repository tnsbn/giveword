
/**
 * Scroll down menu
 **/
$(document).ready(function() {
  $('a[href*=#]').bind('click', function(e) {
    e.preventDefault(); // prevent hard jump, the default behavior

    let target = $(this).attr("href"); // Set the target as letiable

    // perform animated scrolling by getting top-position of target-element and set it as scroll target
    $('html, body').stop().animate({
      scrollTop: $(target).offset().top
    }, 600, function() {
      location.hash = target; //attach the hash (#jumptarget) to the pageurl
    });

    return false;
  });
});

$(window).scroll(function() {
  let scrollDistance = $(window).scrollTop();

  // Show/hide menu on scroll
  //if (scrollDistance >= 850) {
  //		$('nav').fadeIn("fast");
  //} else {
  //		$('nav').fadeOut("fast");
  //}

  // Assign active class to nav links while scolling
  $('.page-section').each(function(i) {
    if ($(this).position().top <= scrollDistance) {
      $('.navigation a.active').removeClass('active');
      $('.navigation a').eq(i).addClass('active');
    }
  });
}).scroll();
/**
 * End of Scroll down menu
 **/

/**
 * Slider
 **/
//current position
var pos = 0;
//number of slides
var totalSlides = $('#slider-wrap ul li').length;
//get the slide width
var sliderWidth = $('#slider-wrap').width();


$(document).ready(function(){


  /*****************
   BUILD THE SLIDER
   *****************/
  //set width to be 'x' times the number of slides
  $('#slider-wrap ul#slider').width(sliderWidth*totalSlides);

  //next slide
  $('#next').click(function(){
    slideRight();
  });

  //previous slide
  $('#previous').click(function(){
    slideLeft();
  });



  /*************************
   //*> OPTIONAL SETTINGS
   ************************/
    //automatic slider
  // var autoSlider = setInterval(slideRight, 3000);

  //for each slide
  $.each($('#slider-wrap ul li'), function() {
    //set its color
    var c = $(this).attr("data-color");
    $(this).css("background",c);

    //create a pagination
    var li = document.createElement('li');
    $('#pagination-wrap ul').append(li);
  });

  //counter
  countSlides();

  //pagination
  pagination();

  //hide/show controls/btns when hover
  //pause automatic slide when hover
  $('#slider-wrap').hover(
    function(){ $(this).addClass('active'); /*clearInterval(autoSlider);*/ },
    function(){ $(this).removeClass('active'); /*autoSlider = setInterval(slideRight, 3000);*/ }
  );



});//DOCUMENT READY



/***********
 SLIDE LEFT
 ************/
function slideLeft(){
  pos--;
  if(pos==-1){ pos = totalSlides-1; }
  $('#slider-wrap ul#slider').css('left', -(sliderWidth*pos));

  //*> optional
  countSlides();
  pagination();
}


/************
 SLIDE RIGHT
 *************/
function slideRight(){
  pos++;
  if(pos==totalSlides){ pos = 0; }
  $('#slider-wrap ul#slider').css('left', -(sliderWidth*pos));

  //*> optional
  countSlides();
  pagination();
}


/************************
 //*> OPTIONAL SETTINGS
 ************************/
function countSlides(){
  $('#counter').html(pos+1 + ' / ' + totalSlides);
}

function pagination(){
  $('#pagination-wrap ul li').removeClass('active');
  $('#pagination-wrap ul li:eq('+pos+')').addClass('active');
}
/**
 * End of Slider
 **/

/**
 * Progress bar
 **/
window.onscroll = function() {myFunction()};

function myFunction() {
  let winScroll = document.body.scrollTop || document.documentElement.scrollTop;
  let height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
  let scrolled = (winScroll / height) * 100;
  document.getElementById("myBar").style.width = scrolled + "%";
}
/**
 * End of Progress bar
 **/
