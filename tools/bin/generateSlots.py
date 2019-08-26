#!/usr/bin/env python
#---------------------------------------------------------------------------------------------------
# Script to read the standardized input from the physics department to generate the required slot
# tokens. No entries in the database are generated. The tokens are then used to make an assignment
# files which will be pushed into the database.
#
#   input table: CourseResources
#
#                                                             Written: Aug 04, 2017 (Christoph Paus)
#                                                                      May 25, 2018 (Christoph Paus)
#---------------------------------------------------------------------------------------------------
import sys,os
import MySQLdb
import Database

period = ''
test = ''
debug = True

usage  = " usage: generateSlots.py  <semesterId>  <dbUpdate> \n\n"
usage += "          semesterId       identification string for a specific semster\n"
usage += "                           ex. F2017 (Fall 2017), I2018 (IAP 2018), S2018 (Spring 2018)\n"
usage += "          debug            should this script just produce the list?\n"
usage += "                           '' (anything will trigger clean list)\n\n"

if len(sys.argv) < 2:
    print "\n ERROR - need to specify the semester id and the database option.\n"
    print usage
    sys.exit(0)

# command line arguments
period = sys.argv[1]
if len(sys.argv) > 2:
    test = sys.argv[2]

if test != '':
    debug = False

# Open database connection
db = Database.DatabaseHandle()
# Prepare a cursor object using cursor() method
cursor = db.getCursor()

# Make a new objects of students
courseResources = Database.Container()
rc = courseResources.fillWithCourseResources(db.handle,period)
if rc != 0:
    print " ERROR - filling course resources."
    # disconnect from server
    db.disco()
    sys.exit()


# initialize counters
totalLec = 0
totalRec = 0
totalTasF = 0.
totalTasP = 0.

# loop through the list of resources
for number in sorted(courseResources.getHash()):

    # get the specific course resource
    cr = courseResources.retrieveElement(number)
    
    if debug:
        print ' ---- '
        cr.show()

    # print the slots
    cr.printSlots()

    # some overall accounting
    nCourseTas = (cr.numHalfRecTas+cr.numHalfUtilTas)/2. +cr.numFullRecTas+cr.numFullUtilTas
    totalLec   += cr.numLecturers
    totalRec   += cr.numRecitators
    totalTasF  += nCourseTas;
    totalTasP  += cr.numPartUtilTas;
    
if debug:
    print "  Total %3d %3d                %4.1f %4.1f\n"%(totalLec,totalRec,totalTasF,totalTasP)

sys.exit(0)
