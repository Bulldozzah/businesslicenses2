$(document).ready(function(){
 $("#load").click(function(){
  loadmore();
 });
});

function loadmore()
{
 var val = document.getElementById("result_no").value;
 $.ajax({
 type: 'post',
 url: 'CommentController.php',
 data: {
  getresult:val
 },
 success: function (response) {
  var content = document.getElementById("result_para");
  content.innerHTML = content.innerHTML+response;

  // We increase the value by 2 because we limit the results by 2
  document.getElementById("result_no").value = Number(val)+2;
 }
 });
}
