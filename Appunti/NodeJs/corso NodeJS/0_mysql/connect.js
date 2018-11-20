var mysql = require('db-mysql');
 
new mysql.Database({
      hostname: 'MySql',
      user: 'root',
      password: 'Sciarra82',
      database: 'node_test'
}).connect(function(error) {
       
      if(error) return console.log("Connection error");
      this.query().select("*").from("libri")
                  .where("letto = ?", [true])
                  .order({ titolo: true })
                  .execute(function(error, rows, cols) {
                     if(error) return console.log("Query error");
                     for(var i in rows) console.log(rows[i]);
                  });
});