const express = require("express");
const http = require("http");
const socketIo = require("socket.io");
const axios = require("axios");
const port = process.env.PORT || 4001;
const mysql = require('mysql');
const index = require("./routes/index");
const app = express();
var bodyParser = require('body-parser');


app.use(bodyParser.urlencoded({
    extended: true
}));



var con = mysql.createConnection({
  host: "localhost",
  user: "root",
  password: "123",
  database: "portal_db"
});

con.connect(function(err) {
  if (err) throw err;
  console.log("DataBase Connected!");
});

app.use(index);

const server = http.createServer(app);

const io = socketIo(server);

io.set('heartbeat timeout',  50000);
io.set('heartbeat interval', 25000);

//Routing To Public Folder For Any Static Context
app.use(express.static(__dirname + '/public'));


//For Tracking When User Connects:
io.sockets.on("connection",function(socket){
  
  	console.log('In Socket');

 //  	socket.on('ping', function(){
	// console.log('ping');
	// });

	// socket.on('pong', function(ms){
	// console.log('pong ' + ms + "ms");
	// });


	socket.on("join",function(req,res){

	  console.log('In join : ',req);

	  id = req.user_id;

	  if(id != undefined){
		  var sql = `UPDATE users SET is_connected = 'Y' WHERE id = ${id}`;
		  
		  console.log(sql);

		  con.query(sql, function (err, result) {
		    if (err) throw err;
		    console.log(result.affectedRows + " record(s) updated");
		  });
		  console.log('Use Got connected');
	  }

	})



	  //For Tracking When User Disconnects:
	socket.on("disconnect",function(req,res){

		  console.log('In Leave : ',req);

		  id = req.user_id;
		if(id != undefined){
		  var sql = `UPDATE users SET is_connected = 'N' WHERE id = ${id}`;
		  con.query(sql, function (err, result) {
		    if (err) throw err;
		    console.log(result.affectedRows + " record(s) updated");
		  });
		  console.log('You are disconncted:',id);
		}

	})


	socket.on("updatelocation",function(req,res){

		  console.log('In location : ',req);

		  id = req.user_id;
		  // let data = {latitude:req.latitude,longitude:req.longitude}

		if(id != undefined){
		  var sql = `UPDATE users SET latitude = ? , longitude = ? WHERE id = ${id}`;
		  con.query(sql, [req.latitude,req.longitude],function (err, result) {
		    if (err) throw err;
		    console.log(result.affectedRows + " record(s) updated");
		  });
		  console.log('Location updated:',id);
		}

	})


})

server.listen(port, () => console.log(`Listening on port ${port}`));
