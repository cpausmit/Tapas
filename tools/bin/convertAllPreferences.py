#!/usr/bin/python
# --------------------------------------------------------------------------------------------------
# Convert all Preferences from many separate tables per semester to a
# single table with additional semester Id.
#
#---------------------------------------------------------------------------------------------------
import sys,os,re
import MySQLdb
import Database

#---------------------------------------------------------------------------------------------------
# H E L P E R
#---------------------------------------------------------------------------------------------------
def makePreferencesTable(cursor,execute):
    # test whether requested table exists already and if not make the table

    # Prepare SQL query to test whether table exists
    sql = "describe Preferences;"
    try:
        # Execute the SQL command
        print " MYSQL> " + sql
        if execute == "exec":
            cursor.execute(sql)
            print ' INFO -- table (Preferences) exists already.\n'
    except:
        print ' INFO - table (Preferences) does not yet exist.\n'

        # Prepare SQL query to create the new table
        sql = "create table Preferences" +\
              "(Term char(5), Email char(40), Pref1 text, Pref2 text, Pref3 text);" + \
              " alter table Preferences add constraint onePerTerm unique(Term, Email);"
        try:
            # Execute the SQL command
            print " MYSQL> " + sql
            if execute == "exec":
                cursor.execute(sql)
        except:
            print ' ERROR - table creation failed.'

def findAllPreferencesTables(cursor):

    tables = []
    results = []

    sql = "show tables like 'Preferences_____'"
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

def convertPreferencesTable(cursor,table,execute):
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
        pref1 = row[1]
        pref2 = row[2]
        pref3 = row[3]

        sql = "insert into Preferences values ('" + \
              term + "','" + \
              email + "','" + \
              pref1 + "','" + \
              pref2 + "','" + \
              pref3 + "');"
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
usage  = " usage:  convertAllPreferences.py [ <execute = no> ]\n\n"
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
makePreferencesTable(cursor,execute)

# Find all tables to be converted
tables = findAllPreferencesTables(cursor)

# Loop over all old tables and convert
nEntries = 0
for table in tables:
    #print ' Convert table: ' + table
    nEntries += convertPreferencesTable(cursor,table,execute)

print '\n Converted %d entries in total.\n'%(nEntries)

# make sure to commit all changes
db.commit()
# disconnect from server
db.disco()

# exit
sys.exit()
