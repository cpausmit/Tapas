#!/usr/bin/python
# --------------------------------------------------------------------------------------------------
# Convert all assignments from many separate tables per semester to a single table with additional
# semester Id.
#
#---------------------------------------------------------------------------------------------------
import sys,os,re
import MySQLdb
import Database

#---------------------------------------------------------------------------------------------------
# H E L P E R
#---------------------------------------------------------------------------------------------------
def makeAssignmentsTable(cursor,execute):
    # test whether requested table exists already and if not make the table

    # Prepare SQL query to test whether table exists
    sql = "describe Assignments;"
    try:
        # Execute the SQL command
        print " MYSQL> " + sql
        if execute == "exec":
            cursor.execute(sql)
            print ' INFO -- table (Assignments) exists already.\n'
    except:
        print ' INFO - table (Assignments) does not yet exist.\n'

        # Prepare SQL query to create the new table
        sql = "create table Assignments(Term char(5), Task char(40), Person char(40));" + \
              " alter table Assignments add unique idTask(Task);"
        try:
            # Execute the SQL command
            print " MYSQL> " + sql
            if execute == "exec":
                cursor.execute(sql)
        except:
            print ' ERROR - table creation failed.'

def findAllAssignmentTables(cursor):

    tables = []
    results = []

    sql = "show tables like 'Assignments_____'"
    try:
        # Execute the SQL command
        cursor.execute(sql)
        results = cursor.fetchall()
    except:
        print ' ERROR - select failed: ' + sql

    for row in results:
        table = row[0]
        tables.append(table)
        
    return tables

def convertAssignmentTable(cursor,table,execute):
    # convert all entries in the given table

    # Prepare SQL query to insert record into the existing table
    term = table[-5:]
    sql = "select * from " + table + ";"
    try:
        # Execute the SQL command
        cursor.execute(sql)
        results = cursor.fetchall()
    except:
        print ' ERROR - select failed: ' + sql
        return
        
    for row in results:
        task = row[0]
        person = row[1]
        sql = "insert into Assignments values ('" + term + "','" + task + "','" + person + "');"
        try:
            # Execute the SQL command
            print " MYSQL> " + sql
            if execute == "exec":
                cursor.execute(sql)
        except:
            print ' ERROR - insert failed: ' + sql

    return
        
#---------------------------------------------------------------------------------------------------
# M A I N
#---------------------------------------------------------------------------------------------------
usage  = " usage:  convertAllAssignments.py [ <execute = no> ]\n\n"
usage += "           execute         should we execute the insertion into the database\n"
usage += "                           activate by setting: execute = exec\n\n"

# Read command line arguments
execute = "no"
if len(sys.argv) > 1:
    execute = sys.argv[1]

# Open database connection
db = Database.DatabaseHandle()
# Prepare a cursor object using cursor() method
cursor = db.getCursor()

# Make the new summary table
makeAssignmentsTable(cursor,execute)

# Find all tables to be converted
tables = findAllAssignmentTables(cursor)

# Loop through the relevant tables
for table in tables:
    print ' Convert table: ' + table
    convertAssignmentTable(cursor,table,execute)

# make sure to commit all changes
db.commit()
# disconnect from server
db.disco()

# exit
sys.exit()
