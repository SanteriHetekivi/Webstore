var Columns = function (columns, showNew) {
  this.columns = columns;
  this.showNEW = (isset(showNew) && !showNew) ? false : true;
};

Columns.prototype.VAL = function(name, ob, bare)
{
  var val = "";
  var needOper = false;
  if(isset(name) && isset(ob) && isset(this.columns[name]))
  {
    var col = this.columns[name];
    if(isset(ob[col.name]))
    {
      val = ob[col.name];
      needOper = (isset(col.combi) && isset(col.combi.cal)
        && isset(col.combi.pair) && isset(ob[col.combi.pair]));

      if(isset(bare) && bare) return val;

      if(col.isID() && isset(col.data))
      {
        val = (isset(col.data[val])) ? col.data[val] : "";
      }

      if(needOper)
      {
        var cal = CAL(col.combi.cal, val, ob[col.combi.pair]);
        if(isset(cal))
        {
          val = cal;
          needOper = false;
        }
      }

      if(isset(col.format)) val = FORMAT(col.format, val);

      if(needOper)
      {
        var second = ob[col.combi.pair];
        if(isset(col.combi.format)) second = FORMAT(col.combi.format, second);
        var c = col.combi.cal;

        if(c == "")         val += second;
        else if(c == " ")   val += " " + second;
        else if(c == "()")  val += " (" + second + ")";
        else                val += c + second;
      }

    }
    return val;
  }

  Columns.prototype.Click = function(name, ob)
  {
    if(isset(this.columns[name]))
    {
      var col = this.columns[name];
      col.click();
    }
  }

  return val;
}
