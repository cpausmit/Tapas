#!/usr/bin/python
#---------------------------------------------------------------------------------------------------
# Add preferences for a given TA.
#
#---------------------------------------------------------------------------------------------------
import sys,os,re
import MySQLdb
import Database

usage  = " usage: addPreferences.py  <email>  <term>  <pref1>  <pref2>  <pref3> [ <comment> ]\n\n"
usage += "           email           students email address             (ex. example@mit.edu)\n"
usage += "           term            term                               (ex. F2022)\n"
usage += "           pref1           preference 1                       (ex. 8.06-TaFR)\n"
usage += "           pref2           preference 2                       (ex. 8.08-TaFR)\n"
usage += "           pref3           preference 3                       (ex. 8.962-TaFR)\n"
usage += "           comment         comment about situation            (ex. 'I love to TA.')\n\n"

comment = ""
if len(sys.argv) < 6:
    print("\n ERROR - need to specify full set of required parameters (%d)\n"%(len(sys.argv)))
    print(usage)
    sys.exit(0)

# Read command line arguments
email = sys.argv[1]
term = sys.argv[2]
pref1 = sys.argv[3]
pref2 = sys.argv[4]
pref3 = sys.argv[5]
if len(sys.argv)>6:
    comment = sys.argv[6]
   
# Open database connection
db = Database.DatabaseHandle()
# Prepare a cursor object using cursor() method
cursor = db.getCursor()

# Find the list of active TA table
sql = "select * from Preferences where Email = '%s' and Term = '%s'"%(email,term);
nMatches = 0
try:
    # Execute the SQL command
    cursor.execute(sql)
    # Fetch all the rows in a list of lists.
    results = cursor.fetchall()
    for row in results:
        nMatches += 1
        print(row)
except:
    print(" Error (%s): unable to fetch data."%(sql))
    # disconnect from server
    db.disco()
    sys.exit()

if nMatches == 0:    # now we just add the new student
    sql = "insert into Teaching.Preferences (Term,Email,Pref1,Pref2,Pref3,Comment)" + \
          " values ('%s','%s','%s-%s-1','%s-%s-1','%s-%s-1','%s')"%(term,email,term,pref1,term,pref2,term,pref3,comment)
    print(" SQL> " + sql)
#    try:
#        # Execute the SQL command
#        cursor.execute(sql)
#    except:
#        print " ERROR - insertion of new TA (%s) into the database failed."%(email)

# disconnect from server
db.disco()
