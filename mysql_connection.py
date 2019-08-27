import mysql.connector
mydb = mysql.connector.connect(
  host="localhost",
  user="root",
  passwd="Hemdip@2810",
 database ="Reha"
)
mycursor = mydb.cursor()

sql = "INSERT INTO users (username, password) VALUES (%s, %s)"
val = ("hemant", "hemdip28")

mycursor.execute(sql, val)

mydb.commit()

print(mycursor.rowcount, "record inserted.")

