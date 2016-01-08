#!/usr/bin/python
#---------------------------------------------------------------------------------------------------
# Show all courses in the database.
#
#---------------------------------------------------------------------------------------------------
import sys
import MySQLdb
import Database

usage  = " usage: showCourses.py  <semesterId>\n\n"
usage += "          semesterId    identification string for a specific semster\n"
usage += "                        ex. F13 (Fall 2013), I13 (IAP 2013), S13 (Spring 2013)\n\n"

if len(sys.argv) < 2:
    print "\n ERROR - need to specify the semester id.\n"
    print usage
    sys.exit(0)

# Open database connection
db = Database.DatabaseHandle()
# Prepare a cursor object using cursor() method
cursor = db.getCursor()

# Make a new objects of all courses
courses = Database.Container()
rc = courses.fillWithCourses(db.handle)
if rc != 0:
    print " ERROR - filling courses."
    # disconnect from server
    db.disco()
    sys.exit()

courses.show()

# disconnect from server
db.disco()
