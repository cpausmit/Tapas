#!/usr/bin/python
# --------------------------------------------------------------------------------------------------
# Convert all Evaluations from many separate tables per semester to a single table with additional
# semester Id.
#
#---------------------------------------------------------------------------------------------------
import sys,os,re
import MySQLdb
import Database

#---------------------------------------------------------------------------------------------------
# H E L P E R
#---------------------------------------------------------------------------------------------------
def makeEvaluationsTable(cursor,execute):
    # test whether requested table exists already and if not make the table

    # Prepare SQL query to test whether table exists
    sql = "describe Evaluations;"
    try:
        # Execute the SQL command
        print " MYSQL> " + sql
        if execute == "exec":
            cursor.execute(sql)
            print ' INFO -- table (Evaluations) exists already.\n'
    except:
        print ' INFO - table (Evaluations) does not yet exist.\n'

        # Prepare SQL query to create the new table
        sql = "create table Evaluations" +\
              "(Term char(5), TeacherEmail char(40), TaEmail char(40)," +\
              " Award tinyint(4), EvalText text, Citation text);"
        try:
            # Execute the SQL command
            print " MYSQL> " + sql
            if execute == "exec":
                cursor.execute(sql)
        except:
            print ' ERROR - table creation failed.'

def findAllEvaluationsTables(cursor):

    tables = []
    results = []

    sql = "show tables like 'Evaluations_____'"
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

def convertEvaluationsTable(cursor,table,execute):
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
        teacherEmail = row[0]
        taEmail = row[1]
        award = int(row[2])
        evalText = re.escape(row[3])
        citation = re.escape(row[4])

        sql = "insert into Evaluations values ('" + \
              term + "','" + \
              teacherEmail + "','" + \
              taEmail + "'," + \
              "%d"%award + ",'" + \
              evalText + "','" + \
              citation + "');"
        try:
            # Execute the SQL command
            #print " MYSQL> " + sql
            if execute == "exec":
                cursor.execute(sql)
        except:
            print ' ERROR - insert failed: ' + sql

    print ' -->  %d  in  %s.\n'%(len(results),table)


    return len(results)
        
#---------------------------------------------------------------------------------------------------
# M A I N
#---------------------------------------------------------------------------------------------------
usage  = " usage:  convertAllEvaluations.py [ <execute = no> ]\n\n"
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

# Create the new summary table
makeEvaluationsTable(cursor,execute)

# Find all tables to be converted
tables = findAllEvaluationsTables(cursor)

# Loop over all old tables and convert
nEntries = 0
for table in tables:
    #print ' Convert table: ' + table
    nEntries += convertEvaluationsTable(cursor,table,execute)


print '\n Converted %d entries in total.\n'%(nEntries)

# make sure to commit all changes
db.commit()
# disconnect from server
db.disco()

# exit
sys.exit()
