var app = require('express')();
var http = require('http').Server(app);
var io = require('socket.io')(http);
var port = process.env.PORT || 3000;

let onlineCount = 0;
let users = [];

io.on('connection', function(socket){
  //上線加人
  onlineCount++;
  // 發送人數給網頁
  io.emit("sum", onlineCount);

  socket.on('online', function(user) {
    if (users.indexOf(user) === -1) {	// 判斷user是否重複
			users.push(user);
    }
    console.log(user + '加入聊天室');
    io.emit('online', users);
  });

  // 接收訊息
  socket.on('chat message', function(name, msg){
    //msg內容鍵值小於 1等於是訊息傳送不完全 return終止函式執行。
    if (Object.keys(msg).length < 1) return;
    // 發送人數給網頁
    io.emit('chat message', name, msg);
  });

  //離線
  socket.on('leave', function (user) {
    socket.on('disconnect', () => {
    // 離線扣人
    onlineCount = onlineCount > 0 ? onlineCount - 1 : 0;
    io.emit("sum", onlineCount);

    var index = users.indexOf(user);
		if (index > -1) {
        users.splice(index, 1);
			}
    console.log(user + '離開聊天室')
		io.emit('online', users);
    });
  });
});

http.listen(port, function(){
  console.log('listening on *:' + port);
});

