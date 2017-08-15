#!/usr/bin/env python
#---------------------------------------------------------------------------------------------------
# Script to read the standardized input from the physics department to generate the required slots.
#
#   input file: $TAPAS_TOOLS_DATA/spreadsheets/${SEMESTERID}Courses.csv
#
#                                                             Written: Aug 04, 2017 (Christoph Paus)
#---------------------------------------------------------------------------------------------------
import sys,os

empty = 'EMPTY@mit.edu'
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

# say what we do
print '\n Generating slots for semester: %s\n'%(period)

# reading the file in one shot
with open('%s/spreadsheets/%sCourses.csv'%(os.getenv('TAPAS_TOOLS_DATA'),period),'r') as f:
    content = f.read()

# get all lines into an array
lines = content.split('\n')

# initialize counters
totalLec = 0
totalRec = 0
totalTasF = 0.
totalTasP = 0.

# loop through the lines
for line in lines:
    if len(line) < 1 or line[0] == '#':
        continue
    
    if debug:
        print ' > ' + line

    f = line.split(",")
    if len(f) > 8:
        course        = f[0]
        courseEmail   = f[1].replace('/',',')
        nLec          = int(f[2])
        nRec          = int(f[3])
        nFullTimeTasR = int(f[4])
        nFullTimeTasU = int(f[5])
        nHalfTimeTasR = int(f[6])
        nHalfTimeTasU = int(f[7])
        nPartTimeTasU = int(f[8])

        nCourseTas    = 0.5 * (nHalfTimeTasR+nHalfTimeTasU)+nFullTimeTasR+nFullTimeTasU

        totalLec       += nLec
        totalRec       += nRec
        totalTasF      += nCourseTas;
        totalTasP      += nPartTimeTasU;
        
        base = " insert into Assignments%s values ('%s-%s"%(period,period,course)
    
        # print all slots per course
        
        # lecturer slots
        i = 0
        while i < nLec:
            n = i+1;
            query = "%s-Lec-%d','%s')"%(base,n,empty);
            #print query
            i += 1

        # recitation instructor slots
        i = 0
        while i < nRec:
            n = i+1;
            query = "%s%s-Rec-%d','%s')"%(base,course,n,empty);
            #print query
            i += 1

        # TA slots -------------------------------------
        i = 0
        while i < nFullTimeTasR:
            n = i+1;
            query = "%s-TaFR-%d','%s')"%(base,n,empty);
            #print query
            print '%s-%s-TaFR-%s'%(period,course,n)
            i += 1
        i = 0
        while i < nFullTimeTasU:
            n = i+1;
            query = "%s-TaFU-%d','%s')"%(base,n,empty);
            #print query
            print '%s-%s-TaFU-%s'%(period,course,n)
            i += 1
        i = 0
        while i < nHalfTimeTasR:
            n = i+1;
            query = "%s-TaHR-%d','%s')"%(base,n,empty);
            #print query
            print '%s-%s-TaHR-%s'%(period,course,n)
            i += 1
        i = 0
        while i < nHalfTimeTasU:
            n = i+1;
            query = "%s-TaHU-%d','%s')"%(base,n,empty);
            #print query
            print '%s-%s-TaHU-%s'%(period,course,n)
            i += 1
        i = 0
        while i < nPartTimeTasU:
            n = i+1;
            query = "%s-TaPU-%d','%s')"%(base,n,empty);
            #print query
            print '%s-%s-TaPU-%s'%(period,course,n)
            i += 1
            
if debug:
    print "  Total %3d %3d                %4.1f %4.1f\n"%(totalLec,totalRec,totalTasF,totalTasP)
    
sys.exit(0)
