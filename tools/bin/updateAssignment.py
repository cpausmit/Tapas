#!/usr/bin/python
#---------------------------------------------------------------------------------------------------
# Update one assignments in the database using the unique task id. If there is no previous record
# that matches the query will fail.
#---------------------------------------------------------------------------------------------------
import sys,os,re
import MySQLdb
import Database

debug = False

usage  = " usage:  updateAssignment.py <taskId>  <email>  [ <execute = no> ]\n\n"
usage += "           taskId          identification string for a specific task\n"
usage += "                           ex. S14-8.962-Lec-1\n"
usage += "           email           unique email of the person for the task\n"
usage += "                           ex. paus@mit.edu\n"
usage += "           execute         should we execute the insertion into the database\n"
usage += "                           activate by setting: execute = exec\n\n"

if len(sys.argv) < 3:
    print "\n ERROR - need to specify all required parameters.\n"
    print usage
    sys.exit(0)

# Read command line arguments
taskId = sys.argv[1]
email  = sys.argv[2]
execute = "no"
if len(sys.argv) > 3:
    execute = sys.argv[3]

# extract the semsterId
semesterId = taskId.split('-')[0]

# Open database connection
db = Database.DatabaseHandle()
# Prepare a cursor object using cursor() method
cursor = db.getCursor()

# Prepare SQL query to screate the new table
sql = "select * from Assignments" + semesterId + " where Task = '" + taskId + "';"
nResults = 0
try:
    # Execute the SQL command
    if debug:
        print " MYSQL> " + sql
    cursor.execute(sql)
    results = cursor.fetchall()
    for row in results:
        nResults += 1
        task    = row[0]
        person  = row[1]
        print " >> " + task + "  -->  " + person
except:
    print " ERROR - table search failed."

if nResults != 1:
    print " ERROR - EXIT entry did not find one match (" + str(nResults) + ") task: " + taskId
    # disconnect from server
    db.disco()
    # exit
    sys.exit(1)

# Prepare SQL query to screate the new table
# UPDATE  table_name  SET  field1=new-value1, field2=new-value2
sql = "update Assignments" + semesterId + " set Person = '" + email + \
      "' where Task = '" + taskId + "';"
nResults = 0
try:
    # Execute the SQL command
    if debug or execute != 'exec':
        print " MYSQL> " + sql
    if execute == 'exec':
        cursor.execute(sql)
except:
    print " ERROR - table update failed."

# disconnect from server
db.disco()
# exit
sys.exit()
