var app = require('express')();
var server = require('http').Server(app);
var io = require('socket.io')(server);
var redis = require('redis');
var redisClient = redis.createClient();
server.listen(8890);
users = {};
var pub = redis.createClient();
var sub = redis.createClient();
redisClient.psubscribe('*', function (err, count) {
  console.log('err', err);
  console.log('count', count);
});

/**
 * Get the saved data and send to socket user
 */
redisClient.on("pmessage", function (subscribed, channel, data) {
  if (channel != 'save-data') {
    var data = JSON.parse(data);
    console.log('data', data);
    io.emit(channel, data);
  }
});

/**
 * Made a connection
 * Create and room and send the data to laravel server and save the data into DB
 */
io.on('connection', function (socket) {
  console.log('connection');
  socket.on('switchRoom', function (room, payload) {
    if (socket.room != room) {
      console.log('in room change');
      socket.leave(socket.room);
      socket.room = room;
      socket.join(room);
      // socket.emit('updaterooms', socket.room);
    }
    // Send the data to DB for saving the data
    if (room != undefined && payload != undefined) {
      console.log('INCOMING MESSAGE', payload.room_id);
      var payload = JSON.stringify(payload);
      pub.publish('save-data', payload);
    }
  });

  /**
   * Check the login user online/offline
   */
  socket.on('isOnline', function (channel, data) {
    data.channel = channel;
    io.emit(channel, data);
    // saving userId to array with socket ID
    users[socket.id] = data;
  });

  /**
   * Check the save online user data
   */
  socket.on('checkOnlineUser', function (data) {
    if (typeof users === 'object' && users !== null) {
      Object.entries(users).forEach(entry => {
        const [key, value] = entry;
        if (value.channel === data.check_user && value.room_id === data.room_id && value.online === true) {
          io.emit(value.channel, value);
        }
      });
    }
  });

  /**
   * Disconnect the connection
   */
  socket.on('disconnect', function () {
    console.log('disconnect');
    if (users[socket.id] != undefined && users[socket.id] != null && Object.keys(users[socket.id]).length !== 0) {
      var onlineOffline = users[socket.id];
      onlineOffline.online = false;
      io.emit(onlineOffline.channel, onlineOffline);

      delete users[socket.id];
    }
    socket.leave(socket.room);
  });
});
