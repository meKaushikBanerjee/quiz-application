// Internet Explorer 6-11
var isIE = false || !!document.documentMode;
if(isIE)
{
  alert('This site does not support INTERNET EXPLORER! Open in another browser');
  window.location.replace("ie-error.php");
}