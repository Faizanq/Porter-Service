const express = require("express");
const router = express.Router();
const fs = require('fs');


router.get("/", (req, res) => {
  res.send({ response: "I am alive" }).status(200);
});

router.get("/test", (req, res) => {

  // res.render(__dirname + '/../resources/views/socket/index');

  res.writeHead(200,{"Content-Type":"text/html"});
  //Passing HTML To Browser
  res.write(fs.readFileSync(__dirname + '/../resources/views/socket/index.html'));
  //Ending Response
  res.end();

});


module.exports = router;