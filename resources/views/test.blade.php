@extends('layouts.app')

@section('css')
  <style>
     * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font: 13px Helvetica, Arial; }
    #messages { list-style-type: none; margin: 0; padding: 0; }
    #messages li { padding: 5px 10px; }
    #messages li:nth-child(odd) { background: #eee; }
    #messages { margin-bottom: 10px }
    #container {
        top: 50px;
        width: 500px;
        margin: 0 auto;
        display: block;
        position: relative;
    }
    #content {
        width: 100%;
        height: 350px;
        border: 1px solid darkolivegreen;
        border-radius: 5px;
        overflow: auto;
    }
    #m{
      display: inline-block;
      width: 90%;
    }
   #btn {
        width: 10%;
    }

  </style>
@endsection

@section('content')
    <div id="container">
        <div id="status-box">Server: <span id="status">-</span> / <span id="online">0</span> online.</div>
            <div id="content">
                <ul id="messages"></ul>
            </div>
        <form action="">
            <input id="m" autocomplete="off" /><button id="btn">Send</button>
        </form>
    </div>
    <div class="col-md-4 col-md-offset-8">
        <div class="panel panel-default">
            <div class="panel-heading">誰在線上</div>
            <div class="panel-body" id="online_people" style="overflow:scroll; height:200px;"> </div>
        </div>
    </div>
@endsection

@section('js')
<script src="https://cdn.socket.io/socket.io-1.2.0.js"></script>
<script src="https://code.jquery.com/jquery-1.11.1.js"></script>
<script>
  $(function () {
    var socket = io('localhost:3000');  
    var status = document.getElementById("status");
    var online = document.getElementById("online");
    var div = document.getElementById('content');
    var name = "{{ Auth::user()->name }}";

    socket.on("connect", function () {
      socket.emit('online', '{{ Auth::user()->name }}');
      socket.emit('leave', '{{ Auth::user()->name }}');
      status.innerText = "Connected.";
    });

    socket.on("disconnect", function () {
      status.innerText = "Disconnected.";
    });

    socket.on("sum", function (amount) {
      online.innerText = amount;
    });

    socket.on('online', people => {
        $('#online_people').empty();
        people.map((value, key) => {
            $('#online_people').append('<li>' + value + '</li>')
        });
    });

    $('form').submit(function(){
      socket.emit('chat message', name, $('#m').val());
      $('#m').val('');
      return false;
    });
    
    socket.on('chat message', function(name, msg){
      $('#messages').append('<label>' + name + '</label>' + ' 說: ' + msg + '<br />');
      window.scrollTo(0, document.body.scrollHeight);
      div.scrollTop = div.scrollHeight;
    });
  });
</script>
@endsection