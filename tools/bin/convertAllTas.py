#!/usr/bin/python
# --------------------------------------------------------------------------------------------------
# Convert all tas from many separate tables per semester to a single table with additional
# semester Id.
#
#---------------------------------------------------------------------------------------------------
import sys,os,re
import MySQLdb
import Database

#---------------------------------------------------------------------------------------------------
# H E L P E R
#---------------------------------------------------------------------------------------------------
def makeTasTable(cursor,execute):
    # test whether requested table exists already and if not make the table

    # Prepare SQL query to test whether table exists
    sql = "describe Tas;"
    try:
        # Execute the SQL command
        print " MYSQL> " + sql
        if execute == "exec":
            cursor.execute(sql)
            print ' INFO -- table (Tas) exists already.\n'
    except:
        print ' INFO - table (Tas) does not yet exist.\n'

        # Prepare SQL query to create the new table
        sql = "create table Tas(Term char(5), Email char(40)," + \
              " FullTime tinyint(4), PartTime tinyint(4));" + \
              " alter table Tas add constraint onePerTerm unique(Term, Email);"
        try:
            # Execute the SQL command
            print " MYSQL> " + sql
            if execute == "exec":
                cursor.execute(sql)
        except:
            print ' ERROR - table creation failed.'

def findAllTaTables(cursor):

    tables = []
    results = []

    sql = "show tables like 'Tas_____'"
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

def convertTaTable(cursor,table,execute):
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
        email = row[0]
        fullTime = int(row[1])
        partTime = int(row[2])
        sql = "insert into Tas values ('" + email + "',%d,%d"%(fullTime,partTime)+ ");"
        try:
            # Execute the SQL command
            #print " MYSQL> " + sql
            if execute == "exec":
                cursor.execute(sql)
        except:
            print ' ERROR - insert failed: ' + sql

    return
        
#---------------------------------------------------------------------------------------------------
# M A I N
#---------------------------------------------------------------------------------------------------
usage  = " usage:  convertAllTas.py [ <execute = no> ]\n\n"
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
makeTasTable(cursor,execute)

# Find all tables to be converted
tables = findAllTaTables(cursor)

# Loop through the relevant tables
for table in tables:
    print ' Convert table: ' + table
    convertTaTable(cursor,table,execute)

# make sure to commit all changes
db.commit()
# disconnect from server
db.disco()

# exit
sys.exit()
