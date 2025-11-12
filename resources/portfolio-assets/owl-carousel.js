$(function () {
  var count = 0;
  $('.owl-carousel').each(function () {
    $(this).attr('id', 'owl-demo' + count);
    $('#owl-demo' + count).owlCarousel({
      navigation: true,
      slideSpeed: 300,
      pagination: true,
      singleItem: true,
      autoPlay: false,
      autoHeight: false,
    });
    count++;
  });
});