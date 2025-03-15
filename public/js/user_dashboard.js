$(document).ready(function () {
  // Show modal when button is clicked
  $(".clickable-div").click(function () {
    $("#myModal").removeClass("hidden").addClass("flex");
  });

  // Hide modal when close button is clicked
  $("#closeModal").click(function () {
    $("#myModal").addClass("hidden").removeClass("flex");
  });
});
//
//$(document).ready(function () {
//  $("#modalOverlay").click(function () {
//    $("#myModal").addClass("hidden").removeClass("flex");
//  });
//});
