var Column = function (name, intype, format, data, combi, onClick) {
  var inputType = function ()
  {
    var t = intype;
    var type = "text"
    if(this.read) type = "text";
    return type;
  }
  this.name = name;
  this.intype = intype;
  this.data = data;
  this.format = format;
  this.combi = combi;
  this.iType = inputType();
  this.read = (this.intype == "read");
  this.click = "";

};

Column.prototype.isID = function ()
{
  return isset(this.data);
}

Column.prototype.id = function (idExtra)
{
  var id = (isset(idExtra)) ? idExtra+"."+this.name : this.name;
  return id;
}

Column.prototype.isInput = function(extra){ return (!(this.read || this.isID())); };
Column.prototype.isSelect = function(extra){ return (!this.read && this.isID()); };
Column.prototype.isRead = function(extra){ return (!(this.isSelect() || this.isInput())); };
