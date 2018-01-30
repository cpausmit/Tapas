#!/usr/bin/env python
from bs4 import BeautifulSoup
import sys, re, os
import requests
import pandas as pd
import Evaluation
import Database

def getTerm(mitTerm):

    year = int(mitTerm[:4])
    
    if 'FA' in mitTerm:
        tapasTerm = "F%4d"%(year-1)
    elif 'SP' in mitTerm:
        tapasTerm = "S%4d"%(year)
    else:
        print " ERROR - MIT term (%s) not defined."%(mitTerm)
        sys.exit(1)
        
    return tapasTerm
        
def getCookies(cookie_file):

    cookies = requests.cookies.RequestsCookieJar()

    with open(cookie_file,"r") as f:
        data = f.read()

    lines = data.split(";")
    for cookie in lines:
        cookie = cookie.strip()
        (cookie_key,cookie_value) = cookie.split('=')
        cookies.set(cookie_key,cookie_value,domain='mit.edu', path='/')
        print cookie_key + " --> " + cookie_value

    return cookies

def getEvaluationsForSubject(url,cookies,number,evs):
    
    r = requests.get(url,cookies=cookies)
    data = r.text
    soup = BeautifulSoup(data,"lxml")
    
    table = soup.find_all('table')[4]
    rows = table.find_all('tr')[2:]

    for row in rows:
        tds = row.find_all('td')
        if len(tds)>0:                              # make sure we do not have crashes
            strongs = tds[0].find_all('strong')
            if len(strongs)>0:                      # make sure we do not have crashes
                name = strongs[0].get_text()
                last_name = name.split(',')[0]
                first_name = name.split(',')[-1]
                overall_grade = float(row.find_all('td')[4].find_all('span')[0].get_text())
                
                #print "%20s %20s %4.1f"%(last_name, first_name, overall_grade)
                ev = Evaluation.Eval(number,last_name,first_name,'UNKNOWN',overall_grade)
                evs.append(ev)
                
    return evs

def findLastName(name,list):
    nMatch = 0
    for eml, entry in list.getHash().iteritems():
        if name == entry.lastName:
            nMatch += 1
    return nMatch
            
#===================================================================================================
#                                       M A I N
#===================================================================================================
# get our cookies
cookies = getCookies("/home/paus/.cookies")

# command line
term = sys.argv[1]
tapasTerm = getTerm(term)

print " TERM: %s (MIT: %s)"%(tapasTerm,term)

# base
trunc = 'https://edu-apps.mit.edu/ose-rpt/'
evaluation_url = 'subjectEvaluationSearch.htm?'
parameters = 'departmentId=+++8&search=Search&termId=%s&subjectCode=&instructorName='%term

# the url
url = trunc + evaluation_url + parameters

# get the page output
r = requests.get(url,cookies=cookies)

data = r.text

# scrape it
evs = []
soup = BeautifulSoup(data,"lxml")
paras = soup.find_all('p')
for row in paras:
    for link in row.find_all('a'):
        evaluation_link = link.get('href')
        if 'subjectId' in evaluation_link:

            p = re.compile('subjectId=.*$')
            matches = p.findall(evaluation_link)
            if len(matches) == 0:
                p = re.compile('subjectId=.*&')
                matches = p.findall(evaluation_link)
            if len(matches) > 0:
                number = matches[0].split("=")[1]
                print " %s --> %s"%(term,number)
                evs = getEvaluationsForSubject(trunc+evaluation_link,cookies,number,evs)

# Open database connection
db = Database.DatabaseHandle()

# Make a new objects of faculties
teachers = Database.Container()
rc = teachers.fillWithTeachers(db.handle)
if rc != 0:
    print " ERROR - filling teachers."
    # disconnect from server
    db.disco()
    sys.exit()

# Make a new objects of students
students = Database.Container()
rc = students.fillWithStudents(db.handle)
if rc != 0:
    print " ERROR - filling students."
    # disconnect from server
    db.disco()
    sys.exit()

# Make a new objects of students
assignments = Database.Container()
rc = assignments.fillWithAssignments(db.handle)
if rc != 0:
    print " ERROR - filling assignments."
    # disconnect from server
    db.disco()
    sys.exit()
    
# find matching emails for the evaluation
for ev in evs:

    nMatchStudents = findLastName(ev.lastName,students)
    nMatchTeachers = findLastName(ev.lastName,teachers)
    
    if nMatchStudents+nMatchTeachers > 1:
        print '\n ==== AMBIGUOUS ===='
        ev.show()
        print " nStudents: %d, NTeachers: %d"%(nMatchStudents,nMatchTeachers)
        
    done = False
    for eml, student in students.getHash().iteritems():
        if ev.lastName == student.lastName:
            done = True
            #print '\n ==== MATCH ===='
            #ev.show()
            #student.show()
            ev.update(student.eMail)
    if not done:
        for eml, teacher in teachers.getHash().iteritems():
            if ev.lastName == teacher.lastName:
                done = True
                #print '\n ==== MATCH ===='
                #ev.show()
                #teacher.show()
                ev.update(teacher.eMail)

    if not done:
        print '\n ERROR - could not match evaluation.\n '
        ev.show()
        print '\n '


for ev in evs:

    if ev.email == 'UNKNOWN':
        ev.show()
        continue
    if ev.lastName == 'Williams':
        ev.show()
    
    for task, assignment in assignments.getHash().iteritems():
        if ev.email == assignment.person:
            assignment.update(ev.evalO)

# list the updates
for task, assignment in assignments.getHash().iteritems():
    if assignment.term == tapasTerm:
        assignment.show()

