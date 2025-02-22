$(document).ready(function () {
  $("#myModal").hide();
});

$(document).ready(function () {
  $(".clickable-div").click(function () {
    $("#myModal").fadeIn();
  });
});

$(document).ready(function () {
  $("#closeModal").click(function () {
    $("#myModal").fadeOut();
  });
});
