$(document).ready(function () {
  $(".clickable-div").click(function () {
    $("#myModal").addClass("flex").removeClass("hidden");
  });
});

$(document).ready(function () {
  $("#closeModal").click(function () {
    $("#myModal").addClass("hidden").removeClass("flex");
  });
});
