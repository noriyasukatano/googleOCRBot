<?php header('Content-Type: text/css; charset=utf-8'); ?>
body{
  font-size:0.5em;
}
thead{
  background-color:#4422bb;
  color:#fff;
}
thead th{
  padding:1em;
}
td{
  padding:1em;
}
dl{
  display:flex;
  flex-wrap:wrap;
}
dt{
  margin-left:2em;
  word-break: keep-all;
}
dt::after{
  content:" : ";
}
dd{
  margin-left:0;
  word-break: break-all;
}


dt.text8em{
  width:8em;
}
dt.text6em{
  width:6em;
}
dt.text4em{
  width:4em;
}
dt.text3em{
  width:3em;
}
dd.text8em{
  width:calc(100% - 10em);
}
dd.text6em{
  width:calc(100% - 8em);
}
dd.text4em{
  width:calc(100% - 6em);
}
dd.text3em{
  width:calc(100% - 5em);
}
