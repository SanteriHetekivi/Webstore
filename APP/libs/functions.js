function isEmpty(obj) {

    // null and undefined are "empty"
    if (obj == null) return true;

    // Assume if it has a length property with a non-zero value
    // that that property is correct.
    if (obj.length > 0)    return false;
    if (obj.length === 0)  return true;

    // Otherwise, does it have any properties of its own?
    // Note that this doesn't handle
    // toString and valueOf enumeration bugs in IE < 9
    for (var key in obj) {
        if (hasOwnProperty.call(obj, key)) return false;
    }

    return true;
}

function isset(variable) {
  return (typeof variable != 'undefined' && variable !== null);
}

function inArray(variable, array) {
  return (array.indexOf(variable) !== -1);
}

function isElement(o)
{
  return (
    typeof HTMLElement === "object" ? o instanceof HTMLElement :
    o && typeof o === "object" && o !== null && o.nodeType === 1 && typeof o.nodeName==="string"
  );
}

var emptyDate = function(val)
{
  var nullDate = "0000-00-00 00:00:00";
  var date = pDateObject(val);
  var years = date.getFullYear() < 1971;
  return (!isset(val) || val == nullDate || years);
}

var toggleError = function(id, on)
{
  $("#"+id).toggleClass( "ERROR", on );
}

var toggleErrors = function(errorColumns, on)
{
  var elem = null;
  angular.forEach(errorColumns,
    function(value, key)
    {
      toggleError(value, on);
    }
  );
}

var pFloat = function(val)
{
  var float = parseFloat(val);
  if(isNaN(float)) float = 0.00;
  return float;
}

var pEuro = function(val)
{
  var no = pFloat(val);
  return no.toFixed(2).replace(".", ",") + " €";
}

var pDateObject = function (val)
{
  var sec = Date.parse(val);

  if(isNaN(sec)) sec = 0;

  return new Date(sec);
}

var pDate = function (val)
{
  var date = pDateObject(val);
  return date.toLocaleDateString();
}

var pTime = function (val)
{
  var date = pDateObject(val);
  return date.toLocaleTimeString();
}

var pDateTime = function (val)
{
  var date = pDateObject(val);
  var str = date.toLocaleString();
  return (emptyDate(date)) ? "" : str;
}

var FORMAT = function(f, val)
{
  if(f == "euro")           val = pEuro(val);
  else if(f == "float")     val = pFloat(val);
  else if(f == "dateTime")  val = pDateTime(val);
  else if(f == "date")      val = pDate(val);
  else if(f == "time")      val = pTime(val);
  else if(f == "image")     val = "<img class='productImage' src='"+val+"' />";
  return val;
}

var CAL = function(op, valA, valB)
{
  var val = null;
  var valA = pFloat(valA);
  var valB = pFloat(valB);

  if(op == "+")                  val = valA + valB;
  else if(op == "-")             val = valA - valB;
  else if(op == "*")             val = valA * valB;
  else if(op == "/" && sNo != 0) val = valA / valB;

  return val;
}

var toggleLogin = function(toggle)
{
  $( document ).ready(function() {
    if(toggle) $("#LOGIN_MODAL").modal('show');
    else $("#LOGIN_MODAL").modal('hide');
  });
}

var clearLoginFields = function(toggle)
{
  $( document ).ready(function() {
    $("#password").VAL("");
    $("#username").VAL("");
  });
}

var STATE = "";
